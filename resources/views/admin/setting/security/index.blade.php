@extends('layouts.back-app')
@section('breadcrumb')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="{{ route('admin.election.index') }}">Home</a></li>
                    </li>
                    <li class="breadcrumb-item active">Security</li>
                </ol>
            </div>
        </div>
    </div>
</section>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row justify-content-md-center">
            <div class="col-lg-6 col-xl-8">
                <div class="card card-red card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            Security
                        </h3>                        
                    </div>
                    <div class="card-body">
                        <form id="updateForm" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-xl-4">
                                    <label class="ml-2"> <i class="fa fa-unlock-alt"></i> OTP Code</label>
                                </div>
                                <div class="col-lg-6 col-xl-8">                                    
                                    <input class="otp_enable toggle-class" type="checkbox" id="otp_enable" name="otp_enable"
                                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                                        data-on="ON" data-off="OFF" {{$setting->otp_enable == 1 ? 'checked' : ''}} onchange="UpdateForm(this);">                                    
                                </div>
                            </div>

                            <hr>

                            <div class="row mt-4">
                                <div class="col-lg-6 col-xl-4">
                                    <label class="ml-2"> <i class="fas fa-file-excel"></i> Voting Result</label>
                                </div>
                                <div class="col-lg-6 col-xl-8">                                    
                                    <input class="result_enable toggle-class" type="checkbox" id="result_enable" name="result_enable"
                                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                                        data-on="ON" data-off="OFF" {{$setting->result_enable == 1 ? 'checked' : ''}} onchange="UpdateForm(this);">                                    
                                </div>                                
                            </div>

                            <hr>

                            <div class="row mt-4">
                                <div class="col-12 col-xl-4">
                                    <label class="ml-2 mt-2"> <i class="fas fa-key"></i> OTP Code Valid Time (Seconds)</label>
                                </div>
                                <div class="col-6 col-xl-3">
                                    <input type="number" name="otp_valid_time" value="{{old('otp_valid_time',$setting->otp_valid_time)}}" class="form-control" min="1">
                                </div>
                                <div class="col-6 col-xl-2">
                                    <select name="time_type" id="time_type" class="form-control">
                                        <option value="s" {{$setting->otp_valid_time_type == 's' ? 'selected' : '' }}>Second</option>
                                        <option value="m" {{$setting->otp_valid_time_type == 'm' ? 'selected' : '' }}>Minute</option>
                                        <option value="h" {{$setting->otp_valid_time_type == 'h' ? 'selected' : '' }}>Hour</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-2 mt-xl-0 col-xl-3">
                                    <button type="button" class="btn btn-success btn-block mt-3 mt-md-0" onclick="UpdateForm(this);">Set</button>
                                </div>
                            </div>

                            
                            @if (Auth::user()->type == 'superadmin')
                            <hr>

                            <div class="row mt-4">
                                <div class="col-lg-6 col-xl-4">
                                    <label class="ml-2"><i class="fas fa-check"></i> Voting Record List</label>
                                </div>
                                <div class="col-lg-6 col-xl-8">
                                    <input class="voting_record toggle-class" type="checkbox" id="voting_record" name="voting_record"
                                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                                        data-on="ON" data-off="OFF" {{$setting->voting_record == 1 ? 'checked' : ''}} onchange="UpdateForm(this);">                                    
                                </div>
                            </div>

                            <hr>

                            <div class="row mt-4">
                                <div class="col-lg-6 col-xl-4">
                                    <label class="ml-2"><i class="fas fa-check"></i> Voting Record Detail</label>
                                </div>
                                <div class="col-lg-6 col-xl-8">                                    
                                    <input class="result_detail toggle-class" type="checkbox" id="result_detail" name="result_detail"
                                        data-onstyle="success" data-offstyle="danger" data-toggle="toggle"
                                        data-on="ON" data-off="OFF" {{$setting->result_detail == 1 ? 'checked' : ''}} onchange="UpdateForm(this);">                                    
                                </div>
                            </div>
                            @endif
                        </form>
                        <hr />
                        <div class="row">
                            <div class="col-lg-6 col-xl-4">
                                <label class="ml-2"> <i class="fa fa-trash-restore-alt"></i> Reset Voters</label>
                            </div>
                            <div class="col-lg-6 col-xl-8">                                    
                                <a href="{{ route('admin.reset.voters') }}" class="btn btn-danger" id="reset-voter-btn">Reset Voters</a>                                  
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-lg-6 col-xl-4">
                                <label class="ml-2"> <i class="fa fa-trash-restore-alt"></i> Reset Elections</label>
                            </div>
                            <div class="col-lg-6 col-xl-8">                                    
                                <a href="{{ route('admin.reset.elections') }}" class="btn btn-danger" id="reset-election-btn">Reset Elections</a>                                  
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script>   
    $('#reset-election-btn').click(function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to reset the elections? This action cannot be undone.')) {
            window.location.href = $(this).attr('href');
        }
    });
    $('#reset-voter-btn').click(function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to reset the voters? This action cannot be undone.')) {
            window.location.href = $(this).attr('href');
        }
    });

    function UpdateForm(form){
        $.ajax({
            url: "{{ route('admin.security.update') }}",
            method: "POST",
            data: new FormData(form.form),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
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