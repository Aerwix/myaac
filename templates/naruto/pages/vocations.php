<?php
/**
 * Characters page: /vocations or /vocations?clan=<Vocation Name>
 * Stats read from <server_path>/data/XML/vocations.xml
 * (slug "characters" is the character search page)
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Characters';

$xmlPath = rtrim((string)config('server_path'), '/\\') . '/data/XML/vocations.xml';

$error = null;
$vocs  = [];
if (!is_file($xmlPath)) {
	$error = 'Character data is not available right now.';
} else {
	libxml_use_internal_errors(true);
	$xml = simplexml_load_file($xmlPath);
	libxml_clear_errors();
	if ($xml === false) {
		$error = 'Character data could not be read.';
	} else {
		foreach ($xml->vocation as $v) {
			$id = (int)$v['id'];
			if ($id === 0) {
				continue;
			}

			$formula = [];
			if (isset($v->formula)) {
				foreach ($v->formula->attributes() as $k => $val) {
					$formula[(string)$k] = (float)$val;
				}
			}

			$name = (string)$v['name'];
			$vocs[$name] = [
				'id'          => $id,
				'name'        => $name,
				'image'       => naruto_vocation_image($name),
				'hp'          => (int)$v['gainhp'],
				'chakra'      => (int)$v['gainmana'],
				'cap'         => (int)$v['gaincap'],
				'speed'       => (int)$v['basespeed'],
				'attackspeed' => (int)$v['attackspeed'],
				'hp_regen'    => ['amount' => (int)$v['gainhpamount'],   'ticks' => (int)$v['gainhpticks']],
				'chakra_regen'=> ['amount' => (int)$v['gainmanaamount'], 'ticks' => (int)$v['gainmanaticks']],
				'formula'     => $formula,
			];
		}
	}
}

// Bars: each stat relative to the highest value among all characters
$max = ['hp' => 1, 'chakra' => 1, 'cap' => 1, 'speed' => 1];
foreach ($vocs as $v) {
	foreach ($max as $k => $m) {
		$max[$k] = max($m, $v[$k]);
	}
}

// Combat section: only these formula keys are shown, in this order (same as vocations.xml)
$formulaLabels = [
	'meleeDamage'  => 'Melee Damage', // Taijutsu + Kenjutsu
	'distDamage'   => 'Throwing Damage',
	'magicDamage'  => 'Jutsu Damage',
	'healing'      => 'Healing',
	'summonDamage' => 'Summon Damage',
	'defense'      => 'Dodge', // shield/weapon block value
	'armor'        => 'Armor',   // armor damage reduction
];
// Combat icons: 'img' = images/skills/<file> (game pixel icon), 'ph' = Phosphor Fill glyph,
// 'svg' = images/icons/<name>.svg (game-icons.net); also add a .n-gi-<name> mask rule in style.css
$formulaIcons = [
	'meleeDamage'  => ['ph'  => 'sword'],
	'distDamage'   => ['svg' => 'star-shuriken'], // images/icons/star-shuriken.svg (game-icons.net, CC BY 3.0)
	'magicDamage'  => ['ph'  => 'fire'],
	'healing'      => ['ph'  => 'first-aid'],
	'summonDamage' => ['ph'  => 'paw-print'],
	'defense'      => ['svg' => 'dodging'], // Dodge (shielding skill), images/icons/dodging.svg
	'armor'        => ['ph'  => 'shield-checkered'],
];
foreach ($vocs as &$v) {
	$v['formula'] = array_intersect_key(array_merge($formulaLabels, $v['formula']), $formulaLabels, $v['formula']);
	// x1.00 = no buff, hide it
	$v['formula'] = array_filter($v['formula'], fn($val) => abs($val - 1.0) > 0.0001);
}
unset($v);

$clan = isset($_GET['clan']) ? (string)$_GET['clan'] : '';
if (!isset($vocs[$clan])) {
	$clan = '';
}

$twig->display('vocations.html.twig', [
	'error'         => $error,
	'vocs'          => $vocs,
	'shown'         => $clan !== '' ? [$vocs[$clan]] : array_values($vocs),
	'clan'          => $clan,
	'max'           => $max,
	'formulaLabels' => $formulaLabels,
	'formulaIcons'  => $formulaIcons,
]);
