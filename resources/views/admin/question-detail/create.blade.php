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
                        <li class="breadcrumb-item active"><a
                                href="{{ route('admin.dashboard', $election->id) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a
                                href="{{ route('admin.question.index', $election->id) }}">Question List</a></li>
                        <li class="breadcrumb-item active"><a
                                href="{{ route('admin.question-detail.index', ['election_id' => $election->id, 'question_id' => $question_id]) }}">Question
                                List</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-red card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                Create Form
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="post" id="question_form" class="form-horizontal" enctype="multipart/form-data" action="{{ route('admin.question-detail.store') }}">
                                @csrf
                                <input type="hidden" name="election_id" value="{{$election->id}}">
                                <input type="hidden" name="question_id" value="{{$question_id}}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name*</label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                placeholder="Enter name..." value="{{ old('name') }}" autofocus>
                                            <p class="text-danger">{{ $errors->first('name') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="image">Image</label>
                                            <input type="file" name="image" id="image" class="form-control">
                                            <p class="text-danger">{{ $errors->first('image') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description*</label>
                                            <textarea class="textarea" placeholder="Place some text here"
                                                name="description" id="description">{{ old('description') }}</textarea>
                                                <p class="text-danger">{{ $errors->first('description') }}</p>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-flat btn-success"><i
                                                class="fas fa-save"></i>
                                            Save</button>
                                        <a href="{{ route('admin.question-detail.index', ['election_id' => $election->id, 'question_id' => $question_id]) }}" type="button"
                                            class="btn btn-flat btn-danger"><i class="fas fa-reply-all"></i> Back</a>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('javascript')
    <script>
       $('.textarea').summernote({
            enterHtml: '',
            height: '300px',
            toolbar: [
                // [groupName, [list of button]]            
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['height', ['height']]
            ]
        });           
    </script>
@endsection
