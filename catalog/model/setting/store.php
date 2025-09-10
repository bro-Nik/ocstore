<?php

require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ModelSettingStore extends Model {
	use \CacheTrait;

	public function getStores() {
		$cache_key = 'stores';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
		$store_data = $query->rows;
    $this->setCache($cache_key, $store_data);

		return $store_data;
	}
}
