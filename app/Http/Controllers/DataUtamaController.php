<?php

namespace App\Http\Controllers;

use App\Models\DataJumlah;
use App\Models\DataUtama;
use App\Models\Department;
use App\Models\JenisDataPtj;
use App\Models\SubUnit;
use App\Models\Tahun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataUtamaController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(auth()->id());
        $perPage = $request->input('perPage', 10);

        $tahunList = Tahun::orderBy('tahun', 'asc')->get();

        if ($user->hasAnyRole(['Superadmin', 'Admin'])) {
            // Superadmin akses semua
            $datautamaList = DataUtama::latest()->paginate($perPage);
        } else {
            // Biasa ikut department
            $datautamaList = DataUtama::where('department_id', $user->department_id)
                ->latest()->paginate($perPage);
        }

        return view('pages.datautama.index', [
            'datautamaList' => $datautamaList,
            'tahunList' => $tahunList,
            'perPage' => $perPage,
        ]);
    }

    public function dashboard(Request $request)
    {
        $query = DataUtama::with(['department', 'jenisDataPtj', 'jumlahs.tahun'])
            ->withCount('jumlahs');

        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        $dataList = $query->get()->groupBy('department.name');

        $departmentList = Department::orderBy('name')->get();

        return view('pages.datautama.dashboard', [
            'dataList' => $dataList,
            'departmentList' => $departmentList,
            'selectedDepartment' => $request->department_id,
        ]);
    }
    public function create()
    {
        $departmentId = Auth::user()->department_id;

        $subunitList = SubUnit::where('department_id', $departmentId)->get();

        $jenisDataIdYangDahIsi = DataUtama::pluck('jenis_data_ptj_id')->toArray();

        $jenisDataList = JenisDataPtj::where('department_id', $departmentId)
            ->whereNotIn('id', $jenisDataIdYangDahIsi)
            ->get();

        $tahunList = Tahun::orderBy('tahun')->get();

        $currentYear = Carbon::now()->year;

        // Check if current year is already in the list
        if (!$tahunList->contains('tahun', $currentYear)) {
            // Tambah sebagai collection manual
            $extra = new \stdClass();
            $extra->id = null; // Sebab bukan dari DB
            $extra->tahun = $currentYear;
            $tahunList->push($extra);
        }

        // Sort semula jika perlu
        $tahunList = $tahunList->sortBy('tahun')->values();

        return view('pages.datautama.create', [
            'save_route' => route('datautama.store'),
            'str_mode' => 'Tambah',
            'subunitList' => $subunitList,
            'jenisDataList' => $jenisDataList,
            'tahunList' => $tahunList,
        ]);
    }

    public function store(Request $request)
    {
        $departmentId = Auth::user()->department_id;
        $request->validate([
            'subunit_id' => 'nullable|exists:sub_units,id',
            'jenis_data_ptj_id' => 'required|exists:jenis_data_ptjs,id',
            'jenis_nilai' => 'required|in:Bilangan,Peratus,Mata Wang',
            'doc_link' => 'nullable|url',
            'jumlah' => 'array',
            'jumlah.*' => 'nullable|numeric',
            'is_kpi' => 'array',
            'is_kpi.*' => 'required|boolean',
            'pi_no' => 'array',
            'pi_target' => 'array',
        ]);

        // Custom validation untuk pi_no & pi_target jika is_kpi = 1
        $errors = [];

        foreach ($request->is_kpi as $tahunKey => $value) {
            if ($value == 1) {
                // No. PI validation
                if (!isset($request->pi_no[$tahunKey]) || trim($request->pi_no[$tahunKey]) === '') {
                    $errors["pi_no.$tahunKey"] = 'No. PI diperlukan jika KPI ditanda Ya bagi tahun ' . $tahunKey;
                }

                // Sasaran PI validation
                $piTargetValue = $request->pi_target[$tahunKey] ?? null;
                if (!isset($piTargetValue) || !is_numeric($piTargetValue)) {
                    $errors["pi_target.$tahunKey"] = 'Sasaran PI mesti nombor dan diperlukan bagi tahun ' . $tahunKey;
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $jenisData = JenisDataPtj::findOrFail($request->jenis_data_ptj_id);

        if ($jenisData->department_id !== $departmentId) {
            abort(403, 'Anda tidak dibenarkan menambah data untuk PTJ lain.');
        }

        $alreadyExists = DataUtama::where('jenis_data_ptj_id', $request->jenis_data_ptj_id)->exists();

        if ($alreadyExists) {
            return redirect()->back()->withErrors(['jenis_data_ptj_id' => 'Data ini telah diisi.']);
        }

        // Simpan data utama
        $dataUtama = DataUtama::create([
            'department_id' => $departmentId,
            'subunit_id' => $request->subunit_id,
            'jenis_data_ptj_id' => $request->jenis_data_ptj_id,
            'jenis_nilai' => $request->jenis_nilai,
            'doc_link' => $request->doc_link,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Simpan nilai jumlah ikut tahun
        if ($request->has('jumlah')) {
            foreach ($request->jumlah as $tahunKey => $value) {
                if ($value === null) continue;

                $tahunId = is_numeric($tahunKey)
                    ? $tahunKey
                    : Tahun::firstOrCreate(
                        ['tahun' => (int) str_replace('year_', '', $tahunKey)],
                        ['publish_status' => 1]
                    )->id;

                DataJumlah::create([
                    'data_utama_id' => $dataUtama->id,
                    'tahun_id' => $tahunId,
                    'jumlah' => $value,
                    'is_kpi' => $request->is_kpi[$tahunKey] ?? false,
                    'pi_no' => ($request->is_kpi[$tahunKey] ?? false) ? $request->pi_no[$tahunKey] ?? null : null,
                    'pi_target' => $request->pi_target[$tahunKey] ?? null,
                ]);
            }
        }

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $tahunList = Tahun::orderBy('tahun', 'asc')->get();
        $datautama = DataUtama::with('jumlahs.tahun')->findOrFail($id);
        $this->authorizeDataAccess($datautama);

        $perbandinganByYear = [];

        foreach ($datautama->jumlahs as $jumlah) {
            if ($jumlah->tahun && $jumlah->tahun->tahun && $jumlah->jumlah !== null && $jumlah->jumlah != 0) {
                $tahun = $jumlah->tahun->tahun;
                $perbandinganByYear[$tahun] = [
                    'jumlah' => $jumlah->jumlah,
                    'pi_target' => $jumlah->pi_target ?? 0 // fallback to 0 if null
                ];
            }
        }

        ksort($perbandinganByYear);

        return view('pages.datautama.view', [
            'datautama' => $datautama,
            'tahunList' => $tahunList,
            'perbandinganByYear' => $perbandinganByYear,
        ]);
    }

    public function edit($id)
    {
        $departmentList = Department::orderBy('name')->get();
        $user = User::find(auth()->id());
        $dataUtama = DataUtama::with('jumlahs')->findOrFail($id);

        $this->authorizeDataAccess($dataUtama);

        if ($user->hasAnyRole(['Superadmin', 'Admin'])) {
            $subunitList = SubUnit::where('department_id', $dataUtama->department_id)->get();
            $jenisDataList = JenisDataPtj::where('department_id', $dataUtama->department_id)->get();
        } else {
            // Pengguna biasa ikut department sendiri
            $subunitList = SubUnit::where('department_id', $user->department_id)->get();
            $jenisDataList = JenisDataPtj::where('department_id', $user->department_id)->get();
        }

        $tahunList = Tahun::orderBy('tahun')->get();

        $jumlahArray = [];

        foreach ($dataUtama->jumlahs as $item) {
            $jumlahArray[$item->tahun_id] = [
                'jumlah' => $item->jumlah,
                'is_kpi' => $item->is_kpi,
                'pi_no' => $item->pi_no,
                'pi_target' => $item->pi_target,
            ];
        }
        return view('pages.datautama.edit', [
            'dataUtama' => $dataUtama,
            'save_route' => route('datautama.update', $dataUtama->id),
            'str_mode' => 'Kemaskini',
            'subunitList' => $subunitList,
            'jenisDataList' => $jenisDataList,
            'departmentList' => $departmentList,
            'tahunList' => $tahunList,
            'jumlahArray' => $jumlahArray,
        ]);
    }

    public function update(Request $request, $id)
    {
        $departmentId = Auth::user()->department_id;

        $dataUtama = DataUtama::findOrFail($id);

        $this->authorizeDataAccess($dataUtama);

        if ($dataUtama->department_id !== $departmentId) {
            abort(403, 'Anda tidak dibenarkan mengemaskini data ini.');
        }

        $request->validate([
            'subunit_id' => 'nullable|exists:sub_units,id',
            'jenis_data_ptj_id' => 'required|exists:jenis_data_ptjs,id',
            'jenis_nilai' => 'required|in:Bilangan,Peratus,Mata Wang',
            'doc_link' => 'nullable|url',
            'jumlah' => 'array',
            'jumlah.*' => 'nullable|numeric',
            'is_kpi' => 'array',
            'is_kpi.*' => 'required|boolean',
            'pi_no' => 'array',
            'pi_target' => 'array',
        ]);

        $errors = [];

        foreach ($request->is_kpi as $tahunKey => $value) {
            if ($value == 1) {
                if (empty($request->pi_no[$tahunKey])) {
                    $errors["pi_no.$tahunKey"] = 'No. PI diperlukan jika KPI ditanda Ya bagi tahun ' . $tahunKey;
                }
                if (empty($request->pi_target[$tahunKey]) || !is_numeric($request->pi_target[$tahunKey])) {
                    $errors["pi_target.$tahunKey"] = 'Sasaran PI mesti nombor dan diperlukan bagi tahun ' . $tahunKey;
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $jenisData = JenisDataPtj::findOrFail($request->jenis_data_ptj_id);
        if ($jenisData->department_id !== $departmentId) {
            abort(403, 'Anda tidak dibenarkan mengemaskini data dari PTJ lain.');
        }

        $exists = DataUtama::where('jenis_data_ptj_id', $request->jenis_data_ptj_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['jenis_data_ptj_id' => 'Data ini telah digunakan.']);
        }

        $dataUtama->update([
            'subunit_id' => $request->subunit_id,
            'jenis_data_ptj_id' => $request->jenis_data_ptj_id,
            'jenis_nilai' => $request->jenis_nilai,
            'doc_link' => $request->doc_link,
            'updated_by' => auth()->id(),
        ]);

        if ($request->has('jumlah')) {
            foreach ($request->jumlah as $tahunKey => $value) {
                $tahunId = is_numeric($tahunKey)
                    ? $tahunKey
                    : Tahun::firstOrCreate(
                        ['tahun' => (int) str_replace('year_', '', $tahunKey)],
                        ['publish_status' => 1]
                    )->id;

                DataJumlah::updateOrCreate(
                    [
                        'data_utama_id' => $dataUtama->id,
                        'tahun_id' => $tahunId,
                    ],
                    [
                        'jumlah' => $value, // boleh null
                        'is_kpi' => $request->is_kpi[$tahunKey] ?? false,
                        'pi_no' => ($request->is_kpi[$tahunKey] ?? false) ? $request->pi_no[$tahunKey] ?? null : null,
                        'pi_target' => $request->pi_target[$tahunKey] ?? null,
                    ]
                );
            }

            $dataUtama->updated_at = now();
            $dataUtama->updated_by = auth()->id();
            $dataUtama->save();
        }

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya dikemaskini.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $datautamaList = DataUtama::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $datautamaList = DataUtama::latest()->paginate(10);
        }

        return view('pages.datautama.index', [
            'datautamaList' => $datautamaList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $dataUtama = DataUtama::findOrFail($id);

        $this->authorizeDataAccess($dataUtama);

        $dataUtama->delete();

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $user = User::find(auth()->id());
        $tahunList = Tahun::orderBy('tahun', 'asc')->get();

        if ($user->hasAnyRole(['Superadmin', 'Admin'])) {
            $trashList = DataUtama::onlyTrashed()->latest()->paginate(10);
        } else {
            $trashList = DataUtama::onlyTrashed()
                ->where('department_id', $user->department_id)
                ->latest()->paginate(10);
        }

        return view('pages.datautama.trash', [
            'trashList' => $trashList,
            'tahunList' => $tahunList,
        ]);
    }

    public function restore($id)
    {
        $datautama = DataUtama::withTrashed()->findOrFail($id);

        $this->authorizeDataAccess($datautama);

        $datautama->restore();

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $datautama = DataUtama::withTrashed()->findOrFail($id);

        $this->authorizeDataAccess($datautama);

        $datautama->forceDelete();

        return redirect()->route('datautama.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }

    private function authorizeDataAccess(DataUtama $dataUtama)
    {
        $user = User::find(auth()->id());

        if (!$user->hasAnyRole(['Superadmin', 'Admin']) && $dataUtama->department_id !== $user->department_id) {
            abort(403, 'Anda tidak dibenarkan mengakses data ini.');
        }
    }
}
