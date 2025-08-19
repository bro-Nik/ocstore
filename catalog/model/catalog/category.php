<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}

	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	public function getCategoryFilters($category_id) {
		$implode = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}

	// Получение связанных услуг
	public function getServiceRelated($category_id) {
    $service_related_data = array();

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_service_related WHERE category_id = '" . (int)$category_id . "'");

    foreach ($query->rows as $result) {
        $service_related_data[] = $result['article_id'];
    }

    return $service_related_data;
	}

	public function getPopularSubcategories($category_id, $limit = 5) {
    $query = $this->db->query("SELECT c.category_id, cd.name, c.parent_id, c.viewed, 
                             	(SELECT COUNT(*) FROM " . DB_PREFIX . "product_to_category p2c 
                              	WHERE p2c.category_id = c.category_id) as product_count
                             	FROM " . DB_PREFIX . "category c
                             	LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
                             	WHERE c.parent_id = '" . (int)$category_id . "' 
                             	AND c.status = '1'
                             	AND c.viewed != '0'
                             	AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                             	ORDER BY c.viewed DESC, product_count DESC, c.sort_order
                             	LIMIT " . (int)$limit);
    
    return $query->rows;
	}
	public function getPopularFilters($category_id, $limit = 5) {
    $query = $this->db->query("SELECT op.page_id, opd.name, op.viewed, 
                              (SELECT GROUP_CONCAT(DISTINCT cp.path_id ORDER BY cp.`level` SEPARATOR '_') 
                               FROM " . DB_PREFIX . "category_path cp 
                               WHERE cp.category_id = op.category_id) AS path
                              FROM " . DB_PREFIX . "ocfilter_page op
                              LEFT JOIN " . DB_PREFIX . "ocfilter_page_description opd 
                                ON (op.page_id = opd.page_id)
                              WHERE op.category_id = '" . (int)$category_id . "' 
                              AND op.status = '1'
                              AND op.viewed != '0'
                              ORDER BY op.viewed DESC
                              LIMIT " . (int)$limit);
    
    return $query->rows;
	}
}
