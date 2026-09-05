<?php 
class ModelCheckList extends Model {
    public function getCheckLists($task_id) {
        $check_list_data = array();

		$check_list_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "check_list WHERE task_id = '" . (int)$task_id . "' ORDER BY sort_order ASC, date_added ASC");

        if($check_list_query->num_rows) {
            foreach($check_list_query->rows as $row) {
                $row['items'] = $this->getCheckListItems($row['check_list_id']);
                $check_list_data[] = $row;
            }
        }

		return $check_list_data;
	}

    public function getCheckListItems($check_list_id) {
        $check_list_data = array();

		$check_list_items_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "check_list_item WHERE check_list_id = '" . (int)$check_list_id . "' ORDER BY sort_order ASC, date_added ASC");

		return $check_list_items_query->rows;
	}

    public function addCheckList($task_id, $data = array()) {
        $fields = array();
		foreach($data as $param => $value) {
			if(!is_float($value) && !is_integer($value)) {
				$value = $this->db->escape($value);
			}
			$fields[] = "`" . $param . "` = '" . $value . "'";
		}

        if($fields) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "check_list SET " . implode(', ', $fields) . ", task_id='" . (int)$task_id . "', date_added = NOW(), date_modified = NOW() ");

            $check_list_id = $this->db->getLastId();

            return $check_list_id;
        }

        return false;
	}

    public function addCheckListItem($check_list_id, $data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "check_list_item SET name='" . $this->db->escape($data['name']) . "', check_list_id='" . (int)$check_list_id . "', date_added = NOW(), date_modified = NOW() ");

		$check_list_item_id = $this->db->getLastId();

		return $check_list_item_id;
	}

    public function editCheckList($check_list_id, $data = array()) {
        $fields = array();
		foreach($data as $param => $value) {
			if(!is_float($value) && !is_integer($value)) {
				$value = $this->db->escape($value);
			}
			$fields[] = "`" . $param . "` = '" . $value . "'";
		}

		if($fields) {
			$this->db->query("UPDATE " . DB_PREFIX . "check_list SET " . implode(', ', $fields) . ", date_modified = NOW() WHERE check_list_id = '" . (int)$check_list_id . "' ");
		}
	}

    public function editCheckListItem($check_list_item_id, $data = array()) {
        $fields = array();
		foreach($data as $param => $value) {
			if(!is_float($value) && !is_integer($value)) {
				$value = $this->db->escape($value);
			}
			$fields[] = "`" . $param . "` = '" . $value . "'";
		}

        if($fields) {
		    $this->db->query("UPDATE " . DB_PREFIX . "check_list_item SET " . implode(', ', $fields) . ", date_modified = NOW() WHERE check_list_item_id = '" . (int)$check_list_item_id . "' ");
        }
	}

    public function deleteCheckList($check_list_id, $data = array()) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "check_list WHERE check_list_id = '" . (int)$check_list_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "check_list_item WHERE check_list_id = '" . (int)$check_list_id . "'");
    }

    public function deleteCheckListItem($check_list_item_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "check_list_item WHERE check_list_item_id = '" . (int)$check_list_item_id . "'");
    }

    public function changeCheckListSortOrder($check_list_id, $sort_order) {
        $this->db->query("UPDATE " . DB_PREFIX . "check_list SET sort_order = '" . (int)$sort_order . "', date_modified = NOW()  WHERE check_list_id = '" . (int)$check_list_id . "' ");
    }

    public function changeCheckListItemsSortOrder($check_list_item_id, $sort_order) {
        $this->db->query("UPDATE " . DB_PREFIX . "check_list_item SET sort_order = '" . (int)$sort_order . "', date_modified = NOW()  WHERE check_list_item_id = '" . (int)$check_list_item_id . "' ");
    }
}