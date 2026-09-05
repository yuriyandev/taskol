<?php 
Class Response {
    protected $registry;
    

	public function __construct($registry) {
		$this->registry = $registry;
        $this->lang = $registry->get('lang');
	}

    public function output($tpl, $data) {
        $tpl = DIR_APP . 'view/' . $tpl . '.tpl';

        $data = array_merge($data, $this->lang->data);
    
        if (is_file($tpl)) {
            extract($data);
    
            ob_start();
    
            require($tpl);
    
            echo ob_get_clean();
        }
    }

    public function render($tpl, $data, $extension = false) {
        $file = DIR_APP . ($extension ? '' : 'view/') . $tpl . '.tpl';

        $data = array_merge($data, $this->lang->data);
    
        if (is_file($file)) {
            extract($data);
    
            ob_start();
    
            require($file);
    
            return ob_get_clean();
        }
    
        throw new \Exception('Error: Could not load template ' . $file . '!');
        exit();
    }

    public function redirect($url, $status = 302) {
        header('Location: ' . $url, true, $status);
        exit();
    }
}