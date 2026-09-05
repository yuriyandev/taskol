<?php 
class ModelComment extends Model {
    public function getComments($task_id) {
		$comments = $this->db->query("SELECT c.*, u.name as user_name FROM " . DB_PREFIX . "comment c LEFT JOIN " . DB_PREFIX . "user u ON(c.user_id = u.user_id) WHERE c.task_id = '" . (int)$task_id . "' ORDER BY c.date_added DESC");

		return $comments->rows;
	}

    public function addComment($text, $task_id) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "comment SET text='" . $this->db->escape($text) . "', task_id='" . (int)$task_id . "', user_id='" . (int)$this->user->getId() . "', date_added = NOW(), date_modified = NOW() ");

		$comment_id = $this->db->getLastId();

		return $comment_id;
	}

    public function editComment($text, $comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "comment SET text='" . $this->db->escape($text) . "', date_modified = NOW() WHERE comment_id = '" . (int)$comment_id . "' AND user_id='" . (int)$this->user->getId() . "'");
	}

    public function checkCommentUserId($comment_id) {
        if($this->user->getId()) {
            $query = $this->db->query("SELECT task_id FROM " . DB_PREFIX . "comment WHERE comment_id = '" . (int)$comment_id . "' AND user_id='" . (int)$this->user->getId() . "'");

            if($query->num_rows) {
                return true;
            }
        }

        return false;
    }

    public function deleteComment($comment_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "comment WHERE comment_id = '" . (int)$comment_id . "' AND user_id='" . (int)$this->user->getId() . "'");
    }
}