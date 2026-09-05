<?php 
class ModelBoard extends Model {
    public function getBoards($filter = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "board WHERE (user_id = '" . (int)$this->user->getId() . "' OR members LIKE '%" . $this->user->getEmail() . "%' ) ";

		if(isset($this->session->data['private'])) {
			if($this->session->data['private']) {
				$sql .= " AND `private` != 1 ";
			}
		} else {
			$sql .= " AND `private` != 1 ";
		}

		$sql .= " ORDER BY sort_order ASC ";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getBoard($board_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "board WHERE (user_id = '" . (int)$this->user->getId() . "' OR members LIKE '%" . $this->user->getEmail() . "%' ) AND  board_id = '" . (int)$board_id . "'";

		if(isset($this->session->data['private']) && $this->session->data['private']) {
            $sql .= " AND `private` != 1 ";
        }

		$board = $this->db->query($sql);

		return $board->row;
	}

	public function editBoard($data, $board_id) {
		$fields = array();
		foreach($data as $param => $value) {
			if($param == 'members') {
				$curent_members = $this->getBoardMembers($board_id);
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

			if(!is_float($value) && !is_integer($value) && !is_array($value)) {
				$value = $this->db->escape($value);
			}
			if(is_array($value)) {
				$value = $this->db->escape(json_encode($value));
			}

			$fields[] = "`" . $param . "` = '" . $value . "'";
		}

		if($fields) {
			$this->db->query("UPDATE " . DB_PREFIX . "board SET " . implode(', ', $fields) . ",  date_modified = NOW()  WHERE board_id = '" . (int)$board_id . "' AND (user_id = '" . (int)$this->user->getId() . "' OR members LIKE '%" . $this->user->getEmail() . "%' )");
		}
	}

	public function getBoardMembers($board_id) {
		$sql = "SELECT members FROM " . DB_PREFIX . "board WHERE board_id = '" . (int)$board_id . "'";

		$query = $this->db->query($sql);

		return $query->row['members'];
	}

	public function addBoard($data) {
		$json = array();

		$board = $data['board'];

		$this->db->query("INSERT INTO " . DB_PREFIX . "board SET name='" . $this->db->escape($board['name']) . "', description='" . $this->db->escape($board['description']) . "', private = '" . (int)$board['private'] . "', user_id = '" . (int)$this->user->getId() . "', date_added = NOW(), date_modified = NOW() ");

		$board_id = $this->db->getLastId();

		return $board_id;
	}

	public function deleteBoard($board_id) {
		$groups = $this->db->query("DELETE FROM " . DB_PREFIX . "board WHERE board_id = '" . (int)$board_id . "' AND user_id = '" . (int)$this->user->getId() . "'");
	}

	public function changeBoardSortOrder($board_id, $sort_order) {
		$this->db->query("UPDATE " . DB_PREFIX . "board SET sort_order = '" . (int)$sort_order . "', date_modified = NOW()  WHERE board_id = '" . (int)$board_id . "' ");
	}
}