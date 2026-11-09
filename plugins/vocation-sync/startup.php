<?php
/**
 * Vocation Sync - HOOK_STARTUP
 *
 * Keeps these core settings in sync with <server_path>/data/XML/vocations.xml:
 *  - vocations         (names ordered by id, starting with id 0)
 *  - vocations_amount  (number of base vocations, without promotions)
 *  - character_samples ("<id>=<Name> Sample", only samples that exist in `players`)
 *
 * The check is cached for 10 minutes and invalidated when vocations.xml changes,
 * so it costs nothing on most requests.
 */

defined('MYAAC') or die('Direct access not allowed!');

require_once __DIR__ . '/sync.php';

vocationSyncRun();

return true;
