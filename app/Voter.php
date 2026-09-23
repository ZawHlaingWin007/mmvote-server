<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use Notifiable;
    
    protected $table = 'voter';
    protected $fillable = [
        'voter_id','phone_no','vote_count','name','email'
    ];

    public function scopeList(){
        $result =Voter::join('election', 'election.id',   '=', 'voter.election_id')
                        ->where('election.status', '=', '1')
                        ->select('voter.*', 'election.name')
                        ->get();

        return $result;
    }

    public function electionVoter()
    {
        return $this->belongsTo(ElectionVoter::class,'id','voter_id');
    }   

    public function log()
    {
        return $this->hasMany(Log::class,'id','voter_id');
    }
}
