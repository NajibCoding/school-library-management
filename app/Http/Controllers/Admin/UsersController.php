<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersController extends Controller
{

    public function index()
    {
        $all_roles_in_database = Role::where('name', '!=', 'SUPERADMIN')->get();
        return view('admin.user.list', [
            'page_title' => 'Pengguna',
            'page_header' => 'Pengguna',
            'all_roles'     => $all_roles_in_database
        ]);
    }

    public function ajax_list(Request $request)
    {
        $column_order = array(null, 'name', 'roles.id', 'status', 'created_at', 'last_login', null);

        $draw = $request->draw;
        $length = $request->length;
        $start = $request->start;
        // $search = '%' . strtolower(strip_tags(trim($request->search["value"]))) . '%';
        $keyword = '%' . strtolower(strip_tags(trim($request->keyword))) . '%';
        $status = isset($request->status) ? strtolower($request->status) : '';
        $role = isset($request->role) ? strtolower($request->role) : '';

        $order = $request->order['0']['column'];
        $dir = $request->order['0']['dir'];

        $order_by = ($column_order[$order]) ? $column_order[$order] : "id";

        $query = User::withoutDeleted()->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.id as role_id', 'roles.name as role_name')
            ->whereNotIn('users.role_id', [1])
            ->where(function ($query) use ($status) {
                if ($status != "" && auth()->user()->hasRole('SUPERADMIN')) {
                    $query->where('users.status', (int)$status);
                }
            })
            ->where(function ($query) use ($role) {
                if (!empty($role) && !in_array($role, [1])) {
                    $query->where('users.role_id', $role);
                }
            })
            ->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->orWhere('users.name', 'like', $keyword);
                }
            })
            ->offset($start)
            ->limit($length)
            ->orderBy('created_at', "DESC")
            ->orderBy($order_by, $dir)->get();



        $total = User::withoutDeleted()->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->selectRaw('count(*) as jumlah')
            ->whereNotIn('users.role_id', [1])
            ->where(function ($query) use ($status) {
                if ($status != "" && auth()->user()->hasRole('SUPERADMIN')) {
                    $query->where('users.status', $status);
                }
            })
            ->where(function ($query) use ($role) {
                if (!empty($role) && !in_array($role, [1])) {
                    $query->where('users.role_id', $role);
                }
            })
            ->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->orWhere('users.name', 'like', $keyword);
                }
            })
            ->first()->jumlah;

        $output = array();
        $output['draw'] = $draw;
        $output['recordsTotal'] = $output['recordsFiltered'] = $total;
        $output['data'] = array();

        $nomor_urut = $start + 1;
        foreach ($query as $row) {
            $dropdownItem = '';
            if (auth()->user()->can('users-edit') || auth()->user()->hasRole("SUPERADMIN")) $dropdownItem .= '<a class="dropdown-item" href="' . url(request()->segment(1) . '/' . request()->segment(2) . '/edit', [$row->id]) . '"><i class="fa fa-edit" style="margin-right:5px;"></i>Edit</a>';
            if (auth()->user()->can('users-delete') || auth()->user()->hasRole("SUPERADMIN")) $dropdownItem .= '<button type="button" data-id="' . $row->id . '" class="dropdown-item delete_data"><i class="fa fa-trash" style="margin-right:5px;"></i>Hapus</button>';
            // if (empty($row->email_verified_at)) $dropdownItem .= '<button class="dropdown-item" onclick="ajaxResendEmail(\'' . $row->id . '\')"><i class="fa fa-share" style="margin-right:5px;"></i>Kirim ulang email</a>';
            $actionBtn = '
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm" data-toggle="dropdown">
                    <span>Action</span> <i class="fa fa-caret-down" style="margin-left:5px;"></i></button>
                    <div class="dropdown-menu" role="menu" style="">
                        ' . $dropdownItem . '
                    </div>
                </div>';
            // dd($actionBtn);
            $output['data'][] = array(
                $nomor_urut, $row->name, (isset($row->role_name) ? $row->role_name : "No Roles"), ($row->status == '2') ? 'Terhapus' : (($row->status == '1') ? 'Aktif' : 'Tidak Aktif'), formatDatetime($row->created_at), formatDatetime($row->last_login), $actionBtn
            );
            $nomor_urut++;
        }
        return response()->json($output);
    }

    public function create()
    {
        $roles = Role::where('id', '!=', '1')->orderBy('id', 'desc')->get();
        return view('admin.user.form', [
            'page_title' => 'Pengguna',
            'page_header' => 'Pengguna',
            'card_title' => 'Tambah Pengguna',
            'roles'         => $roles,
        ]);
    }

    public function edit($id)
    {
        $id = sanitize_string($id, false);
        $check = User::withoutDeleted()->activeWithRoleCheck()->where('id', $id)->where('role_id', '!=', 1)->first();
        if (!$check) {
            Toastr::error('Data users tidak dapat ditemukan!');
            return redirect()->back();
        }
        $roles = Role::where('id', '!=', '1')->orderBy('id', 'desc')->get();
        return view('admin.user.form', [
            'page_title'    => 'Pengguna',
            'page_header'   => 'Pengguna',
            'card_title'    => 'Ubah Pengguna',
            'roles'         => $roles,
        ]);
    }

    public function ajaxGetOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:users,id',
        ], [], [
            'id' => 'id',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        $id = sanitize_string($request->id, false);
        try {
            $result = User::withoutDeleted()->activeWithRoleCheck()
                ->where('users.id', $id)
                ->where('role_id', '!=', 1)
                ->firstOrFail();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error", "Terdapat error, silahkan hubungi admin"), 400);
        }

        return response()->json(apiRes("success", $result), 200);
    }

    public function ajax_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'max:255', 'email', Rule::unique('users', 'email')->where(function ($query) {
                return $query->where('status', '!=', 2);
            })],
            'password'      => 'required|string|min:8|max:20',
            // 'status'        => 'required|in:0,1',
            'role_id'       => 'required|exists:roles,id',
        ], [], [
            'name'          => 'Nama Lengkap',
            'email'         => 'Alamat Email',
            'role_id'       => 'Tipe User',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => sanitize_string($request->name),
                'email' => sanitize_string($request->email),
                'password' => bcrypt(sanitize_string($request->password)),
                'role_id' => sanitize_string($request->input('role_id', 0)) ?? 0,
                'status' => 1,
                'created_by' => auth()->user()->id,
            ]);

            if ($request->input('role_id', 0) != 0) $user->assignRole((int)$request->role_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        return response()->json(apiRes("success", "Berhasil tambah data pengguna"), 200);
    }

    public function ajax_update(Request $request)
    {
        $id = sanitize_string($request->id);
        $validator = Validator::make($request->all(), [
            'id'    => 'exists:users,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'max:255', 'email', Rule::unique('users', 'email')->ignore($id)->where(function ($query) {
                return $query->where('status', '!=', 2);
            })],
            'password' => 'nullable|string|min:8|max:20',
            // 'status' => 'required|in:0,1',
            'role_id' => 'required|exists:roles,id',
        ], [], [
            'name'      => 'Nama Lengkap',
            'email'     => 'Alamat Email',
            'role_id'   => 'Tipe User',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        DB::beginTransaction();
        try {
            $user = User::withoutDeleted()->findOrFail($id);
            $user->update([
                'name' => sanitize_string($request->name),
                'email' => sanitize_string($request->email),
                'password' => sanitize_string($request->password) != null ? bcrypt(sanitize_string($request->password)) : $user->password,
                'role_id' => sanitize_string($request->input('role_id', 0)) ?? 0,
                // 'status' => sanitize_string($request->input('status')),
                'updated_by' => auth()->user()->id,
            ]);

            if ($request->input('role_id', 0) != 0) $user->assignRole((int)$request->role_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        return response()->json(apiRes("success", "Berhasil ubah data pengguna"), 200);
    }

    public function ajax_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        $id = sanitize_string($request->id);
        DB::beginTransaction();
        try {

            $data = User::findOrFail($id);
            $data->update([
                'status' => 2
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(apiRes("error", "Data tidak ditemukan"), 400);
        }

        return response()->json(apiRes("success", "Hapus data berhasil"), 200);
    }
}
