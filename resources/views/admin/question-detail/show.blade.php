@extends('layouts.back-app')
@section('breadcrumb')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          {{-- <h1>candidate List</h1> --}}
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active"><a href="{{route('admin.dashboard',$election->id)}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="{{route('admin.question.index',$election->id)}}">Question List</a></li>
            <li class="breadcrumb-item active"><a href="{{route('admin.question-detail.index',['election_id' => $election->id,'question_id' => $question_detail->question_id])}}">Question Additional List</a></li>
              <li class="breadcrumb-item active">Question Additional Detail</li>
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
       <div class="col-md-3">

         <!-- Profile Image -->
         <div class="card card-red card-outline">
           <div class="card-body box-profile">
             <div class="text-center">
             @if($question_detail->image)
               <img class="profile-user-img img-fluid img-circle"
             src="{{url($question_detail->image)}}"
                    alt="User profile picture">
              @else
              <img class="profile-user-img img-fluid img-circle"
             src="{{url('images/user.png')}}"
                    alt="User profile picture">
              @endif
             </div>

             <h3 class="profile-username text-center">{{$question_detail->name}}</h3>
             <hr>
             <a href="{{route('admin.question-detail.index',['election_id' => $election->id,'question_id' => $question_detail->question_id])}}" class="btn btn-success btn-block"><b><i class="fas fa-reply-all"></i> Go Back</b></a>
           </div>
           <!-- /.card-body -->
         </div>
         <!-- /.card -->
       </div>
       <!-- /.col -->
       <div class="col-md-9">
         <div class="card">
           <div class="card-header">
               <h3 class="card-title"><b>{{$question_detail->name}}</b> ၏ အသေးစိတ်အချက်အလက်များ</h3>
           </div><!-- /.card-header -->
           <div class="card-body">
               {!! $question_detail->description !!}
           </div><!-- /.card-body -->
         </div>
         <!-- /.nav-tabs-custom -->
       </div>
       <!-- /.col -->
     </div>
     <!-- /.row -->
   </div><!-- /.container-fluid -->
 </section>
@endsection
