<?php
defined('MYAAC') or die('Direct access not allowed!');

$config['menu_default_links_color'] = '#8ab8d8';

// Hero
$config['naruto_hero_title']   = 'Naruto';          // white line
$config['naruto_hero_accent']  = 'Shadow Rising';   // cyan line
$config['naruto_hero_tagline'] = 'The War Awaits';  // orange line

// Discord (widget id; empty = hide box)
$config['naruto_discord_invite'] = 'https://discord.gg/EsYGpggM3G';
$config['naruto_discord_widget'] = '284027207934935040';

// Vocation portraits: images/characters/<Vocation Name>.png, fallback ninja_body.png.
// Aliases for vocations whose image file has a different name.
$config['naruto_vocation_image_aliases'] = [
	// 'Vocation Name' => 'ImageFileName',
];

// Portrait file name for a vocation (with alias + fallback).
function naruto_vocation_image(string $vocName): string
{
	$aliases = config('naruto_vocation_image_aliases') ?? [];
	$file = basename($aliases[$vocName] ?? $vocName) . '.png';
	return is_file(__DIR__ . '/images/characters/' . $file) ? $file : 'ninja_body.png';
}
$twig->addFunction(new \Twig\TwigFunction('vocation_image', function ($vocName) use ($template_path) {
	return BASE_URL . $template_path . '/images/characters/' . rawurlencode(naruto_vocation_image((string)$vocName));
}));

// Shinobi rank (players.shinobi_rank, 0-6). id => [letter, name]
$config['naruto_shinobi_ranks'] = [
	0 => ['E',  'Academy Ninja'],
	1 => ['D',  'Genin'],
	2 => ['C',  'Chunin'],
	3 => ['B',  'Jounin'],
	4 => ['A',  'Sannin'],
	5 => ['S',  'Kage'],
	6 => ['S+', 'Rikudo'],
];
// Returns ['id' => int, 'letter' => string, 'name' => string] or null if column missing/unknown.
$twig->addFunction(new \Twig\TwigFunction('shinobi_rank', function ($player) {
	global $db;
	static $hasColumn = null;
	if ($hasColumn === null) {
		$hasColumn = $db->hasColumn('players', 'shinobi_rank');
	}
	if (!$hasColumn || !$player || !$player->isLoaded()) {
		return null;
	}
	$id = (int)$player->getCustomField('shinobi_rank');
	$ranks = config('naruto_shinobi_ranks') ?? [];
	if (!isset($ranks[$id])) {
		return null;
	}
	return ['id' => $id, 'letter' => $ranks[$id][0], 'name' => $ranks[$id][1]];
}));

// Skill display names (skill id => name). Used by getSkillName() and highscores skill box.
$config['skill_names'] = [
	POT::SKILL__MAGLEVEL => 'Ninjutsu',
	POT::SKILL_SHIELD    => 'Dodge',
	POT::SKILL_DIST      => 'Shurikenjutsu',
	POT::SKILL_FISH      => 'Genjutsu',
	POT::SKILL_SWORD     => 'Kenjutsu',
	POT::SKILL_CLUB      => 'Taijutsu',
];

// Character page skill tiles: bar fills toward this max; color = max split in 6 steps
// using the shinobi rank palette (E sand … S+ gold). e.g. 150 → steps of 25.
$config['naruto_skill_max'] = 125;

// Skill images for highscores box: images/skills/<Skill Name>.png (no file = no image).
// Aliases for skills whose image file has a different name.
$config['naruto_skill_image_aliases'] = [
	// 'Skill Name' => 'ImageFileName',
	'Taijutsu' => 'club',
];
// lowercase name => real file name (lookup ignores case)
$config['naruto_skill_images'] = [];
foreach (glob(__DIR__ . '/images/skills/*.png') ?: [] as $_skillImg) {
	$config['naruto_skill_images'][strtolower(basename($_skillImg, '.png'))] = basename($_skillImg);
}
unset($_skillImg);

$config['menu_categories'] = [
	MENU_CATEGORY_COMMUNITY => ['id' => 'community', 'name' => 'Community'],
	MENU_CATEGORY_LIBRARY   => ['id' => 'library',   'name' => 'Library'],
];

$config['menus'] = require __DIR__ . '/menus.php';
