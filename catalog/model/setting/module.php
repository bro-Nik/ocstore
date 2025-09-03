<?php
require_once('catalog/controller/trait/cache.php');

class ModelSettingModule extends Model {
	use \CacheTrait;

	public function getModule($module_id) {
		$cache_key = 'module.' . (int)$module_id;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE module_id = '" . (int)$module_id . "'");
		
		if ($query->row) {
			$result = json_decode($query->row['setting'], true);
		} else {
			$result = array();	
		}
    $this->setCache($cache_key, $result);
		return $result;
	}		
}
