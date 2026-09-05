<?php 
class ModelTask extends Model {
    public function getTask($task_id) {
		$join = " LEFT JOIN " . DB_PREFIX . "cover cv ON(t.cover_id = cv.cover_id) ";

		$task = $this->db->query("SELECT t.*, cv.value as cover_value FROM " . DB_PREFIX . "task t " . $join . " WHERE t.task_id = '" . (int)$task_id . "' AND (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' ) ");

		return $task->row;
	}

	public function getTasks($data = array()) {
		$select = "";
		$join = "";
		$where = " WHERE (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' ) ";
		$sort = '';
		$order = '';
		
		$join .= " LEFT JOIN " . DB_PREFIX . "list l ON(t.list_id = l.list_id) ";
		$join .= " LEFT JOIN " . DB_PREFIX . "board b ON(l.board_id = b.board_id) ";

		if(isset($this->session->data['private']) && $this->session->data['private']) {
			$where .= " AND b.`private` != 1 ";
        }
		
		if(isset($data['board_id']) && $data['board_id'] ) {
			$where .= " AND b.`board_id` = " . (int)$data['board_id'] . " ";
		}

		if(isset($data['stage_id']) && $data['stage_id'] ) {
			$where .= " AND t.`stage_id` = " . (int)$data['stage_id'] . " ";
		}

		if(isset($data['date']) && $data['date']) {
			$where .= " AND (";
			$where .= " (t.type = 'simple' AND (COALESCE(sh_all.status, 0) = 0 OR (sh_all.status = 1 AND sh_all.date_added = '" . $this->db->escape($data['date']) . "')) ) ";

			$where .= " OR (t.type = 'cumulative') ";

			$where .= " OR (t.type = 'date' AND t.date_start = '" . $this->db->escape($data['date']) . "') ";

			$where .= " OR (t.type = 'monthly' AND t.day_number = DAY('" . $this->db->escape($data['date']) . "') ) ";

			$where .= " OR (t.type = 'yearly' AND DAY(t.date_start) = DAY('" . $this->db->escape($data['date']) . "') AND MONTH(t.date_start) = MONTH('" . $this->db->escape($data['date']) . "') ) ";

			if(isset($data['day']) && $data['day']) {
				$implode = array();

				$dows = array('mon','tue','wed','thu','fri','sat','sun');
				foreach($dows as $dow) {
					$implode[] = " " . $dow . "_status = 0 ";
				}

				$where .= " OR (t.type = 'custom' AND ts.weekday IS NOT NULL AND tse.date IS NULL) ";
			}

			$where .= ") ";
		}

		$join .= " LEFT JOIN " . DB_PREFIX . "cover cv ON(t.cover_id = cv.cover_id) ";

		if(isset($data['date']) && $data['date']) {
			$join .= " LEFT JOIN " . DB_PREFIX . "task_schedule ts ON(t.task_id = ts.task_id AND ts.weekday = WEEKDAY('" . $this->db->escape($data['date']) . "') + 1) ";

			$join .= " LEFT JOIN " . DB_PREFIX . "task_schedule_exception tse ON tse.task_id = t.task_id AND tse.date = '" . $this->db->escape($data['date']) . "' ";

			$join .= " LEFT JOIN (
						SELECT task_id, MAX(status) AS status, MAX(date_added) AS date_added
						FROM " . DB_PREFIX . "task_status_history
						GROUP BY task_id
					) sh_all ON sh_all.task_id = t.task_id";

			$join .= " LEFT JOIN " . DB_PREFIX . "task_status_history sh_today ON sh_today.task_id = t.task_id AND sh_today.date_added = '" . $this->db->escape($data['date']) . "'";

			$where .= " AND ((ts.date_start IS NULL AND ts.date_end IS NULL) OR (ts.date_start = 0 AND ts.date_end = 0) OR (ts.date_start != 0 AND ts.date_end = 0 AND ts.date_start <= '" . $this->db->escape($data['date']) . "') OR (ts.date_start = 0 AND ts.date_end != 0 AND ts.date_end >= '" . $this->db->escape($data['date']) . "') OR (ts.date_start != 0 AND ts.date_end != 0 AND ts.date_start <= '" . $this->db->escape($data['date']) . "' AND ts.date_end >= '" . $this->db->escape($data['date']) . "')) ";
		}

		$sql = "SELECT t.*, cv.value as cover_value, b.name as board_name, t.stage_id " . 
		(isset($data['date']) && $data['date'] ? ", COALESCE( CASE WHEN (t.type = 'custom' OR t.type = 'cumulative') THEN sh_today.status ELSE sh_all.status END, 0) AS status, (CASE WHEN t.type = 'custom' THEN ts.time_start ELSE t.time_start END) AS time_start, (CASE WHEN t.type = 'custom' THEN ts.time_end ELSE t.time_end END) AS time_end" : "") . 
		" FROM " . DB_PREFIX . "task t " . $join . $where . " ";

