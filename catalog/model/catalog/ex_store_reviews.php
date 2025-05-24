<?php
class ModelCatalogExStoreReviews extends Model {

	public function addReview($data, $status = '0') {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ex_review_store SET author = '" . $this->db->escape(strip_tags($data['name'])) . "', customer_id = '" . (int)$this->customer->getId() . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" .(int)$status. "', plus = '" . $this->db->escape(strip_tags($data['plus'])) . "', minus = '" . $this->db->escape(strip_tags($data['minus'])) . "', videos = '" . $this->db->escape(strip_tags($data['video_links'])) . "', images = '" . $this->db->escape($data['image_id']) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");
		$review_id = $this->db->getLastId();

		$images = explode(' ', $data['image_id']);
		foreach($images as $image){
			$this->updateImageReviewById($image, $review_id);
		}

		return $review_id;
	}

	public function addReviewAnswer($parent_id, $data, $status = '0') {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ex_review_store SET author = '" . $this->db->escape(strip_tags($data['name'])) . "', customer_id = '" . (int)$this->customer->getId() . "', parent_id = '" . (int)$parent_id . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', status = '" .(int)$status. "', date_added = NOW()");
		$review_id = $this->db->getLastId();
	}

	public function ratingPlus($review_id){
		$this->db->query("UPDATE " . DB_PREFIX . "ex_review_store SET likes = (likes + 1) WHERE review_id = '" . (int)$review_id . "'");
	}

	public function ratingMinus($review_id){
		$this->db->query("UPDATE " . DB_PREFIX . "ex_review_store SET dislikes = (dislikes + 1) WHERE review_id = '" . (int)$review_id . "'");
	}

	public function getReviews($start = 0, $limit = 20, $sort_order = 'date_added', $con = '1') {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$content = 1;

		if($con == 'media'){
			$content = "images <> '' AND videos <> ''";
		}
		if($con == 'photo'){
			$content = "images <> ''";
		}
		if($con == 'video'){
			$content = "videos <> ''";
		}
		if($sort_order == 'media'){
			$sort_order = "images DESC, videos";
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ex_review_store WHERE status = '1' AND parent_id = '0' AND " .$content. " ORDER BY " .$this->db->escape($sort_order). " DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviews() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ex_review_store  WHERE parent_id = '0' AND status = '1'");

		return $query->row['total'];
	}

	public function getReview($id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ex_review_store WHERE review_id = '" . (int)$id . "'");

		return $query->row;
	}

	public function getChildrenReviews(){
		$query = $this->db->query("SELECT * FROM " .DB_PREFIX. "ex_review_store WHERE parent_id != '0' and status = '1'");
		return $query->rows;
	}

	public function addUploadImage($name) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "review_image` SET `image` = '" . $this->db->escape(strip_tags($name)) . "', `product_id` = '0', `date_added` = NOW()");
		$image_id = $this->db->getLastId();
		return $image_id;
	}

	public function addHostingImage($data) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "review_image` SET `image_href` = '" . $this->db->escape(strip_tags($data['image']['url'])) . "', `image_href_t` = '" . $this->db->escape(strip_tags($data['thumb']['url'])) . "', `product_id` = '0', `date_added` = NOW()");

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
		$this->db->query("UPDATE " .DB_PREFIX. "review_image SET review_id = '" .(int)$review_id. "', review_type = '1' WHERE review_image_id = '" .(int)$id. "'");
	}

	public function getImagesByReviewId($review_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "review_image WHERE review_id = '" .(int)$review_id. "' and review_type = '1'");
		return $query->rows;
	}

	public function getAllImages(){
		$query = $this->db->query("SELECT * FROM `" .DB_PREFIX. "review_image` ri JOIN " . DB_PREFIX ."ex_review_store r ON (ri.review_id = r.review_id) WHERE r.status = '1' and review_type = '1'");
		return $query->rows;
	}

	public function getAllVideos(){
		$query = $this->db->query("SELECT videos FROM " .DB_PREFIX. "ex_review_store WHERE  videos <> '' AND status = '1'");
		return $query->rows;
	}

	public function getAverageRating(){
		$query = $this->db->query("SELECT TRUNCATE(AVG(rating),1) as rating FROM " .DB_PREFIX. "ex_review_store WHERE  status = '1'");

		return $query->row['rating'];
	}

	public function addBonus($review_id, $bonus, $text){
		$count = $this->db->query("SELECT 1 FROM " . DB_PREFIX . "customer_reward WHERE review_id = '" . (int)$review_id . "' and review_type='1'");

		if(!isset($count->row[1])){
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET points = '" . (int)$bonus . "', customer_id = '" . (int)$this->customer->getId() . "', review_type ='1', review_id = '" . (int)$review_id . "', description = '" . $this->db->escape($text) . "', date_added = NOW()");
		$this->db->query("UPDATE " .DB_PREFIX. "ex_review_store SET bonus = '1' WHERE review_id = '" .(int)$review_id. "'");
		}
	}

	public function addBonusForLike($review_id, $bonus){
		$this->db->query("UPDATE " . DB_PREFIX . "customer_reward SET points = (points + " .(int)$bonus. ") WHERE review_id = '" . (int)$review_id . "' and review_type='1'");
	}

	public function getPercentReviewsStore() {
		$sql = "SELECT
			TRUNCATE(SUM(IF(rating=1,1,0)) * 100 / COUNT(rating),0) AS star1,
			TRUNCATE(SUM(IF(rating=2,1,0)) * 100 / COUNT(rating),0) AS star2,
			TRUNCATE(SUM(IF(rating=3,1,0)) * 100 / COUNT(rating),0) AS star3,
			TRUNCATE(SUM(IF(rating=4,1,0)) * 100 / COUNT(rating),0) AS star4,
			TRUNCATE(SUM(IF(rating=5,1,0)) * 100 / COUNT(rating),0) AS star5
			FROM " . DB_PREFIX . "ex_review_store WHERE status = '1'";

		$query = $this->db->query($sql);
		$datapercent = array();

		foreach($query->rows as $result){
			$datapercent = array(
				'star1' => $result['star1'] ? $result['star1'] : 0,
				'star2' => $result['star2'] ? $result['star2'] : 0,
				'star3' => $result['star3'] ? $result['star3'] : 0,
				'star4' => $result['star4'] ? $result['star4'] : 0,
				'star5' => $result['star5'] ? $result['star5'] : 0,

			);
		}
		return $datapercent;
	}
}
