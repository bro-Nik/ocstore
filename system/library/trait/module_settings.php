<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

trait TraitModuleSettings {
  use CacheTrait;
    
  // Сохранить настройки
  protected function saveSettings($code, $settings) {
    $settings_json = json_encode($settings);
    
    $this->db->query("INSERT INTO " . DB_PREFIX . "module_settings 
                      SET `code` = '" . $this->db->escape($code) . "', 
                          `setting` = '" . $this->db->escape($settings_json) . "'
                      ON DUPLICATE KEY UPDATE 
                          `setting` = '" . $this->db->escape($settings_json) . "'");

    $this->delCache('module_settings.' . $code);
		$prefixes = explode('_', $code);
    if ($prefixes) {
      $this->delCache('module_settings.by_prefix.' . $prefixes[0]);
    }
  }
    
  // Получить настройки по коду
  protected function getSettings($code) {
		$cache_key = 'module_settings.' . $code;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) return $cache;

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module_settings 
                              WHERE `code` = '" . $this->db->escape($code) . "'");
    
    $result = array();
    if ($query->num_rows) {
      $result = json_decode($query->row['setting'], true);
    }
    $this->setCache($cache_key, $result);
    
    return $result;
  }
    
  protected function getSettingsByPrefix($prefix) {
		$cache_key = 'module_settings.by_prefix.' . $prefix;
		$cache = $this->getCache($cache_key);
    if ($cache !== false) return $cache;

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module_settings 
                              WHERE `code` LIKE '" . $this->db->escape($prefix) . "%'");
    
    $result = array();
    foreach ($query->rows as $row) {
      $result[$row['code']] = json_decode($row['setting'], true);
    }
    $this->setCache($cache_key, $result);
    
    return $result;
  }
}
