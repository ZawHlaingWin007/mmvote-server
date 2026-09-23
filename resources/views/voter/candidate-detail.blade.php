@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="back"><a href="{{ URL::previous() }}#{{ $candidate->id }}"
                class="btn btn-info border-radius"><i class="fa fa-chevron-circle-left"></i> Exit</a></div>
        <div class="row mt-3">
            <div class="col-md-12 pdt-3">
                <div class="card shadow bordertop">
                    <div class="card-body">
                        <div class="mb-5">
                            <div class="text-center pt-2">
                                <strong>
                                    <span>{{$election->candidate_detail_title ?? ''}}</span>
                                </strong>
                            </div>
                            <hr style="height: 2px; background-color: #3c8dbc;">
                            <div class="col-xs-12">

                                <div class="text-center my-2">
                                    @if ($candidate->photo_url)
                                        <img class="rounded-image" src="{{ url($candidate->photo_url) }}"
                                            alt="User profile picture">
                                    @else
                                        <img class="rounded-image" src="{{ url('images/user.png') }}"
                                            alt="User profile picture">
                                    @endif
                                </div>

                                <div class="table-responsive">
                                    <table class="table" style="width: 100%!important;">
                                        @if ($candidate->candidate_no)
                                            <tr>
                                                <td style="width:50%;">
                                                    <p>@lang('admin.candidate_no')</p>
                                                </td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->candidate_no }}</p>
                                                </td>

                                            </tr>
                                        @endif
                                        @if ($candidate->mname)
                                            <tr>
                                                <td>@lang('admin.username')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->mname }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->nrc_no)
                                            <tr>
                                                <td>@lang('admin.nrc_no')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->nrc_no }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->dob)
                                            <tr>
                                                <td>@lang('admin.dob')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->dob }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->education)
                                            <tr>
                                                <td>@lang('admin.education')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->education }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->gender)
                                            <tr>
                                                <td>@lang('admin.gender')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->gender }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->position)
                                            <tr>
                                                <td>@lang('admin.position')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->position }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->phone_no)
                                            <tr>
                                                <td>@lang('admin.phone_no')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->phone_no }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->email)
                                            <tr>
                                                <td>@lang('admin.email')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->email }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->address)
                                            <tr>
                                                <td>@lang('admin.address')</td>
                                                <td colspan="2">
                                                    {!! $candidate->address !!}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->company)
                                            <tr>
                                                <td>@lang('admin.company')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->company }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->company_start_date)
                                            <tr>
                                                <td>@lang('admin.company_start_date')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->company_start_date }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->no_of_employee)
                                            <tr>
                                                <td>@lang('admin.no_of_employee')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->no_of_employee }}</p>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->company_email)
                                            <tr>
                                                <td>@lang('admin.company_email')</td>
                                                <td colspan="2">
                                                    <p>{{ $candidate->company_email }}</p>
                                                </td>
                                            </tr>
                                        @endif


                                        @if ($candidate->company_address)
                                            <tr>
                                                <td>@lang('admin.company_address')</td>
                                                <td colspan="2">
                                                    {!! $candidate->company_address !!}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->experience)
                                            <tr>
                                                <td>@lang('admin.experience')</td>
                                                <td colspan="2">
                                                    {!! $candidate->experience !!}
                                                </td>
                                            </tr>
                                        @endif


                                        @if ($candidate->other_experience)
                                            <tr>
                                                <td>@lang('admin.other_experience')</td>
                                                <td colspan="2">
                                                    {!! $candidate->other_experience !!}
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($candidate->biography)
                                            <tr>
                                                <td>@lang('admin.biography')</td>
                                                <td colspan="2">
                                                    {!! $candidate->biography !!}
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection
