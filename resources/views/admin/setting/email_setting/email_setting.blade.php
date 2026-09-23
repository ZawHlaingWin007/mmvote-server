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
                        <li class="breadcrumb-item active">Email Setting</li>
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
                                Email Integration
                            </h3>
                            <div class="card-tools mr-1">
                                <button type="button"  data-toggle="modal" data-target="#confirmModal"
                                    class="edit btn btn-success btn-flat"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="updateForm" method="POST">
                                @csrf
                                <div class="row p-3">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>SMTP Host</label>
                                            <input type="text" class="form-control" name="smtp_host"
                                                value="{{ old('smtp_host', $setting->smtp_host) }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>SMTP Port</label>
                                            <input type="text" class="form-control" name="smtp_port"
                                                value="{{ old('smtp_port', $setting->smtp_port) }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" class="form-control" name="mail_username"
                                                value="{{ old('mail_username', $setting->mail_username) }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="text" class="form-control" name="mail_password"
                                                value="{{ old('mail_password', $setting->mail_password) }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Encryption</label>
                                            <select class="form-control" name="mail_encryption">
                                                <option value="tls"
                                                    {{ old('mail_encryption', $setting->mail_encryption) == 'tls' ? 'selected' : '' }}>
                                                    TLS
                                                </option>
                                                <option value="ssl"
                                                    {{ old('mail_encryption', $setting->mail_encryption) == 'ssl' ? 'selected' : '' }}>
                                                    SSL
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>From Address</label>
                                            <input type="text" class="form-control" name="mail_from_address"
                                                value="{{ old('mail_from_address', $setting->mail_from_address) }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>From Name</label>
                                            <input type="text" class="form-control" name="mail_from_name"
                                                value="{{ old('mail_from_name', $setting->mail_from_name) }}" />
                                        </div>
                                    </div>   
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Mail Subject</label>
                                            <input type="text" class="form-control" name="mail_subject"
                                                value="{{ old('mail_subject', $setting->mail_subject) }}" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="mail_footer">Mail Footer</label>
                                            <textarea name="mail_footer" id="mail_footer">{{ old('mail_footer',$setting->mail_footer) }}</textarea>
                                        </div>
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
                url: "{{ route('admin.email-setting.update') }}",
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

        $('textarea').each(function(){                
            $(this).summernote({
                dialogsFade: true,
                placeholder: 'Type Something Here...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'help']],
                ],
            });           
        })
    </script>
@endsection
