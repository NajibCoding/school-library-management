<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\BookPublisher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookPublishersController extends Controller
{

    public function index()
    {
        return view('admin.book_publishers.list', [
            'page_title' => 'Penerbit Buku',
            'page_header' => 'Penerbit Buku',
        ]);
    }

    public function ajax_list(Request $request)
    {
        $column_order = array(null, 'name', 'status', 'created_at', null);

        $draw = $request->draw;
        $length = $request->length;
        $start = $request->start;
        // $search = '%' . strtolower(strip_tags(trim($request->search["value"]))) . '%';
        $keyword = '%' . strtolower(strip_tags(trim($request->keyword))) . '%';
        $status = isset($request->status) ? strtolower($request->status) : '';

        $order = $request->order['0']['column'];
        $dir = $request->order['0']['dir'];

        $order_by = ($column_order[$order]) ? $column_order[$order] : "id";

        $query = BookPublisher::activeWithRoleCheck()->where(function ($query) use ($status) {
            if ($status != "" && auth()->user()->hasRole('SUPERADMIN')) {
                $query->where('status', (int)$status);
            }
        })->where(function ($query) use ($keyword) {
            if (!empty($keyword)) {
                $query->where('name', 'like', $keyword);
            }
        })->offset($start)
            ->limit($length)
            ->orderBy('created_at', "DESC")
            ->orderBy($order_by, $dir)->get();

        $total = BookPublisher::activeWithRoleCheck()->selectRaw('count(*) as jumlah')->where(function ($query) use ($status) {
            if ($status != "" && auth()->user()->hasRole('SUPERADMIN')) {
                $query->where('status', (int)$status);
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
            if ((auth()->user()->can('book-publishers-edit') || auth()->user()->hasRole("SUPERADMIN")) && $row->status != 2) $dropdownItem .= '<a class="dropdown-item" href="' . url(request()->segment(1) . '/' . request()->segment(2) . '/edit', [$row->id]) . '"><i class="fa fa-edit" style="margin-right:5px;"></i>Edit</a>';
            if ((auth()->user()->can('book-publishers-delete') || auth()->user()->hasRole("SUPERADMIN")) && $row->status != 2) $dropdownItem .= '<button type="button" data-id="' . $row->id . '" class="dropdown-item delete_data"><i class="fa fa-trash" style="margin-right:5px;"></i>Hapus</button>';
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
                $nomor_urut, $row->name, ($row->status == '2') ? 'Terhapus' : (($row->status == '1') ? 'Aktif' : 'Tidak Aktif'), formatDatetime($row->created_at), $actionBtn
            );
            $nomor_urut++;
        }
        return response()->json($output);
    }

    public function create()
    {
        return view('admin.book_publishers.form', [
            'page_title' => 'Penerbit Buku',
            'page_header' => 'Penerbit Buku',
            'card_title' => 'Tambah Penerbit Buku',
        ]);
    }

    public function edit($id)
    {
        $id = sanitize_string($id, false);
        if (!BookPublisher::withoutDeleted()->find($id)) {
            Toastr::error('Data Penerbit Buku tidak dapat ditemukan!');
            return redirect()->back();
        }
        return view('admin.book_publishers.form', [
            'page_title'    => 'Penerbit Buku',
            'page_header'   => 'Penerbit Buku',
            'card_title'    => 'Ubah Penerbit Buku',
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
            $result = BookPublisher::withoutDeleted()->findOrFail($id);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error", "Terdapat error, silahkan hubungi admin"), 400);
        }

        return response()->json(apiRes("success", $result), 200);
    }

    public function ajax_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], [], [
            'name' => 'Nama Penerbit Buku',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }

        DB::beginTransaction();
        try {
            BookPublisher::create([
                'name' => sanitize_string($request->name),
                'status' => sanitize_string($request->status, false),
                'created_by' => auth()->user()->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        return response()->json(apiRes("success", "Berhasil tambah data Penerbit Buku"), 200);
    }

    public function ajax_update(Request $request)
    {
        $id = sanitize_string($request->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], [], [
            'name' => 'Nama Penerbit Buku',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }


        DB::beginTransaction();
        try {
            $data = BookPublisher::withoutDeleted()->findOrFail($id);
            $data->update([
                'name' => sanitize_string($request->name),
                'status' => sanitize_string($request->status, false),
                'updated_by' => auth()->user()->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        return response()->json(apiRes("success", "Berhasil ubah data Penerbit buku"), 200);
    }

    public function ajax_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|exists:book_publishers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        $id = sanitize_string($request->id);
        DB::beginTransaction();
        try {
            // Book Checking
            if ($buku = Book::withoutDeleted()->where('publisher_id', $request->id)->first()) {
                return response()->json(apiRes("error", "Tidak dapat menghapus, karena data penerbit ini digunakan pada buku " . $buku->name), 400);
            }

            $data = BookPublisher::findOrFail($id);
            $data->update([
                'status' => 2
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error", "Terdapat error, silahkan hubungi admin"), 400);
        }

        return response()->json(apiRes("success", "Hapus data berhasil"), 200);
    }
}
