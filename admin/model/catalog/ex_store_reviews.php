<?php
class ModelCatalogExStoreReviews extends Model {

	public function addReview($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ex_review_store SET author = '" . $this->db->escape($data['author']) . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', admin_reply = '" . $this->db->escape(strip_tags($data['admin_reply'])) . "', admin_name = '" . $this->db->escape($data['admin_name']) . "', bonus = '" .(int)$data['bonus']. "', plus = '" . $this->db->escape(strip_tags($data['plus'])) . "', minus = '" . $this->db->escape(strip_tags($data['minus'])) . "', likes = '" .(int)$data['likes']. "', dislikes = '" .(int)$data['dislikes']. "', videos = '" . $this->db->escape(strip_tags($data['videos'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "'");
		$review_id = $this->db->getLastId();
		if (isset($data['review_image'])) {
		foreach ($data['review_image'] as $review_image) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = '" . (int)$review_id . "', review_type = '1', image = '" . $this->db->escape($review_image['image']) . "'");
				}
			}

		return $review_id;
	}

	public function editReview($review_id, $data) {
		$images_id = '';
		$image_id = '';
		$this->db->query("DELETE FROM " . DB_PREFIX . "review_image WHERE review_id = '" . (int)$review_id . "' and review_type = '1'");
		if (isset($data['review_image'])) {
			foreach ($data['review_image'] as $review_image) {
				if(strpos($review_image['image'], 'ibb.co') !== false){
				$this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = '" . (int)$review_id . "', review_type = '1', image_href = '" . $this->db->escape($review_image['image']) . "', image_href_t = '" . $this->db->escape($review_image['thumb']) . "', product_id = '0'");
				}
				else{
				$this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = '" . (int)$review_id . "', review_type = '1', image = '" . $this->db->escape($review_image['image']) . "', product_id = '0'");
				}
				$image_id .= $this->db->getLastId() . ' ';
			}
		$images_id = trim($image_id);
		}

		$this->db->query("UPDATE " . DB_PREFIX . "ex_review_store SET author = '" . $this->db->escape($data['author']) . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', admin_reply = '" . $this->db->escape(strip_tags($data['admin_reply'])) . "', admin_name = '" . $this->db->escape($data['admin_name']) . "', plus = '" . $this->db->escape(strip_tags($data['plus'])) . "', minus = '" . $this->db->escape(strip_tags($data['minus'])) . "', likes = '" .(int)$data['likes']. "', dislikes = '" .(int)$data['dislikes']. "', images = '" .$this->db->escape($images_id). "', videos = '" . $this->db->escape($data['videos']) . "', bonus = '" .(int)$data['bonus']. "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE review_id = '" . (int)$review_id . "'");

		if($data['bonus']){
			$config = $this->config->get("ex_store_reviews_settings");
			$this->load->language('catalog/extended_reviews');
			if(!empty($data['videos'])){
				$bonus = $config['bonus_video'];
				$text = $this->language->get('text_store_video_bonus');
				}
			elseif(!empty($images_id)){
				$bonus = $config['bonus_photo'];
				$text = $this->language->get('text_store_photo_bonus');
				}
			else{
				$bonus = $config['bonus'];
				$text = $this->language->get('text_store_bonus');
				}

			$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE review_type = '1' and review_id = '" . (int)$review_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET points = '" . (int)$bonus . "', customer_id = '" . (int)$data['customer_id'] . "', review_id = '" . (int)$review_id . "', review_type = '1', description = '" . $this->db->escape($text) . "', date_added = NOW()");
		}
		else{
			$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE review_id = '" . (int)$review_id . "' and review_type ='1'");
		}
	}

	public function deleteReview($review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ex_review_store WHERE review_id = '" . (int)$review_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review_image WHERE review_type='1' and review_id = '" . (int)$review_id . "'");

	}

	public function getReview($review_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ex_review_store  WHERE review_id = '" . (int)$review_id . "'");

		return $query->row;
	}

	public function getReviews($data = array()) {
		$sql = "SELECT review_id, author, rating, status, date_added FROM " . DB_PREFIX . "ex_review_store WHERE 1";

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
			'rating',
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
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReviews($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ex_review_store WHERE 1";

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
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ex_review_store WHERE status = '0'");

		return $query->row['total'];
	}

	public function getImagesByReviewId($review_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "review_image WHERE review_id = '" . (int)$review_id . "' and review_type = '1'");

		return $query->rows;
	}

		public function getUnusedImages(){
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "review_image` WHERE review_id = '0' AND review_type = '1'");
			return $query->rows;
		}

		public function deleteImageById($id){
			$image = $this->db->query("SELECT image FROM `" . DB_PREFIX . "review_image` WHERE review_image_id = '" . (int)$id . "'");
			if(is_file(DIR_IMAGE . $image->row['image'])){
			unlink(DIR_IMAGE . $image->row['image']);
			}
			$this->db->query("DELETE FROM `" . DB_PREFIX . "review_image` WHERE review_image_id = '" . (int)$id . "'");
		}

		public function install() {
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

				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_review_store`( ".
					"`review_id` int(11) NOT NULL AUTO_INCREMENT,".
					"`parent_id` int(11) NOT NULL,".
					"`customer_id` int(11) NOT NULL,".
					"`author` varchar(64) NOT NULL,".
					"`text` text NOT NULL,".
					"`admin_reply` TEXT,".
					"`admin_name` varchar(64),".
					"`rating` int(1),".
					"`status` int(1) NOT NULL default '0',".
					"`bonus` int(1) NOT NULL default '0',".
					"`date_added` datetime NOT NULL,".
					"`date_modified` datetime NOT NULL,".
					"`plus` TEXT,".
					"`minus` TEXT,".
					"`videos` VARCHAR(255),".
					"`images` VARCHAR(255),".
					"`likes` INT(11) NOT NULL default '0',".
					"`dislikes` INT(11) NOT NULL default '0',".
					"PRIMARY KEY (`review_id`)".
					") ENGINE=MyISAM DEFAULT CHARSET=utf8;" );


				 $chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "ex_review_store` WHERE `field` = 'parent_id'");
				 if (!$chk->num_rows) {
						 $this->db->query("ALTER TABLE `" . DB_PREFIX . "ex_review_store` ADD COLUMN  `parent_id` INT(11) NOT NULL");
					}

					$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "ex_review_store` WHERE `field` = 'bonus'");
					if (!$chk->num_rows) {
							$this->db->query("ALTER TABLE `" . DB_PREFIX . "ex_review_store` ADD COLUMN  `bonus` INT(1) NOT NULL default '0'");
					 }

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

						$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_reward` WHERE `field` = 'review_type'");
						if (!$chk->num_rows) {
							 $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_reward` ADD COLUMN  `review_type` int(11)");
						 }

						$chk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_reward` WHERE `field` = 'review_id'");
						if (!$chk->num_rows) {
								$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_reward` ADD COLUMN  `review_id` int(11)");
						 }

					$this->load->model('user/user_group');
					$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'catalog/ex_store_reviews');
					$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'catalog/ex_store_reviews');
					$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/ex_store_reviews');
					$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/ex_store_reviews');
					$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/storereviews');
					$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/storereviews');
		}

}
