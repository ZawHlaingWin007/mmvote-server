<!DOCTYPE html>
<html lang="my">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>{{ $report->electionTitle() }}</title>
    <style>
        @font-face {
            font-family: 'Noto Sans Myanmar';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $fontRegularDataUri }}') format('truetype');
        }

        @font-face {
            font-family: 'Noto Sans Myanmar';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $fontBoldDataUri }}') format('truetype');
        }

        @page {
            size: A4 portrait;
            margin: 12mm 10mm 14mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #2d3748;
            font-family: 'Noto Sans Myanmar', sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .pdf-page {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
        }

        .report-title {
            color: #1e4a8c;
            font-size: 16pt;
            font-weight: 700;
            line-height: 1.35;
            text-align: center;
            margin: 0 0 12mm 0;
        }

        .section-heading {
            color: #1e4a8c;
            font-size: 12pt;
            font-weight: 700;
            text-align: left;
            margin: 0 0 10mm 0;
        }

        table.result-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.result-table col.col-no {
            width: 8%;
        }

        table.result-table col.col-question {
            width: 62%;
        }

        table.result-table col.col-answer {
            width: 30%;
        }

        table.result-table thead th {
            background: #1e4080;
            color: #ffffff;
            font-weight: 700;
            font-size: 10pt;
            padding: 10px 8px;
            border: none;
            vertical-align: middle;
        }

        table.result-table thead th.col-no {
            text-align: center;
        }

        table.result-table thead th.col-question {
            text-align: left;
        }

        table.result-table thead th.col-answer {
            text-align: right;
        }

        table.result-table tbody td {
            padding: 10px 8px;
            vertical-align: top;
            border: none;
            border-bottom: 1px solid #e2e8f0;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }

        table.result-table tbody tr.row-even td {
            background: #eef2f7;
        }

        table.result-table tbody tr.row-odd td {
            background: #ffffff;
        }

        table.result-table tbody td.no {
            text-align: center;
        }

        table.result-table tbody td.question {
            text-align: left;
        }

        table.result-table tbody td.result {
            text-align: right;
            font-size: 10pt;
            line-height: 1.35;
            white-space: nowrap;
        }

        .summary-block {
            margin-top: 14mm;
            page-break-inside: avoid;
        }

        .summary-heading {
            color: #1e4a8c;
            font-size: 11pt;
            font-weight: 700;
            margin: 0 0 6mm 0;
        }

        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.summary-table col.col-summary-label {
            width: 68%;
        }

        table.summary-table col.col-summary-value {
            width: 32%;
        }

        table.summary-table thead th {
            background: #1e4080;
            color: #ffffff;
            font-weight: 700;
            font-size: 10pt;
            padding: 10px 8px;
            border: none;
            vertical-align: middle;
        }

        table.summary-table thead th.col-summary-label {
            text-align: left;
        }

        table.summary-table thead th.col-summary-value {
            text-align: right;
        }

        table.summary-table tbody td {
            padding: 10px 8px;
            vertical-align: middle;
            font-size: 10pt;
            border: none;
            border-bottom: 1px solid #e2e8f0;
        }

        table.summary-table tbody td.label {
            text-align: left;
            font-weight: 400;
            line-height: 1.4;
        }

        table.summary-table tbody td.value {
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        table.summary-table tbody tr.row-even td {
            background: #eef2f7;
        }

        table.summary-table tbody tr.row-odd td {
            background: #ffffff;
        }
    </style>
</head>

<body>
    <div class="pdf-page">
        <h3 class="report-title">
            {{ $report->electionTitle() }}
            <br>
            ဆန္ဒမဲပေးခြင်း ရလဒ်များ
        </h3>

        <div class="section-heading">၁။ အဆိုပြုချက်များအပေါ် ဆန္ဒမဲပေးမှု အခြေအနေ</div>

        <table class="result-table">
            <colgroup>
                <col class="col-no">
                <col class="col-question">
                <col class="col-answer">
            </colgroup>
            <thead>
                <tr>
                    <th class="col-no">စဉ်</th>
                    <th class="col-question">မေးခွန်း / အဆိုပြုချက်</th>
                    <th class="col-answer">မဲရလဒ် (အဖြေ)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report->questions as $question)
                    <tr class="{{ $loop->iteration % 2 === 0 ? 'row-even' : 'row-odd' }}">
                        <td class="no">{{ $question->no }}</td>
                        <td class="question">
                            {{ \App\Support\QuestionVotingResultReport::plainQuestionText($question->ques) }}</td>
                        <td class="result">
                            Yes - {{ number_format($question->yes_count) }}<br>
                            No - {{ number_format($question->no_count) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-block">
            <div class="summary-heading">၂။ မဲပေးမှု အကျဉ်းချုပ်</div>
            <table class="summary-table">
                <colgroup>
                    <col class="col-summary-label">
                    <col class="col-summary-value">
                </colgroup>
                <thead>
                    <tr>
                        <th class="col-summary-label">အကြောင်းအရာ</th>
                        <th class="col-summary-value">အရေအတွက် / ရာခိုင်နှုန်း</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report->summaryRows as $row)
                        <tr class="{{ $loop->iteration % 2 === 0 ? 'row-even' : 'row-odd' }}">
                            <td class="label">{{ $row['label_mm'] }} ({{ $row['label_en'] }})</td>
                            <td class="value">
                                @if ($row['format'] === 'percent')
                                    {{ number_format($row['value'] * 100, 2) }} %
                                @elseif ($row['format'] === 'persons')
                                    {{ number_format($row['value']) }} ဦး
                                @else
                                    {{ number_format($row['value']) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
