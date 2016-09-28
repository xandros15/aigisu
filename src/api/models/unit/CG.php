<?php

namespace Aigisu\Api\Models\Unit;

use Aigisu\Api\Models\Unit;
use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Image
 */

/**
 * @property string $md5
 * @property string $server
 * @property int $scene
 * @property Unit $unit
 * @property int $id
 * @property bool $archival
 * @property string $local
 */
class CG extends Model
{
    const UNIT_RELATION_COLUMN = 'unit_id';
    const UPLOAD_DIRECTORY = 'cg';
    const
        SERVER_NUTAKU = 'nutaku',
        SERVER_DMM = 'dmm';

    protected $table = 'cg';

    /** @var array */
    protected $fillable = [
        'md5',
        'unit_id',
        'archival',
        'server',
        'scene',
        'local',
        'google_id',
        'imgur_id',
        'imgur_delhash',
    ];
    /** @var array */
    protected $casts = [
        'archival' => 'bool',
    ];
    /** @var array */
    protected $hidden = [
        'unit_id',
        'google_id',
        'imgur_id',
        'imgur_delhash',
    ];
    /** @var array */
    protected $appends = [
        'google',
        'imgur',
    ];

    /**
     * @return array
     */
    public static function getServersNames() : array
    {
        return [self::SERVER_DMM, self::SERVER_NUTAKU];
    }

    /**
     * @return BelongsTo
     */
    public function unit() : BelongsTo
    {
        return $this->belongsTo(Unit::class, self::UNIT_RELATION_COLUMN, 'id');
    }

    /**
     * @return string
     */
    public function getGoogleAttribute() : string
    {
        if (!empty($this->attributes['google_id'])) {
            $url = sprintf('http://drive.google.com/uc?export=view&id=%s', $this->attributes['google_id']);
        } else {
            $url = $this->getAttribute('local');
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getImgurAttribute() : string
    {
        if (!empty($this->attributes['imgur_id'])) {
            $url = sprintf('http://i.imgur.com/%s.png', $this->attributes['imgur_id']);
        } else {
            $url = $this->getAttribute('google');
        }

        return $url;
    }

    public function getLocalAttribute() : string
    {
        return $this->urlTo('storage.images', ['path' => $this->attributes['local']]);
    }
}