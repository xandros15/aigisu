<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 18:17
 */
namespace Aigisu\Components\Http\Filesystem;


use Aigisu\Core\Configuration;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class FilesystemManager
{
    /** @var array*/
    protected $config;

    /**
     * The array of resolved filesystem drivers.
     *
     * @var array
     */
    protected $disks = [];

    /**
     * Create a new filesystem manager instance.
     *
     * @param  array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string $name
     * @return Filesystem
     */
    public function drive(string $name = ''): Filesystem
    {
        return $this->disk($name);
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string $name
     * @return Filesystem
     */
    public function disk(string $name = '') : Filesystem
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver() : string
    {
        return $this->config['default'];
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string $name
     * @return Filesystem
     */
    protected function get(string $name): Filesystem
    {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given disk.
     *
     * @param  string $name
     * @return Filesystem
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name): Filesystem
    {
        $config = $this->getConfig($name);

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Get the filesystem connection configuration.
     *
     * @param  string $name
     * @return array
     */
    protected function getConfig(string $name) : array
    {
        return $this->config['disks'][$name];
    }

    /**
     * Get a default cloud filesystem instance.
     *
     * @return Filesystem
     */
    public function cloud() : Filesystem
    {
        $name = $this->getDefaultCloudDriver();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudDriver() : string
    {
        return $this->config['cloud'];
    }

    /**
     * Create an instance of the local driver.
     *
     * @param  array $config
     * @return Filesystem
     */
    public function createLocalDriver(array $config) : Filesystem
    {
        $permissions = isset($config['permissions']) ? $config['permissions'] : [];

        $links = isset($config['links']) && $config['links'] === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS;

        return $this->createFlySystem(new LocalAdapter($config['root'], LOCK_EX, $links, $permissions), $config);
    }

    /**
     * Create a FlySystem instance with the given adapter.
     *
     * @param  AdapterInterface $adapter
     * @param  array $config
     * @return  FilesystemInterface
     */
    protected function createFlySystem(AdapterInterface $adapter, array $config) : FilesystemInterface
    {
        return new Filesystem($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Create an instance of the ftp driver.
     *
     * @param  array $config
     * @return Filesystem
     */
    public function createFtpDriver(array $config) : Filesystem
    {
        return $this->createFlySystem(new FtpAdapter($config), $config);
    }
}
