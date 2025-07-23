<?php
class ControllerExtensionModuleRelatedCategories extends Controller { 	
  private $error = array();

	public function getRelatedCategoriesForm($query) {
		$this->load->model('extension/module/related_categories');
		$this->load->model('catalog/category');

		$data['user_token'] = $this->session->data['user_token'];
    
    $related_categories = array();
    $categories = $this->model_extension_module_related_categories->getRelatedCategoriesRows($query);

    foreach ($categories as $row) {
      $pages = json_decode($row['pages'], true) ?: array();
      
      $pages_data = array();
      if ($pages) {
        $this->load->model('extension/module/ocfilter/page');
        $pages_data = $this->model_extension_module_ocfilter_page->getPages(array(
          'selected' => $pages,
          'filter_status' => 1
        ));
      }
        
      $related_categories[] = array(
        'name' => $row['name'],
        'category_id' => $row['category_id'],
        'sort_order' => $row['sort_order'],
        'pages' => $pages,
        'pages_list' => $pages_data
      );
    }

    return $related_categories;
  }
}
