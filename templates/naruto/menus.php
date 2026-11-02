<?php
// Menu items shown in the left sidebar (Aerwix layout).
// Append " [NEW]" to a name to show the blinking NEW badge.
// The Account section is rendered by index.php (depends on login state).

return [
	MENU_CATEGORY_COMMUNITY => [
		'Search Character' => 'characters',
		'Who is Online?'   => 'online',
		'Highscores'       => 'highscores',
		'Last Kills'       => 'last-kills',
		'Guilds'           => 'guilds',
	],
	MENU_CATEGORY_LIBRARY => [
		'Server Info'   => 'ots-info',
		'Bosses [NEW]'  => 'bosses',
		'Quests [NEW]'  => 'quests',
		'World Map'     => 'world-map',
	],
];
