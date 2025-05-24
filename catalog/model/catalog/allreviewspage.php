<?php
class ModelCatalogAllreviewspage extends Model
{
    public function getLatestCustomerReviews($limit, $category_id) {
       if($category_id !='0'){
			$query = $this->db->query(" SELECT DISTINCT * FROM (  (  SELECT DISTINCT r.*, pd.name, p.price, p.image  FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			WHERE p2s.store_id = '0' AND p.date_available <= NOW()
			AND p.status = '1'
			AND r.status = '1'
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND p2c.category_id = '" . (int)$category_id . "'
			ORDER BY date_added DESC  LIMIT ". (int)$limit .")) a  LIMIT ". (int)$limit ."");
		} else {
			$query = $this->db->query(" SELECT DISTINCT * FROM (  (  SELECT DISTINCT r.*, pd.name, p.price, p.image  FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			WHERE p2s.store_id = '0' AND p.date_available <= NOW()
			AND p.status = '1'
			AND r.status = '1'
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND p2c.category_id != '" . (int)$category_id . "'
			ORDER BY date_added DESC  LIMIT ". (int)$limit .")) a  LIMIT ". (int)$limit ."");

		}
		return $query->rows;
    }

    public function getRandomCustomerReviews($limit, $category_id) {
        if($category_id !='0'){
			$query = $this->db->query(" SELECT DISTINCT * FROM (  (  SELECT DISTINCT r.*, pd.name, p.price, p.image  FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			WHERE p2s.store_id = '0' AND p.date_available <= NOW()
			AND p.status = '1'
			AND r.status = '1'
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND p2c.category_id = '" . (int)$category_id . "'
			ORDER BY RAND()  LIMIT ". (int)$limit .")) a  LIMIT ". (int)$limit ."");
		} else {
			$query = $this->db->query(" SELECT DISTINCT * FROM (  (  SELECT DISTINCT r.*, pd.name, p.price, p.image  FROM " . DB_PREFIX . "review r
			LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			WHERE p2s.store_id = '0' AND p.date_available <= NOW()
			AND p.status = '1'
			AND r.status = '1'
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND p2c.category_id != '" . (int)$category_id . "'
			ORDER BY RAND() LIMIT ". (int)$limit .")) a  LIMIT ". (int)$limit ."");

		}
		return $query->rows;
    }

	 public function getAllCustomerReviews($start = 0, $limit = 12) {
        $query = $this->db->query("SELECT r.review_id, r.author, r.purchased, r.plus, r.minus, r.rating, r.text, r.videos, r.admin_name, r.admin_reply, r.likes, r.dislikes, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }

    public function getChildrenReviews($review_id){
      $query = $this->db->query("SELECT * FROM " .DB_PREFIX. "ex_review_answer WHERE parent_id = '" .(int)$review_id. "' and status = '1'");
      return $query->rows;
    }

    public function getTotalCustomerReviews() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1'");

        return $query->row['total'];
    }
}
?>
