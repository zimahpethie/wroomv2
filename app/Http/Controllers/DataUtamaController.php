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

    public function create()
    {
        $user = User::find(auth()->id());
        $departmentList = Department::orderBy('name')->get();
        $isSuperadmin = $user->hasAnyRole(['Superadmin', 'Admin']);
        $departmentId = $isSuperadmin ? null : $user->department_id;

        $subunitList = $departmentId
            ? SubUnit::where('department_id', $departmentId)->get()
            : SubUnit::with('department')->get();

        $jenisDataIdYangDahIsi = DataUtama::pluck('jenis_data_ptj_id')->toArray();

        $jenisDataList = JenisDataPtj::when($departmentId, function ($query) use ($departmentId) {
            return $query->where('department_id', $departmentId);
        })
            ->whereNotIn('id', $jenisDataIdYangDahIsi)
            ->get();

        $tahunList = Tahun::orderBy('tahun')->get();
        $currentYear = now()->year;

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
            'departmentList' => $departmentList,
            'tahunList' => $tahunList,
        ]);
    }

    public function store(Request $request)
    {
        $departmentId = Auth::user()->department_id;
        $request->validate([
            'subunit_id' => 'nullable|exists:sub_units,id',
            'jenis_data_ptj_id' => 'required|exists:jenis_data_ptjs,id',
            'is_kpi' => 'required|boolean',
            'pi_no' => 'required_if:is_kpi,1',
            'pi_target' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->is_kpi == 1 && ($value === null || !is_numeric($value))) {
                        $fail('PI Target diperlukan dan mesti dalam nombor jika KPI dipilih.');
                    }
                }
            ],
            'doc_link' => 'nullable|url',
            'jumlah' => 'array',
            'jumlah.*' => 'nullable|numeric',
        ], [
            'jenis_data_ptj_id.required'     => 'Sila isi jenis data',
            'jenis_data_ptj_id.unique' => 'Jenis data telah wujud',
        ]);

        $alreadyExists = DataUtama::where('jenis_data_ptj_id', $request->jenis_data_ptj_id)->exists();

        if ($alreadyExists) {
            return redirect()->back()->withErrors(['jenis_data_ptj_id' => 'Data ini telah diisi.']);
        }

        // Simpan data utama
        $dataUtama = DataUtama::create([
            'department_id' => $departmentId,
            'subunit_id' => $request->subunit_id,
            'jenis_data_ptj_id' => $request->jenis_data_ptj_id,
            'is_kpi' => $request->is_kpi,
            'pi_no' => $request->is_kpi ? $request->pi_no : null,
            'pi_target' => $request->is_kpi ? $request->pi_target : null,
            'doc_link' => $request->doc_link,
        ]);

        // Simpan nilai jumlah ikut tahun
        if ($request->has('jumlah')) {
            foreach ($request->jumlah as $tahunKey => $value) {
                if ($value === null) continue;

                if (is_numeric($tahunKey)) {
                    // Tahun dari DB
                    $tahunId = $tahunKey;
                } elseif (substr($tahunKey, 0, 5) === 'year_') {
                    // Extract 2025 dari 'year_2025'
                    $tahunTahun = (int) str_replace('year_', '', $tahunKey);

                    // Insert ke table tahun dengan publish_status = 1
                    $tahun = \App\Models\Tahun::firstOrCreate(
                        ['tahun' => $tahunTahun],
                        ['publish_status' => 1]
                    );

                    $tahunId = $tahun->id;
                } else {
                    continue;
                }

                DataJumlah::create([
                    'data_utama_id' => $dataUtama->id,
                    'tahun_id' => $tahunId,
                    'jumlah' => $value,
                ]);
            }
        }

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $tahunList = Tahun::orderBy('tahun', 'asc')->get();
        $datautama = DataUtama::findOrFail($id);
        $this->authorizeDataAccess($datautama);

        return view('pages.datautama.view', [
            'datautama' => $datautama,
            'tahunList' => $tahunList,
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

        $jumlahArray = $dataUtama->jumlahs->pluck('jumlah', 'tahun_id')->toArray();

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
            'is_kpi' => 'required|boolean',
            'pi_no' => 'required_if:is_kpi,1',
            'pi_target' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->is_kpi == 1 && ($value === null || !is_numeric($value))) {
                        $fail('PI Target diperlukan dan mesti nombor jika KPI dipilih.');
                    }
                }
            ],
            'doc_link' => 'nullable|url',
            'jumlah' => 'array',
            'jumlah.*' => 'nullable|numeric',
        ], [
            'jenis_data_ptj_id.required'     => 'Sila isi jenis data',
            'jenis_data_ptj_id.unique' => 'Jenis data telah wujud',
        ]);

        $exists = DataUtama::where('jenis_data_ptj_id', $request->jenis_data_ptj_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['jenis_data_ptj_id' => 'Data ini telah digunakan.']);
        }

        $dataUtama->update([
            'subunit_id' => $request->subunit_id,
            'jenis_data_ptj_id' => $request->jenis_data_ptj_id,
            'is_kpi' => $request->is_kpi,
            'pi_no' => $request->is_kpi ? $request->pi_no : null,
            'pi_target' => $request->is_kpi ? $request->pi_target : null,
            'doc_link' => $request->doc_link,
        ]);

        if ($request->has('jumlah')) {
            foreach ($request->jumlah as $tahunKey => $value) {
                if ($value === null) continue;

                if (is_numeric($tahunKey)) {
                    $tahunId = $tahunKey;
                } elseif (substr($tahunKey, 0, 5) === 'year_') {
                    $tahunTahun = (int) str_replace('year_', '', $tahunKey);

                    // Insert ke table tahun jika belum wujud
                    $tahun = Tahun::firstOrCreate(
                        ['tahun' => $tahunTahun],
                        ['publish_status' => 1]
                    );

                    $tahunId = $tahun->id;
                } else {
                    continue;
                }

                DataJumlah::updateOrCreate(
                    [
                        'data_utama_id' => $dataUtama->id,
                        'tahun_id' => $tahunId
                    ],
                    [
                        'jumlah' => $value
                    ]
                );
            }
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
