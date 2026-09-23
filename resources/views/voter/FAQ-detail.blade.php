@extends('layouts.app')


@section('content')
    <div class="container-fluid mb-5 pb-5" id="FAQ">
        <div class="row mt-2">
            <div class="col-md-12">
                <h5 class="text-center"><span
                        style="line-height:35px; font-weight:bold;">{{ $election->ques_title ?? '' }}</span></h5>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-12">
                <h5 class="text-center"><span
                        style="line-height:15px; font-size: 16px; font-weight:bold;">{{ $election->ques_description ?? '' }}</span>
                </h5>
            </div>            
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <a href="{{ route('vote.faq',$election->id) }}" class="btn btn-info mb-3"><i class="fa fa-arrow-left"></i> Back</a>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 d-flex align-items-center justify-content-center">                                
                                <img src="{{url($quesDetail->image)}}" alt="Image" class="ml-3 mt-5 mt-lg-0 border border-info rounded-circle" width="250px" height="250px" style="object-fit: cover; object-position: top center;">
                            </div>
                            <div class="col-lg-8">                        
                                <div class="mt-5">
                                    {{ $quesDetail->name }} <br>    
                                    <div class="mt-4">
                                        {!! $quesDetail->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>                
            </div>            
        </div>
    </div>
@endsection