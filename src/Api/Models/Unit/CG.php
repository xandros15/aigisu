<?php

namespace Aigisu\Api\Models\Unit;

use Aigisu\Api\Models\Unit;
use Aigisu\Components\Http\UploadedFile;
use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Slim\Http\Request;

/**
 * Class Image
 */

/**
 * @property string $server
 * @property int $scene
 * @property Unit $unit
 * @property int $id
 * @property bool $archival
 * @property string $local
 * @property string $google
 * @property string $imgur
 */
class CG extends Model
{
    const
        CG_UPLOAD_KEY_NAME = 'cg',
        CG_UPLOAD_CATALOG = 'cg';
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
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    /**
     * @return string
     */
    public function getGoogleAttribute()
    {
        $url = null;
        if (!empty($this->attributes['google_id'])) {
            $url = sprintf('http://drive.google.com/uc?export=view&id=%s', $this->attributes['google_id']);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getImgurAttribute()
    {
        $url = null;
        if (!empty($this->attributes['imgur_id'])) {
            $url = sprintf('http://i.imgur.com/%s.png', $this->attributes['imgur_id']);
        }

        return $url;
    }

    /**
     * @param Request $request
     */
    public function uploadCG(Request $request)
    {
        /** @var $cg UploadedFile */
        $cg = $request->getUploadedFiles()[self::CG_UPLOAD_KEY_NAME] ?? null;

        if ($cg && $storagePath = $cg->store(self::CG_UPLOAD_CATALOG)) {
            $this->setAttribute('local', $storagePath);
        }
    }
}
