<?php
/**
 * Vocation Sync - shared logic
 */

use MyAAC\Cache\Cache;
use MyAAC\Models\Settings as SettingsModel;

defined('MYAAC') or die('Direct access not allowed!');

const VOCATION_SYNC_SAMPLE_SUFFIX = ' Sample';
const VOCATION_SYNC_TTL = 10 * 60;

// Samples created by the MyAAC installer (install/tools/7-finish.php).
// Deleted automatically unless they are one of the samples built from vocations.xml.
// Set to [] to keep them.
const VOCATION_SYNC_REMOVE_DEFAULT_SAMPLES = [
	'Rook Sample', 'Sorcerer Sample', 'Druid Sample', 'Paladin Sample', 'Knight Sample',
];

if (!function_exists('vocationSyncRun')) {
	/**
	 * @param bool $force skip the cache check
	 * @return bool true if settings were changed
	 */
	function vocationSyncRun(bool $force = false): bool
	{
		global $db;

		$file = config('server_path') . 'data/XML/vocations.xml';
		if (!is_file($file)) {
			return false;
		}

		$cache = Cache::getInstance();
		$cacheKey = 'vocation_sync_' . md5($file . '|' . filemtime($file));
		if (!$force && $cache->enabled()) {
			$tmp = '';
			if ($cache->fetch($cacheKey, $tmp)) {
				return false;
			}
		}

		$xml = @simplexml_load_file($file);
		if ($xml === false) {
			return false;
		}

		// id => name, and base vocations (no promotions)
		$names = [];
		$base = [];
		foreach ($xml->vocation as $vocation) {
			$id = (int)$vocation['id'];
			$name = trim((string)$vocation['name']);
			$fromVoc = (int)$vocation['fromvoc'];

			$names[$id] = $name;

			// base vocation: not a promotion of another one
			if ($id > 0 && ($fromVoc === 0 || $fromVoc === $id)) {
				$base[$id] = $name;
			}
		}

		if (empty($names)) {
			return false;
		}

		ksort($names);
		ksort($base);

		// vocations setting must have one entry per id, starting at 0, without gaps
		$vocationList = [];
		for ($i = 0, $max = max(array_keys($names)); $i <= $max; $i++) {
			$vocationList[] = $names[$i] ?? 'Unknown';
		}

		// only offer samples that really exist, otherwise character creation fails
		$existing = [];
		if (!empty($base)) {
			$quoted = array_map(fn($name) => $db->quote($name . VOCATION_SYNC_SAMPLE_SUFFIX), $base);
			$query = $db->query('SELECT `name` FROM `players` WHERE `name` IN (' . implode(',', $quoted) . ')');
			foreach ($query->fetchAll() as $row) {
				$existing[$row['name']] = true;
			}
		}

		$samples = [];
		foreach ($base as $id => $name) {
			if (isset($existing[$name . VOCATION_SYNC_SAMPLE_SUFFIX])) {
				$samples[] = $id . '=' . $name . VOCATION_SYNC_SAMPLE_SUFFIX;
			}
		}

		// remove the installer's default samples (never one we are going to use)
		$ourSamples = array_map(fn($name) => $name . VOCATION_SYNC_SAMPLE_SUFFIX, $base);
		$toRemove = array_values(array_diff(VOCATION_SYNC_REMOVE_DEFAULT_SAMPLES, $ourSamples));
		if (!empty($samples) && !empty($toRemove)) {
			$quoted = array_map(fn($name) => $db->quote($name), $toRemove);
			$db->exec('DELETE FROM `players` WHERE `name` IN (' . implode(',', $quoted) . ')');
		}

		$wanted = [
			'vocations' => implode(', ', $vocationList),
			'vocations_amount' => (string)count($base),
		];

		// keep MyAAC defaults if no sample exists yet
		if (!empty($samples)) {
			$wanted['character_samples'] = implode("\n", $samples);
		}

		$current = SettingsModel::where('name', 'core')
			->whereIn('key', array_keys($wanted))
			->pluck('value', 'key')
			->toArray();

		$changed = false;
		foreach ($wanted as $key => $value) {
			if (($current[$key] ?? null) === $value) {
				continue;
			}

			\MyAAC\Settings::getInstance()->updateInDatabase('core', $key, $value);

			// apply to the current request too (compat config was filled before this hook)
			setting(['core.' . $key, $value]);
			config([$key, setting('core.' . $key)]);

			$changed = true;
		}

		if ($cache->enabled()) {
			$cache->set($cacheKey, '1', VOCATION_SYNC_TTL);
		}

		return $changed;
	}
}
