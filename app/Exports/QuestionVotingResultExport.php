<?php

namespace App\Exports;

use App\Support\QuestionVotingResultReport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class QuestionVotingResultExport implements FromArray, WithTitle, WithEvents
{
    /** @var QuestionVotingResultReport */
    private $report;

    /** @var int */
    private $firstQuestionRow = 5;

    /** @var int */
    private $lastQuestionRow = 4;

    /** @var int */
    private $summaryTitleRow;

    /** @var int */
    private $summaryStartRow;

    /** @var int */
    private $summaryEndRow;

    public function __construct($election_id)
    {
        $this->report = QuestionVotingResultReport::forElection((int) $election_id);
    }

    public function title(): string
    {
        return 'Result';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['', '', '', '', '', ''];
        $rows[] = ['', '', $this->report->electionTitle() . ' ဆန္ဒမဲပေးခြင်းရလဒ်များ', '', '', ''];
        $rows[] = ['', '', '', '', '', ''];
        $rows[] = ['', '', 'NO', 'မေးခွန်း ', 'အဖြေ ', ''];

        $rowIndex = 5;
        foreach ($this->report->questions as $question) {
            $rows[] = [
                '',
                '',
                $question->no,
                QuestionVotingResultReport::plainQuestionText($question->ques),
                QuestionVotingResultReport::resultLine($question->yes_count, $question->no_count),
                '',
            ];
            $this->lastQuestionRow = $rowIndex;
            $rowIndex++;
        }

        $rows[] = ['', '', '', '', '', ''];

        $this->summaryTitleRow = $rowIndex;
        $rows[] = ['', '', 'မဲပေးမှု အကျဉ်းချုပ် စာရင်းအင်း (Summary Statistics)', '', '', ''];
        $rowIndex++;

        $this->summaryStartRow = $rowIndex;
        foreach ($this->report->summaryRows as $summaryRow) {
            $rows[] = ['', '', '', $summaryRow['label_en'], $summaryRow['value'], ''];
            $rowIndex++;
        }

        $this->summaryEndRow = $rowIndex - 1;

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach (['A' => 9.14, 'B' => 13, 'C' => 13, 'D' => 255.71, 'E' => 26, 'F' => 9.14] as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $fontName = 'Palatino Linotype';
                $thinBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];

                $sheet->getStyle('C2')->getFont()->setName($fontName)->setSize(11)->setBold(true);
                $sheet->getRowDimension(2)->setRowHeight(17.25);

                $sheet->getStyle('C4:E4')->applyFromArray(array_merge($thinBorder, [
                    'font' => [
                        'name' => $fontName,
                        'size' => 11,
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9D9D9'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]));
                $sheet->getRowDimension(4)->setRowHeight(24);

                if ($this->lastQuestionRow >= $this->firstQuestionRow) {
                    $sheet->getStyle('C' . $this->firstQuestionRow . ':E' . $this->lastQuestionRow)
                        ->applyFromArray(array_merge($thinBorder, [
                            'font' => [
                                'name' => $fontName,
                                'size' => 11,
                            ],
                        ]));

                    for ($row = $this->firstQuestionRow; $row <= $this->lastQuestionRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(17.25);
                        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
                    }
                }

                $sheet->getStyle('C' . $this->summaryTitleRow)->getFont()->setName($fontName)->setSize(11)->setBold(true);
                $sheet->getRowDimension($this->summaryTitleRow)->setRowHeight(17.25);

                $sheet->getStyle('D' . $this->summaryStartRow . ':E' . $this->summaryEndRow)->getFont()
                    ->setName($fontName)
                    ->setSize(11)
                    ->setBold(true);

                for ($row = $this->summaryStartRow; $row <= $this->summaryEndRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(17.25);
                }
            },
        ];
    }
}
