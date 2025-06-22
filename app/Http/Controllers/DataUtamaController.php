<?php

namespace App\Http\Controllers;

use App\Models\DataJumlah;
use App\Models\DataUtama;
use App\Models\Department;
use App\Models\JenisDataPtj;
use App\Models\SubUnit;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataUtamaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $datautamaList = DataUtama::latest()->paginate($perPage);

        return view('pages.datautama.index', [
            'datautamaList' => $datautamaList,
            'perPage' => $perPage,
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
            'subunit_id' => 'nullable|exists:subunits,id',
            'jenis_data_ptj_id' => 'required|exists:jenis_data_ptjs,id',
            'is_kpi' => 'required|boolean',
            'pi_no' => 'required_if:is_kpi,1',
            'pi_target' => 'required_if:is_kpi,1|numeric',
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
            foreach ($request->jumlah as $tahunId => $value) {
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
        $datautama = DataUtama::findOrFail($id);

        return view('pages.datautama.view', [
            'datautama' => $datautama,
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $dataUtama = DataUtama::with('jumlahs')->findOrFail($id);

        if ($dataUtama->department_id !== $departmentId) {
            abort(403, 'Anda tidak dibenarkan mengakses data ini.');
        }

        $subunitList = SubUnit::where('department_id', $departmentId)->get();

        $jenisDataList = JenisDataPtj::where('department_id', $departmentId)->get();

        $tahunList = Tahun::orderBy('tahun')->get();

        $jumlahArray = $dataUtama->jumlahs->pluck('jumlah', 'tahun_id')->toArray();

        return view('pages.datautama.edit', [
            'dataUtama' => $dataUtama,
            'save_route' => route('datautama.update', $dataUtama->id),
            'str_mode' => 'Kemaskini',
            'subunitList' => $subunitList,
            'jenisDataList' => $jenisDataList,
            'tahunList' => $tahunList,
            'jumlahArray' => $jumlahArray,
        ]);
    }

    public function update(Request $request, $id)
    {
        $departmentId = Auth::user()->department_id;

        $dataUtama = DataUtama::findOrFail($id);

        if ($dataUtama->department_id !== $departmentId) {
            abort(403, 'Anda tidak dibenarkan mengemaskini data ini.');
        }

        $request->validate([
            'subunit_id' => 'nullable|exists:subunits,id',
            'jenis_data_ptj_id' => 'required|exists:jenis_data_ptjs,id',
            'is_kpi' => 'required|boolean',
            'pi_no' => 'required_if:is_kpi,1',
            'pi_target' => 'required_if:is_kpi,1|numeric',
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

        // Kemaskini atau tambah data_jumlah
        foreach ($request->jumlah as $tahunId => $value) {
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
        $datautama = DataUtama::findOrFail($id);

        $datautama->delete();

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = DataUtama::onlyTrashed()->latest()->paginate(10);

        return view('pages.datautama.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        DataUtama::withTrashed()->where('id', $id)->restore();

        return redirect()->route('datautama')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $datautama = DataUtama::withTrashed()->findOrFail($id);

        $datautama->forceDelete();

        return redirect()->route('datautama.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
