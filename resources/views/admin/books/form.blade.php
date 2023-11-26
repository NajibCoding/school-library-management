@extends('admin._layout')

@php
    $status = auth()
        ->user()
        ->hasRole('SUPERADMIN')
        ? null
        : 'disabled';
    $display = auth()
        ->user()
        ->hasRole('SUPERADMIN')
        ? null
        : 'd-none';
@endphp

@section('container')
    <div class="card">
        <form id="form1" enctype="multipart/form-data">
            <div class="card-header">
                <h3 class="card-title">{{ $card_title }}</h3>
            </div>
            <!-- /.card-header -->
            @csrf
            <div class="card-body">

                <input type="hidden" name="id" id="id" value="{{ request()->segment(4) }}">
                <div class="row">
                    <div class="form-group col-6">
                        <label class="col-form-label">Title</label>
                        <input type="text" class="form-control form-control-sm" name="name" id="name"
                            value="{{ old('name', isset($res) ? $res->name : '') }}">
                        <small class="form-text text-danger" id="name_error"></small>
                    </div>

                    <div class="form-group col-6">
                        <label class="col-form-label">ISBN</label>
                        <input type="text" class="form-control form-control-sm number" name="isbn" id="isbn"
                            value="{{ old('isbn', isset($res) ? $res->isbn : '') }}">
                        <small class="form-text text-danger" id="isbn_error"></small>
                    </div>

                    <div class="form-group col-6 {{ $display }}">
                        <label class="col-form-label">Author</label>
                        <select type="text" class="form-control form-control-sm" name="author_id" id="author_id"
                            value="{{ old('author_id', isset($res) ? $res->author_id : '') }}" {{ $status }}>
                            <option value="">Pilih Author</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}" {{ $author->status == 0 ? 'disabled' : null }}>
                                    {{ $author->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-danger" id="author_id_error"></small>
                    </div>

                    <div class="form-group col-6 {{ $display }}">
                        <label class="col-form-label">Publisher</label>
                        <select type="text" class="form-control form-control-sm" name="publisher_id" id="publisher_id"
                            value="{{ old('publisher_id', isset($res) ? $res->publisher_id : '') }}" {{ $status }}>
                            <option value="">Pilih Publisher</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->id }}" {{ $publisher->status == 0 ? 'disabled' : null }}>
                                    {{ $publisher->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-danger" id="publisher_id_error"></small>
                    </div>

                    <div class="form-group col-6">
                        <label class="col-form-label">Publication Year</label>
                        <input type="text" class="form-control form-control-sm number" name="publication_year" id="publication_year"
                            value="{{ old('publication_year', isset($res) ? $res->publication_year : '') }}">
                        <small class="form-text text-danger" id="publication_year_error"></small>
                    </div>

                    <div class="form-group col-6">
                        <label class="col-form-label">Number of Pages</label>
                        <input type="text" class="form-control form-control-sm number" name="number_of_pages" id="number_of_pages"
                            value="{{ old('number_of_pages', isset($res) ? $res->number_of_pages : '') }}">
                        <small class="form-text text-danger" id="number_of_pages_error"></small>
                    </div>

                    <div class="form-group col-12">
                        <label class="col-form-label">Description</label>
                        <textarea rows="10" class="form-control form-control-sm" name="description" id="description">{{ old('description', isset($res) ? $res->description : '') }}</textarea>
                        <small class="form-text text-danger" id="description_error"></small>
                    </div>

                </div>
            </div>

            <!-- /.card-body -->
            <div class="card-footer">
                <a onclick="ajaxSubmit()" class="btn btn-primary float-right">Submit</a>
                <a href="{{ url(request()->segment(1) . '/' . request()->segment(2)) }}" class="btn btn-default">Cancel</a>
            </div>

        </form>
    </div>
    <!-- /.card -->
@endsection



@section('javascript')
    <script>
        $(function() {
            if ($("#id").val() != "") ajaxGetOne()
            $('#tanggal_lahir').datetimepicker({
                format: 'L',
                format: 'DD/MM/YYYY'
            });
        });

        function ajaxGetOne() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url(implode('/', [request()->segment(1), request()->segment(2), 'ajax_get_one'])) }}",
                type: 'POST',
                data: {
                    "id": $('#id').val()
                },
                beforeSend: function() {
                    $.LoadingOverlay("show");
                },
                complete: function() {
                    $.LoadingOverlay("hide");
                },
                success: function(json) {
                    $.LoadingOverlay("hide");
                    console.log(json);
                    if (json.status == "success") {
                        var result = json.result;
                        console.log(result);
                        $('#name').val(result.name);
                        $('#isbn').val(result.isbn);
                        $('#author_id').val(result.author_id);
                        $('#publisher_id').val(result.publisher_id);
                        $('#publication_year').val(result.publication_year);
                        $('#number_of_pages').val(result.number_of_pages);
                        $('#description').text(result.description);

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

        function ajaxSubmit() {
            var form1 = $('#form1').serializeArray();
            var fields = [];
            for (let i = 0; i < form1.length; i++) {
                const el = form1[i];
                fields = [...fields, el.name];
            }

            for (let j = 0; j < fields.length; j++) {
                $("#" + fields[j] + "_error").text("");
                $("#" + fields[j]).removeClass("is-invalid");

            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var url = "{{ url(implode('/', [request()->segment(1), request()->segment(2), 'ajax_save'])) }}";
            if ($('#id').val() != "") { //update
                url = "{{ url(implode('/', [request()->segment(1), request()->segment(2), 'ajax_update'])) }}";
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: form1,
                beforeSend: function() {
                    $.LoadingOverlay("show");
                },
                complete: function() {
                    $.LoadingOverlay("hide");
                },
                success: function(json) {
                    $.LoadingOverlay("hide");
                    console.log(json);
                    if (json.status == "success") {
                        swal({
                            type: 'success',
                            html: json.result,
                            allowOutsideClick: false,
                        }).then((result) => {
                            window.location.href =
                                "{{ url(implode('/', [request()->segment(1), request()->segment(2)])) }}";
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
