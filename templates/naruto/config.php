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
	'Rock Lee' => 'Lee',
	'Jiraiya'  => 'Jiraya',
];

$config['menu_categories'] = [
	MENU_CATEGORY_COMMUNITY => ['id' => 'community', 'name' => 'Community'],
	MENU_CATEGORY_LIBRARY   => ['id' => 'library',   'name' => 'Library'],
];

$config['menus'] = require __DIR__ . '/menus.php';
