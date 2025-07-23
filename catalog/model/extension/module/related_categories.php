<?php

class ModelExtensionModuleRelatedCategories extends Model {

  public function getRelatedCategoriesRows($arg) {
    $sql = "SELECT rc.*, cd.name 
          FROM related_categories rc
          LEFT JOIN " . DB_PREFIX . "category_description cd 
          ON (rc.category_id = cd.category_id)
          WHERE rc.query = '" . $this->db->escape($arg) . "' 
          ORDER BY sort_order ASC";
    
    $query = $this->db->query($sql);
    
    return $query->rows;
  }
}
