@extends('layouts.back-app')
@section('breadcrumb')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{ route('admin.election.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">SMS API Setting</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-md-center">
                <div class="col-lg-6 col-xl-8">
                    <div class="card card-red card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                SMS API Integration
                            </h3>
                            <div class="card-tools mr-1">
                                <button type="button" data-toggle="modal" data-target="#confirmModal"
                                    class="edit btn btn-success btn-flat"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="updateForm" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 col-xl-4">
                                        <label class="ml-2"> <i class="fa fa-unlock-alt"></i> SMS API URL</label>
                                    </div>
                                    <div class="col-lg-6 col-xl-8">
                                        <input type="text" name="sms_api_url" class="form-control" id="sms_api_url"
                                            placeholder="Enter SMS API URL..."
                                            value="{{ old('sms_api_url', $setting->sms_api_url) }}">
                                    </div>
                                </div>

                                <hr>

                                <div class="row mt-4">
                                    <div class="col-lg-6 col-xl-4">
                                        <label class="ml-2"> <i class="fas fa-file-excel"></i> SMS API URL Method</label>
                                    </div>
                                    <div class="col-lg-6 col-xl-8">
                                        <input type="text" name="sms_url_method" class="form-control" id="sms_url_method"
                                            placeholder="Enter SMS API URL Method..."
                                            value="{{ old('sms_url_method', $setting->sms_url_method) }}">
                                    </div>
                                </div>

                                <hr>

                                <div class="row mt-4">
                                    <div class="col-lg-6 col-xl-4">
                                        <label class="ml-2"> <i class="fas fa-file-excel"></i> API Key</label>
                                    </div>
                                    <div class="col-lg-6 col-xl-8">
                                        <input type="text" name="sms_token" class="form-control" id="sms_token"
                                            placeholder="Enter SMS API KEY..."
                                            value="{{ old('sms_token', $setting->sms_token) }}">
                                    </div>
                                </div>

                                <hr>

                                <div class="row mt-4">
                                    <div class="col-lg-6 col-xl-4">
                                        <label class="ml-2"> <i class="fas fa-file-excel"></i> Sender Name</label>
                                    </div>
                                    <div class="col-lg-6 col-xl-8">
                                        <input type="text" name="sms_sender" class="form-control" id="sms_sender"
                                            placeholder="Enter SMS Sender Name..." required
                                            value="{{ old('sms_sender', $setting->sms_sender) }}">
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="justify-content-center" style="margin:0;">Are you sure you want to update this data?</h6>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-success" onclick="UpdateForm();">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection
@section('javascript')
    <script>
        function UpdateForm() {
            let form = $('#updateForm')[0];
            $.ajax({
                url: "{{ route('admin.sms-setting.update') }}",
                method: "POST",
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function(data) {
                    $("#confirmModal").modal('hide');
                    if (data.errors) {
                        for (var count = 0; count < data.errors.length; count++) {
                            toastr.error('Info - ' + data.errors[count])
                        }
                    }
                    if (data.success) {
                        toastr.success('Info - ' + data.success)
                    }
                }
            })
        }
    </script>
@endsection
