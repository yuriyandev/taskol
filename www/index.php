<?php

require_once('core/config.php');
require_once('core/db.php');
require_once('core/registry.php');

require_once('core/session.php');
require_once('core/user.php');
require_once('core/loader.php');
require_once('core/lang.php');
require_once('core/response.php');

require_once('core/controller.php');
require_once('core/model.php');

$config = new Config();
$db = new Db($config->get('db_host'), $config->get('db_login'), $config->get('db_password'), $config->get('db_name'), $config->get('db_port'));

$registry = new Registry();
$registry->set('config', $config);
$registry->set('db', $db);

$session = new Session();
$registry->set('session', $session);

$user = new User($registry);
$registry->set('user', $user);

$loader = new Loader($registry);
$registry->set('load', $loader);

$lang = new Lang($registry);
$registry->set('lang', $lang);

$response = new Response($registry);
$registry->set('response', $response);

function get_url($action, $params = array()) {
	return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/index.php?action=' . $action;
}

$action = isset($_GET['action']) ? $_GET['action'] : false;

if(!$user->getId()) {
	if(!in_array($action, array('user/login', 'user/register'))) {
		$action = 'user/login';
	}
}


if(!$action) {
	$action = 'main/home';
}

$parts = explode('/', str_replace('../', '', (string)$action));

while ($parts) {
	$file = DIR_APP . 'controller/' . implode('/', $parts) . '.php';
	$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', implode('/', $parts));

	if (is_file($file)) {
		include_once($file);

		break;
	} else {
		$method = array_pop($parts);
	}
}

if(!class_exists($class)) {
	$file = DIR_APP . 'controller/main/not_found.php';
	include_once($file);
	$class = 'ControllerMainNotFound';
	$method = 'index';
}
	
$controller = new $class($registry);

if (!isset($method)) {
	$method = 'index';
}

if (substr($method, 0, 2) == '__') {
	exit;
}

if (is_callable(array($controller, $method))) {
	call_user_func(array($controller, $method), array());
}
?>