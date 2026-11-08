<?php
defined('MYAAC') or die('Direct access not allowed!');

// Determine active page for menu highlight
if (PAGE !== 'news') {
	$_activePage = str_replace('/', '_', isset($_REQUEST['subtopic']) ? escapeHtml($_REQUEST['subtopic']) : PAGE);
	$_exp = explode('/', PAGE);
	if (PAGE !== 'account/create' && PAGE !== 'account/lost' && isset($_exp[1])) {
		if ($_exp[0] === 'account' && $_exp[1] === 'lost') {
			$_activePage = 'account_lost';
		} elseif ($_exp[0] === 'account') {
			$_activePage = 'account_manage';
		} elseif ($_exp[0] === 'news' && $_exp[1] === 'archive') {
			$_activePage = 'news_archive';
		} elseif (in_array($_exp[0], ['characters', 'highscores', 'guilds', 'forum'])) {
			$_activePage = $_exp[0];
		}
	}
} else {
	$_activePage = 'news';
}

$_heroTitle   = $config['naruto_hero_title']   ?? setting('core.servername');
$_heroAccent  = $config['naruto_hero_accent']  ?? '';
$_heroTagline = $config['naruto_hero_tagline'] ?? '';
$_discordInvite = $config['naruto_discord_invite'] ?? '';
$_discordWidget = $config['naruto_discord_widget'] ?? '';

