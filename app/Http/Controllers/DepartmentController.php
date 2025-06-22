<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $departmentList = Department::orderBy('name', 'asc')->paginate($perPage);

        return view('pages.department.index', [
            'departmentList' => $departmentList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('pages.department.form', [
            'save_route' => route('department.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments',
            'publish_status' => 'required|in:1,0',
        ],[
            'name.required'     => 'Sila isi nama kampus',
            'name.unique' => 'Nama kampus telah wujud',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $department = new Department();

        $department->fill($request->all());
        $department->save();

        return redirect()->route('department')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);

        return view('pages.department.view', [
            'department' => $department,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.department.form', [
            'save_route' => route('department.update', $id),
            'str_mode' => 'Kemas Kini',
            'department' => Department::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:departments,name,' . $id,
            'publish_status' => 'required|in:1,0',
        ],[
            'name.required'     => 'Sila isi nama kampus',
            'name.unique' => 'Nama kampus telah wujud',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $department = Department::findOrFail($id);

        $department->fill($request->all());
        $department->save();

        return redirect()->route('department')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $departmentList = Department::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $departmentList = Department::latest()->paginate(10);
        }

        return view('pages.department.index', [
            'departmentList' => $departmentList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $department->delete();

        return redirect()->route('department')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = Department::onlyTrashed()->latest()->paginate(10);

        return view('pages.department.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        Department::withTrashed()->where('id', $id)->restore();

        return redirect()->route('department')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $department = Department::withTrashed()->findOrFail($id);

        $department->forceDelete();

        return redirect()->route('department.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
