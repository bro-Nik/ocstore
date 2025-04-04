<?php
class ModelCatalogExtendedReviews extends Model {
	public function addReview($data, $review_type = 'default') {
		if($review_type == 'default'){
    $this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['author']) . "', product_id = '" . (int)$data['product_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', admin_reply = '" . $this->db->escape(strip_tags($data['admin_reply'])) . "', admin_name = '" . $this->db->escape($data['admin_name']) . "', bonus = '" .(int)$data['bonus']. "', plus = '" . $this->db->escape(strip_tags($data['plus'])) . "', minus = '" . $this->db->escape(strip_tags($data['minus'])) . "', purchased = '" . (int)$data['purchased'] . "', likes = '" .(int)$data['likes']. "', dislikes = '" .(int)$data['dislikes']. "', videos = '" . $this->db->escape(strip_tags($data['videos'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");
		$review_id = $this->db->getLastId();
		if (isset($data['review_image'])) {
    foreach ($data['review_image'] as $review_image) {
    	$this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = '" . (int)$review_id . "', review_type = '0', image = '" . $this->db->escape($review_image['image']) . "', product_id = '" . (int)$data['product_id'] . "'");
    		}
			}
    }
		else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "ex_review_answer SET author = '" . $this->db->escape($data['author']) . "', product_id = '" . (int)$data['product_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', parent_id = '" . (int)$data['parent_id'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");
			$review_id = $this->db->getLastId();
		}

		$this->cache->delete('product');

		return $review_id;
	}

	public function editReview($review_id, $data, $review_type = 'default') {
		if($review_type == 'default'){
			$images_id = '';
			$image_id = '';
			$this->db->query("DELETE FROM " . DB_PREFIX . "review_image WHERE review_id = '" . (int)$review_id . "' and review_type = '0'");
			if (isset($data['review_image'])) {
				foreach ($data['review_image'] as $review_image) {
					if(strpos($review_image['image'], 'ibb.co') !== false){
					$this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = '" . (int)$review_id . "', review_type = '0', image_href = '" . $this->db->escape($review_image['image']) . "', image_href_t = '" . $this->db->escape($review_image['thumb']) . "', product_id = '" . (int)$data['product_id'] . "'");
					}
					else{
					$this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = '" . (int)$review_id . "', review_type = '0', image = '" . $this->db->escape($review_image['image']) . "', product_id = '" . (int)$data['product_id'] . "'");
					}
					$image_id .= $this->db->getLastId() . ' ';
				}
			$images_id = trim($image_id);
			}

  		$this->db->query("UPDATE " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['author']) . "', product_id = '" . (int)$data['product_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', admin_reply = '" . $this->db->escape(strip_tags($data['admin_reply'])) . "', admin_name = '" . $this->db->escape($data['admin_name']) . "', bonus = '" .(int)$data['bonus']. "', plus = '" . $this->db->escape(strip_tags($data['plus'])) . "', minus = '" . $this->db->escape(strip_tags($data['minus'])) . "', purchased = '" . (int)$data['purchased'] . "', likes = '" .(int)$data['likes']. "', dislikes = '" .(int)$data['dislikes']. "', images = '" .$this->db->escape($images_id). "', videos = '" . $this->db->escape($data['videos']) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE review_id = '" . (int)$review_id . "'");

			if($data['bonus']){
				$config = $this->config->get("extended_reviews_settings");
				$this->load->language('catalog/extended_reviews');
				if(!empty($data['videos'])){
					$bonus = $config['bonus_video'];
					$text = $this->language->get('text_video_bonus');
					}
				elseif(!empty($images_id)){
					$bonus = $config['bonus_photo'];
					$text = $this->language->get('text_photo_bonus');
					}
				else{
					$bonus = $config['bonus'];
					$text = $this->language->get('text_bonus');
					}

				$product_name = $this->db->query("SELECT DISTINCT name FROM " .DB_PREFIX. "product_description WHERE product_id = '" . (int)$data['product_id'] . "' and language_id = '" . (int)$this->config->get('config_language_id') . "'")->row['name'];
				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE review_type = '0' and review_id = '" . (int)$review_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET points = '" . (int)$bonus . "', customer_id = '" . (int)$data['customer_id'] . "', review_type = '0', review_id = '" . (int)$review_id . "', description = '" . $this->db->escape($text) . $this->db->escape($product_name) ."', date_added = NOW()");

			}
			else{
				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE review_type = '0' and review_id = '" . (int)$review_id . "'");
			}
		}
		else{
			$this->db->query("UPDATE " . DB_PREFIX . "ex_review_answer SET author = '" . $this->db->escape($data['author']) . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" . (int)$data['status'] . "', parent_id = '" . (int)$data['parent_id'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE review_id = '" . (int)$review_id . "'");
		}

		$this->cache->delete('product');
	}

	public function getImagesByReviewId($review_id) {
    		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "review_image WHERE review_id = '" . (int)$review_id . "' and review_type = '0'");

    		return $query->rows;
    	}

