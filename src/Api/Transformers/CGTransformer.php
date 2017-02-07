<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 22:28
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Models\Unit\CG;
use League\Fractal\TransformerAbstract;
use Slim\Interfaces\RouterInterface;

class CGTransformer extends TransformerAbstract
{
    use TimestampTrait;

    /** @var array */
    protected $availableIncludes = [
        'unit'
    ];
    /** @var RouterInterface */
    private $router;

    /**
     * UnitTransformer constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param CG $cg
     * @return array
     */
    public function transform(CG $cg) : array
    {
        return [
            'id' => (int) $cg->id,
            'scene' => (int) $cg->scene,
            'archival' => (bool) $cg->archival,
            'links' => [
                'local' => $this->router->pathFor('storage.images.cg', ['id' => $cg->id]),
                'google' => $this->getGoogleAttribute($cg->google_id),
                'imgur' => $this->getImgurAttribute($cg->imgur_id),
            ],
            'created_at' => $this->createTimestamp($cg->created_at),
            'updated_at' => $this->createTimestamp($cg->updated_at),
        ];
    }

    /**
     * @param CG $cg
     * @return \League\Fractal\Resource\Item
     */
    public function includeUnit(CG $cg)
    {
        return $this->item($cg->unit, new UnitTransformer($this->router));
    }

    /**
     * @param $id
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
}
