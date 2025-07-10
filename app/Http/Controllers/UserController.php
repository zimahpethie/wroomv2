<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\EmailVerificationToken;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    use SoftDeletes;

    public function __construct()
    {
        $this->middleware('permission:Lihat Pengguna')->only(['index', 'show', 'search']);
        $this->middleware('permission:Tambah Pengguna')->only(['create', 'store']);
        $this->middleware('permission:Edit Pengguna')->only(['edit', 'update']);
        $this->middleware('permission:Padam Pengguna')->only(['destroy', 'trashList', 'restore', 'forceDelete']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $userList = User::latest()->paginate($perPage);

        return view('pages.user.index', [
            'userList' => $userList,
            'perPage' => $perPage
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departmentList = Department::where('publish_status', 1)->get();
        $campusList = Campus::where('publish_status', 1)->get();
        $positionList = Position::where('publish_status', 1)->get();
        $roles = Role::where('publish_status', 1)->get();

        return view('pages.user.create', [
            'save_route' => route('user.store'),
            'str_mode' => 'Tambah',
            'departmentList' => $departmentList,
            'campusList' => $campusList,
            'positionList' => $positionList,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'staff_id' => 'required|unique:users,staff_id',
            'email'    => 'required|email|unique:users,email',
            'position_id' => 'required',
            'roles'    => 'required|array|exists:roles,name',
            'campus_id' => 'required|exists:campuses,id',
            'department_id' => 'required|exists:departments,id',
            'office_phone_no' => 'nullable|string',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required'     => 'Sila isi nama pengguna',
            'staff_id.required' => 'Sila isi no. pekerja pengguna',
            'staff_id.unique' => 'No. pekerja telah wujud',
            'email.required'    => 'Sila isi emel pengguna',
            'email.unique'    => 'Emel telah wujud',
            'position_id.required' => 'Sila isi jawatan pengguna',
            'roles.required'    => 'Sila isi peranan pengguna',
            'campus_id.required' => 'Sila isi kampus pengguna',
            'department_id.required' => 'Sila isi bahagian/unit pengguna',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $user = new User();
        $user->fill($request->except('roles'));
        $user->password = null;
        $user->email_verified_at = null;
        $user->save();

        // Assign roles to the user
        $user->assignRole($request->input('roles'));

        // Send password reset link to the new user with the isNewAccount flag set to true
        $token = Password::broker()->createToken($user);
        $user->notify(new ResetPasswordNotification($token, true));

        return redirect()->route('user')
            ->with('success', 'Maklumat berjaya disimpan');
    }

    public function importForm()
    {
        return view('pages.user.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $data = array_map('str_getcsv', file($file));

        // Assumes first row is header
        $header = array_map('trim', $data[0]);
        unset($data[0]);

        foreach ($data as $row) {
            $row = array_combine($header, $row);

            // Validation per row
            if (empty($row['name']) || empty($row['email']) || empty($row['staff_id'])) {
                continue;
            }

            // Skip if staff_id or email already exists
            if (User::where('staff_id', $row['staff_id'])->exists() || User::where('email', $row['email'])->exists()) {
                continue;
            }

            $user = new User();
            $user->name = $row['name'];
            $user->staff_id = $row['staff_id'];
            $user->email = $row['email'];
            $user->position_id = $row['position_id'] ?? null;
            $user->campus_id = $row['campus_id'] ?? null;
            $user->department_id = $row['department_id'] ?? null;
            $user->office_phone_no = $row['office_phone_no'] ?? null;
            $user->publish_status = $row['publish_status'] ?? 1;
            $user->password = null;
            $user->email_verified_at = null;
            $user->save();

            // Assign default role if present in CSV
            if (!empty($row['role_name'])) {
                $user->assignRole($row['role_name']);
            }

            // // Send reset link for first time login
            // $token = Password::broker()->createToken($user);
            // $user->notify(new ResetPasswordNotification($token, true));
        }

        return redirect()->route('user')->with('success', 'Import CSV berjaya!');
    }

    public function showPublicRegisterForm()
    {
        $departmentList = Department::where('publish_status', 1)->get();
        $campusList = Campus::where('publish_status', 1)->get();
        $positionList = Position::where('publish_status', 1)->get();

        return view('auth.register', compact('campusList', 'positionList', 'departmentList'));
    }

    public function storePublicRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'staff_id' => 'required|unique:users,staff_id',
            'email'    => 'required|email|unique:users,email',
            'position_id' => 'required',
            'campus_id' => 'required|exists:campuses,id',
            'department_id' => 'required|exists:departments,id',
            'office_phone_no' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required'     => 'Sila isi nama pengguna',
            'staff_id.required' => 'Sila isi no. pekerja pengguna',
            'staff_id.unique' => 'No. pekerja telah wujud',
            'email.required'    => 'Sila isi emel pengguna',
            'email.unique'    => 'Emel telah wujud',
            'position_id.required' => 'Sila isi jawatan pengguna',
            'campus_id.required' => 'Sila isi kampus pengguna',
            'department_id.required' => 'Sila isi bahagian/unit pengguna',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->staff_id = $request->staff_id;
        $user->email = $request->email;
        $user->campus_id = $request->campus_id;
        $user->password = Hash::make($request->password);
        $user->position_id = $request->position_id;
        $user->department_id = $request->department_id;
        $user->publish_status = 1;
        $user->email_verified_at = null;
        $user->save();

        $user->assignRole('Pegawai Rekod');

        $token = Str::random(40);

        EmailVerificationToken::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $token]
        );

        $user->notify(new EmailVerificationNotification($user, $token));

        return view('auth.register-confirm');
    }

    public function verifyEmail($token)
    {
        $record = EmailVerificationToken::where('token', $token)->first();

        if (!$record) {
            return redirect('/login')->withErrors(['msg' => 'Token tidak sah atau telah luput.']);
        }

        $user = User::find($record->user_id);
        $user->email_verified_at = Carbon::now();
        $user->save();

        // Padam token selepas sah
        $record->delete();

        return redirect('/login')->with('success', 'Emel anda telah disahkan. Sila log masuk.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('pages.user.view', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('publish_status', 1)->get();
        $campusList = Campus::where('publish_status', 1)->get();
        $departmentList = Department::where('publish_status', 1)->get();
        $positionList = Position::where('publish_status', 1)->get();

        return view('pages.user.edit', [
            'save_route' => route('user.update', $id),
            'str_mode' => 'Kemas Kini',
            'roles' => $roles,
            'user' => $user,
            'campusList' => $campusList,
            'departmentList' => $departmentList,
            'positionList' => $positionList,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required',
            'staff_id'   => 'required|unique:users,staff_id,' . $id,
            'email'      => 'required|email|unique:users,email,' . $id,
            'position_id' => 'required|exists:positions,id',
            'roles'      => 'required|array|exists:roles,name',
            'campus_id'  => 'required|exists:campuses,id',
            'department_id'  => 'required|exists:departments,id',
            'office_phone_number' => 'nullable|string',
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required'     => 'Sila isi nama pengguna',
            'staff_id.required' => 'Sila isi no. pekerja pengguna',
            'staff_id.unique' => 'No. pekerja telah wujud',
            'email.required'    => 'Sila isi emel pengguna',
            'email.unique'    => 'Emel telah wujud',
            'position_id.required' => 'Sila isi jawatan pengguna',
            'roles.required'    => 'Sila isi peranan pengguna',
            'campus_id.required' => 'Sila isi kampus pengguna',
            'department_id.required' => 'Sila isi Bahagian/Unit pengguna',
            'publish_status.required' => 'Sila isi status pengguna',
        ]);

        $user = User::findOrFail($id);
        $user->fill($request->except('roles', 'password'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        $user->syncRoles($request->input('roles'));

        return redirect()->route('user')
            ->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = User::onlyTrashed()->latest()->paginate(10);

        return view('pages.user.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->where('id', $id)->restore();

        return redirect()->route('user')->with('success', 'Maklumat berjaya dikembalikan');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('user.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $userList = User::where('name', 'LIKE', "%$search%")
                ->orWhere('position_id', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $userList = User::latest()->paginate(10);
        }

        return view('pages.user.index', [
            'userList' => $userList,
        ]);
    }
}
