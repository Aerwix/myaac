<?php
/**
 * Edit forum post
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if(Forum::canPost($account_logged))
{
	$post_id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : false;
	if(!$post_id) {
		echo 'Please enter post id.';
		return;
	}
	
	$thread = $db->query("SELECT `author_guid`, `author_aid`, `first_post`, `post_topic`, `post_date`, `post_text`, `post_smile`, `id`, `section` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$post_id." LIMIT 1")->fetch();
	if(isset($thread['id']))
	{
		$first_post = $db->query("SELECT `" . TABLE_PREFIX . "forum`.`author_guid`, `" . TABLE_PREFIX . "forum`.`author_aid`, `" . TABLE_PREFIX . "forum`.`first_post`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_smile`, `" . TABLE_PREFIX . "forum`.`id`, `" . TABLE_PREFIX . "forum`.`section` FROM `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`id` = ".(int) $thread['first_post']." LIMIT 1")->fetch();
		echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($thread['section']) . '">'.$sections[$thread['section']]['name'].'</a> >> <a href="' . getForumThreadLink($thread['first_post']) . '">'.$first_post['post_topic'].'</a> >> <b>Edit post</b>';
		if(Forum::hasAccess($thread['section'] && ($account_logged->getId() == $thread['author_aid'] || Forum::isModerator())))
		{
			$char_id = $post_topic = $text = $smile = null;
			$players_from_account = $db->query("SELECT `players`.`name`, `players`.`id` FROM `players` WHERE `players`.`account_id` = ".(int) $account_logged->getId())->fetchAll();
			$saved = false;
			if(isset($_REQUEST['save']))
			{
				$text = stripslashes(trim($_REQUEST['text']));
				$char_id = (int) $_REQUEST['char_id'];
				$post_topic = stripslashes(trim($_REQUEST['topic']));
				$smile = (int) $_REQUEST['smile'];
				$lenght = 0;
				for($i = 0; $i <= strlen($post_topic); $i++)
				{
					if(ord($post_topic[$i]) >= 33 && ord($post_topic[$i]) <= 126)
						$lenght++;
				}
				if(($lenght < 1 || strlen($post_topic) > 60) && $thread['id'] == $thread['first_post'])
					$errors[] = 'Too short or too long topic (short: '.$lenght.' long: '.strlen($post_topic).' letters). Minimum 1 letter, maximum 60 letters.';
				$lenght = 0;
				for($i = 0; $i <= strlen($text); $i++)
				{
					if(ord($text[$i]) >= 33 && ord($text[$i]) <= 126)
						$lenght++;
				}
				
				if($lenght < 1 || strlen($text) > 15000)
					$errors[] = 'Too short or too long post (short: '.$lenght.' long: '.strlen($text).' letters). Minimum 1 letter, maximum 15000 letters.';
				if($char_id == 0)
					$errors[] = 'Please select a character.';
				if(empty($post_topic) && $thread['id'] == $thread['first_post'])
					$errors[] = 'Thread topic can\'t be empty.';
				
				$player_on_account == false;
				
				if(count($errors) == 0)
				{
					foreach($players_from_account as $player)
						if($char_id == $player['id'])
							$player_on_account = true;
					if(!$player_on_account)
						$errors[] = 'Player with selected ID '.$char_id.' doesn\'t exist or isn\'t on your account';
				}
				
				if(count($errors) == 0) {
					$saved = true;
					if($account_logged->getId() != $thread['author_aid'])
						$char_id = $thread['author_guid'];
					$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `author_guid` = ".(int) $char_id.", `post_text` = ".$db->quote($text).", `post_topic` = ".$db->quote($post_topic).", `post_smile` = ".(int) $smile.", `last_edit_aid` = ".(int) $account_logged->getId().",`edit_date` = ".time()." WHERE `id` = ".(int) $thread['id']);
					$post_page = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`post_date` <= ".$thread['post_date']." AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread['first_post'])->fetch();
					$_page = (int) ceil($post_page['posts_count'] / $config['forum_threads_per_page']) - 1;
					header('Location: ' . getForumThreadLink($thread['first_post'], $_page));
					echo '<br />Thank you for editing post.<br /><a href="' . getForumThreadLink($thread['first_post'], $_page) . '">GO BACK TO LAST THREAD</a>';
				}
			}
			else {
				$text = $thread['post_text'];
				$char_id = (int) $thread['author_guid'];
				$post_topic = $thread['post_topic'];
				$smile = (int) $thread['post_smile'];
			}
			
			if(!$saved)
			{
				if(!empty($errors))
					echo $twig->render('error_box.html.twig', array('errors' => $errors));
				
				echo $twig->render('forum.edit_post.html.twig', array(
					'post_id' => $post_id,
					'players' => $players_from_account,
					'player_id' => $char_id,
					'topic' => htmlspecialchars($post_topic),
					'text' => htmlspecialchars($text),
					'smile' => $smile
				));
			}
		}
		else
			echo '<br/>You are not an author of this post.';
	}
	else
		echo "<br/>Post with ID " . $post_id . " doesn't exist.";
}
else
	echo "<br/>Your account is banned, deleted or you don't have any player with level " . $config['forum_level_required'] . " on your account. You can't post.";

?>