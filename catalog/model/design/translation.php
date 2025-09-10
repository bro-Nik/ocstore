<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ModelDesignTranslation extends Model {
	use \CacheTrait;

	public function getTranslations($route) {
		$cache_key = 'translation.' . md5($route);
		$cache = $this->getCache($cache_key);
    if ($cache !== false) return $cache;

		$language_code = $this->config->get('config_language');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "translation WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "' AND (route = '" . $this->db->escape($route) . "' OR route = '" . $this->db->escape($language_code) . "')");

		$translations = $query->rows;
    $this->setCache($cache_key, $translations);
		return $translations;
	}
}
