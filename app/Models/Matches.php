<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matches extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_team_goals',
        'away_team_goals',
        'week',
        'status',
        'tournament_id',
        'is_match_played'
    ];

    public const WIN = 'win';
    public const DRAW = 'draw';
    public const LOSS = 'loss';

    public const PROBABILITIES = [self::WIN, self::DRAW, self::LOSS];

    public const PENDING = 'pending';
    public const COMPLETE = 'complete';

    public function tournament(): BelongsTo {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function homeTeam(): BelongsTo {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
