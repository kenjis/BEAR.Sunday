<?php
namespace sandbox\Resource\Page;

use BEAR\Framework\Resource\AbstractPage as Page;
use BEAR\Framework\Inject\ResourceInject;
use BEAR\Framework\Framework;
use APCIterator;
use Ray\Di\Di\Inject;

/**
 * Index page
 */
class Index extends Page
{
    use ResourceInject;

    public function __construct()
    {
        $this['greeting'] ='Hello, BEAR.Sunday.';
        $this['version'] = [
            'php'  => phpversion(),
            'BEAR' => Framework::VERSION
        ];
        $this['extentions'] = [
            'apc'  => extension_loaded('apc') ? phpversion('apc') : 'n/a',
            'memcache'  => extension_loaded('memcache') ? phpversion('memcache') : 'n/a',
            'mysqlnd'  => extension_loaded('mysqlnd') ? phpversion('mysqlnd') : 'n/a',
            'pdo_sqlite'  => extension_loaded('pdo_sqlite') ? phpversion('pdo_sqlite') : 'n/a',
            'Xdebug'  => extension_loaded('Xdebug') ? phpversion('Xdebug') : 'n/a',
            'xhprof' => extension_loaded('xhprof') ? phpversion('xhprof') : 'n/a'
        ];
    }

    /**
     * Get
     */
    public function onGet()
    {
        $cache = apc_cache_info('user');
        $this['apc'] = [
           'total' => $cache['num_entries'],
           'size' => $cache['mem_size']
        ];
    	// page / sec
        $this['performance'] = $this->resource->get->uri('app://self/performance')->request();
        return $this;
    }
}