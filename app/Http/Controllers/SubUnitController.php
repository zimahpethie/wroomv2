<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\SubUnit;
use Illuminate\Http\Request;

class SubUnitController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $subunitList = SubUnit::latest()->paginate($perPage);

        return view('pages.subunit.index', [
            'subunitList' => $subunitList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $departmentList = Department::where('publish_status', 1)->get();

        return view('pages.subunit.create', [
            'save_route' => route('subunit.store'),
            'str_mode' => 'Tambah',
            'departmentList' => $departmentList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|unique:sub_units',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required'     => 'Sila isi nama sub unit',
            'name.unique' => 'Nama sub unit telah wujud',
            'department_id.required' => 'Sila isi bahagian/unit',
            'publish_status.required' => 'Sila isi status',
        ]);

        $subunit = new SubUnit();

        $subunit->fill($request->all());
        $subunit->save();

        return redirect()->route('subunit')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $subunit = SubUnit::findOrFail($id);

        return view('pages.subunit.view', [
            'subunit' => $subunit,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $departmentList = Department::where('publish_status', 1)->get();

        return view('pages.subunit.edit', [
            'save_route' => route('subunit.update', $id),
            'str_mode' => 'Kemas Kini',
            'subunit' => SubUnit::findOrFail($id),
            'departmentList' => $departmentList,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|unique:sub_units,name,' . $id,
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required'     => 'Sila isi nama sub unit',
            'name.unique' => 'Nama sub unit telah wujud',
            'department_id.required' => 'Sila isi bahagian/unit',
            'publish_status.required' => 'Sila isi status',
        ]);

        $subunit = SubUnit::findOrFail($id);

        $subunit->fill($request->all());
        $subunit->save();

        return redirect()->route('subunit')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $subunitList = SubUnit::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $subunitList = SubUnit::latest()->paginate(10);
        }

        return view('pages.subunit.index', [
            'subunitList' => $subunitList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $subunit = SubUnit::findOrFail($id);

        $subunit->delete();

        return redirect()->route('subunit')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = SubUnit::onlyTrashed()->latest()->paginate(10);

        return view('pages.subunit.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        SubUnit::withTrashed()->where('id', $id)->restore();

        return redirect()->route('subunit')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $subunit = SubUnit::withTrashed()->findOrFail($id);

        $subunit->forceDelete();

        return redirect()->route('subunit.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
