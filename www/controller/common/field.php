<?php 
class ControllerCommonField extends Controller {
    public function edit() {
        $allowed_objects = array(
            'check_list',
            'board',
            'task',
            'task_schedule'
        );
        if(!isset($_POST) || empty($_POST)) {
            exit;
        }

        $object = '';
        $param = 0;
        $fields = array();

        if(!empty($_POST)) {
            foreach($_POST as $k => $items) {
                $object = $k;
                foreach($items as $item_id => $item_fields) {
                    $param = $item_id;
                    $fields = $item_fields;
                }
            }
        }

        $children = array();
        $object_parts = explode('_', $object);
        while($object_parts) {
            $object = implode('_', $object_parts);
            if(in_array($object, $allowed_objects)) {
                break;
            } else {
                $child = array_pop($object_parts);
                $child[0] = strtoupper($child[0]);
                $children[] = $child;
            }
        }

        if($object && in_array($object, $allowed_objects) && $param) {
            $this->load->model($object);

            for($i = 0; $i < count($object_parts); $i++) {
                $object_parts[$i][0] = strtoupper($object_parts[$i][0]);
            }
            $method = 'edit' . implode('', $object_parts) . ($children ? implode('', $children) : '');

            $this->{'model_' . $object}->$method($fields, $param);
        } else {
            exit;
        }
    }
}