// Top navbar links: [label, icon, page, active-id]
$_topNav = [
	['News',        '🗞️', 'news',       'news'],
	['Highscores',  '🏆', 'highscores', 'highscores'],
	['Characters',  '👥', 'characters', 'characters'],
	['World Info',  '🗺️', 'ots-info',   'ots-info'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo template_place_holder('head_start'); ?>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/svg+xml" href="<?php echo $template_path; ?>/images/favicon.svg" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
	<link href="<?php echo $template_path; ?>/style.css" rel="stylesheet" type="text/css" />
	<?php
	// Vocation portraits for the vocation pickers (account/create, character create)
	$_vocImgDir = __DIR__ . '/images/characters/';
	$_vocImgUrl = $template_path . '/images/characters/';
	$_vocAliases = $config['naruto_vocation_image_aliases'] ?? [];
	$_vocCss = '';
	foreach ((array)config('vocations') as $_vocId => $_vocName) {
		if ((int)$_vocId === 0) continue;
		$_file = $_vocAliases[$_vocName] ?? $_vocName;
		$_file = basename($_file) . '.png';
		if (!is_file($_vocImgDir . $_file)) {
			$_file = 'ninja_body.png';
		}
		$_vocCss .= 'label[for="vocation' . (int)$_vocId . '"]::before{background-image:url("' . $_vocImgUrl . rawurlencode($_file) . '")}';
	}
	if ($_vocCss !== '') echo '<style>' . $_vocCss . '</style>';
	?>
	<script src="tools/basic.js" type="text/javascript"></script>

	<script type="text/javascript">
		var ACTIVE_PAGE = "<?php echo $_activePage; ?>";
		var LOGIN_STATUS = "<?php echo ($logged ? 'true' : 'false'); ?>";
		var BASE = "<?php echo BASE_URL; ?>";
	</script>
	<?php echo template_place_holder('head_end'); ?>
</head>
<body>
<?php echo template_place_holder('body_start'); ?>

<header id="naruto-header">
	<!-- ═══ TOP NAVBAR ═══ -->
	<nav id="naruto-nav">
		<div class="n-nav-inner">
			<ul class="n-nav-list">
				<?php foreach ($_topNav as [$_label, $_icon, $_page, $_id]): ?>
				<li>
					<a href="<?php echo getLink($_page); ?>" class="n-nav-link<?php echo ($_activePage === $_id) ? ' is-active' : ''; ?>">
						<span aria-hidden="true"><?php echo $_icon; ?></span> <?php echo $_label; ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>

			<div class="n-nav-status">
				<?php if ($status['online']): ?>
					<span class="n-dot n-dot-online"></span>
					<a href="<?php echo getLink('online'); ?>" class="n-status-online">
						Server Online <strong><?php echo (int)$status['players']; ?></strong> players
					</a>
				<?php else: ?>
					<span class="n-dot n-dot-offline"></span>
					<span class="n-status-offline">Server Offline</span>
				<?php endif; ?>
			</div>

			<ul class="n-nav-list n-nav-account">
				<?php if ($logged): ?>
				<li>
					<a href="<?php echo getLink('account/manage'); ?>" class="n-nav-link<?php echo ($_activePage === 'account_manage') ? ' is-active' : ''; ?>">
						<span aria-hidden="true">🔐</span> My Account
					</a>
				</li>
				<li class="n-nav-pill-wrap">
					<a href="<?php echo getLink('account/logout'); ?>" class="n-pill n-pill-red">Logout</a>
				</li>
				<?php else: ?>
				<li>
					<a href="<?php echo getLink('account/manage'); ?>" class="n-nav-link">
						<span aria-hidden="true">🔐</span> Login
					</a>
				</li>
				<li class="n-nav-pill-wrap">
					<a href="<?php echo getLink('account/create'); ?>" class="n-pill"><span aria-hidden="true">🔑</span> Register</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</nav>

	<!-- ═══ HERO ═══ -->
	<div id="naruto-hero">
		<div class="n-hero-bg" style="background-image:url('<?php echo $template_path; ?>/images/header.jpg')"></div>
		<div class="n-hero-scrim"></div>
		<a href="<?php echo getLink('news'); ?>" class="n-hero-content">
			<h1 class="n-hero-title">
				<span class="n-hero-line1"><?php echo htmlspecialchars($_heroTitle); ?></span>
				<?php if ($_heroAccent !== ''): ?>
				<span class="n-hero-line2"><?php echo htmlspecialchars($_heroAccent); ?></span>
				<?php endif; ?>
			</h1>
			<div class="n-hero-divider" aria-hidden="true">
				<span class="n-hero-rule"></span><span class="n-hero-diamond"></span><span class="n-hero-rule n-hero-rule-r"></span>
			</div>
			<?php if ($_heroTagline !== ''): ?>
			<p class="n-hero-tagline"><?php echo htmlspecialchars($_heroTagline); ?></p>
			<?php endif; ?>
		</a>
	</div>
</header>

<!-- ═══ LAYOUT ═══ -->
<div id="naruto-layout">

	<!-- LEFT: MENU COLUMN -->
	<aside id="naruto-menu-col">

		<!-- Account (depends on login state) -->
		<section class="n-box">
			<header class="n-box-title">Account</header>
			<nav class="n-menu-items">
				<?php if ($logged): ?>
				<a href="<?php echo getLink('account/manage'); ?>" class="n-menu-item<?php echo ($_activePage === 'account_manage') ? ' n-menu-item-active' : ''; ?>">My Account</a>
				<a href="<?php echo getLink('account/character/create'); ?>" class="n-menu-item">Create Character</a>
				<a href="<?php echo getLink('account/logout'); ?>" class="n-menu-item n-menu-item-red">Logout</a>
				<?php else: ?>
				<a href="<?php echo getLink('account/manage'); ?>" class="n-menu-item<?php echo ($_activePage === 'account_manage') ? ' n-menu-item-active' : ''; ?>">Login</a>
				<a href="<?php echo getLink('account/create'); ?>" class="n-menu-item<?php echo ($_activePage === 'account_create') ? ' n-menu-item-active' : ''; ?>">Register</a>
				<?php endif; ?>
				<a href="<?php echo getLink('downloads'); ?>" class="n-menu-item<?php echo ($_activePage === 'downloads') ? ' n-menu-item-active' : ''; ?>">Download Client</a>
			</nav>
		</section>

		<!-- Community / Library (Admin → Menus) -->
		<?php
		$_menus = get_template_menus();

		// Auto-seed: if this template has no menus in DB yet (e.g. fresh install
		// or template switched in Settings), load them from menus.php.
		if (empty($_menus) && !empty($config['menus'])) {
			\MyAAC\Plugins::installMenus($template_name, $config['menus']);
			$_cache = \MyAAC\Cache\Cache::getInstance();
			if ($_cache->enabled()) {
				$_cache->delete('template_menus_' . $template_name);
			}
			$_menus = get_template_menus();
		}

		foreach ($config['menu_categories'] as $id => $cat):
			if (empty($_menus[$id])) continue;
		?>
		<section class="n-box">
			<header class="n-box-title"><?php echo $cat['name']; ?></header>
			<nav class="n-menu-items">
				<?php foreach ($_menus[$id] as $menu):
					$_linkId = str_replace('/', '_', $menu['link']);
					$_isActive = ($_activePage === $_linkId) ? ' n-menu-item-active' : '';
					$_name = $menu['name'];
					$_isNew = false;
					if (preg_match('/\s*\[NEW\]\s*$/i', $_name)) {
						$_isNew = true;
						$_name = preg_replace('/\s*\[NEW\]\s*$/i', '', $_name);
					}
				?>
				<a href="<?php echo $menu['link_full']; ?>" class="n-menu-item<?php echo $_isActive; ?>"<?php echo $menu['target_blank']; ?>>
					<span><?php echo $_name; ?></span>
					<?php if ($_isNew): ?><span class="badge-new">NEW</span><?php endif; ?>
				</a>
				<?php endforeach; ?>
			</nav>
		</section>
		<?php endforeach; ?>

		<?php if (!empty($config['template_allow_change'])): ?>
		<section class="n-box">
			<header class="n-box-title">Template</header>
			<div class="n-box-body"><?php echo template_form(); ?></div>
		</section>
		<?php endif; ?>

	</aside>

	<!-- CENTER: CONTENT COLUMN -->
	<main id="naruto-content-col">
		<?php echo tickers(); ?>
		<?php if (PAGE === 'news'): ?>
		<div class="n-content-box n-content-news">
			<?php echo template_place_holder('center_top') . $content; ?>
		</div>
		<?php else: ?>
		<div class="n-content-box">
			<header class="n-content-title">
				<h2><?php echo htmlspecialchars($title); ?></h2>
			</header>
			<div class="n-content-body">
				<?php echo template_place_holder('center_top') . $content; ?>
			</div>
		</div>
		<?php endif; ?>
		<footer id="naruto-footer"><?php echo template_footer(); ?></footer>
	</main>

	<!-- RIGHT: SIDEBAR COLUMN -->
	<aside id="naruto-sidebar-col">
		<div class="n-sticky">

			<?php if ($_discordInvite !== '' || $_discordWidget !== ''): ?>
			<section class="n-box">
				<header class="n-box-title">Community Chat</header>
				<?php if ($_discordInvite !== ''): ?>
				<div class="n-box-body">
					<a href="<?php echo htmlspecialchars($_discordInvite); ?>" target="_blank" rel="noopener noreferrer" class="n-btn n-btn-discord">
						<span aria-hidden="true">💬</span> Join Discord
					</a>
				</div>
				<?php endif; ?>
				<?php if ($_discordWidget !== ''): ?>
				<iframe class="n-discord-frame"
					src="https://discord.com/widget?id=<?php echo urlencode($_discordWidget); ?>&theme=dark"
					width="100%" height="500" frameborder="0" loading="lazy"
					sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
				<?php endif; ?>
			</section>
			<?php endif; ?>

		</div>
	</aside>

</div><!-- /#naruto-layout -->

<?php echo template_place_holder('body_end'); ?>
</body>
</html>
