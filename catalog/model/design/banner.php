<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ModelDesignBanner extends Model {
	use \CacheTrait;

	public function getBanner($banner_id) {
		$cache_key = 'banner.' . $banner_id;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) return $cache;

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner b LEFT JOIN " . DB_PREFIX . "banner_image bi ON (b.banner_id = bi.banner_id) WHERE b.banner_id = '" . (int)$banner_id . "' AND b.status = '1' AND bi.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY bi.sort_order ASC");
		$banners = $query->rows;
    $this->setCache($cache_key, $banners);
		return $banners;
	}
}
