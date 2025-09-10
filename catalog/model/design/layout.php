<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ModelDesignLayout extends Model {
	use \CacheTrait;

	public function getLayout($route) {
		$cache_key = 'layout.' . md5($route);
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($route) . "' LIKE route AND store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY route DESC LIMIT 1");

		if ($query->num_rows) {
			$result = (int)$query->row['layout_id'];
		} else {
			$result = 0;
		}
    $this->setCache($cache_key, $result);
		return $result;
	}
	
	public function getLayoutModules($layout_id, $position) {
		$cache_key = 'layout_modules.' . $layout_id . '.' . $position;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "' AND position = '" . $this->db->escape($position) . "' ORDER BY sort_order");
		$modules = $query->rows;
    $this->setCache($cache_key, $modules);
		
		return $modules;
	}
}
