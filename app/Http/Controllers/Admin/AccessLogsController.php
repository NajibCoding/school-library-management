<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class AccessLogsController extends Controller
{
    public function index()
    {
        return view('admin.access_logs.list', [
            'page_title' => 'Access Logs',
            'page_header' => 'Access Logs'
        ]);
    }

    public function ajaxList(Request $request)
    {
        $column_order = array(null, 'id_task', 'created_at', 'status' ,'method', 'url', 'pathname' ,'ip', 'user_name', null);

        $draw = $request->draw;
        $length = $request->length;
        $start = $request->start;
        $keyword = '%' . strtolower(strip_tags(trim($request->keyword))) . '%';


        $order = $request->order['0']['column'];
        $dir = $request->order['0']['dir'];

        $order_by = ($column_order[$order]) ? $column_order[$order] : "id";

        $query = AccessLog::where(function ($query) use ($keyword) {
                if ($keyword != "") {
                    $query->where('id_task', 'like', $keyword);
                    $query->orWhere('pathname', 'like', $keyword);
                    $query->orWhere('user_name', 'like', $keyword);
                }
            })
            ->offset($start)->limit($length)
            ->orderBy($order_by, $dir)
            ->orderBy("id_task", "ASC")
            ->latest()
            ->get();



        $total = AccessLog::where(function ($query) use ($keyword) {
                if ($keyword != "") {
                    $query->where('id_task', 'like', $keyword);
                    $query->orWhere('pathname', 'like', $keyword);
                    $query->orWhere('user_name', 'like', $keyword);
                }
            })
            ->selectRaw('count(*) as jumlah')
            ->first()->jumlah;


        $output = array();
        $output['draw'] = $draw;
        $output['recordsTotal'] = $output['recordsFiltered'] = $total;
        $output['data'] = array();

        $nomor_urut = $start + 1;
        foreach ($query as $row) {

            $actionBtn = '
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm"  dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">Action <i class="fa fa-caret-down" style="margin-left:5px;"></i></button>

                <div class="dropdown-menu" role="menu" style="">
                <a class="dropdown-item" href="' . url(request()->segment(1) . '/' . request()->segment(2) . '/detail', [$row->id]) . '"><i class="fa fa-book-open" style="margin-right:5px;"></i>Detail</a>
        </div>
            </div>';



            $output['data'][] = array(
                $nomor_urut,
                $row->id_task,
                formatDatetime($row->created_at),
                $row->status,
                $row->method,
                Str::limit($row->url, 30),
                $row->pathname,
                $row->ip,
                $row->user_name,
                $actionBtn
            );
            $nomor_urut++;
        }

        return response()->json($output);
    }

    public function detil($id)
    {

        $logs = AccessLog::findOrFail($id);


        return view('admin.access_logs.form_detil', [
            'page_title' => 'Access Logs',
            'page_header' => 'Access Logs',
            'card_title' => 'Detil Access Logs',
            'res' => $logs,
        ]);
    }

}
