<?php 
class ControllerMainFooter extends Controller {
    public function index() {
        $data = array();

        return $this->response->render('main/footer', $data);
    }
}