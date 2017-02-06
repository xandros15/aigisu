<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-21
 * Time: 16:10
 */

namespace Aigisu\Models\Unit;


use Illuminate\Database\Eloquent\Collection;

class MissingCG
{
    /** @var Collection */
    private $cg;

    /** @var array */
    private $missing = [];

    /** @var array */
    private $requiredMap = [
        'dmm' => [
            [
                'server' => CG::SERVER_DMM,
                'scene' => 1,
            ],
            [
                'server' => CG::SERVER_DMM,
                'scene' => 2,
            ],
        ],
        'special' => [
            [
                'server' => CG::SERVER_DMM,
                'scene' => 3,
            ],
        ],

        'nutaku' => [
            [
                'server' => CG::SERVER_NUTAKU,
                'scene' => 1,
            ],
            [
                'server' => CG::SERVER_NUTAKU,
                'scene' => 2,
            ],
        ],
    ];

    /**
     * MissingCG constructor.
     * @param null $collection
     */
    public function __construct($collection = null)
    {
        $this->cg = $collection instanceof Collection ? $collection : new Collection();
    }

    /**
     * @param Collection $cg
     */
    public function attachCGCollection(Collection $cg)
    {
        $this->missing = [];
        $this->cg = $cg;
    }

    public function filterArchival()
    {
        $this->cg = $this->cg->filter(function (CG $cg) {
            return $cg->archival === false;
        });
    }

    public function applyNutaku()
    {
        $this->applyIfNotFound($this->requiredMap['nutaku']);
    }

    public function applyDmm()
    {
        $this->applyIfNotFound($this->requiredMap['dmm']);
    }

    public function applySpecialDmm()
    {
        $this->applyIfNotFound($this->requiredMap['special']);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->missing;
    }

    /**
     * @param array $requiredMap
     */
    protected function applyIfNotFound(array $requiredMap)
    {
        foreach ($requiredMap as $required) {
            $hasCG = $this->cg->contains(function (CG $cg) use ($required) {
                return $cg->scene == $required['scene'] && $cg->server == $required['server'];
            });
            if (!$hasCG) {
                $this->missing[] = $required;
            }
        }
    }
}
