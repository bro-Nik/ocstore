<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ModelSettingEvent extends Model {
	use \CacheTrait;

	function getEvents() {
		$cache_key = 'events.all';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE `trigger` LIKE 'catalog/%' AND status = '1' ORDER BY `sort_order` ASC");
		$result = $query->rows;
    $this->setCache($cache_key, $result);

		return $result;
	}
}
