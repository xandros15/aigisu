<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-06
 * Time: 18:48
 */

namespace Aigisu\Api\Transformers;


use Aigisu\Components\Transformer\StorageProviderTrait;
use Aigisu\Components\Transformer\TimestampTrait;
use Aigisu\Models\Unit;
use Aigisu\Models\Unit\MissingCG;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Psr\Http\Message\UriInterface;

class UnitTransformer extends TransformerAbstract
{
    use TimestampTrait, StorageProviderTrait;

    /** @var array */
    protected $availableIncludes = [
        'cg',
        'missing_cg',
    ];

    /**
     * @param Unit $unit
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeCg(Unit $unit): Collection
    {
        $transformer = new CGTransformer();
        if ($uri = $this->getStorageUri()) {
            $transformer->setStorageUri($uri);
        }

        return $this->collection($unit->cg, $transformer);
    }

    public function includeMissingCg(Unit $unit): Collection
    {
        $missing = new MissingCG($unit['cg']);
        $missing = $missing->filter([
            'is_male' => $unit['gender'] == Unit::GENDER_MALE,
            'is_dmm' => $unit['dmm'],
            'is_nutaku' => $unit['nutaku'],
            'is_special_cg' => $unit['special_cg'],
        ]);

        return $this->collection($missing, function ($missing) {
            return $missing;
        });
    }

    /**
     * @param Unit $unit
     *
     * @return array
     */
    public function transform(Unit $unit): array
    {
        return [
            'id' => (int) $unit->id,
            'name' => $unit->name,
            'japanese_name' => $unit->japanese_name,
            'rarity' => $unit->rarity,
            'icon' => $this->getIcon($unit),
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

    /**
     * @param Unit $unit
     *
     * @return string
     */
    private function getIcon(Unit $unit):? string
    {
        $storageUri = $this->getStorageUri();
        if ($unit->icon && $storageUri instanceof UriInterface) {
            return $storageUri->withPath($unit->icon);
        }

        return $unit->icon;
    }
}
