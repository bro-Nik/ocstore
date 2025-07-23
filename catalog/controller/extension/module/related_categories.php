<?php
class ControllerExtensionModuleRelatedCategories extends Controller { 	
  private $error = array();

	public function getRelatedCategories($query) {

    $data['related_categories'] = array();
    // $this->load->model('catalog/category');
    $this->load->model('extension/module/ocfilter');
    $this->load->model('extension/module/related_categories');

    $categories = $this->model_extension_module_related_categories->getRelatedCategoriesRows($query);
    
    foreach ($categories as $recommend) {
        $category_info = $this->model_catalog_category->getCategory($recommend['category_id']);
        
        if ($category_info) {
            // Получаем страницы фильтров
            $pages = json_decode($recommend['pages'], true) ?: array();
            $filter_links = array();
            
            foreach ($pages as $page_id) {
                $page_info = $this->model_extension_module_ocfilter->getPage($page_id);
                if ($page_info) {
                  $filter_links[] = array(
                    'name' => $page_info['name'],
                    'href' => $this->url->link('product/category', 'path=' . $category_info['category_id'] . '&filter_ocfilter=' . $page_info['keyword'])
                  );
                }
            }
            
            if ($filter_links) {
                // Загрузка изображения категории
                if ($category_info['image']) {
                    $image = $this->model_tool_image->resize($category_info['image'], 300, 300);
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', 300, 300);
                }
                
                $data['related_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => $category_info['name'],
                    'image' => $image,
                    'href' => $this->url->link('product/category', 'path=' . $category_info['category_id']),
                    'filters' => $filter_links
                );
            }
        }
    }




    return $this->load->view('extension/module/related_categories', $data);
  }
}
