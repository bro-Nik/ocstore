<?php
require_once('catalog/controller/trait/cache.php');

class ModelSettingSetting extends Model {
	use \CacheTrait;

	public function getSetting($code, $store_id = 0) {
		$cache_key = 'setting.' . $code;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = json_decode($result['value'], true);
			}
		}

    $this->setCache($cache_key, $data);
		return $data;
	}
	
	public function getSettingValue($key, $store_id = 0) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}	
}
