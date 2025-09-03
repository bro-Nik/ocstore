<?php
require_once('catalog/controller/trait/cache.php');

class ModelCatalogInformation extends Model {
	use \CacheTrait;

	public function getInformation($information_id) {
		$cache_key = 'information.' . $information_id;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");
		$information = $query->row;
    $this->setCache($cache_key, $information);

		return $information;
	}

	public function getInformations() {
		$cache_key = 'information.all';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");
		$informations = $query->rows;
    $this->setCache($cache_key, $informations);

		return $informations;
	}

	public function getInformationLayoutId($information_id) {
		$cache_key = 'information_layout.' . $information_id;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			$result = (int)$query->row['layout_id'];
		} else {
			$result = 0;
		}
    $this->setCache($cache_key, $result);
		return $result;
	}
}
