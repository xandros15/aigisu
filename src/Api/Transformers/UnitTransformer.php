<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 18:48
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Models\Unit;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Slim\Interfaces\RouterInterface;

class UnitTransformer extends TransformerAbstract
{
    use TimestampTrait;

    /** @var array */
    protected $availableIncludes = [
        'cg'
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
     * @param Unit $unit
     * @return \League\Fractal\Resource\Collection
     */
    public function includeCg(Unit $unit) : Collection
    {
        return $this->collection($unit->cg, new CGTransformer($this->router));
    }

    /**
     * @param Unit $unit
     * @return array
     */
    public function transform(Unit $unit) : array
    {
        return [
            'id' => (int) $unit->id,
            'name' => $unit->name,
            'japanese_name' => $unit->japanese_name,
            'rarity' => $unit->rarity,
            'icon' => $unit->icon ? $this->router->pathFor('storage.images', ['path' => $unit->icon]) : null,
            'gender' => $unit->gender,
            'dmm' => (bool) $unit->dmm,
            'nutaku' => (bool) $unit->nutaku,
            'special_cg' => (bool) $unit->special_cg,
            'links' => [
                'seesaw' => $unit->link_seesaw,
                'gc' => $unit->link_gc,
            ],
            'created_at' => $this->createTimestamp($unit->created_at),
            'updated_at' => $this->createTimestamp($unit->updated_at),
        ];
    }
}