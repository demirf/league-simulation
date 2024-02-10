<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchStandings extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'team_name',
        'tournament_id',
        'points',
        'goals_for',
        'goals_against',
        'win',
        'draw',
        'loss'
    ];

    public function tournament(): BelongsTo {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
