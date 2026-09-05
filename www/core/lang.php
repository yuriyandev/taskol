<?php 
Class Lang {
    protected $registry;
    public $data = array();

	public function __construct($registry) {
		$this->registry = $registry;

        $this->load();
	}

    public function load() {
        $file = DIR_APP . 'lang/ru.php';

		if (file_exists($file)) {
			include_once($file);

			$this->data = $data;
		} else {
			trigger_error('Error: Could not load lang ' . $file . '!');
			exit();
		}
    }

	public function get($key) {
        if(isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			return $key;
		}
    }
}