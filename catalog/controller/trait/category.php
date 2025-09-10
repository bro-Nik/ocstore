<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

trait CategoryTrait {
	use \CacheTrait;

  public function categoriesTree($setting_header_menu) {
		$cache_key = 'categoriesTree';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$config_image_category_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
		$config_image_category_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
		$setting_header_menu = $this->config->get('revtheme_header_menu');
		
		$result = array();
		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();
				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$children_data_level2 = array();
					if (!$setting_header_menu['image_in_ico'] || $setting_header_menu['tri_level']) {
						$children_level2 = $this->model_catalog_category->getCategories($child['category_id']);
						foreach ($children_level2 as $child_level2) {
							$data_level2 = array(
								'filter_category_id'  => $child_level2['category_id'],
								'filter_sub_category' => true
							);
							
							$filter_data_2 = array(
								'filter_category_id'  => $child_level2['category_id'],
								'filter_sub_category' => true
							);

							$children_data_level2[] = array(
								'name'  =>  $child_level2['name'] . ($this->config->get('config_product_count') ? ' <sup>' . $this->model_catalog_product->getTotalProducts($filter_data_2) . '</sup>' : ''),
								'category_id' => $child_level2['category_id'],
								'href'  => $this->url->link('product/category', 'path=' . $child['category_id'] . '_' . $child_level2['category_id']),
								
							);
						}
					}
					
					$filter_data_1 = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$child_info = $this->model_catalog_category->getCategory($child['category_id']);
					if ($child_info) {
						if ($child_info['image']) {
							$thumb = $this->model_tool_image->resize($child_info['image'], $config_image_category_width, $config_image_category_height);
						} else {
							$thumb = $this->model_tool_image->resize('no_image.png', $config_image_category_width, $config_image_category_height);
						}
						if ($setting_header_menu['image_in_ico']) {
							$style = ' hidden';
						} else {
							$style = '';
						}
						if ($child_info['category_icontype']) {
							if ($child_info['category_icon'] == 'fa none') {
								$style = ' hidden';
							}
							$category_image = '<i class="am_category_icon '.$child_info['category_icon'].$style.'"></i>';
						} else {
							if (!$child_info['category_image'] || $child_info['category_image'] == 'no_image.png') {
								$style = 'hidden';
							}
							$category_image = '<span class="'.$style.'"><img src="'.$this->model_tool_image->resize($child_info['category_image'], 21, 21).'" alt=""/></span>';
						}
					}
					
					$children_data[] = array(
						'name'        	 => $child['name'] . ($this->config->get('config_product_count') ? ' <sup>' . $this->model_catalog_product->getTotalProducts($filter_data_1) . '</sup>' : ''),
						'thumb' 		 => $thumb,
						'category_image' => $category_image,
						'category_id' 	 => $child['category_id'],
						'children'   	 => $children_data_level2,
						'href'        	 => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}
				
				$category_info = $this->model_catalog_category->getCategory($category['category_id']);
				if ($category_info) {
					if ($category_info['image2']) {
						$thumb2 = $this->model_tool_image->resize($category_info['image2'], 300, 300);
					} else {
						$thumb2 = '';
					}
					$style = '';
					if ($category_info['category_icontype']) {
						if ($category_info['category_icon'] == 'fa none') {
							$style = ' hidden';
						}
						$category_image = '<i class="am_category_icon hidden-md '.$category_info['category_icon'].$style.'"></i>';
					} else {
						if (!$category_info['category_image'] || $category_info['category_image'] == 'no_image.png') {
							$style = ' hidden';
						}
						$category_image = '<span class="hidden-md'.$style.'"><img src="'.$this->model_tool_image->resize($category_info['category_image'], 21, 21).'" alt=""/></span>';
					}
				}
				
				$result[] = array(
					'category_id' 	 => $category['category_id'],
					'name'     		 => $category['name'],
					'thumb2'   		 => $thumb2,
					'category_image' => $category_image,
					'children' 		 => $children_data,
					'column'   		 => $category['column'] ? $category['column'] : 1,
					'href'    		 => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			
			}
		}
    $this->setCache($cache_key, $result, 108000);
    return $result;
  }
}
