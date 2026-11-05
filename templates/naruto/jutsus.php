<?php
/**
 * Jutsu list shown on /jutsus (templates/naruto/pages/jutsus.php).
 *
 * level: required level (null = no level shown)
 * name: jutsu name
 * description: short text (optional)
 * evolutions: optional list of upgrades by level, shown as steps under the jutsu:
 *   'evolutions' => [
 *       ['level' => 8,  'text' => '1 kunai'],
 *       ['level' => 20, 'text' => '2 kunai'],
 *   ],
 */
defined('MYAAC') or die('Direct access not allowed!');

return [
	// Jutsus every clan learns
	'common' => [
		['level' => 8,  'name' => 'Throw Kunai',    'description' => 'Rapidly throws kunai at your current target, dealing physical damage.',
			'evolutions' => [
				['level' => 8,  'text' => '1 Kunai'],
				['level' => 20, 'text' => '2 Kunai'],
				['level' => 50, 'text' => '3 Kunai'],
			],
		],
		['level' => 25, 'name' => 'Chakra Release', 'description' => 'Unleash a burst of accumulated chakra, damaging all enemies around you. The blast radius grows as you level up.',
			'evolutions' => [
				['level' => 25, 'text' => 'Area 1X1'],
				['level' => 50, 'text' => 'Area 2X2'],
				['level' => 51, 'text' => 'Area 3X3'],
			],
		],
	],

	// Used for any clan without its own entry in 'clans'
	'clan_default' => [
		['level' => 50,   'name' => 'Chakra Blast', 'description' => 'Unleash a concentrated blast of chakra at your enemies. As your mastery grows, the blast strikes more targets.',
			'evolutions' => [
					['level' => 20, 'text' => '2 Targets'],
					['level' => 40, 'text' => '4 Targets'],
					['level' => 60, 'text' => '6 Targets'],
					['level' => 80, 'text' => '8 Targets'],
				],
	],
		['level' => null, 'name' => 'Clan Special',      'description' => 'Signature technique of the clan.'],
	],

	// Clan specific jutsus. Key = vocation name exactly as in config (e.g. 'Naruto').
	'clans' => [
		// 'Naruto' => [
		// 	['level' => 50,  'name' => 'Rasengan',          'description' => 'Spinning chakra sphere that hits your target.'],
		// 	['level' => 100, 'name' => 'Kage Bunshin',      'description' => 'Summons shadow clones to fight beside you.'],
		// ],
	],
];
