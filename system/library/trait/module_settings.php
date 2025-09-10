<?php
trait TraitModuleSettings {
    
    // Сохранить настройки
    protected function saveSettings($code, $settings) {
        $settings_json = json_encode($settings);
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "module_settings 
                         SET `code` = '" . $this->db->escape($code) . "', 
                             `setting` = '" . $this->db->escape($settings_json) . "'
                         ON DUPLICATE KEY UPDATE 
                             `setting` = '" . $this->db->escape($settings_json) . "'");
    }
    
    // Получить настройки по коду
    protected function getSettings($code) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module_settings 
                                  WHERE `code` = '" . $this->db->escape($code) . "'");
        
        if ($query->num_rows) {
            return json_decode($query->row['setting'], true);
        }
        
        return array();
    }
    
    protected function getSettingsByPrefix($prefix) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module_settings 
                                  WHERE `code` LIKE '" . $this->db->escape($prefix) . "%'");
        
        $result = array();
        foreach ($query->rows as $row) {
            $result[$row['code']] = json_decode($row['setting'], true);
        }
        
        return $result;
    }
}
