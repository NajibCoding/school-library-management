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
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row mb-2 justify-content-between">
                <div class="col-12 col-md-6 mb-2">
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <label>Pencarian</label>
                            <input type="text" name="label" id="keyword" class=" form-control form-control-sm"
                                value="{{ request()->get('label') }}">
                        </div>

                        <div class="col-md-1 col-sm-1">
                            <a href="javascript:void(0)" id="find" class="btn btn-default btn-sm "
                                style="margin-top: 28px;">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-12 col-md-4 text-right mb-2 mt-auto">
                    @if (auth()->user()->can('konfigurasi-add') ||
    auth()->user()->hasRole('SUPERADMIN'))
                        <a href="{{ url(request()->segment(1) . '/' . request()->segment(2) . '/create') }}"
                            class="btn btn-success btn-sm"><i class="fa fa-plus" style="margin-right:5px"></i>Tambah
                            Data</a>
                    @endif
                </div> --}}
            </div>

            <div class="scroll-x-table">
                <table class="table table-bordered table-striped table-sm" id="dt_table" width="100%"
                    style=" font-size:13.5px;">
                    <thead>
                        <tr>
                            <th class="no-sort" width="10">No</th>
                            <th>Nama Konfigurasi</th>
                            <th>Nilai</th>
                            <th>Autoload</th>
                            <th class="no-sort" width="80"></th>
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

            $('#keyword').on('keypress', function() {
                showDataTable();
            });

            $('#find').on('click', function() {
                showDataTable();
            });

            $("#dt_table").on("click", ".reset_data", function(e) {
                swal({
                    title: 'Yakin reset data?',
                    confirmButtonText: 'Reset',
                    showCancelButton: true,
                }).then((result) => {
                    if (result) {
                        ajaxReset($(this).data('id'))
                    }
                })
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
                    },
                ],
                "order": [
                    [1, "asc"]
                ],
                ajax: {
                    url: "{{ url(request()->segment(1) . '/' . request()->segment(2) . '/ajax_list') }}",
                    type: 'GET',
                    data: {
                        'keyword': $('#keyword').val(),
                    }
                }

            });
        }

        function ajaxReset(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var url =
                "{{ url(implode('/', [request()->segment(1), request()->segment(2), 'ajax_reset'])) }}";
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: id
                },
                beforeSend: function() {
                    $.LoadingOverlay("show");
                },
                complete: function() {
                    $.LoadingOverlay("hide");
                },
                success: function(json) {
                    $.LoadingOverlay("hide");
                    //console.log(json);
                    if (json.status == "success") {
                        swal({
                            type: 'success',
                            html: json.result,
                            allowOutsideClick: false,
                        }).then((result) => {
                            showDataTable();
                        }).catch(swal.noop);

                    } else if (json.status == "error_val") {
                        $.each(json.error_message, function(key, val) {
                            $("#" + key + "_error").text(val[0]);
                            $("#" + key).addClass("is-invalid");
                        });
                        toastr["error"]('Validation Error!')
                    } else {
                        swal({
                            type: 'error',
                            html: json.error_message,
                        });
                    }

                },
                error: function(error) {
                    $.LoadingOverlay("hide");
                    if (typeof error === 'object') {
                        if (error.responseJSON.status == "error_val") {
                            $.each(error.responseJSON.error_message, function(key, val) {
                                $("#" + key + "_error").text(val[0]);
                                $("#" + key).addClass("is-invalid");
                            });
                            toastr["error"]('Validation Error!')
                        } else {
                            swal({
                                type: 'error',
                                html: error.responseJSON.error_message,
                            });
                        }
                    } else {
                        //console.log(error.responseText);
                        swal({
                            type: 'error',
                            html: error.responseText,
                        })
                    }
                }
            })
        }
    </script>
@endsection
