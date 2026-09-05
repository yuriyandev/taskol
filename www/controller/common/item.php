<?php 
class ControllerCommonItem extends Controller {
    public function add() {
        $allowed_objects = array(
            'task',
            'check_list',
            'task_schedule',
        );

        if(!isset($_POST['object']) || empty($_POST['object']) || !isset($_POST['param']) || !$_POST['param']) {
            exit;
        }

        $object = $_POST['object']; unset($_POST['object']);
        $param = $_POST['param']; unset($_POST['param']);
        $fields = $_POST;

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
            $method = 'add' . implode('', $object_parts) . ($children ? implode('', $children) : '');

            $this->{'model_' . $object}->$method($param, $fields);

            echo json_encode(array('success' => true));
        } else {
            exit;
        }
    }

    public function delete() {
        $allowed_objects = array(
            'task',
            'check_list',
            'task_schedule',
        );

        if(!isset($_POST['object']) || empty($_POST['object']) || !isset($_POST['param']) || !$_POST['param']) {
            exit;
        }

        $object = $_POST['object']; unset($_POST['object']);
        $param = $_POST['param']; unset($_POST['param']);
        $fields = $_POST;

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
            $method = 'delete' . implode('', $object_parts) . ($children ? implode('', $children) : '');

            $this->{'model_' . $object}->$method($param, $fields);

            echo json_encode(array('success' => true));
        } else {
            exit;
        }
    }
}