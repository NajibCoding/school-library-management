<?php

namespace App\Http\Controllers\Admin;

use App\Models\KeteranganCustomBond;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookAuthorsController extends Controller
{

    public function index()
    {
        return view('admin.keterangan_custom_bond.list', [
            'page_title' => 'Keterangan Custom Bond',
            'page_header' => 'Keterangan Custom Bond',
        ]);
    }

    public function ajax_list(Request $request)
    {
        $column_order = array(null, 'name', 'is_active', 'created_at', null);

        $draw = $request->draw;
        $length = $request->length;
        $start = $request->start;
        // $search = '%' . strtolower(strip_tags(trim($request->search["value"]))) . '%';
        $keyword = '%' . strtolower(strip_tags(trim($request->keyword))) . '%';
        $is_active = isset($request->is_active) ? strtolower($request->is_active) : '';

        $order = $request->order['0']['column'];
        $dir = $request->order['0']['dir'];

        $order_by = ($column_order[$order]) ? $column_order[$order] : "id";

        $query = KeteranganCustomBond::withoutDeleted()->where(function ($query) use ($is_active) {
            if ($is_active != "") {
                $query->where('is_active', $is_active);
            }
        })->where(function ($query) use ($keyword) {
            if (!empty($keyword)) {
                $query->where('name', 'like', $keyword);
            }
        })->offset($start)
            ->limit($length)
            ->orderBy('created_at', "DESC")
            ->orderBy($order_by, $dir)->get();

        $total = KeteranganCustomBond::withoutDeleted()->selectRaw('count(*) as jumlah')->where(function ($query) use ($is_active) {
            if ($is_active != "") {
                $query->where('is_active', $is_active);
            }
        })->where(function ($query) use ($keyword) {
            if (!empty($keyword)) {
                $query->where('name', 'like', $keyword);
            }
        })->first()->jumlah;

        $output = array();
        $output['draw'] = $draw;
        $output['recordsTotal'] = $output['recordsFiltered'] = $total;
        $output['data'] = array();

        $nomor_urut = $start + 1;
        foreach ($query as $row) {
            $dropdownItem = '';
            if (auth()->user()->can('keterangan-custom-bond-edit') || auth()->user()->hasRole("SUPERADMIN")) $dropdownItem .= '<a class="dropdown-item" href="' . url(request()->segment(1) . '/' . request()->segment(2) . '/edit', [$row->id]) . '"><i class="fa fa-edit" style="margin-right:5px;"></i>Edit</a>';
            if (auth()->user()->can('keterangan-custom-bond-delete') || auth()->user()->hasRole("SUPERADMIN")) $dropdownItem .= '<button type="button" data-id="' . $row->id . '" class="dropdown-item delete_data"><i class="fa fa-trash" style="margin-right:5px;"></i>Hapus</button>';
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
                $nomor_urut, $row->name, ($row->is_active == '2') ? 'Deleted' : (($row->is_active == '1') ? 'Active' : 'Deactivated'), formatDatetime($row->created_at), $actionBtn
            );
            $nomor_urut++;
        }
        return response()->json($output);
    }

    public function create()
    {
        return view('admin.keterangan_custom_bond.form', [
            'page_title' => 'Keterangan Custom Bond',
            'page_header' => 'Keterangan Custom Bond',
            'card_title' => 'Tambah Keterangan Custom Bond',
        ]);
    }

    public function edit($id)
    {
        $id = sanitize_string($id, false);
        if (!KeteranganCustomBond::withoutDeleted()->find($id)){
            Toastr::error('Data keterangan custom bond tidak dapat ditemukan!');
            return redirect()->back();
        }
        return view('admin.keterangan_custom_bond.form', [
            'page_title'    => 'Keterangan Custom Bond',
            'page_header'   => 'Keterangan Custom Bond',
            'card_title'    => 'Ubah Keterangan Custom Bond',
        ]);
    }

    public function ajaxGetOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [], [
            'id' => 'id',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }

        $id = sanitize_string($request->id, false);
        try {
            $result = KeteranganCustomBond::withoutDeleted()->findOrFail($id);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 200);
            return response()->json(apiRes("error", "Terdapat error, silahkan hubungi admin"), 200);
        }

        return response()->json(apiRes("success", $result), 200);
    }

    public function ajax_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], [], [
            'name' => 'Nama Keterangan Custom Bond',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }

        DB::beginTransaction();
        try {
            KeteranganCustomBond::create([
                'name' => sanitize_string($request->name),
                'is_active' => sanitize_string($request->status, false),
                'created_by' => auth()->user()->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(apiRes("error_message", $e->getMessage()), 200);
        }
        return response()->json(apiRes("success", "Berhasil tambah data keterangan custom bond"), 200);
    }

    public function ajax_update(Request $request)
    {
        $id = sanitize_string($request->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], [], [
            'name' => 'Nama Keterangan Custom Bond',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }


        DB::beginTransaction();
        try {
            $data = KeteranganCustomBond::withoutDeleted()->findOrFail($id);
            $data->update([
                'name' => sanitize_string($request->name),
                'is_active' => sanitize_string($request->status, false),
                'updated_by' => auth()->user()->id,
            ]);

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(apiRes("error_message", "Data tidak ditemukan"), 200);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(apiRes("error_message", $e->getMessage()), 200);
        }
        return response()->json(apiRes("success", "Berhasil ubah data keterangan custom bond"), 200);
    }

    public function ajax_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|exists:keterangan,id',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }

        $id = sanitize_string($request->id);
        DB::beginTransaction();
        try {

            $data = KeteranganCustomBond::findOrFail($id);
            $data->update([
                'is_active' => 2
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(apiRes("error", "Data tidak ditemukan"), 200);
        }

        return response()->json(apiRes("success", "Hapus data berhasil"), 200);
    }

}
