<?php 
class ModelTaskSchedule extends Model {
	public function editTaskSchedule($data, $task_id) {
		if($data) {
			foreach($data as $weekday => $fields) {
				if(isset($fields['status']) && !$fields['status']) {
					$query = $this->db->query("DELETE FROM " . DB_PREFIX . "task_schedule WHERE task_id = '" . (int)$task_id . "' AND weekday = '" . (int)$weekday . "' ");
				}

				$query = $this->db->query("SELECT task_id FROM " . DB_PREFIX . "task_schedule WHERE task_id = '" . (int)$task_id . "' AND weekday = '" . (int)$weekday . "' ");

				if(isset($fields['status']) && $fields['status'] && !$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "task_schedule SET task_id = '" . (int)$task_id . "', weekday = '" . (int)$weekday . "' ");
				}

				if(!isset($fields['status']) && $query->num_rows) {
					foreach($fields as $field_name => $field_value) {
						$this->db->query("UPDATE " . DB_PREFIX . "task_schedule SET " . $this->db->escape($field_name) . " = '" . $this->db->escape($field_value) . "' WHERE task_id = '" . (int)$task_id . "' AND weekday = '" . (int)$weekday . "' ");
					}
				}
			}
		}
	}

    public function getTaskSchedule($task_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "task_schedule WHERE task_id = '" . (int)$task_id . "' ORDER BY weekday ASC");

        return $query->rows;
    }

	public function addTaskScheduleException($task_id, $data = array()) {
		$data['date'] = date('Y-m-d', strtotime($data['date']));

		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "task_schedule_exception SET task_id = '" . (int)$task_id . "', date = '" . $this->db->escape($data['date']) . "'");
	}

	public function deleteTaskScheduleException($task_id, $data = array()) {
		$data['date'] = date('Y-m-d', strtotime($data['date']));
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "task_schedule_exception WHERE task_id = '" . (int)$task_id . "' AND date = '" . $this->db->escape($data['date']) . "'");
	}

	public function getTaskScheduleExceptions($task_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "task_schedule_exception WHERE task_id = '" . (int)$task_id . "' ORDER BY date DESC");

        return $query->rows;
	}
 }