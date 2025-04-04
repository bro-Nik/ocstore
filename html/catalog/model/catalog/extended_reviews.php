<?php
class ModelCatalogExtendedReviews extends Model {

	public function addReview($product_id, $data, $status = '0') {

		$purchased = 0;

		$implode = array();

		$order_statuses = $this->config->get('config_complete_status');

		if ($order_statuses) {

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($this->db->query("SELECT COUNT(*) AS total FROM " .DB_PREFIX. "order_product op LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id) WHERE o.customer_id = '" .(int)$this->customer->getId(). "' and op.product_id = '" .(int)$product_id. "' and (" . implode(" OR ", $implode) . ")")->row['total'] > 0){

		$purchased = 1;

		}
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape(strip_tags($data['name'])) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape($data['text']) . "', status = '" .(int)$status. "', plus = '" . $this->db->escape(strip_tags($data['plus'])) . "', minus = '" . $this->db->escape(strip_tags($data['minus'])) . "', videos = '" . $this->db->escape(strip_tags($data['video_links'])) . "', images = '" . $this->db->escape($data['image_id']) . "', rating = '" . (int)$data['rating'] . "', purchased = '" .(int)$purchased. "', date_added = NOW()");

		$review_id = $this->db->getLastId();

		$images = explode(' ', $data['image_id']);

		foreach($images as $image){

			$this->updateImageReviewById($image, $review_id);

		}

		return $review_id;
	}

	public function addReviewAnswer($product_id, $parent_id, $data, $status = '0') {

		$this->db->query("INSERT INTO " . DB_PREFIX . "ex_review_answer SET author = '" . $this->db->escape(strip_tags($data['name'])) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', parent_id = '" . (int)$parent_id . "', text = '" . $this->db->escape($data['text']) . "', status = '" .(int)$status. "', date_added = NOW()");

		$review_id = $this->db->getLastId();
	}

	public function ratingPlus($review_id){

		$this->db->query("UPDATE " . DB_PREFIX . "review SET likes = (likes + 1) WHERE review_id = '" . (int)$review_id . "'");

	}

	public function ratingMinus($review_id){

		$this->db->query("UPDATE " . DB_PREFIX . "review SET dislikes = (dislikes + 1) WHERE review_id = '" . (int)$review_id . "'");

	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20, $sort_order = 'r.date_added') {

		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}
		if($sort_order == 'media'){
			$sort_order = "r.images DESC, r.videos";
		}

		$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.likes, r.dislikes, r.text, r.admin_reply, r.admin_name, r.plus, r.minus, r.purchased, r.videos, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY " .$this->db->escape($sort_order). " DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviewsByProductId($product_id) {

		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}

	public function getChildrenReviews($product_id){

		$query = $this->db->query("SELECT * FROM " .DB_PREFIX. "ex_review_answer WHERE product_id = '" .(int)$product_id. "' and status = '1'");

		return $query->rows;
	}

	public function addUploadImage($name, $product_id) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "review_image` SET `image` = '" . $this->db->escape(strip_tags($name)) . "', `product_id` = '" . (int)$product_id . "', `date_added` = NOW()");

		$image_id = $this->db->getLastId();

		return $image_id;
	}

	public function addHostingImage($data, $product_id) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "review_image` SET `image_href` = '" . $this->db->escape(strip_tags($data['image']['url'])) . "', `image_href_t` = '" . $this->db->escape(strip_tags($data['thumb']['url'])) . "', `product_id` = '" . (int)$product_id . "', `date_added` = NOW()");

		$image_id = $this->db->getLastId();

		return $image_id;
	}

	public function getUploadByImageId($id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "review_image` WHERE review_image_id = '" . (int)$id . "'");

		return $query->row;
	}

	public function deleteImageById($id){

		$image = $this->getUploadByImageId($id);

		if(is_file(DIR_IMAGE . $image['image'])){

		unlink(DIR_IMAGE . $image['image']);

		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "review_image` WHERE review_image_id = '" . (int)$id . "'");

	}

	public function updateImageReviewById($id, $review_id){

		$this->db->query("UPDATE " .DB_PREFIX. "review_image SET review_id = '" .(int)$review_id. "', review_type = '0' WHERE review_image_id = '" .(int)$id. "'");

	}

	public function getImagesByReviewId($review_id){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "review_image WHERE review_id = '" .(int)$review_id. "' and review_type = '0'");

		return $query->rows;
	}

	public function getAllImages(){

		$query = $this->db->query("SELECT * FROM `" .DB_PREFIX. "review_image` WHERE 1");

		return $query->rows;
	}

	public function getAllProductVideos($product_id){

		$query = $this->db->query("SELECT videos FROM " .DB_PREFIX. "review WHERE product_id = '" .(int)$product_id. "' and videos <> '' AND status = '1'");

		return $query->rows;
	}

	public function getImagesByProductId($id) {

		$query = $this->db->query("SELECT ri.image, ri.review_image_id, ri.review_id, ri.image_href, ri.image_href_t FROM `" . DB_PREFIX . "review_image` ri JOIN " .DB_PREFIX. "review r ON (ri.review_id = r.review_id) WHERE ri.product_id = '" . (int)$id . "' and ri.review_id != 0 and r.status = '1'");

		return $query->rows;
	}

	public function addBonus($review_id, $bonus, $text, $product_id){

		$count = $this->db->query("SELECT 1 FROM " . DB_PREFIX . "customer_reward WHERE review_id = '" . (int)$review_id . "' and review_type='0'");

		if(!isset($count->row[1])){

		$product_name = $this->db->query("SELECT DISTINCT name FROM " .DB_PREFIX. "product_description WHERE product_id = '" . (int)$product_id . "' and language_id = '" . (int)$this->config->get('config_language_id') . "'")->row['name'];

		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE review_type = '0' and review_id = '" . (int)$review_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET points = '" . (int)$bonus . "', customer_id = '" . (int)$this->customer->getId() . "', review_id = '" . (int)$review_id . "', review_type = '0', description = '" . $this->db->escape($text) . $this->db->escape($product_name) ."', date_added = NOW()");

		$this->db->query("UPDATE " .DB_PREFIX. "review SET bonus = '1' WHERE review_id = '" .(int)$review_id. "'");

		}
	}

	public function addBonusForLike($review_id, $bonus){

		$this->db->query("UPDATE " . DB_PREFIX . "customer_reward SET points = (points + " .(int)$bonus. ") WHERE review_type = '0' and review_id = '" . (int)$review_id . "'");

	}

}
