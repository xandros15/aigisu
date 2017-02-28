<?php

namespace Aigisu\Models\Unit;


use Aigisu\Components\Http\UploadedFile;
use Aigisu\Core\Model;
use Aigisu\Models\Unit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Slim\Http\Request;

/**
 * @property string $server
 * @property int $scene
 * @property Unit $unit
 * @property int $id
 * @property bool $archival
 * @property string $local
 * @property string $google_id
 * @property string $imgur_id
 * @property string $imgur_delhash
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
     * @param Request $request
     */
    public function uploadCG(Request $request)
    {
        /** @var $cg UploadedFile */
        $cg = $request->getUploadedFiles()[self::CG_UPLOAD_KEY_NAME] ?? null;

        if ($cg && $storagePath = $cg->storeAsPublic(self::CG_UPLOAD_CATALOG)) {
            $this->setAttribute('local', $storagePath);
        }
    }

    /**
     * @param Request $request
     */
    public function saveOrFailCG(Request $request) : void
    {
        $this->fill($request->getParams());
        $this->uploadCG($request);
        $this->saveOrFail();
    }
}
