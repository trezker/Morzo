<?php

/* TODO:
 * Tests need to put global variables in a known state.
 * Session, post and get should perhaps be provided to the controller by index.php
 */
require_once '../controllers/actor.php';

class Actor_tests {
	public function Request_actor_not_logged_in() {
		assert("false");
	}
}
