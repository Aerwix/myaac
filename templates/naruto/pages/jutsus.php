<?php
/**
 * Jutsus page: /jutsus or /jutsus?clan=<Vocation Name>
 * Data: templates/naruto/jutsus.php
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Jutsus';

$jutsuData = require __DIR__ . '/../jutsus.php';
$common      = $jutsuData['common'] ?? [];
$clanDefault = $jutsuData['clan_default'] ?? [];
$clanJutsus  = $jutsuData['clans'] ?? [];

// Clans = vocations (skip "None"), with portrait
$imgDir  = __DIR__ . '/../images/characters/';
$aliases = config('naruto_vocation_image_aliases') ?? [];
$clans = [];
foreach ((array)config('vocations') as $vocId => $vocName) {
	if ((int)$vocId === 0 || isset($clans[$vocName])) {
		continue;
	}
	$file = basename($aliases[$vocName] ?? $vocName) . '.png';
	if (!is_file($imgDir . $file)) {
		$file = 'ninja_body.png';
	}
	$clans[$vocName] = $file;
}

$clan = isset($_GET['clan']) ? (string)$_GET['clan'] : '';
if (!isset($clans[$clan])) {
	$clan = '';
}

$rows = [];
foreach ($common as $j) {
	$rows[] = $j + ['clan' => false];
}
foreach (($clan !== '' && isset($clanJutsus[$clan]) ? $clanJutsus[$clan] : $clanDefault) as $j) {
	$rows[] = $j + ['clan' => true];
}

// Evolutions sorted by level
foreach ($rows as &$row) {
	$row['evolutions'] = array_values($row['evolutions'] ?? []);
	usort($row['evolutions'], fn($a, $b) => ($a['level'] ?? 0) <=> ($b['level'] ?? 0));
}
unset($row);

// Sort by level, jutsus without level last
usort($rows, function ($a, $b) {
	$la = $a['level'] ?? PHP_INT_MAX;
	$lb = $b['level'] ?? PHP_INT_MAX;
	return $la <=> $lb;
});

$twig->display('jutsus.html.twig', [
	'clans' => $clans,
	'clan'  => $clan,
	'rows'  => $rows,
]);
