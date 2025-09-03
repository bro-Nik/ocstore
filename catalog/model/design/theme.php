<?php

require_once('catalog/controller/trait/cache.php');

class ModelDesignTheme extends Model {
	use \CacheTrait;

	public function getTheme($route, $theme) {
		$cache_key = 'theme.' . $route . '.' . $theme;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "theme WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND theme = '" . $this->db->escape($theme) . "' AND route = '" . $this->db->escape($route) . "'");
		$result = $query->row;
    $this->setCache($cache_key, $result);

		return $result;
	}
}
