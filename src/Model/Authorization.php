<?php

namespace Tzm\Authorizator;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Verification
 * @property int user_id
 * @property string class
 * @property string uuid
 * @property \Carbon\Carbon expires_at
 * @property int verification_code
 * @property \Illuminate\Support\Carbon verified_at
 * @property \Illuminate\Support\Carbon sent_at
 * @property string sent_via
 * @package App\Models
 * @method static IsUuidExists(string $uuid)
 */
class Authorization extends Model
{
    const SESSION_UUID_NAME = '_authorizator_uuid';

    protected $dates = [
        'expires_at',
        'sent_at'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('not_expired_codes', function (Builder $builder) {
            $builder->where('expires_at', '>', now());
        });
    }

    /**
     * Scope a query to only not used codes.
     *
     * @param $query
     * @return mixed
     */
    public function scopeAvailableCodes($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Mark verification as used.
     */
    public function markAsVerified(): void
    {
        $this->verified_at = now();
        $this->save();
    }

    /**
     * Set sent_at field
     */
    public function setSentAt(): void
    {
        $this->sent_at = now();
        $this->save();
    }

    /**
     * Set channel for code delivery
     *
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->sent_via = $channel;
        $this->save();
    }

    /**
     * User relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check is the given uuid exist in db
     * @param $query
     * @param $uuid
     * @return bool
     */
    public function scopeIsUuidExists($query, $uuid)
    {
        return $query->whereUuid($uuid)->exists();
    }

    /**
     * Retrieve uuid from session
     *
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public static function retrieveUuidFromSession()
    {
        return session(Authorization::SESSION_UUID_NAME);
    }

    /**
     * Find model by session uuid
     *
     * @param $uuid
     * @return mixed
     */
    public static function retrieveByUuid($uuid)
    {
        return self::whereUuid($uuid)->first();
    }


}
