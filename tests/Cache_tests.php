<?php

require_once "../framework/cache.php";

class TestOfCache extends UnitTestCase {
    function test_Set_Get() {
		$cache = new Cache();
		$cache->Set("testkey", "testvalue");
		$result = $cache->Get("testkey");
		
		$this->assertTrue($result === "testvalue");
    }

    function test_Delete() {
		$cache = new Cache();
		$cache->Set("testkey", "testvalue");
		$cache->Delete("testkey");
		$result = $cache->Get("testkey");
		
		$this->assertTrue($result === false);
    }
}
