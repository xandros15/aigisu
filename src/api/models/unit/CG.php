<?php

namespace Aigisu\Api\Models\Unit;

use Aigisu\Api\Models\Unit;
use Aigisu\Core\Model;

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
        'google_id',
        'imgur_id',
        'imgur_delhash',
    ];
    /** @var array */
    protected $casts = [
        'archival' => 'bool'
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
        'local',
    ];

    public static function getImageSchemeArray()
    {
        return [
            self::SERVER_DMM => [1, 2, 3],
            self::SERVER_NUTAKU => [1, 2]
        ];
    }

    public static function getServersNames()
    {
        return [self::SERVER_DMM, self::SERVER_NUTAKU];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, self::UNIT_RELATION_COLUMN, 'id');
    }

    public function getGoogleAttribute()
    {
        return sprintf('http://drive.google.com/uc?export=view&id=%s', $this->attributes['google_id']);
    }

    public function getImgurAttribute()
    {
        return sprintf('http://i.imgur.com/%s.png', $this->attributes['imgur_id']);
    }

    public function getLocalAttribute()
    {
        return $this->urlTo('storage.images', ['path' => self::UPLOAD_DIRECTORY . '/' . $this->attributes['md5']]);
    }
}