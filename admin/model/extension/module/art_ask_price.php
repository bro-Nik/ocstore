<?php
/*
@author	Artem Serbulenko
@link	http://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/
class ModelExtensionModuleArtAskprice extends Model {
		
	public function getAskPrice($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "art_ask_price aap JOIN " . DB_PREFIX . "product_description pd ON(aap.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
		$sort_data = array(	
			'aap.askprice_id',		
			'aap.email',
			'aap.phone',
			'aap.user',
			'aap.date_added'
		);		
	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY aap.date_added";	
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
	
	public function getTotalAskPrice() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "art_ask_price");
		
		return $query->row['total'];
	}

	public function deleteAskPrice($askprice_id){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "art_ask_price` WHERE askprice_id = '" . (int)$askprice_id . "'");
	}

	public function createTables() {
		$sql  = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "art_ask_price` ( ";
		$sql .= "`askprice_id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`product_id` int(11) NOT NULL, ";
		$sql .= "`user` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`email` varchar(96) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`comment` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', ";
		$sql .= "`date_added` datetime DEFAULT NULL, ";
		$sql .= "PRIMARY KEY (`askprice_id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
		$this->db->query($sql);
	}
}