@extends('admin._layout')

@section('container')
    <div class="card">
        <div class="card-header">
            <table width="100%">
                <tr>
                    <td>
                        <h3 class="card-title">List</h3>
                    </td>
                </tr>
            </table>
        </div>
        <!-- /.card-header -->
        <div class="card-body">

            <div class="row">
                <div class="col-md-2 col-sm-6" style="margin-bottom: 10px;">
                    <label>Pencarian</label>
                    <input type="text" name="keyword" id="keyword" class=" form-control form-control-sm"
                        value="{{ request()->get('keyword') }}">
                </div>

                <div class="col-md-1">
                    <a href="javascript:void(0)" id="searchKey" class="btn btn-default btn-sm " style="margin-top: 28px;">
                        <i class="fa fa-search"></i>
                    </a>
                </div>

            </div>




            <div class="scroll-x-table">
                <table class="table table-bordered table-striped table-condensed" id="dt_table" width="100%">
                    <thead>
                        <tr>
                            <th class="no-sort" width="10px">No</th>
                            <th class="no-sort">ID Task</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Method</th>
                            <th>URL</th>
                            <th>Pathname</th>
                            <th>IP Address</th>
                            <th>User Name</th>
                            <th class="no-sort"></th>

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <br>
            </div>
        </div>
        <!-- /.card-body -->
    </div>

    <!-- /.card -->


    <!-- /.modal -->
@endsection



@section('javascript')
    <script>
        $(function() {
            showDataTable();
            const keyword = $('#keyword').val();


            $('#searchKey').on('click', function() {
                if (keyword != '') {
                    $('#dt_table').DataTable().destroy();
                    showDataTable(keyword);
                } else {
                    $('#dt_table').DataTable().destroy();
                    showDataTable();
                }
            });

            $('#keyword').keypress(function(e) {
            if (e.which == 13) {
                $('#searchKey').click();
            }
        })

        })

        function showDataTable() {
            $("#dt_table").dataTable().fnDestroy();
            $('#dt_table').DataTable({
                "lengthChange": false,
                "searching": false,
                // 'scrollX': true,
                // 'scrollCollapse' : true,
                'processing': true,
                'serverSide': true,
                "columnDefs": [{
                        "targets": 'no-sort',
                        "orderable": false,
                    },
                    {
                        className: 'text-center',
                        targets: []
                    },
                    {
                        className: 'text-left',
                        targets: []
                    },
                    {
                        className: 'text-right',
                        targets: []
                    }
                    // {
                    //     render: function(data, type, full, meta) {
                    //         return "<div style='white-space:normal; width:400pt'>" + data + "</div>";
                    //     },
                    //     targets: [7, 8,10, 11]
                    // },
                ],
                "order": [
                    [0, "desc"]
                ],
                ajax: {
                    url: "{{ url(request()->segment(1) . '/' . request()->segment(2) . '/ajax_list') }}",
                    type: 'GET',
                    data: {
                        'keyword': $('#keyword').val(),
                    }
                },
            });
        }

    </script>
@endsection
