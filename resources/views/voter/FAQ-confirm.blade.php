@extends('layouts.app')
@section('style')
    <style>
        .fieldset {
            display: block;
            */ margin-inline-start: 2px;
            margin-inline-end: 2px;
            padding-block-start: 0.35em;
            padding-inline-start: 0.75em;
            padding-inline-end: 0.75em;
            padding-block-end: 0.625em;
            min-inline-size: min-content;
            border-width: 2px;
            border-style: groove;
            border-color: white;
            border-image: initial;
            /* display:table-cell!important;
                                        width: 100%!important; */
        }

        font {
            /* word-break: break-all !important; */
        }
    </style>
@endsection
@section('content')
    <div class="container mb-5">
        <div class="row mb-5 pb-5">
            <div class="col-12 text-center px-0">

                <h3>{{ $election->company_name }}</h3>
                <h4
                    class="mb-4"
                    style="line-height: 50px"
                >လူကြီးမင်း၏ ရွေးချယ်ထားသော မေးခွန်းများအား
                    မှန်/မမှန် ပြန်လည်စစ်ဆေးရန်</h4>

                <div class="action mb-4 sticky-top">
                    <a
                        href="{{ route('vote.faq', $election->id) }}"
                        class="btn btn-danger btn-block btn-flat"
                    >
                        <h6 class="text-white">ပြန်လည်ရွေးချယ်မည်။</h6>
                    </a>
                    <button
                        class="btn btn-success btn-block mt-0 btn-flat"
                        id="btn_submit"
                    >
                        <h6 class="text-white">ဖြေဆို မဲပေးမည်။</h6>
                    </button>
                </div>
                <div class="text-left fieldset main-content text-dark">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        var res = JSON.parse(sessionStorage.getItem('data'));
        var ids = [];
        res.forEach(val => {
            ids.push(val.ques_id);
        });
        var election_id = @json($election->id);
        var html = ``;
        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: {
                '_token': '{{ csrf_token() }}',
                'ids': ids,
            },
            success: function(data) {
                console.log("DATA", data)
                data.forEach((element, index) => {
                    let d = res.filter(function(val) {
                        return val.ques_id == element.id;
                    })

                    html += `
        <div class="my-2 fieldset">
            <p style="width:100%!important;" class="h6">
                <i class="fa fa-question-circle"></i> ${element.no}
            </p>
            <span class="h6" style="line-height:50px;">${element.ques}</span>
    `;
                    html += `<hr>`;

                    html += `
                        <p style="font-size:18px;" class="mt-3 d-flex align-items-center h6">
                            <i class="fab fa-adn mr-2"></i>
                    `;

                    if (!d[0]) {
                        html += `မရွေးထားပါ`;
                    } else if (d[0].checked_val == 1) {
                        html += `အတည်ပြုပါသည်။`;
                    } else {
                        html += `အတည်မပြုပါ။`;
                    }

                    html += `</p>`;

                    html += `</div>`;
                });

                $('.main-content').html(html);
            }
        })


        $("#btn_submit").on('click', function() {

            $.confirm({
                title: 'မဲပေးရန်သေချာပါသလား ?',
                content: '',
                type: 'blue',
                typeAnimated: true,
                onOpen: function() {
                    $(".btn-blue").focus();
                },
                buttons: {
                    tryAgain: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function() {
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
                                message: '<img src="{{ url('images/loader.gif') }}" width="150" />',
                            });

                            $.ajax({
                                type: "post",
                                url: "{{ route('vote.faq.store') }}",
                                data: {
                                    'dtData': res,
                                    'election_id': election_id,
                                    "_token": "{{ csrf_token() }}",
                                },
                                dataType: "json",
                                success: function(data) {
                                    $.unblockUI();
                                    if (data.errors) {
                                        if (data.errors == 'already') {
                                            var url =
                                                '{{ route('vote.complete', ['election_id' => ':election_id']) }}';
                                            url = url.replace(':election_id', data
                                                .election_id);

                                            window.location.href = url;
                                        } else {
                                            toastr.error('Warning - ' + data.errors)
                                        }
                                    } else if (data.success) {
                                        var url =
                                            '{{ route('vote.complete', ['election_id' => ':election_id']) }}';
                                        url = url.replace(':election_id', data
                                            .election_id);

                                        window.location.href = url;
                                    }
                                },
                                error: function(data) {
                                    $.unblockUI();
                                    toastr.error(
                                        'Warning - Something went wrong!')
                                }
                            });
                        }
                    },
                    cancel: function() {
                        $.unblockUI();
                    }
                }
            });

        })
    </script>
@endsection
