<?php
/**
 * class_cache.php
 *
 * @package Kateglo
 */
require_once($base_dir . '/vendor/phpfastcache/phpfastcache.php');

/**
 * Cache class
 *
 * @package Kateglo
 * @author  Ivan Lanin <ivan@lanin.org>
 * @since   2014-02-13
 */
class cache
{

    /** @var phpFastCache Cache object */
    var $cache;

    /** @var string Cache ID */
    var $id;

    /**
     * Constructor
     */
    function __construct($prefix = 'index.php?')
    {
        global $base_dir, $_SERVER;
        phpFastCache::setup('storage', 'files');
        phpFastCache::setup('path', $base_dir);
        phpFastCache::setup('securityKey', 'cache');
        $this->cache = phpFastCache();
        $this->id = $_SERVER['QUERY_STRING'];
        if ($this->id == '') {
            $this->id = 'mod=home';
        }
        $this->id = $this->prefix . $this->id;
    }

    /**
     * Get cached value
     */
    function get()
    {
        $ret = $this->cache->get($this->id);
        if ($ret != null) {
            return $ret;
        }
    }

    /**
     * Set cached value
     *
     * @param   string  $cachedValue Catched value
     */
    function set($cachedValue)
    {
        $this->cache->set($this->id, $cachedValue, 3600);
    }

}