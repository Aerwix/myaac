<?php
/**
 * Vocation Sync - install script
 * Runs on plugin install (admin upload or `php aac plugin:setup vocation-sync`).
 */
defined('MYAAC') or die('Direct access not allowed!');

require_once __DIR__ . '/sync.php';

vocationSyncRun(true);
