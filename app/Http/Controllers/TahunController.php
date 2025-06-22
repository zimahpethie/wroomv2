<?php

namespace App\Http\Controllers;

use App\Models\Tahun;
use Illuminate\Http\Request;

class TahunController extends Controller
{
public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $tahunList = Tahun::latest()->paginate($perPage);

        return view('pages.tahun.index', [
            'tahunList' => $tahunList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('pages.tahun.form', [
            'save_route' => route('tahun.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|unique:tahuns',
            'publish_status' => 'required|in:1,0',
        ],[
            'tahun.required'     => 'Sila isi tahun',
            'tahun.unique' => 'Tahun telah wujud',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $tahun = new Tahun();

        $tahun->fill($request->all());
        $tahun->save();

        return redirect()->route('tahun')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $tahun = Tahun::findOrFail($id);

        return view('pages.tahun.view', [
            'tahun' => $tahun,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.tahun.form', [
            'save_route' => route('tahun.update', $id),
            'str_mode' => 'Kemas Kini',
            'tahun' => Tahun::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|unique:tahuns,tahun,' . $id,
            'publish_status' => 'required|in:1,0',
        ],[
            'tahun.required'     => 'Sila isi tahun',
            'tahun.unique' => 'tahun telah wujud',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $tahun = Tahun::findOrFail($id);

        $tahun->fill($request->all());
        $tahun->save();

        return redirect()->route('tahun')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $tahunList = Tahun::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $tahunList = Tahun::latest()->paginate(10);
        }

        return view('pages.tahun.index', [
            'tahunList' => $tahunList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $tahun = Tahun::findOrFail($id);

        $tahun->delete();

        return redirect()->route('tahun')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = Tahun::onlyTrashed()->latest()->paginate(10);

        return view('pages.tahun.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        Tahun::withTrashed()->where('id', $id)->restore();

        return redirect()->route('tahun')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $tahun = Tahun::withTrashed()->findOrFail($id);

        $tahun->forceDelete();

        return redirect()->route('tahun.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
