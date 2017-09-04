<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 22:28
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Transformer\StorageProviderTrait;
use Aigisu\Components\Transformer\TimestampTrait;
use Aigisu\Models\Unit\CG;
use League\Fractal\TransformerAbstract;
use Psr\Http\Message\UriInterface;

class CGTransformer extends TransformerAbstract
{
    use TimestampTrait, StorageProviderTrait;

    /** @var array */
    protected $availableIncludes = [
        'unit',
    ];

    /**
     * @param CG $cg
     *
     * @return array
     */
    public function transform(CG $cg): array
    {
        return [
            'id' => (int) $cg->id,
            'scene' => (int) $cg->scene,
            'server' => (string) $cg->server,
            'archival' => (bool) $cg->archival,
            'links' => [
                'local' => $this->getLocalAttribute($cg),
                'google' => $this->getGoogleAttribute($cg),
                'imgur' => $this->getImgurAttribute($cg),
            ],
            'created_at' => $this->createTimestamp($cg->created_at),
            'updated_at' => $this->createTimestamp($cg->updated_at),
        ];
    }

    /**
     * @param CG $cg
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeUnit(CG $cg)
    {
        $transformer = new UnitTransformer();
        if ($uri = $this->getStorageUri()) {
            $transformer->setStorageUri($uri);
        }

        return $this->item($cg->unit, $transformer);
    }

    /**
     * @param $cg
     *
     * @return null|string
     */
    private function getGoogleAttribute(CG $cg)
    {
        if (!$cg->google_id) {
            return null;
        }

        return sprintf('https://drive.google.com/uc?export=view&id=%s', $cg->google_id);
    }

    /**
     * @param $cg
     *
     * @return null|string
     */
    private function getImgurAttribute(CG $cg)
    {
        if (!$cg->imgur_id) {
            return null;
        }

        return sprintf('https://i.imgur.com/%s.png', $cg->imgur_id);

    }

    /**
     * @param CG $cg
     *
     * @return string
     */
    private function getLocalAttribute(CG $cg): string
    {
        $storageUri = $this->getStorageUri();
        if ($storageUri instanceof UriInterface) {
            return $storageUri->withPath($storageUri->getPath() . '/' . $cg->local);
        }

        return $cg->local;
    }
}
