<?php
chdir(dirname(__FILE__) . '/docroot');
error_reporting(~0);

require_once '../util/htmltemplate.php';
require_once '../util/log.php';

$memcache;
$memcache = new Memcache;	
$memcache->addServer('127.0.0.1', 11211);
# Gets key / value pair from memcache
function Get_cache($key) {
	global $memcache;
	return ($memcache) ? $memcache->get($key) : false;
}

# Puts key / value pair into memcache
function Set_cache($key, $object, $timeout = 60) {
	global $memcache;
	return ($memcache) ? $memcache->set($key, $object, MEMCACHE_COMPRESSED, $timeout) : NULL;
}

function Delete_cache($key) {
	global $memcache;
	return ($memcache) ? $memcache->delete($key) : NULL;
}

$controller_path = '../controllers/update.php';
$r = include($controller_path);

$obj = new Update;
$obj->Index(false);
