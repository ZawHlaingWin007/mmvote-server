@extends('layouts.back-app')
@section('breadcrumb')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          {{-- <h1>Question List</h1> --}}
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item active"><a href="{{route('admin.dashboard',$election->id)}}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="{{route('admin.question.index',$election->id)}}">Question List</a></li>
            <li class="breadcrumb-item active">Question Additional List</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-red card-outline">
                    <div class="card-header">
                        <div class="card-title">Question Additional List</div>
                        <div class="card-tools">
                            @if ($election->status == 0)
                            <a href="{{ route('admin.question-detail.create',['election_id' => $election->id,'question_id' => $question->id]) }}"
                            class="btn btn-success btn-flat"><i class="fas fa-plus"></i> Add</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="Questiontable" class="table table-bordered table-hover table-striped" style="width: 100%">
                            <thead>
                              <tr>
                                <th>NO</th>
                                <th>Image</th>
                                <th>Name</th>                                
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>

                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 align="center" style="margin:0;">Are you sure you want to remove this data?</h6>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
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
$(document).ready(function(){ 
    $('#Questiontable').DataTable({
        processing: true,
        language: {
         processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
        serverSide: true,
        ajax: window.location.href,
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
            },
            {
                data: 'image',
                name: 'image',
                render: function(data, type, full, meta) {
                    return "<img src={{ URL::to('/') }}" + data +
                        " width='70' class='img-thumbnail' />";
                },
            },
            {
                data: 'name',
                name: 'name',     
                width: "50%",           
            },           
            {
                data: 'action',
                name: 'action',
                orderable: false,                
            }
        ],
        });            

        var Question_id;

        $(document).on('click', '.delete', function() {
            Question_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function() {
            $.ajax({
                url: "{{ url('/admin/question/detail-destroy/') }}/" + Question_id,
                beforeSend: function() {
                    $('#ok_button').text('Deleting...');                    
                },
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                        $('#ok_button').text('OK');
                        toastr.success('Info - Successfully Deleted!')
                        $('#Questiontable').DataTable().ajax.reload();
                    }, 2000);
                }
            })
        });
    })
</script>
@endsection
