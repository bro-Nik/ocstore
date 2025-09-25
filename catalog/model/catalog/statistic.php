<?php
class ModelCatalogStatistic extends Model {
  public function pageViewCounter($type, $id) {
    $valid_types = ['product', 'category', 'manufacturer', 'ocfilter_page'];
    
    if (!in_array($type, $valid_types) || $id <= 0) {
      return;
    }
    
    $table = $type;
    $field = $type === 'ocfilter_page' ? 'page' : $type;
    $field = $field . '_id';

    $this->db->query("UPDATE " . DB_PREFIX . $table . "
                      SET viewed = viewed + 1 
                    	WHERE " . $field . " = '" . (int)$id . "'");
  }
}
