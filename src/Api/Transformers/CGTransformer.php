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
                'local' => $this->getLocalAttribute($cg->local),
                'google' => $this->getGoogleAttribute($cg->google_id),
                'imgur' => $this->getImgurAttribute($cg->imgur_id),
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
     * @param $id
     *
     * @return null|string
     */
    private function getGoogleAttribute($id)
    {
        $url = null;
        if ($id) {
            $url = sprintf('https://drive.google.com/uc?export=view&id=%s', $id);
        }

        return $url;
    }

    /**
     * @param $id
     *
     * @return null|string
     */
    private function getImgurAttribute($id)
    {
        $url = null;
        if ($id) {
            $url = sprintf('https://i.imgur.com/%s.png', $id);
        }

        return $url;
    }

    /**
     * @param string $local
     *
     * @return string
     */
    private function getLocalAttribute(string $local): string
    {
        $storageUri = $this->getStorageUri();
        if ($storageUri instanceof UriInterface) {
            return $storageUri->withPath($local);
        }

        return $local;
    }
}
