<?php

namespace App\Support;

use App\Election;
use App\ElectionVoter;
use App\Question;
use App\Voter;
use Illuminate\Support\Collection;

class QuestionVotingResultReport
{
    /** @var Collection */
    public $questions;

    /** @var Election|null */
    public $election;

    /** @var int|float */
    public $totalVotedShareAmount;

    /** @var int|float */
    public $totalSharedAmount;

    /** @var int */
    public $totalVoter;

    /** @var int */
    public $totalVotedVoter;

    /** @var float */
    public $votedShareRatio;

    /** @var array<int, array{label_mm: string, label_en: string, value: int|float, format: string}> */
    public $summaryRows;

    public static function forElection(int $electionId): self
    {
        $report = new self();
        $report->load($electionId);

        return $report;
    }

    public function electionTitle(): string
    {
        $name = $this->election ? $this->election->name : '';

        return trim($name);
    }

    public static function plainQuestionText($html): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    public static function resultLine($yesCount, $noCount): string
    {
        return sprintf(
            'Yes - %s%s/%sNo - %s',
            $yesCount,
            "\u{00a0}",
            "\u{00a0}",
            $noCount
        );
    }

    private function load(int $electionId): void
    {
        $questions = Question::with(['answers'])->where('election_id', $electionId)->get();
        foreach ($questions as $question) {
            $yesCount = 0;
            $noCount = 0;
            foreach ($question->answers as $answer) {
                $totalVoteCount = $answer->voter_vote_count;
                if ($answer->ans_flag == 1) {
                    $yesCount += $totalVoteCount;
                } else {
                    $noCount += $totalVoteCount;
                }
            }
            $question->yes_count = $yesCount;
            $question->no_count = $noCount;
        }

        $this->questions = $questions;
        $this->election = Election::find($electionId);
        $this->totalSharedAmount = Voter::all()->sum('vote_count');
        $this->totalVotedShareAmount = Voter::join('election_voters', 'voter.id', '=', 'election_voters.voter_id')
            ->where('election_voters.election_id', $electionId)
            ->where('election_voters.done', '=', 1)
            ->sum('voter.vote_count');
        $this->totalVoter = ElectionVoter::all()->count();
        $this->totalVotedVoter = ElectionVoter::where('done', 1)->count();

        $this->votedShareRatio = $this->totalSharedAmount > 0
            ? round($this->totalVotedShareAmount / $this->totalSharedAmount, 4)
            : 0;

        $this->summaryRows = [
            [
                'label_mm' => 'စုစုပေါင်း ရှယ်ယာ အရေအတွက်',
                'label_en' => 'Total Share',
                'value' => $this->totalSharedAmount,
                'format' => 'integer',
            ],
            [
                'label_mm' => 'မဲပေးခဲ့သည့် ရှယ်ယာ အရေအတွက်',
                'label_en' => 'Total Number of Voted Share',
                'value' => $this->totalVotedShareAmount,
                'format' => 'integer',
            ],
            [
                'label_mm' => 'မဲပေးခဲ့သည့် ရှယ်ယာ ရာခိုင်နှုန်း',
                'label_en' => 'Total Number of Voted Share %',
                'value' => $this->votedShareRatio,
                'format' => 'percent',
            ],
            [
                'label_mm' => 'စုစုပေါင်း မဲပေးပိုင်ခွင့်ရှိသူ ဦးရေ',
                'label_en' => 'Total Number of Voter',
                'value' => $this->totalVoter,
                'format' => 'persons',
            ],
            [
                'label_mm' => 'ဆန္ဒမဲ ပေးခဲ့သူ ဦးရေ',
                'label_en' => 'Total Number of Voted Person',
                'value' => $this->totalVotedVoter,
                'format' => 'persons',
            ],
            [
                'label_mm' => 'ဆန္ဒမဲ မပေးခဲ့သူ ဦးရေ',
                'label_en' => 'Total Number of Not Voted Person',
                'value' => max(0, $this->totalVoter - $this->totalVotedVoter),
                'format' => 'persons',
            ],
        ];
    }
}
