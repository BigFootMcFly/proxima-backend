<?php

namespace App\Models;

use App\Traits\HasChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;

class DiscordUser extends Model
{
    use HasFactory, SoftDeletes, HasApiTokens, HasChanges;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'snowflake',
        'user_name',
        'global_name',
        'avatar',
        'locale',
        'timezone',
    ];

    protected $hidden = [
        'avatar',
    ];

    /**
     * retuns only the non-trashed remainders for the DiscordUser
     */
    public function remainders()
    {
        return $this->hasMany(Remainder::class);//->withTrashed();
    }

    /**
     * retuns only the trashed remainders for the DiscordUser
     */
    public function trashedRemainders()
    {
        return $this->hasMany(Remainder::class)->onlyTrashed();
    }

    /**
     * retuns all remainders for the DiscordUser
     */
    public function allRemainders()
    {
        return $this->hasMany(Remainder::class)->withTrashed();
    }

    /**
     * retuns the count of non-trashed remainders of the DiscordUser
     */
    public function getRemainderCountAttribute(): int
    {
        return cache()->remember(
            key: 'DiscordUserRemainderCount_'.$this->id,
            ttl: 3,
            callback: fn () => $this->remainders_count ?? $this->remainders()->count()
        );
    }

    /**
     * retuns the count of trashed remainders of the DiscordUser
     */
    public function getTrashedRemainderCountAttribute(): int
    {
        return cache()->remember(
            key: 'DiscordUserTrashedRemainderCount_'.$this->id,
            ttl: 1,
            callback: fn () => $this->trashedRemainders()->count()
        );
    }

    /**
     * retuns the count of all remainders of the DiscordUser
     */
    public function getAllRemainderCountAttribute(): int
    {
        return cache()->remember(
            key: 'DiscordUserAllRemainderCount_'.$this->id,
            ttl: 3,
            callback: fn () => $this->remainders()->withTrashed()->count()
        );
    }

    /**
     * Adds the remainders count to the model
     * @param Builder $builder
     * @return void
     */
    public function scopeRemainderCount(Builder $builder): void
    {
        $builder->withCount('remainders');
    }

    /**
     * Permanantly delete the DiscordUser and all its remainders
     *
     * The records are not protected by soft-delete
     *
     * @return [type]
     *
     */
    public function permanentDelete()
    {
        $this->allRemainders()->forceDelete();

        $this->forceDelete();
    }
}
