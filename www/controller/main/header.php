<?php 
class ControllerMainHeader extends Controller {
    public function index() {
        $data = array();

        $data['base'] = BASE_APP;

        if(isset($this->session->data['private'])) {
            $data['private'] = $this->session->data['private'];
        } else {
            $data['private'] = 1;
        }

        if($this->user->getId()) {
            $data['logged'] = true;
        } else {
            $data['logged'] = false;
        }

        return $this->response->render('main/header', $data);
    }
}