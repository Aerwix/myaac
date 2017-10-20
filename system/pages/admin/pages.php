<?php
/**
 * Pages
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Pages';

if(!hasFlag(FLAG_CONTENT_PAGES) && !superAdmin())
{
	echo 'Access denied.';
	return;
}

$name = $p_title = '';
$groups = new OTS_Groups_List();

$php = false;
$access = 0;

if(!empty($action))
{
	if($action == 'delete' || $action == 'edit' || $action == 'hide')
		$id = $_REQUEST['id'];

	if(isset($_REQUEST['name']))
		$name = $_REQUEST['name'];

	if(isset($_REQUEST['title']))
		$p_title = $_REQUEST['title'];

	$php = isset($_REQUEST['php']);
	//if($php)
	//	$body = $_REQUEST['body'];
	//else
	if(isset($_REQUEST['body']))
		$body = html_entity_decode(stripslashes($_REQUEST['body']));

	if(isset($_REQUEST['access']))
		$access = $_REQUEST['access'];

	$errors = array();
	$player_id = 1;

	if($action == 'add') {
		if(Pages::add($name, $p_title, $body, $player_id, $php, $access, $errors))
		{
			$name = $p_title = $body = '';
			$player_id = $access = 0;
			$php = false;
		}
	}
	else if($action == 'delete') {
		Pages::delete($id, $errors);
	}
	else if($action == 'edit')
	{
		if(isset($id) && !isset($_REQUEST['name'])) {
			$_page = Pages::get($id);
			$name = $_page['name'];
			$p_title = $_page['title'];
			$body = $_page['body'];
			$php = $_page['php'] == '1';
			$access = $_page['access'];
		}
		else {
			Pages::update($id, $name, $p_title, $body, $player_id, $php, $access);
			$action = $name = $p_title = $body = '';
			$player_id = 1;
			$access = 0;
			$php = false;
		}
	}
	else if($action == 'hide') {
		Pages::toggleHidden($id, $errors);
	}

	if(!empty($errors))
		echo $twig->render('error_box.html.twig', array('errors' => $errors));
}

$query =
	$db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'pages'));

$pages = array();
foreach($query as $_page) {
	$pages[] = array(
		'link' => getFullLink($_page['name'], $_page['name']),
		'title' => substr($_page['title'], 0, 20),
		'id' => $_page['id'],
		'hidden' => $_page['hidden']
	);
}

echo $twig->render('admin.pages.form.html.twig', array(
	'action' => $action,
	'id' => $action == 'edit' ? $id : null,
	'name' => $name,
	'title' => $p_title,
	'php' => $php,
	'body' => isset($body) ? htmlentities($body, ENT_COMPAT, 'UTF-8') : '',
	'groups' => $groups->getGroups(),
	'access' => $access
));

echo $twig->render('admin.pages.html.twig', array(
	'pages' => $pages
));

class Pages
{
	static public function get($id)
	{
		global $db;
		$query = $db->select(TABLE_PREFIX . 'pages', array('id' => $id));
		if($query !== false)
			return $query;

		return false;
	}

	static public function add($name, $title, $body, $player_id, $php, $access, &$errors)
	{
		global $db;
		if(isset($name[0]) && isset($title[0]) && isset($body[0]) && $player_id != 0)
		{
			$query = $db->select(TABLE_PREFIX . 'pages', array('name' => $name));
			if($query === false)
				$db->insert(TABLE_PREFIX . 'pages', array('name' => $name, 'title' => $title, 'body' => $body, 'player_id' => $player_id, 'php' => $php ? '1' : '0', 'access' => $access));
			else
				$errors[] = 'Page with this words already exists.';
		}
		else
			$errors[] = 'Please fill all inputs.';

		return !count($errors);
	}

	static public function update($id, $name, $title, $body, $player_id, $php, $access) {
		global $db;
		$db->update(TABLE_PREFIX . 'pages', array('name' => $name, 'title' => $title, 'body' => $body, 'player_id' => $player_id, 'php' => $php ? '1' : '0', 'access' => $access), array('id' => $id));
	}

	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if($db->select(TABLE_PREFIX . 'pages', array('id' => $id)) !== false)
				$db->delete(TABLE_PREFIX . 'pages', array('id' => $id));
			else
				$errors[] = 'Page with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			$query = $db->select(TABLE_PREFIX . 'pages', array('id' => $id));
			if($query !== false)
				$db->update(TABLE_PREFIX . 'pages', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'Page with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}
}
?>