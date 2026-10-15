@extends('layouts.app')
@section('style')
<style>
    #FAQ .card {
        border: 4px solid gray;
        border-radius: 10px;
    }

    #FAQ .container {
        padding-top: 10px;
        padding-left: 30px;
    }

    /* Hide the browser's default radio button */
    #FAQ .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom radio button */
    #FAQ .checkmark {
        position: absolute;
        top: 12px;
        left: 10px;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border-radius: 50%;
    }

    /* On mouse-over, add a grey background color */
    #FAQ .container:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    #FAQ .container input:checked~.checkmark {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    #FAQ .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    #FAQ .container input:checked~.checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    #FAQ .container .checkmark:after {
        top: 9px;
        left: 9px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
    }

    #FAQ .notice-box {
        background-color: #fff3e0;
        border: 3px solid #000;
        border-radius: 10px;
        padding: 16px 20px;
        color: #000;
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 16px;
    }

    #FAQ .notice-box p {
        margin-bottom: 8px;
    }

    #FAQ .notice-box .notice-names {
        margin: 0 0 16px 1.5rem;
    }

    #FAQ .notice-box .notice-names:last-child {
        margin-bottom: 0;
    }

    #FAQ .submit-wrap {
        padding: 16px 12px 24px;
    }

    #FAQ #btn_submit {
        width: 100%;
        min-height: 48px;
        padding: 12px 16px;
        font-size: 18px;
        font-weight: 600;
        border-radius: 8px;
    }
</style>
@endsection
@section('content')
<div
    class="container-fluid mb-5 pb-5"
    id="FAQ">
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
            <p
                class="text-center text-danger"
                style="font-weight:bold; font-size:20px;"
                id="error_msg"></p>
            <!-- <div class="notice-box">
                    <p>မြန်မာအက်ဂရိုအိတ်စ်ချိန်း အများနှင့်သက်ဆိုင်သော ကုမ္ပဏီလီမိတက် ဒဿမအကြိမ်မြောက် နှစ်ပတ်လည်အထွေထွေ အစည်းအဝေးတွင် အလှည့်ကျ နှုတ်ထွက်သော ဒါရိုက်တာ (၃)ဦးမှာ -</p>
                    <div class="notice-names">
                        ၁။ ဦးမင်းစိန်<br>
                        ၂။ ဒေါ်ရန်လင်း<br>
                        ၃။ ဦးသူရိန်အောင် တို့ဖြစ်ပါသည်။
                    </div>
                    <p>၂၁.၈.၂၀၂၆ ရက်နေ့ ညနေ (၄:၃၀) အထိ ဒါရိုက်တာအရွေးခံရန် အဆိုပြုလာသူ (၃) ဦးသာ ရှိပါသဖြင့် အောက်ဖော်ပြပါ ပုဂ္ဂိုလ်များအား Voting ပြုလုပ်ရန် မလိုဘဲ ဒါရိုက်တာအသစ်များ အဖြစ် ပြန်လည်ရွေးချယ်ခန့်ထားပါကြောင်း အစုရှယ်ယာရှင်များအား အသိပေးကြေညာအပ်ပါသည်။</p>
                    <div class="notice-names">
                        ၁။ ဦးမင်းစိန်<br>
                        ၂။ ဒေါ်ရန်လင်း<br>
                        ၃။ ဦးသူရိန်အောင် တို့ဖြစ်ပါသည်။
                    </div>
                </div> -->
            @foreach ($ques as $key => $que)
            <div class="card shadow-lg my-2 py-3">
                <div class="card-body">
                    <div class="row">
                        <input
                            type="hidden"
                            name="ques_id"
                            value="{{ $que->id }}">
                        <div class="col-lg-8">
                            <h5><span class="ques-no">{{ $que->no }}</span> <span
                                    style="line-height:35px;">{!! $que->ques !!}</span>
                            </h5>
                            <div class="row">
                                @foreach ($que->question_detail as $data)
                                <div class="col-md-6 d-flex">
                                    <a
                                        href="{{ route('vote.faq.detail', ['election_id' => $election->id, 'id' => $data->id]) }}">
                                        <img
                                            src="{{ url($data->image) }}"
                                            alt=""
                                            width="150px"
                                            height="200px"
                                            style="object-fit: cover; object-position: top center;"> </a>
                                    <div class="ml-3">
                                        {{ $data->name }} <br> <br>
                                        {!! \App\Support\HtmlWordTruncator::truncate($data->description, 10) !!} <br>                                        <a
                                            href="{{ route('vote.faq.detail', ['election_id' => $election->id, 'id' => $data->id]) }}">See
                                            more</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <label
                                class="container"
                                style="font-size:18px;"> အတည်ပြုပါသည်။
                                <input
                                    type="radio"
                                    name="{{ $que->id }}"
                                    value="1">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label
                                class="container"
                                style="font-size:18px;"> အတည်မပြုပါ။
                                <input
                                    type="radio"
                                    name="{{ $que->id }}"
                                    value="0">
                                <span class="checkmark"></span>
                            </label>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-12 submit-wrap">
            <button
                type="button"
                id="btn_submit"
                class="btn btn-info btn-lg btn-block">Submit</button>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $(document).ready(function() {

        var election_id = "{{ $election->id }}";


        var data = [];
        $(".card").on('change', function() {
            data = [];
            $(".card").each(function() {

                var ques_id = $(this).find('input[name=ques_id]').val();
                var checked_val = $(this).find('input[type=radio]:checked').val();
                var ques_no = $(this).find('.ques-no').text();

                data.push({
                    ques_id: ques_id,
                    checked_val: checked_val,
                });

                sessionStorage.setItem('data', JSON.stringify(data));
            })
        })

        var old = JSON.parse(sessionStorage.getItem('data'));

        if (old && old.length > 0) {
            data = old;
            old.forEach(element => {
                $('input[name=' + element.ques_id + ']').each(function() {
                    if ($(this).val() == element.checked_val) {
                        $(this).prop('checked', true);
                    }
                })
            });
        }

        $("#btn_submit").on('click', function() {
            $.blockUI({
                css: {
                    backgroundColor: 'transparent',
                    top: '0px',
                    left: '0px',
                    width: $(document).width(),
                    height: $(document).height(),
                    padding: '20%',
                },
                baseZ: 2000,
                message: '<img src="{{ url("images/loader.gif") }}" width="150" alt="" />',
            });

            var i;
            if (data.length == 0) {
                toastr.error('Warning - Answer All Question')
                $.unblockUI();
                return false;
            }
            for (i = 1; i < data.length; i++) {
                if (data[i].checked_val == null) {
                    toastr.error('Warning - Answer All Question')
                    $.unblockUI();
                    return false;
                }
            }

            var url = "{{ route('vote.faq.question.confirm', ['election_id' => ':election_id']) }}"
                .replace(':election_id', election_id);

            window.location.href = url;
        })
    })
</script>
@endsection