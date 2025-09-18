<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');
require_once('catalog/controller/trait/category.php');

class ControllerRevolutionRevmenu extends Controller {
	use \CacheTrait, \CategoryTrait;

	public function index() {
		$cache_key = 'menu';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) return $cache;
		
		$this->load->language('revolution/revolution');
		$setting = $this->config->get('revtheme_header_menu');
		$config_image_category_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
		$config_image_category_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
		
		if (!$setting['type']) {
			return false;
		}
		
		$data['setting_revtheme_header_menu'] = $setting;
		
		$setting_all_settings = $this->config->get('revtheme_all_settings');
		$data['text_revmenu_manufs'] = $this->language->get('text_revmenu_manufs');
		
		$this->load->model('tool/image');
		
		$data['cats_status'] = $setting['cats_status'];
		// if ($setting['image_in_ico']) {
		// 	$data['image_in_ico'] = true;
		// } else {
		// 	$data['image_in_ico'] = false;
		// }
		// if ($setting['tri_level']) {
			$data['tri_level'] = true;
		// } else {
		// 	$data['tri_level'] = false;
		// }
		// if ($setting['image_in_ico_m']) {
		// 	$data['image_in_ico_m'] = true;
		// } else {
		// 	$data['image_in_ico_m'] = false;
		// }
		
		// $style = '';
		// if ($setting['icontype']) {
		// 	if ($setting['icon'] == 'fa none') {
		// 		$style = ' hidden';
		// 	}
		// 	$image = '<i class="'.$setting['icon'].$style.'"></i>';
		// } else {
		// 	if (!$setting['image'] || $setting['image'] == 'no_image.png') {
		// 		$style = ' hidden';
		// 	}
		// 	$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($setting['image'], 21, 21).'" alt=""/></span>';
		// }
		// $data['heading_title'] = ($image . $this->language->get('text_header_menu2_heading'));
		// $data['heading_title'] = $this->language->get('text_header_menu2_heading');
		$data['text_show_all'] = $this->language->get('text_show_all');
		$data['text_hide_all'] = $this->language->get('text_hide_all');
		
