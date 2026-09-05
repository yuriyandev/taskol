<?php
class Loader {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function controller($route, $data = array(), $extension = false) {		
		$output = null;

		$method = 'index';

		$parts = explode('/', preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route));

		while ($parts) {
			$file = DIR_APP . ($extension ? '' : 'controller') . '/' . implode('/', $parts) . ($extension ? '/controller' : '') . '.php';

			if (is_file($file)) {
				$route = implode('/', $parts);		
				
				break;
			} else {
				$method = array_pop($parts);
			}
		}

		// Stop any magical methods being called
		if (substr($method, 0, 2) == '__') {
			return new \Exception('Error: Calls to magic methods are not allowed!');
		}

		$file = DIR_APP . ($extension ? '' : 'controller') . '/' . $route . ($extension ? '/controller' : '') . '.php';		
		$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

		// Initialize the class
		if (is_file($file)) {
			include_once($file);
		
			$controller = new $class($this->registry);
		} else {
			return new \Exception('Error: Could not call ' . $route . '/' . $method . '!');
		}
		
		$reflection = new ReflectionClass($class);
		
		if ($reflection->hasMethod($method) && $reflection->getMethod($method)->getNumberOfRequiredParameters() <= count($data)) {
			$output = call_user_func_array(array($controller, $method), $data);
		} else {
			$output = new \Exception('Error: Could not call ' . $route . '/' . $method . '!');
		}
		
		if ($output instanceof Exception) {
			return false;
		}

		return $output;
	}

	public function model($model, $extension = false) {
		$model = str_replace('../', '', (string)$model);

		$file = DIR_APP . ($extension ? $model : 'model') . '/' . ($extension ? 'model' : $model) . '.php';
	
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

		if (file_exists($file)) {
			include_once($file);

			$this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
		} else {
			trigger_error('Error: Could not load model ' . $file . '!');
			exit();
		}
	}
}