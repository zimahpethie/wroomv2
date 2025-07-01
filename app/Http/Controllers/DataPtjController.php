<?php

namespace App\Http\Controllers;

use App\Models\DataJumlahPtj;
use App\Models\DataPtj;
use App\Models\Department;
use App\Models\SubUnit;
use App\Models\Tahun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DataPtjController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(auth()->id());
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');
        $departmentId = $request->input('department_id');

        $tahunList = Tahun::orderBy('tahun', 'asc')->get();

        $query = DataPtj::query();

        if (!$user->hasAnyRole(['Superadmin', 'Admin'])) {
            $query->where('department_id', $user->department_id);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($search) {
            $query->where('nama_data', 'LIKE', "%$search%");
        }

        $canFilterDepartments = $user->hasAnyRole(['Superadmin', 'Admin']);

        $departmentList = $canFilterDepartments
            ? Department::orderBy('name')->get()
            : collect();

        $dataptjList = $query->latest()->paginate($perPage);

        return view('pages.dataptj.index', [
            'dataptjList' => $dataptjList,
            'tahunList' => $tahunList,
            'perPage' => $perPage,
            'departmentList' => $departmentList,
            'selectedDepartment' => $departmentId,
            'search' => $search,
            'canFilterDepartments' => $canFilterDepartments,
            'userDepartmentId' => $user->department_id,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = User::find(auth()->id());

        $query = DataPtj::with(['department', 'jumlahs.tahun'])
            ->withCount('jumlahs');

        // Hanya Superadmin/Admin boleh tengok semua department
        // if (!$user->hasAnyRole(['Superadmin', 'Admin'])) {
        //     $query->where('department_id', $user->department_id);
        // }

        // Jika ada filter department_id
        if ($request->has('department_id') && $request->department_id != '') {
            $query->where('department_id', $request->department_id);
        }

        $dataList = $query->get()->groupBy('department.name');

        // Kira total count selepas tapis
        $totalCount = $query->count();

        // Count per department (juga ikut akses)
        $departmentCounts = DataPtj::select('department_id', DB::raw('count(*) as total'))
            ->when(!$user->hasAnyRole(['Superadmin', 'Admin']), function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->groupBy('department_id')
            ->pluck('total', 'department_id')
            ->toArray();

        // Senarai department tetap semua untuk butang filter
        $departmentList = Department::orderBy('name')->get();

        return view('pages.dataptj.dashboard', [
            'dataList' => $dataList,
            'departmentList' => $departmentList,
            'selectedDepartment' => $request->department_id,
            'totalCount' => $totalCount,
            'departmentCounts' => $departmentCounts,
        ]);
    }

    public function create()
    {
        $departmentId = Auth::user()->department_id;

        $subunitList = SubUnit::where('department_id', $departmentId)->get();

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

        return view('pages.dataptj.create', [
            'save_route' => route('dataptj.store'),
            'str_mode' => 'Tambah',
            'subunitList' => $subunitList,
            'tahunList' => $tahunList,
        ]);
    }

    public function store(Request $request)
    {
        $departmentId = Auth::user()->department_id;
        $request->validate([
            'subunit_id' => 'nullable|exists:sub_units,id',
            'nama_data' => [
                'required',
                Rule::unique('data_ptjs')->where(function ($query) use ($departmentId) {
                    return $query->where('department_id', $departmentId);
                }),
            ],
            'jenis_nilai' => 'required|in:Bilangan,Peratus,Mata Wang',
            'doc_link' => 'nullable|url',
            'jumlah' => 'array',
            'jumlah.*' => 'nullable|numeric',
            'is_kpi' => 'array',
            'is_kpi.*' => 'required|boolean',
            'pi_no' => 'array',
            'pi_target' => 'array',
        ], [
            'nama_data.required'     => 'Sila isi tajuk data',
            'nama_data.unique' => 'Tajuk data telah wujud',
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

        // Simpan data utama
        $dataptj = DataPtj::create([
            'department_id' => $departmentId,
            'subunit_id' => $request->subunit_id,
            'nama_data' => $request->nama_data,
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

                DataJumlahPtj::create([
                    'data_ptj_id' => $dataptj->id,
                    'tahun_id' => $tahunId,
                    'jumlah' => $value,
                    'is_kpi' => $request->is_kpi[$tahunKey] ?? false,
                    'pi_no' => ($request->is_kpi[$tahunKey] ?? false) ? $request->pi_no[$tahunKey] ?? null : null,
                    'pi_target' => $request->pi_target[$tahunKey] ?? null,
                ]);
            }
        }

        return redirect()->route('dataptj')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $tahunList = Tahun::orderBy('tahun', 'asc')->get();
        $dataptj = DataPtj::with('jumlahs.tahun')->findOrFail($id);
        // $this->authorizeDataAccess($dataptj);

        $perbandinganByYear = [];

        foreach ($dataptj->jumlahs as $jumlah) {
            if ($jumlah->tahun && $jumlah->tahun->tahun && $jumlah->jumlah !== null && $jumlah->jumlah != 0) {
                $tahun = $jumlah->tahun->tahun;
                $perbandinganByYear[$tahun] = [
                    'jumlah' => $jumlah->jumlah,
                    'pi_target' => $jumlah->pi_target ?? 0 // fallback to 0 if null
                ];
            }
        }

        ksort($perbandinganByYear);

        return view('pages.dataptj.view', [
            'dataptj' => $dataptj,
            'tahunList' => $tahunList,
            'perbandinganByYear' => $perbandinganByYear,
        ]);
    }

    public function edit($id)
    {
        $departmentList = Department::orderBy('name')->get();
        $user = User::find(auth()->id());
        $dataptj = DataPtj::with('jumlahs')->findOrFail($id);

        $this->authorizeDataAccess($dataptj);

        if ($user->hasAnyRole(['Superadmin', 'Admin'])) {
            $subunitList = SubUnit::where('department_id', $dataptj->department_id)->get();
        } else {
            // Pengguna biasa ikut department sendiri
            $subunitList = SubUnit::where('department_id', $user->department_id)->get();
        }

        $tahunList = Tahun::orderBy('tahun')->get();

        $jumlahArray = [];

        foreach ($dataptj->jumlahs as $item) {
            $jumlahArray[$item->tahun_id] = [
                'jumlah' => $item->jumlah,
                'is_kpi' => $item->is_kpi,
                'pi_no' => $item->pi_no,
                'pi_target' => $item->pi_target,
            ];
        }
        return view('pages.dataptj.edit', [
            'dataptj' => $dataptj,
            'save_route' => route('dataptj.update', $dataptj->id),
            'str_mode' => 'Kemaskini',
            'subunitList' => $subunitList,
            'departmentList' => $departmentList,
            'tahunList' => $tahunList,
            'jumlahArray' => $jumlahArray,
        ]);
    }

    public function update(Request $request, $id)
    {
        $departmentId = Auth::user()->department_id;

        $dataptj = DataPtj::findOrFail($id);

        $this->authorizeDataAccess($dataptj);

        if ($dataptj->department_id !== $departmentId) {
            abort(403, 'Anda tidak dibenarkan mengemaskini data ini.');
        }

        $request->validate([
            'subunit_id' => 'nullable|exists:sub_units,id',
            'nama_data' => [
                'required',
                Rule::unique('data_ptjs')->where(function ($query) use ($departmentId, $id) {
                    return $query->where('department_id', $departmentId)
                        ->where('id', '!=', $id);
                }),
            ],
            'jenis_nilai' => 'required|in:Bilangan,Peratus,Mata Wang',
            'doc_link' => 'nullable|url',
            'jumlah' => 'array',
            'jumlah.*' => 'nullable|numeric',
            'is_kpi' => 'array',
            'is_kpi.*' => 'required|boolean',
            'pi_no' => 'array',
            'pi_target' => 'array',
        ], [
            'nama_data.required' => 'Sila isi tajuk data',
            'nama_data.unique' => 'Tajuk data telah wujud',
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

        $dataptj->update([
            'subunit_id' => $request->subunit_id,
            'nama_data' => $request->nama_data,
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

                DataJumlahPtj::updateOrCreate(
                    [
                        'data_ptj_id' => $dataptj->id,
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

            $dataptj->updated_at = now();
            $dataptj->updated_by = auth()->id();
            $dataptj->save();
        }

        return redirect()->route('dataptj')->with('success', 'Maklumat berjaya dikemaskini.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $dataptjList = DataPtj::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $dataptjList = DataPtj::latest()->paginate(10);
        }

        return view('pages.dataptj.index', [
            'dataptjList' => $dataptjList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $dataptj = DataPtj::findOrFail($id);

        $this->authorizeDataAccess($dataptj);

        $dataptj->delete();

        return redirect()->route('dataptj')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $user = User::find(auth()->id());
        $tahunList = Tahun::orderBy('tahun', 'asc')->get();

        if ($user->hasAnyRole(['Superadmin', 'Admin'])) {
            $trashList = DataPtj::onlyTrashed()->latest()->paginate(10);
        } else {
            $trashList = DataPtj::onlyTrashed()
                ->where('department_id', $user->department_id)
                ->latest()->paginate(10);
        }

        return view('pages.dataptj.trash', [
            'trashList' => $trashList,
            'tahunList' => $tahunList,
        ]);
    }

    public function restore($id)
    {
        $dataptj = DataPtj::withTrashed()->findOrFail($id);

        $this->authorizeDataAccess($dataptj);

        $dataptj->restore();

        return redirect()->route('dataptj')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $dataptj = DataPtj::withTrashed()->findOrFail($id);

        $this->authorizeDataAccess($dataptj);

        $dataptj->forceDelete();

        return redirect()->route('dataptj.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }

    private function authorizeDataAccess(DataPtj $dataptj)
    {
        $user = User::find(auth()->id());

        if (!$user->hasAnyRole(['Superadmin', 'Admin']) && $dataptj->department_id !== $user->department_id) {
            abort(403, 'Anda tidak dibenarkan mengakses data ini.');
        }
    }
}