		if(isset($data['sort']) && $data['sort']) {
			$sort = $this->db->escape($data['sort']);
		}

		if(isset($data['order']) && $data['order']) {
			$order = $this->db->escape($data['order']);
		}

		$sql .= " ORDER BY b.sort_order ASC, " . ($sort ? $sort . " " . ($order ? $order : 'ASC') . ", " : "") . " t.sort_order ASC ";

		//echo($sql);

		$tasks = $this->db->query($sql);

		//print_r($tasks->rows);

		return $tasks->rows;
	}

	public function getTaskTotal() {
		$tasks = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "task t WHERE (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' ) ");

		return $tasks->row['total'];
	}

	public function getTaskTotalByListId($list_id) {
		$tasks = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "task t WHERE t.list_id = '" . (int)$list_id . "' AND (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' ) ");

		return $tasks->row['total'];
	}

	public function editTask($data, $task_id) {
		$fields = array();
		foreach($data as $param => $value) {
			if(!is_float($value) && !is_integer($value) && !is_array($value)) {
				$value = $this->db->escape($value);
			}

			if($param == 'members') {
				$curent_members = $this->getTaskMembers($task_id);
				$curent_members = $curent_members ? json_decode($curent_members, 1) : array();

				$value = array_pop($value);
				if($value['email']) {
					if($value['priv'] == 'del') {
						if(isset($curent_members[md5($value['email'])])) {
							unset($curent_members[md5($value['email'])]);
						}
					} else {
						$value['member_id'] = md5($value['email']);
						$curent_members[$value['member_id']] = $value;
					}
				}

				$value = $curent_members;
			}

			if(is_array($value)) {
				$value = $this->db->escape(json_encode($value));
			}

			$fields[] = "`" . $param . "` = '" . $value . "'";
		}

		if($fields) {
			$this->db->query("UPDATE " . DB_PREFIX . "task SET " . implode(', ', $fields) . ", date_modified = NOW()  WHERE task_id = '" . (int)$task_id . "' ");

			//var_dump("UPDATE " . DB_PREFIX . "task SET " . implode(', ', $fields) . ", date_modified = NOW()  WHERE task_id = '" . (int)$task_id . "' ");
		}
	}

	public function getTaskMembers($task_id) {
		$sql = "SELECT members FROM " . DB_PREFIX . "task WHERE task_id = '" . (int)$task_id . "'";

		$query = $this->db->query($sql);

		return $query->row['members'];
	}

	public function addTask($data, $list_id = 0) {
		$task_id = 0;

		if($list_id) {
			$data['list_id'] = $list_id;
		}

		$data['sort_order'] = $this->getMaxSortValue($data['list_id']) + 1000;

		$fields = array();
		foreach($data as $param => $value) {
			if(!is_float($value) && !is_integer($value)) {
				$value = $this->db->escape($value);
			}
			$fields[] = "`" . $param . "` = '" . $value . "'";
		}

		if($fields) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "task SET " . implode(', ', $fields) . ", user_id = '" . (int)$this->user->getId() . "', date_added = NOW(), date_modified = NOW() ");

			$task_id = $this->db->getLastId();
		}

		return $task_id;
	}

	public function getMaxSortValue($list_id) {
		$query = $this->db->query("SELECT MAX(sort_order) as max_sort_value FROM " . DB_PREFIX . "task WHERE list_id = '" . (int)$list_id . "' ");

		return $query->row['max_sort_value'];
	}

	public function changeTaskList($task_id, $list_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "task SET list_id='" . (int)$list_id . "' WHERE task_id = '" . (int)$task_id . "'");
	}

	public function changeTaskSortOrder($task_id, $sort_order) {
		$this->db->query("UPDATE " . DB_PREFIX . "task SET sort_order='" . (float)$sort_order . "' WHERE task_id = '" . (int)$task_id . "'");
	}

	public function deleteTask($task_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "task WHERE task_id='" . (int)$task_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "task_schedule WHERE task_id='" . (int)$task_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "task_status_history WHERE task_id='" . (int)$task_id . "'");
	}

	public function addTaskStatusHistory($task_id, $status, $date_added) {
		$query = $this->db->query("SELECT type FROM " . DB_PREFIX . "task WHERE task_id = '" . (int)$task_id . "' ");

		if($query->row['type'] == 'custom' OR $query->row['type'] == 'cumulative' OR $query->row['type'] == 'monthly' OR $query->row['type'] == 'yearly') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "task_status_history WHERE task_id = '" . (int)$task_id . "' AND date_added  = '" . $this->db->escape($date_added) . "'");
		}

		if($query->row['type'] == 'simple' || $query->row['type'] == 'date') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "task_status_history WHERE task_id = '" . (int)$task_id . "' ");
		}

		if($status) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "task_status_history SET task_id = '" . (int)$task_id . "', status = '" . (int)$status . "', date_added  = '" . $this->db->escape($date_added) . "'");
		}
	}

	public function getTaskStatusHistoryByPeriod($filter_data = array()) {
		$period = "day";

		if(isset($filter_data['period']) && $filter_data['period']) {
			$period = $filter_data['period'];
		}

		switch ($period) {
			case 'day':
			$group = "d.d";
			break;

			case 'week':
			$group = "d.d";
			break;

			case 'month':
			$group = "DATE(d.d - INTERVAL WEEKDAY(d.d) DAY)";
			break;

			case 'year':
			$group = "MONTH(d.d)";
			break;

			default:
			$group = "d.d";
		}

		$sql ="SELECT
			" . $group . " AS period,

			COUNT(*) AS total_tasks,

			SUM(CASE WHEN COALESCE(st.status,0) = 1 THEN 1 ELSE 0 END) AS done,

			SUM(CASE WHEN COALESCE(st.status,0) = 0 THEN 1 ELSE 0 END) AS not_done,

			ROUND(
				SUM(CASE WHEN COALESCE(st.status,0)=1 THEN 1 ELSE 0 END)
				* 100 / COUNT(*),
				2
			) AS percent_done

			FROM lt_calendar d

			JOIN " . DB_PREFIX . "task t
			ON (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' ) 

			" . ($filter_data['board_id'] ? " JOIN lt_list l ON t.list_id = l.list_id AND l.board_id = " . (int)$filter_data['board_id'] : "") . "

			LEFT JOIN " . DB_PREFIX . "task_schedule ts
			ON ts.task_id = t.task_id
			AND ts.weekday = WEEKDAY(d.d) + 1 

			LEFT JOIN " . DB_PREFIX . "task_schedule_exception tse
			ON tse.task_id = t.task_id
			AND tse.date = d.d 

			LEFT JOIN " . DB_PREFIX . "task_status_history st
			ON st.task_id = t.task_id
			AND st.date_added = d.d 
				
			WHERE
			" . ($filter_data['stage_id'] ? "t.stage_id = " . (int)$filter_data['stage_id'] . " AND " : "") . "
			d.d " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . " 
			AND (
				t.type = 'simple'
				OR
				(t.type = 'date' AND t.date_start = d.d) 
				OR
				(t.type = 'monthly' AND DAY(d.d) = t.day_number) 
				OR
				(t.type = 'yearly' AND DAY(t.date_start) = DAY(d.d) AND MONTH(t.date_start) = MONTH(d.d)) 
				OR
				(
					t.type = 'custom' AND ts.weekday IS NOT NULL AND ((ts.date_start = 0 AND ts.date_end = 0) OR (ts.date_start != 0 AND ts.date_end = 0 AND d.d >= ts.date_start) OR (ts.date_start = 0 AND ts.date_end != 0 AND d.d <= ts.date_end) OR (ts.date_start != 0 AND ts.date_end != 0 AND d.d >= ts.date_start AND d.d <= ts.date_end)) AND tse.date IS NULL 
				)
				OR (t.type = 'cumulative')
			)

			";

		$sql .= " GROUP BY period ";

		$sql .= " ORDER BY d.d;";

		//echo $sql;
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTaskStatusHistoryByTask($filter_data = array()) {
		$sql = "SELECT
			q.task_id,
			q.name,
			q.type,
			q.total_tasks,
			(CASE WHEN q.done IS NULL THEN 0 ELSE q.done END) as done
		FROM (";

		$sql .="SELECT
				t.task_id,
				t.name,
				'custom' AS type,
				COUNT(*) AS total_tasks,
				SUM(tsh.status = 1) AS done
			FROM " . DB_PREFIX . "calendar d
			JOIN " . DB_PREFIX . "task t 
				ON (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' )
				AND t.type = 'custom' 

			" . ($filter_data['board_id'] ? " JOIN " . DB_PREFIX . "list l ON t.list_id = l.list_id AND l.board_id = " . (int)$filter_data['board_id'] : "") . "

			JOIN " . DB_PREFIX . "task_schedule ts
				ON ts.task_id = t.task_id
				AND ts.weekday = WEEKDAY(d.d) + 1 
			LEFT JOIN " . DB_PREFIX . "task_status_history tsh
				ON tsh.task_id = t.task_id
				AND tsh.date_added = d.d
			LEFT JOIN " . DB_PREFIX . "task_schedule_exception tse
				ON tse.task_id = t.task_id
				AND tse.date = d.d 
			WHERE d.d " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . " 
			AND ((ts.date_start = 0 AND ts.date_end = 0) OR (ts.date_start != 0 AND ts.date_end = 0 AND d.d >= ts.date_start) OR (ts.date_start = 0 AND ts.date_end != 0 AND d.d <= ts.date_end) OR (ts.date_start != 0 AND ts.date_end != 0 AND d.d >= ts.date_start AND d.d <= ts.date_end)) 
			AND tse.date IS NULL 
			GROUP BY t.task_id 

			UNION 

			SELECT
				t.task_id,
				t.name,
				t.type,
				1 AS total_tasks,
				MAX(tsh.status = 1) AS done
			FROM " . DB_PREFIX . "task t 
			" . ($filter_data['board_id'] ? " JOIN " . DB_PREFIX . "list l ON t.list_id = l.list_id AND l.board_id = " . (int)$filter_data['board_id'] : "") . " 
			LEFT JOIN " . DB_PREFIX . "task_status_history tsh
				ON tsh.task_id = t.task_id
				AND tsh.date_added " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . "
			WHERE (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' )
			AND (
				t.type = 'simple' 
				OR (t.type = 'date' AND t.date_start " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . ")
			)
			GROUP BY t.task_id, t.type
			
			UNION 

			SELECT
				t.task_id,
				t.name,
				'monthly' AS type,
				COUNT(*) AS total_tasks,
				SUM(tsh.status = 1) AS done
			FROM " . DB_PREFIX . "calendar d
			JOIN " . DB_PREFIX . "task t 
				ON (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' )
				AND t.type = 'monthly' 

			" . ($filter_data['board_id'] ? " JOIN " . DB_PREFIX . "list l ON t.list_id = l.list_id AND l.board_id = " . (int)$filter_data['board_id'] : "") . "

			LEFT JOIN " . DB_PREFIX . "task_status_history tsh
				ON tsh.task_id = t.task_id
				AND tsh.date_added = d.d
			WHERE d.d " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . " 
			AND DAY(d.d) = t.day_number  
			GROUP BY t.task_id 
			
			UNION 

			SELECT
				t.task_id,
				t.name,
				'yearly' AS type,
				COUNT(*) AS total_tasks,
				SUM(tsh.status = 1) AS done
			FROM " . DB_PREFIX . "calendar d
			JOIN " . DB_PREFIX . "task t 
				ON (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' )
				AND t.type = 'yearly' 

			" . ($filter_data['board_id'] ? " JOIN " . DB_PREFIX . "list l ON t.list_id = l.list_id AND l.board_id = " . (int)$filter_data['board_id'] : "") . "

			LEFT JOIN " . DB_PREFIX . "task_status_history tsh
				ON tsh.task_id = t.task_id
				AND tsh.date_added = d.d
			WHERE d.d " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . " 
			AND DAY(d.d) = DAY(t.date_start) AND MONTH(d.d) = MONTH(t.date_start) 
			GROUP BY t.task_id 
			
			UNION 

			SELECT
				t.task_id,
				t.name,
				'cumulative' AS type,
				COUNT(*) AS total_tasks,
				SUM(tsh.status = 1) AS done
			FROM " . DB_PREFIX . "calendar d
			JOIN " . DB_PREFIX . "task t 
				ON (t.user_id = '" . (int)$this->user->getId() . "' OR t.members LIKE '%" . $this->user->getEmail() . "%' )
				AND t.type = 'cumulative' 

			" . ($filter_data['board_id'] ? " JOIN " . DB_PREFIX . "list l ON t.list_id = l.list_id AND l.board_id = " . (int)$filter_data['board_id'] : "") . "

			LEFT JOIN " . DB_PREFIX . "task_status_history tsh
				ON tsh.task_id = t.task_id
				AND tsh.date_added = d.d
			WHERE d.d " . (isset($filter_data['date_end']) && $filter_data['date_end'] ? " BETWEEN '" . $this->db->escape($filter_data['date_start']) . "' AND '" . $this->db->escape($filter_data['date_end']) . "' " : " = '" . $this->db->escape($filter_data['date_start']) . "' ") . " 
			AND tsh.date_added 
			GROUP BY t.task_id ";

		$sql .= ") q ORDER BY (q.done / NULLIF(q.total_tasks,0)) ASC;";
		
		//echo $sql;
		$query = $this->db->query($sql);

		//print_r($query->rows);

		return $query->rows;
	}

 }