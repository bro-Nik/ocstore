<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogManufacturer extends Model {
	
	public function getManufacturerLayoutId($manufacturer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_layout WHERE manufacturer_id = '" . (int)$manufacturer_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}
	
	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $query->row;
	}

	public function getManufacturers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'name',
				'sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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
		} else {
			$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id') . '.' . (int)$this->config->get('config_language_id'));

			if (!$manufacturer_data) {
				
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");	
				
				$manufacturer_data = $query->rows;	
				
				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id') . '.' . (int)$this->config->get('config_language_id'), $manufacturer_data);
 			}
			return $manufacturer_data;
		}
	}

	public function getManufacturerCategories($manufacturer_id) {
    $sql = "SELECT 
                c.category_id, 
                cd.name, 
                c.image,
                COUNT(p.product_id) as product_count,
                SUM(p.viewed) as total_views
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
            LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
            LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
            WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "' 
            AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            AND c.status = '1'
            AND c.category_id NOT IN (
                SELECT DISTINCT parent_id 
                FROM " . DB_PREFIX . "category 
                WHERE parent_id > 0
                AND status = '1'
            )
            GROUP BY c.category_id
            ORDER BY total_views DESC, product_count DESC, cd.name";
    
    $query = $this->db->query($sql);
    return $query->rows;
	}

	public function getManufacturerReviews($manufacturer_id, $limit = 5) {
		$query = $this->db->query("SELECT 
                              r.*, 
                              pd.name as product_name,
                              p.image as product_image,
                              p.product_id
                              FROM " . DB_PREFIX . "review r
                              LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id)
                              LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                              WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "'
                              AND r.status = '1'
                              AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                              ORDER BY r.date_added DESC
                              LIMIT " . (int)$limit);
    
    return $query->rows;
	}
}