		// if ($setting['inhome']) {
		// 	$data['module_class'] = 'inhome';
		// } else {
		// 	$data['module_class'] = false;
		// }
		
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}
		
		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}
		
		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}
		
		if (isset($parts[2])) {
            $data['child2_id'] = $parts[2];
        } else {
            $data['child2_id'] = 0;
        }
			
		$data['categories'] = $this->categoriesTree($setting);
		
		$results_amazon_links = $this->config->get('revtheme_header_menu_link');
		if (!empty($results_amazon_links)){
			foreach ($results_amazon_links as $result) {
				// $style = '';
				// if ($result['icontype']) {
				// 	if ($result['icon'] == 'fa none') {
				// 		$style = ' hidden';
				// 	}
				// 	$image = '<i class="am_category_icon '.$result['icon'].$style.'"></i>';
				// } else {
				// 	if (!$result['image'] || $result['image'] == 'no_image.png') {
				// 		$style = ' hidden';
				// 	}
				// 	$image = '<span class="am_category_image'.$style.'"><img src="'.$this->model_tool_image->resize($result['image'], 21, 21).'" alt=""/><span class="mask"></span></span>';
				// }
				$data['revtheme_header_menu_links'][] = array(
					// 'image' => $image,
					'link'  => $result['link'][$this->config->get('config_language_id')],
					'title' => $result['title'][$this->config->get('config_language_id')],
					'sort'  => $result['sort']
				);
			}
		} else {
			$data['revtheme_header_menu_links'] = false;
		}
		if (!empty($data['revtheme_header_menu_links'])){
			foreach ($data['revtheme_header_menu_links'] as $key => $value) {
				$sort[$key] = $value['sort'];
			}
			if (count($data['revtheme_header_menu_links']) > 1) {
			array_multisort($sort, SORT_ASC, $data['revtheme_header_menu_links']);
			}
		}
		
			$data['url_revmenu_manufs'] = $this->url->link('product/manufacturer');
		// $data['manuf_status'] = $setting['manuf'];
		// if ($setting['manuf']){
		// 	$data['text_revmenu_manufs'] = $this->language->get('text_revmenu_manufs');
		// 	$data['url_revmenu_manufs'] = $this->url->link('product/manufacturer');
		// 	$data['n_column'] = $setting['n_column'];
		// 	$style = '';
		// 	if ($setting['manuf_icontype']) {
		// 		if ($setting['manuf_icon'] == 'fa none') {
		// 			$style = ' hidden';
		// 		}
		// 		$data['manuf_image'] = '<i class="am_category_icon '.$setting['manuf_icon'].$style.'"></i>';
		// 	} else {
		// 		if (!$setting['manuf_image'] || $setting['manuf_image'] == 'no_image.png') {
		// 			$style = ' hidden';
		// 		}
		// 		$data['manuf_image'] = '<span class="am_category_image'.$style.'"><img src="'.$this->model_tool_image->resize($setting['manuf_image'], 21, 21).'" alt=""/><span class="mask"></span></span>';
		// 	}	
		// 	$this->load->model('catalog/manufacturer');
		// 	$data['categories_m'] = array();
		// 	$results = $this->model_catalog_manufacturer->getManufacturers();
		// 	foreach ($results as $result) {
		// 		$name = $result['name'];
		// 		if (is_numeric(utf8_substr($name, 0, 1))) {
		// 			$key = '0 - 9';
		// 		} else {
		// 			$key = utf8_substr(utf8_strtoupper($name), 0, 1);
		// 		}
		// 		if (!isset($data['categories_m'][$key])) {
		// 			$data['categories_m'][$key]['name'] = $key;
		// 		}
		// 		if ($result['image']) {
		// 			$thumb = $this->model_tool_image->resize($result['image'], $config_image_category_width, $config_image_category_height);
		// 		} else {
		// 			$thumb = $this->model_tool_image->resize('placeholder.png', $config_image_category_width, $config_image_category_height);
		// 		}
		// 		$data['categories_m'][$key]['manufacturer'][] = array(
		// 			'thumb' => $thumb,
		// 			'name' => $name,
		// 			'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
		// 		);
		// 	}
		// }
		
		// $data['revblog_status'] = $setting['revblog_status'];
		// if ($setting['revblog_status'] && (isset($setting['revblog_in_amazon']) && $setting['revblog_in_amazon'])){
		// 	if (isset($setting['image_in_ico_revblog']) && $setting['image_in_ico_revblog']) {
		// 		$data['image_in_ico_revblog'] = true;
		// 	} else {
		// 		$data['image_in_ico_revblog'] = false;
		// 	}
		// 	$data['text_revmenu_manufs'] = $this->language->get('text_revmenu_manufs');
		// 	$data['url_revmenu_manufs'] = $this->url->link('product/manufacturer');
		// 	$data['revblog_column'] = $setting['revblog_column'];
		// 	$this->load->model('catalog/manufacturer');
		// 	$this->load->model('revolution/revolution');
		// 	$data['blog_categories'] = array();
		// 	$blog_categories = $this->model_revolution_revolution->getBlogCategories();
		// 	foreach ($blog_categories as $category) {
		// 		$children_data = array();
		// 		$children = $this->model_revolution_revolution->getBlogCategories($category['category_id']);
		// 		foreach($children as $child) {
		// 			if ($child['image']) {
		// 				$thumb = $this->model_tool_image->resize($child['image'], $config_image_category_width, $config_image_category_height);
		// 			} else {
		// 				$thumb = $this->model_tool_image->resize('placeholder.png', $config_image_category_width, $config_image_category_height);
		// 			}
		// 			$children_data_level2 = array();
		// 			if (!$setting['image_in_ico'] || $setting['tri_level']) {
		// 				$children_level2 = $this->model_revolution_revolution->getBlogCategories($child['category_id']);
		// 				foreach ($children_level2 as $child_level2) {
		// 					$children_data_level2[] = array(
		// 						'name'  =>  $child_level2['title'],
		// 						'category_id' => $child_level2['category_id'],
		// 						'href'  => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child_level2['category_id'])
		// 					);
		// 				}
		// 			}
		// 			$children_data[] = array(
		// 				'category_id' => $child['category_id'],
		// 				'children'    => $children_data_level2,
		// 				'name'  => $child['title'],
		// 				'thumb' => $thumb,
		// 				'href'  => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id'] . '_' . $child['category_id'])
		// 			);
		// 		}
		// 		$data['blog_categories'][] = array(
		// 			'category_id' => $category['category_id'],
		// 			'name'        => $category['title'],
		// 			'href'        => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id']),
		// 			'children'    => $children_data
		// 		);
		// 	}
		// } else {
		// 	$data['revblog_status'] = false;
		// }
		
		$revtheme_dop_menu = $this->config->get('revtheme_dop_menu');
		if (!empty($revtheme_dop_menu)){
			$data['revtheme_dop_menus'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu), true);
			$data['config_language_id'] = $this->config->get('config_language_id');
			foreach($data['revtheme_dop_menus'] as $key => $val){
				if (!empty($data['revtheme_dop_menus'][$key]['dop_menu_image'])){
					$data['revtheme_dop_menus'][$key]['dop_menu_image'] = $this->model_tool_image->resize($data['revtheme_dop_menus'][$key]['dop_menu_image'], $config_image_category_width, $config_image_category_height);
				} else {
					$data['revtheme_dop_menus'][$key]['dop_menu_image'] = false;
				}
				if (isset($data['revtheme_dop_menus'][$key]['children'])){
					$children = $data['revtheme_dop_menus'][$key]['children'];
					foreach($children as $key2 => $val2){
						if (!empty($data['revtheme_dop_menus'][$key]['children'][$key2]['dop_menu_image'])){
							$data['revtheme_dop_menus'][$key]['children'][$key2]['dop_menu_image'] = $this->model_tool_image->resize($data['revtheme_dop_menus'][$key]['children'][$key2]['dop_menu_image'], $config_image_category_width, $config_image_category_height);
						} else {
							$data['revtheme_dop_menus'][$key]['children'][$key2]['dop_menu_image'] = false;
						}
					}
				}
				if (!empty($data['revtheme_dop_menus'][$key]['column'])){
					$data['revtheme_dop_menus'][$key]['column'] = $data['revtheme_dop_menus'][$key]['column'];
				} else {
					$data['revtheme_dop_menus'][$key]['column'] = 1;
				}
			}
		} else {
			$data['revtheme_dop_menus'] = false;
		}

		$output = $this->load->view('revolution/revmenu', $data);
    $this->setCache($cache_key, $output);
		
		return $output;
  }
}
