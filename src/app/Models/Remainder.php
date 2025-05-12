<?php

namespace App\Models;

use App\Enums\RemainderStatus;
use App\Traits\HasChanges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Remainder extends Model
{
    use HasFactory, SoftDeletes, HasChanges;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discord_user_id',
        'channel_id',
        'due_at',
        'message',
        'status',
        'error',
    ];

    protected $casts = [
        'status' => RemainderStatus::class,
        'due_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * retuns the owner of the remainder
     */
    public function discordUser(): Relation|EloquentBuilder
    {
        return $this->belongsTo(DiscordUser::class)->withTrashed();
    }

    /**
     * Adds the channel_id to the model
     *
     * @param EloquentBuilder $query
     * @param bool $present
     *
     * @return void
     *
     */
    public function scopeChannel(EloquentBuilder $query, bool $present = true): void
    {
        $query->whereNull('channel_id', not: $present);
    }

    /**
     * Returns the first active remainder
     *
     * @return self|null the first remainder if found, null if there are no active remainders
     *
     */
    public static function getFirstDueAt(): self|null
    {
        return cache()->remember(
            key: 'RemainderFirstDueAt',
            ttl: 1,
            callback: fn () => self::orderBy('due_at')->first()
        );
    }

    /**
     * Returns the last active remainder
     *
     * @return self|null the last remainder if found, null if there are no active remainders
     *
     */
    public static function getLastDueAt(): self|null
    {
        return cache()->remember(
            key: 'RemainderLastDueAt',
            ttl: 1,
            callback: fn () => self::orderByDesc('due_at')->first()
        );
    }

    /**
     * Checks, if the remainder has failed
     *
     * @return bool true if the remainder failed, false otherwise
     *
     */
    public function isFailed(): bool
    {
        return $this->status === RemainderStatus::FAILED;
    }

    /**
     * Checks, if the remainder is overdue
     *
     * @return bool true if the remainder is overdue, false otherwise
     *
     */
    public function isOverDue(): bool
    {
        return
            $this->due_at < Carbon::now()
            && $this->status !== RemainderStatus::FINISHED
            && $this->status !== RemainderStatus::FAILED
        ;
    }

}
