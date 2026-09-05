<?php
define('DB_PREFIX', 'lt_');
define('DIR_APP', getcwd() . '/');
define('BASE_APP', '/');

class Config
{
	public $params = array();
	public function __construct() {
		// DB
		$this->params['db_host'] = getenv('DB_HOST');
		$this->params['db_name'] = getenv('MYSQL_DB');
		$this->params['db_login'] = getenv('MYSQL_USER');
		$this->params['db_password'] = getenv('MYSQL_PASSWORD');
		$this->params['db_port'] = '3306';
		
		// Settings
		$this->params['statuses'] = array(
			0 => array(
				'name' => 'Не выполнена',
				'icon' => '<i class="fas fa-times-circle"></i>'
			),
			1 => array(
				'name' => 'Выполнена',
				'icon' => '<i class="fas fa-check-circle"></i>'
			),
		);

		$this->params['stages'] = array(
			0 => array(
				'stage_id' => 0,
				'name' => 'New',
				'icon' => '<i class="fas fa-calendar-day"></i>'
			),
			1 => array(
				'stage_id' => 1,
				'name' => 'In progress',
				'icon' => '<i class="fas fa-calendar-day"></i>'
			),
			2 => array(
				'stage_id' => 2,
				'name' => 'Completed',
				'icon' => '<i class="fas fa-calendar-day"></i>'
			),
			3 => array(
				'stage_id' => 3,
				'name' => 'Canceled',
				'icon' => '<i class="fas fa-calendar-day"></i>'
			),
		);
	}

	public function get($key) {
		return $this->params[$key];
	}
}