<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-11
 * Time: 02:03
 */

namespace Aigisu\Core;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class Model
 * @package Aigisu
 * @mixin \Eloquent
 */
abstract class Model extends EloquentModel
{
    /**
     * Fill the model with an array of attributes.
     *
     * @param  array $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();

        $fillable = $this->fillableFromArray($attributes);
        $this->setProperties(array_diff($attributes, $fillable));

        foreach ($fillable as $key => $value) {
            $key = $this->removeTableFromKey($key);

            // The developers may choose to place some attributes in the "fillable"
            // array, which means only those attributes may be set through mass
            // assignment to the model, and all others will just be ignored.
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded) {
                throw new MassAssignmentException($key);
            }
        }


        return $this;
    }

    /**
     * @param array $properties
     * @return $this
     */
    protected function setProperties(array $properties)
    {
        foreach ($properties as $key => $value) {
            $key = $this->removeTableFromKey($key);
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }
}