<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 19:08
 *
 * @see credits by http://flysystem.thephpleague.com/
 */

namespace Aigisu\Components\Configure;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Pimple\Container;

class Config extends Container implements ContainerInterface
{

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }
}