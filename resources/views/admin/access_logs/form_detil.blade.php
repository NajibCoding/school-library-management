@extends('admin._layout')

@section('container')
    <div class="row">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">{{ $card_title }}</h3>
                </div>

                <form method="POST" id="form1">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    {{-- <input type="hidden" id="customer_id" name="customer_id" value="">
                <input type="hidden" id="customer_detail_id" name="customer_detail_id" value=""> --}}
                    <div class="card-body">
                        <div class="col-md-12" style="border:1px solid gray; padding:5px 15px;">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="" class="col-form-label">ID Task</label>
                                    <input type="text" class="form-control form-control-sm bg-white"
                                        value="{{ isset($res->id_task) ? $res->id_task : '' }}" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="col-form-label">Tanggal</label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="{{ isset($res->created_at) ? $res->created_at : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-form-label">Method</label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="{{ isset($res->method) ? $res->method : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-form-label">Status</label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="{{ isset($res->status) ? $res->status : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="col-form-label">URL</label>
                                    <textarea class="form-control form-control-sm" style="background-color: white" rows="5" disabled>{{ isset($res->url) ? $res->url : '' }}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-form-label">Path Name</label>
                                    <input class="form-control form-control-sm"
                                        value="{{ isset($res->pathname) ? $res->pathname : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-form-label">Referral URL</label>
                                    <input class="form-control form-control-sm"
                                        value="{{ isset($res->referral_url) ? $res->referral_url : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-form-label">Request</label>
                                    <textarea class="form-control form-control-sm" cols="30" rows="17" style="background-color: white" disabled>{{ isset($res->content_request) && json_validate($res->content_request) ? prettyPrint($res->content_request) : $res->content_request }}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="col-form-label">Response</label>
                                    <textarea class="form-control form-control-sm" cols="30" rows="17" style="background-color: white" disabled>{{ isset($res->content_response) && json_validate($res->content_response) ? prettyPrint($res->content_response) : $res->content_response }}</textarea>
                                </div>


                                {{-- <div class="form-group col-md-6">
                                    <label class="col-form-label">Response</label>
                                    <textarea class="form-control form-control-sm" cols="30" rows="17" style="background-color: white" disabled>{{ isset($res->content_response) ? prettyPrint(isset(json_decode($res->content_response, true)[0]) ? json_decode($res->content_response, true)[0] : $res->content_response) : '' }}</textarea>
                                </div> --}}


                            </div>

                            <div class="row">

                                <div class="form-group col-md-12">
                                    <label class="col-form-label">User Agent</label>
                                    <input class="form-control form-control-sm"
                                        value="{{ isset($res->user_agent) ? $res->user_agent : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-form-label">User</label>
                                    <input class="form-control form-control-sm"
                                        value="{{ isset($res->user_name) ? $res->user_name : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-form-label">Email</label>
                                    <input class="form-control form-control-sm"
                                        value="{{ isset($res->user_email) ? $res->user_email : '' }}"
                                        style="background-color: white" disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="col-form-label">IP Address</label>
                                    <input class="form-control form-control-sm"
                                        value="{{ isset($res->ip) ? $res->ip : '' }}" style="background-color: white"
                                        disabled>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
            </div>

        </div>
    </div>
@endsection



@section('javascript')
@endsection
