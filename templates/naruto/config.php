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

// Skill display names (skill id => name). Used by getSkillName() and highscores skill box.
$config['skill_names'] = [
	POT::SKILL__MAGLEVEL => 'Ninjutsu',
	POT::SKILL_SHIELD    => 'Dodge',
	POT::SKILL_DIST      => 'Shurikenjutsu',
	POT::SKILL_FISH      => 'Genjutsu',
	POT::SKILL_SWORD     => 'Kenjutsu',
	POT::SKILL_CLUB      => 'Taijutsu',
];

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
