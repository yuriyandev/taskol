<?php 
class ControllerMainNotFound extends Controller {
    public function index() {
        $this->load->model('task');

		$data = array();

		$data['header'] = $this->load->controller('main/header');
		$data['footer'] = $this->load->controller('main/footer');

		$tpl = 'main/not_found';

		$this->response->output($tpl, $data);
	}
}