<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\BookAuthor;
use Illuminate\Http\Request;
use App\Models\BookPublisher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BooksController extends Controller
{

    public function index()
    {
        return view('admin.books.list', [
            'page_title' => 'Buku',
            'page_header' => 'Buku',
        ]);
    }

    public function ajax_list(Request $request)
    {
        $column_order = array(null, 'name', 'author_name', 'publisher_name', 'publication_year', 'number_of_pages', 'status', 'created_at', null);

        $draw = $request->draw;
        $length = $request->length;
        $start = $request->start;
        // $search = '%' . strtolower(strip_tags(trim($request->search["value"]))) . '%';
        $keyword = '%' . strtolower(strip_tags(trim($request->keyword))) . '%';
        $status = isset($request->status) ? strtolower($request->status) : '';

        $order = $request->order['0']['column'];
        $dir = $request->order['0']['dir'];

        $order_by = ($column_order[$order]) ? $column_order[$order] : "id";

        $query = Book::activeWithRoleCheck()
            ->leftJoin('book_authors', 'book_authors.id', '=', 'books.author_id')
            ->leftJoin('book_publishers', 'book_publishers.id', '=', 'books.publisher_id')
            ->select('books.*', 'book_authors.name AS author_name', 'book_publishers.name AS publisher_name')
            ->where(function ($query) use ($status) {
                if ($status != "" && auth()->user()->hasRole('SUPERADMIN')) {
                    $query->where('books.status', (int)$status);
                }
            })->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->orWhere('books.name', 'like', $keyword);
                    $query->orWhere('book_authors.name', 'like', $keyword);
                    $query->orWhere('book_publishers.name', 'like', $keyword);
                }
            })->offset($start)
            ->limit($length)
            ->orderBy('created_at', "DESC")
            ->orderBy($order_by, $dir)->get();

        $total = Book::activeWithRoleCheck()
            ->leftJoin('book_authors', 'book_authors.id', '=', 'books.author_id')
            ->leftJoin('book_publishers', 'book_publishers.id', '=', 'books.publisher_id')
            ->selectRaw('count(books.id) as jumlah')->where(function ($query) use ($status) {
                if ($status != "" && auth()->user()->hasRole('SUPERADMIN')) {
                    $query->where('books.status', (int)$status);
                }
            })->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->orWhere('books.name', 'like', $keyword);
                    $query->orWhere('book_authors.name', 'like', $keyword);
                    $query->orWhere('book_publishers.name', 'like', $keyword);
                }
            })->first()->jumlah;

        $output = array();
        $output['draw'] = $draw;
        $output['recordsTotal'] = $output['recordsFiltered'] = $total;
        $output['data'] = array();

        $nomor_urut = $start + 1;
        foreach ($query as $row) {
            $dropdownItem = '';
            if ((auth()->user()->can('books-edit') || auth()->user()->hasRole("SUPERADMIN")) && $row->status != 2) $dropdownItem .= '<a class="dropdown-item" href="' . url(request()->segment(1) . '/' . request()->segment(2) . '/edit', [$row->id]) . '"><i class="fa fa-edit" style="margin-right:5px;"></i>Edit</a>';
            if ((auth()->user()->can('books-delete') || auth()->user()->hasRole("SUPERADMIN")) && $row->status != 2) $dropdownItem .= '<button type="button" data-id="' . $row->id . '" class="dropdown-item delete_data"><i class="fa fa-trash" style="margin-right:5px;"></i>Hapus</button>';
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
                $nomor_urut, $row->name, $row->author_name ?? "Belum dipilih", $row->publisher_name ?? "Belum dipilih", $row->publication_year, $row->number_of_pages, ($row->status == '2') ? 'Terhapus' : (($row->status == '1') ? 'Aktif' : 'Tidak Aktif'), formatDatetime($row->created_at), $actionBtn
            );
            $nomor_urut++;
        }
        return response()->json($output);
    }

    public function create()
    {
        return view('admin.books.form', [
            'page_title'    => 'Buku',
            'page_header'   => 'Buku',
            'card_title'    => 'Tambah Buku',
            'authors'       => BookAuthor::withoutDeleted()->get(),
            'publishers'    => BookPublisher::withoutDeleted()->get(),
        ]);
    }

    public function edit($id)
    {
        $id = sanitize_string($id, false);
        if (!Book::withoutDeleted()->find($id)) {
            Toastr::error('Data Buku tidak dapat ditemukan!');
            return redirect()->back();
        }
        return view('admin.books.form', [
            'page_title'    => 'Buku',
            'page_header'   => 'Buku',
            'card_title'    => 'Ubah Buku',
            'authors'       => BookAuthor::withoutDeleted()->get(),
            'publishers'    => BookPublisher::withoutDeleted()->get(),
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
            $result = Book::withoutDeleted()->findOrFail($id);
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
            'isbn' => 'nullable|numeric|min_digits:10|max_digits:13',
            'author_id' => 'nullable|numeric|exists:book_authors,id,status,!=2',
            'publisher_id' => 'nullable|numeric|exists:book_publishers,id,status,!=2',
            'publication_year' => 'nullable|numeric|min_digits:4|max_digits:4',
            'number_of_pages' => 'nullable|numeric|min_digits:1|max_digits:6',
            'description' => 'nullable|string',
        ], [], [
            'name' => 'Title',
            'isbn' => 'ISBN',
            'author_id' => 'Author',
            'publisher_id' => 'Publisher',
            'publication_year' => 'Publication Year',
            'number_of_pages' => 'Number of Pages',
            'description' => 'Description',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        DB::beginTransaction();
        try {
            $book = Book::create([
                'name' => sanitize_string($request->name),
                'isbn' => sanitize_string($request->isbn),
                'publication_year' => sanitize_string($request->publication_year),
                'number_of_pages' => sanitize_string($request->number_of_pages),
                'description' => sanitize_string($request->description),
                'created_by' => auth()->user()->id,
            ]);

            // Jika dia superadmin bisa masukin author idnya
            if (auth()->user()->hasRole('SUPERADMIN')) {
                $book->update([
                    'author_id' => sanitize_string($request->author_id),
                    'publisher_id' => sanitize_string($request->publisher_id),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        return response()->json(apiRes("success", "Berhasil tambah data Buku"), 200);
    }

    public function ajax_update(Request $request)
    {
        $id = sanitize_string($request->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'isbn' => 'nullable|numeric|min_digits:10|max_digits:13',
            'author_id' => 'nullable|numeric|exists:book_authors,id,status,!=2',
            'publisher_id' => 'nullable|numeric|exists:book_publishers,id,status,!=2',
            'publication_year' => 'nullable|numeric|min_digits:4|max_digits:4',
            'number_of_pages' => 'nullable|numeric|min_digits:1|max_digits:6',
            'description' => 'nullable|string',
        ], [], [
            'name' => 'Title',
            'isbn' => 'ISBN',
            'author_id' => 'Author',
            'publisher_id' => 'Publisher',
            'publication_year' => 'Publication Year',
            'number_of_pages' => 'Number of Pages',
            'description' => 'Description',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 200);
        }


        DB::beginTransaction();
        try {
            $data = Book::withoutDeleted()->findOrFail($id);
            $data->update([
                'name' => sanitize_string($request->name),
                'isbn' => sanitize_string($request->isbn),
                'publication_year' => sanitize_string($request->publication_year),
                'number_of_pages' => sanitize_string($request->number_of_pages),
                'description' => sanitize_string($request->description),
                'updated_by' => auth()->user()->id,
            ]);

            if (auth()->user()->hasRole('SUPERADMIN')) {
                $data->update([
                    'author_id' => sanitize_string($request->author_id),
                    'publisher_id' => sanitize_string($request->publisher_id),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof ModelNotFoundException) return response()->json(apiRes("error", 'Data tidak ditemukan'), 400);
            return response()->json(apiRes("error_message", $e->getMessage()), 400);
        }
        return response()->json(apiRes("success", "Berhasil ubah data buku"), 200);
    }

    public function ajax_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(apiRes("error_val", $validator->getMessageBag()->toArray()), 400);
        }

        $id = sanitize_string($request->id);
        DB::beginTransaction();
        try {

            $data = Book::withoutDeleted()->findOrFail($id);
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
