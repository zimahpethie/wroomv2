<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\JenisDataPtj;
use App\Models\SubUnit;
use Illuminate\Http\Request;

class JenisDataPtjController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $jenisdataptjList = JenisDataPtj::latest()->paginate($perPage);

        return view('pages.jenisdataptj.index', [
            'jenisdataptjList' => $jenisdataptjList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $departmentList = Department::where('publish_status', 1)->get();
        $subunitList = SubUnit::where('publish_status', 1)->get();

        return view('pages.jenisdataptj.create', [
            'save_route' => route('jenisdataptj.store'),
            'str_mode' => 'Tambah',
            'departmentList' => $departmentList,
            'subunitList' => $subunitList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:jenis_data_ptjs',
            'department_id' => 'required|exists:departments,id',
            'subunit_id' => 'nullable',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required'     => 'Sila isi tajuk data',
            'name.unique' => 'tajuk data telah wujud',
            'department_id.required' => 'Sila isi bahagian/unit',
            'publish_status.required' => 'Sila isi status',
        ]);

        $jenisdataptj = new JenisDataPtj();

        $data = $request->all();
        $data['subunit_id'] = $request->input('subunit_id') ?: null; // Tukar '' kepada null

        $jenisdataptj->fill($data);
        $jenisdataptj->save();

        return redirect()->route('jenisdataptj')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $jenisdataptj = JenisDataPtj::findOrFail($id);

        return view('pages.jenisdataptj.view', [
            'jenisdataptj' => $jenisdataptj,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $jenisdataptj = JenisDataPtj::findOrFail($id);
        $departmentList = Department::where('publish_status', 1)->get();
        $subunitList = SubUnit::where('publish_status', 1)
            ->where('department_id', $jenisdataptj->department_id)
            ->get();

        return view('pages.jenisdataptj.edit', [
            'save_route' => route('jenisdataptj.update', $id),
            'str_mode' => 'Kemas Kini',
            'jenisdataptj' => $jenisdataptj,
            'departmentList' => $departmentList,
            'subunitList' => $subunitList,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:jenis_data_ptjs,name,' . $id,
            'department_id' => 'required|exists:departments,id',
            'subunit_id' => 'nullable',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required'     => 'Sila isi tajuk data',
            'name.unique' => 'Tajuk data telah wujud',
            'department_id.required' => 'Sila isi bahagian/unit',
            'publish_status.required' => 'Sila isi status',
        ]);

        $jenisdataptj = JenisDataPtj::findOrFail($id);

        $data = $request->all();
        $data['subunit_id'] = $request->input('subunit_id') ?: null; // Tukar '' kepada null

        $jenisdataptj->fill($data);
        $jenisdataptj->save();

        return redirect()->route('jenisdataptj')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function getSubunits($department_id)
    {
        $subunits = SubUnit::where('department_id', $department_id)->get();

        return response()->json($subunits);
    }


    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $jenisdataptjList = JenisDataPtj::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $jenisdataptjList = JenisDataPtj::latest()->paginate(10);
        }

        return view('pages.jenisdataptj.index', [
            'jenisdataptjList' => $jenisdataptjList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $jenisdataptj = JenisDataPtj::findOrFail($id);

        $jenisdataptj->delete();

        return redirect()->route('jenisdataptj')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = JenisDataPtj::onlyTrashed()->latest()->paginate(10);

        return view('pages.jenisdataptj.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        JenisDataPtj::withTrashed()->where('id', $id)->restore();

        return redirect()->route('jenisdataptj')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $jenisdataptj = JenisDataPtj::withTrashed()->findOrFail($id);

        $jenisdataptj->forceDelete();

        return redirect()->route('jenisdataptj.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