		public function getUnusedImages(){
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "review_image` WHERE review_id = '0' AND review_type = '0'");
			return $query->rows;
		}

  	public function deleteImageById($id){
			$image = $this->db->query("SELECT image FROM `" . DB_PREFIX . "review_image` WHERE review_image_id = '" . (int)$id . "'");
			if(is_file(DIR_IMAGE . $image->row['image'])){
			unlink(DIR_IMAGE . $image->row['image']);
		  }
			$this->db->query("DELETE FROM `" . DB_PREFIX . "review_image` WHERE review_image_id = '" . (int)$id . "'");
  	}

	public function deleteReview($review_id, $review_type = 'default') {
		if($review_type == 'default'){
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$review_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ex_review_answer WHERE parent_id = '" . (int)$review_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review_image WHERE review_type='0' and review_id = '" . (int)$review_id . "'");
		}
		else{
		$this->db->query("DELETE FROM " . DB_PREFIX . "ex_review_answer WHERE review_id = '" . (int)$review_id . "'");
		}
		$this->cache->delete('product');
	}

	public function getReview($review_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT pd.name FROM " . DB_PREFIX . "product_description pd WHERE pd.product_id = r.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS product FROM " . DB_PREFIX . "review r WHERE r.review_id = '" . (int)$review_id . "'");

		return $query->row;
	}

	public function getReviewAnswer($review_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ex_review_answer r WHERE r.review_id = '" . (int)$review_id . "'");

		return $query->row;
	}

	public function getReviews($data = array()) {
		$sql = "SELECT r.review_id, pd.name, r.author, r.rating, r.status, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'pd.name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getReviewAnswers($data = array()) {
		$sql = "SELECT r.review_id, pd.name, r.author, r.status, r.date_added FROM " . DB_PREFIX . "ex_review_answer r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'pd.name',
			'r.author',
			'r.status',
			'r.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getChildrenReviews($parent_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ex_review_answer WHERE parent_id = '" . (int)$parent_id . "'");

		return $query->rows;
	}

	public function getTotalReviewAnswers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ex_review_answer r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}


	public function getTotalReviews($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review WHERE status = '0'");

		return $query->row['total'];
	}

	public function install() {
		$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_reward` WHERE `field` = 'review_type'");
		if (!$chk->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_reward` ADD COLUMN  `review_type` int(11)");
		 }
		 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_reward` WHERE `field` = 'review_id'");
		 if (!$chk->num_rows) {
				 $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_reward` ADD COLUMN  `review_id` int(11)");
			}
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "review_image`( ".
			"`review_image_id` int(11) NOT NULL AUTO_INCREMENT,".
			"`product_id` int(11) NOT NULL,".
			"`review_id` int(11) NOT NULL default '0',".
			"`review_type` int(11) NOT NULL default '0',".
			"`image` varchar(255) default NULL,".
			"`image_href` varchar(255) default NULL,".
			"`image_href_t` varchar(255) default NULL,".
			"`date_added`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,".
			"PRIMARY KEY (`review_image_id`)".
			") ENGINE=MyISAM DEFAULT CHARSET=utf8;" );

			$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review_image` WHERE `field` = 'image_href'");
			if (!$chk->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "review_image` ADD COLUMN  `image_href` varchar(255) default NULL");
			}

			 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review_image` WHERE `field` = 'image_href_t'");
			 if (!$chk->num_rows) {
					 $this->db->query("ALTER TABLE `" . DB_PREFIX . "review_image` ADD COLUMN  `image_href_t` varchar(255) default NULL");
			}

			$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review_image` WHERE `field` = 'review_type'");
			if (!$chk->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "review_image` ADD COLUMN  `review_type` INT(11) NOT NULL default '0'");
			 }

				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_review_answer`( ".
					"`review_id` int(11) NOT NULL AUTO_INCREMENT,".
					"`product_id` int(11) NOT NULL,".
					"`customer_id` int(11) NOT NULL,".
					"`author` varchar(64) NOT NULL,".
					"`text` text NOT NULL,".
					"`status` int(1) NOT NULL default '0',".
					"`date_added` datetime NOT NULL,".
					"`date_modified` datetime NOT NULL,".
					"`parent_id` INT(11) NOT NULL default '0',".
					"PRIMARY KEY (`review_id`)".
					") ENGINE=MyISAM DEFAULT CHARSET=utf8;" );

		 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'plus'");
		 if (!$chk->num_rows) {
				 $this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `plus` TEXT");
			}

			$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'minus'");
			if (!$chk->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `minus` TEXT");
			 }

			 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'videos'");
			 if (!$chk->num_rows) {
					 $this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `videos` VARCHAR(255)");
				}

				$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'images'");
				if (!$chk->num_rows) {
						$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `images` VARCHAR(255)");
				 }

				 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'bonus'");
				 if (!$chk->num_rows) {
						 $this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `bonus` INT(1) NOT NULL default '0'");
					}

				$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'likes'");
				if (!$chk->num_rows) {
						$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `likes` INT(11) NOT NULL default '0'");
				 }

				 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'dislikes'");
				 if (!$chk->num_rows) {
						 $this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `dislikes` INT(11) NOT NULL default '0'");
					}

					$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'purchased'");
					if (!$chk->num_rows) {
							$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `purchased` TINYINT(1) NOT NULL default '0'");
					 }

					 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'admin_reply'");
					 if (!$chk->num_rows) {
							 $this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `admin_reply` TEXT");
						}

						$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE `field` = 'admin_name'");
						if (!$chk->num_rows) {
								$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD COLUMN  `admin_name` varchar(64)");
						 }

						 $this->load->model('user/user_group');
 						$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'catalog/extended_reviews');
 						$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'catalog/extended_reviews');
 						$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'module/extended_reviews');
 						$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'module/extended_reviews');
	}
}
