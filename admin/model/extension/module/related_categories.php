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

  public function saveRelatedCategories($arg, $data) {
    $this->db->query("DELETE FROM related_categories WHERE query = '" . $this->db->escape($arg) . "'");
    
    if (isset($data['related_category'])) {
      foreach ($data['related_category'] as $category) {
        if (!empty($category['category_id'])) {
          $pages = isset($category['pages']) ? $this->db->escape(json_encode($category['pages'])) : '[]';
          
          $this->db->query("INSERT INTO related_categories SET
            query = '" . $this->db->escape($arg) . "',
            category_id = '" . (int)$category['category_id'] . "',
            pages = '" . $pages . "',
            sort_order = '" . (isset($category['sort_order']) ? (int)$category['sort_order'] : 0) . "'");
        }
      }
    }
  }
}
