<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KonfigurasiController extends Controller
{

    public function index()
    {
        return view('admin.konfigurasi.list', [
            'page_title' => 'Konfigurasi',
            'page_header' => 'Konfigurasi',
        ]);
    }

    public function ajax_list(Request $request)
    {
        $column_order = array(null, 'name', 'value', 'autoload', null);

        $draw = $request->draw;
        $length = $request->length;
        $start = $request->start;
        // $search = '%' . strtolower(strip_tags(trim($request->search["value"]))) . '%';
        $keyword = '%' . strtolower(strip_tags(trim($request->keyword))) . '%';
        $is_active = isset($request->is_active) ? strtolower($request->is_active) : '';
        $role = isset($request->role) ? strtolower($request->role) : '';

        $order = $request->order['0']['column'];
        $dir = $request->order['0']['dir'];

        $order_by = ($column_order[$order]) ? $column_order[$order] : "id";

        $query = Setting::where(function ($query) use ($is_active) {
            if ($is_active != "") {
                $query->where('is_active', $is_active);
            }
        })->where(function ($query) use ($keyword) {
            if (!empty($keyword)) {
                $query->where('name', 'like', $keyword);
                $query->orwhere('value', 'like', $keyword);
            }
        })->offset($start)
            ->limit($length)
            ->orderBy($order_by, $dir)->get();



        $total = Setting::selectRaw('count(*) as jumlah')->where(function ($query) use ($is_active) {
            if ($is_active != "") {
                $query->where('is_active', $is_active);
            }
        })->where(function ($query) use ($keyword) {
            if (!empty($keyword)) {
                $query->where('name', 'like', $keyword);
                $query->orwhere('value', 'like', $keyword);
            }
        })->first()->jumlah;

        $output = array();
        $output['draw'] = $draw;
        $output['recordsTotal'] = $output['recordsFiltered'] = $total;
        $output['data'] = array();

        $nomor_urut = $start + 1;
        foreach ($query as $row) {
            $dropdownItem = '';
            if (auth()->user()->can('konfigurasi-edit') || auth()->user()->hasRole("SUPERADMIN")) $dropdownItem .= '<a class="dropdown-item" href="' . url(request()->segment(1) . '/' . request()->segment(2) . '/edit', [$row->id]) . '"><i class="fa fa-edit" style="margin-right:5px;"></i>Edit</a>';
            if (auth()->user()->can('konfigurasi-reset') || auth()->user()->hasRole("SUPERADMIN")) $dropdownItem .= '<button type="button" data-id="' . $row->id . '" class="dropdown-item reset_data"><i class="fa fa-rotate-left" style="margin-right:5px;"></i>Reset</button>';
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
                $nomor_urut, snakeToTitleCase($row->name), Str::limit(sanitize_string($row->value), 150, '...'), Str::title($row->autoload), $actionBtn
            );
            $nomor_urut++;
        }
        return response()->json($output);
    }

    public function edit($id)
    {
        $id = sanitize_string($id, false);
        $setting = Setting::find($id);
        if (!$setting) {
            Toastr::error('Data konfigurasi tidak dapat ditemukan!');
            return redirect()->back();
        }
        return view('admin.konfigurasi.form', [
            'page_title'    => 'Konfigurasi',
            'page_header'   => 'Konfigurasi',
            'card_title'    => 'Ubah Konfigurasi ' . snakeToTitleCase($setting->name),
            'type'          => $setting->type,
            'enum'          => json_decode($setting->enum),
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
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        $id = sanitize_string($request->id, false);
        try {
            $result = Setting::findOrFail($id);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error", "Terdapat error, silahkan hubungi admin"), 400);
        }

        return response()->json(apiRes("success", $result), 200);
    }

    public function ajax_update(Request $request)
    {
        $id = sanitize_string($request->id);

        DB::beginTransaction();
        try {
            // Checking setting is available
            $data = Setting::findOrFail($id);
            $validation = [
                'value' => 'required|string|max:255'
            ];
            if ($data->type === 'file') {
                $mimes = empty($data->file_allowed_mimes) ? null : 'mimes:' . preg_replace('/\s+/', '', $data->file_allowed_mimes);
                $max_size = empty($data->file_allowed_mimes) ? null : 'max:' . $data->file_allowed_max_size;
                $validation = array(
                    'value' => implode('|', array_filter(['required', 'file', $mimes, $max_size])),
                );
            }

            $validator = Validator::make(
                $request->all(),
                $validation,
                [],
                [
                    'value' => 'Nilai Konfigurasi',
                ]
            );

            if ($validator->fails()) {
                return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
            }

            if ($data->type === 'file') {
                $old_file = $data->value;
                $file = $request->file('value')->store('konfigurasi', 'public');
                $data->update([
                    'value' => $file,
                    'updated_by' => auth()->user()->id,
                ]);
            } else {
                $data->update([
                    'value' => sanitize_string($request->value, false),
                    'updated_by' => auth()->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        //Delete if setting type file and has old file
        if (isset($old_file) && !empty($old_file) && Storage::disk('public')->exists($old_file)) {
            Storage::disk('public')->delete($old_file);
        }

        return response()->json(apiRes("success", "Berhasil ubah data konfigurasi"), 200);
    }

    public function ajax_reset(Request $request)
    {
        $id = sanitize_string($request->id);

        DB::beginTransaction();
        try {
            // Checking setting is available
            $data = Setting::findOrFail($id);

            if ($data->type === 'file') {
                $old_file = $data->value;
                $data->update([
                    'value' => null,
                    'updated_by' => auth()->user()->id,
                ]);
            } else {
                $data->update([
                    'value' => null,
                    'updated_by' => auth()->user()->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }

        //Delete if setting type file and has old file
        if (isset($old_file) && !empty($old_file) && Storage::disk('public')->exists($old_file)) {
            Storage::disk('public')->delete($old_file);
        }

        return response()->json(apiRes("success", "Berhasil reset konfigurasi"), 200);
    }
}
