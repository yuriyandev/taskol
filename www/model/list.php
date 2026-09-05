<?php 
class ModelList extends Model {
    public function getLists($filter = array()) {
		$select = "";
		$join = "";
		$where = "";

		if(isset($this->session->data['private']) && $this->session->data['private']) {
			$join .= " LEFT JOIN " . DB_PREFIX . "board b ON(l.board_id = b.board_id) ";
			$where .= " AND b.`private` != 1 ";
        }

		if(isset($filter['board_id']) && $filter['board_id']) {
			$where .= " AND l.board_id = '" . (int)$filter['board_id'] . "' ";
		}

		$sql = "SELECT l.* FROM " . DB_PREFIX . "list l " . $join . " WHERE 1 " . $where;

		$sql .= " ORDER BY l.sort_order ASC, l.date_added ASC ";

		$lists = $this->db->query($sql);

		return $lists->rows;
	}

	public function getList($list_id) {
		$select = "";
		$join = "";
		$where = "";

		if(isset($this->session->data['private']) && $this->session->data['private']) {
			$join .= " LEFT JOIN " . DB_PREFIX . "board b ON(l.board_id = b.board_id) ";
			$where .= " AND b.`private` != 1 ";
        }

		$sql = "SELECT l.* FROM " . DB_PREFIX . "list l " . $join . " WHERE l.list_id = '" . (int)$list_id . "'" . $where;

		$list = $this->db->query($sql);

		return $list->row;
	}

	public function editList($data, $board_id, $list_id) {
		$json = array();

		$list = $data['list'];

		$this->db->query("UPDATE " . DB_PREFIX . "list SET name='" . $this->db->escape($list['name']) . "', description='" . $this->db->escape($list['description']) . "', row = '" . (int)$list['row'] . "', date_modified = NOW()  WHERE list_id = '" . (int)$list_id . "' AND board_id = '" . (int)$board_id . "'");
	}

	public function addList($data, $board_id) {
		$json = array();

		$list = $data['list'];

		$this->db->query("INSERT INTO " . DB_PREFIX . "list SET name='" . $this->db->escape($list['name']) . "', description='" . $this->db->escape($list['description']) . "', board_id = '" . (int)$board_id . "', row = '" . (int)$list['row'] . "', date_added = NOW(), date_modified = NOW() ");

		$list_id = $this->db->getLastId();

		return $list_id;
	}

	public function deleteList($list_id) {
		$groups = $this->db->query("DELETE FROM " . DB_PREFIX . "list WHERE list_id = '" . (int)$list_id . "'");
	}

	public function changeListSortOrder($list_id, $sort_order) {
		$this->db->query("UPDATE " . DB_PREFIX . "list SET sort_order = '" . (int)$sort_order . "', date_modified = NOW()  WHERE list_id = '" . (int)$list_id . "' ");
	}
}