<?php
class ModelRevolutionRevblogReview extends Model {
	public function addReview($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "revblog_review SET author = '" . $this->db->escape($data['author']) . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");
		$review_id = $this->db->getLastId();
		return $review_id;
	}

	public function editReview($review_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "revblog_review SET author = '" . $this->db->escape($data['author']) . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "' WHERE review_id = '" . (int)$review_id  . "'");
	}

	public function deleteReview($review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "revblog_review WHERE review_id = '" . (int)$review_id . "'");
	}

	public function getReview($review_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "revblog_review WHERE review_id = '" . (int)$review_id . "'");
		return $query->row;
	}

	public function getReviews($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "revblog_review WHERE review_id != 0";
		if (!empty($data['filter_author'])) {
			$sql .= " AND author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		$sort_data = array(
			'author',
			'status',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 50;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalReviews($data = array()) {
		
		$db_query_431 = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "revblog_review'");
		if (!$db_query_431->num_rows) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "revblog_review` ( 
			`review_id` int(11) NOT NULL AUTO_INCREMENT,
			`revblog_id` int(11) NOT NULL,
			`parent_review_id` int(11) NOT NULL,
			`author` varchar(64) NOT NULL,
			`text` text NOT NULL,
			`status` tinyint(1) NOT NULL DEFAULT '0',
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`review_id`),
			KEY `revblog_id` (`revblog_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");
		}
		
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "revblog_review WHERE review_id != 0";
		if (!empty($data['filter_author'])) {
			$sql .= " AND author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "revblog_review WHERE status = '0'");
		return $query->row['total'];
	}
}