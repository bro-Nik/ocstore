<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');
require_once('catalog/controller/trait/category.php');

class ControllerCommonHeader extends Controller {
	use \CacheTrait, \CategoryTrait;

	public function index() {
		$cache_key = 'header';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
      return $cache;
    }

		// Analytics
		$data['analytics'] = array();
		
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';
		
		if(mb_strpos($route, 'account') === false && mb_strpos($route, 'affiliate') === false) {
			$this->load->model('setting/extension');
			
			$analytics = $this->model_setting_extension->getExtensions('analytics');

			foreach ($analytics as $analytic) {
				if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
					$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
				}
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['developer_mode'] = $this->config->get('developer_mode');

		# Revolution start
		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$class = '-' . $this->request->get['information_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}

		$this->load->model('tool/image');
		if ($this->config->get('revtheme_geo_set')['status']) {
			require_once(DIR_SYSTEM . 'library/revolution/SxGeo.php');
			$SxGeo = new SxGeo();
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$ip_city = $SxGeo->getCity($ip)['city']['name_ru'];
			$ip_region = $SxGeo->getCityFull($ip)['region']['name_ru'];
			$rev_geo_data = $this->config->get('revtheme_geo');
			$data['rev_geos'] = array();
			if (!empty($rev_geo_data)){
				foreach ($rev_geo_data as $rev_geo) {
					if ($ip_city == $rev_geo['city'] || $ip_region == $rev_geo['city']) {
						$data['rev_geos'][] = array(
							'code' => $rev_geo['code'],
							'text' => $rev_geo['text'][$this->config->get('config_language_id')]
						);
					}
				}
			}
		}
		$data['config_customer_price'] = $this->config->get('config_customer_price');
		$data['stikers_settings'] = $this->config->get('revtheme_catalog_stiker');
		$data['setting_header_search'] = $setting_header_search = $this->config->get('revtheme_header_search');
		$setting_all_settings = $this->config->get('revtheme_all_settings');
		$data['revtheme_cat_attributes'] = $this->config->get('revtheme_cat_attributes');
		$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
		$data['header_search_doptext'] = '';
		if (!$data['developer_mode']) {
			// Читаем manifest.json, если он существует
			$manifest = [];
			if (file_exists('catalog/view/manifest.json')) {
    		$manifest = json_decode(file_get_contents('catalog/view/manifest.json'), true);
			}

			// Подключаем файлы из manifest.json (минифицированные версии)
    	if (!empty($manifest['main.css'])) {
				$this->document->addStyle($manifest['main.css']);
    	}

		} else {
			$this->document->addStyle('catalog/view/css/styles.css');
		}

		if ($setting_header_search['ch_text']) {
			$data['header_search_doptext'] = html_entity_decode($setting_header_search[$this->config->get('config_language_id')]['doptext'], ENT_QUOTES, 'UTF-8');
		}
		$setting_header_menu = $this->config->get('revtheme_header_menu');
		$data['cats_status'] = $setting_header_menu['cats_status'];
		$data['image_in_ico'] = !empty($setting_header_menu['image_in_ico']);
		$data['tri_level'] = !empty($setting_header_menu['tri_level']);
		$data['image_in_ico_m'] = !empty($setting_header_menu['image_in_ico_m']);

		$this->load->language('revolution/revolution');
		$data['text_mobile_catalog'] = $this->language->get('text_mobile_catalog');
		$data['text_header_back'] = $this->language->get('text_header_back');
		$data['text_header_information'] = $this->language->get('text_header_information');
		// $data['text_header_revpopup_phone'] = $this->language->get('text_header_revpopup_phone');
		$data['text_header_menu2_heading'] = $this->language->get('text_header_menu2_heading');
		$data['text_rev_text_order'] = $this->language->get('text_rev_text_order');
		$data['text_rev_text_download'] = $this->language->get('text_rev_text_download');
		$data['revmenu'] = $this->load->controller('revolution/revmenu');
		$settings_popupphone = $this->config->get('revtheme_header_popupphone');
		$data['revtheme_header_popupphone'] = $settings_popupphone;
		$settings_header_standart_links = $this->config->get('revtheme_header_standart_links');
		// $data['popup_login'] = $settings_header_standart_links['popup_login'];
		// $data['rev_lang'] = $settings_header_standart_links['rev_lang'];
		// $data['rev_curr'] = $settings_header_standart_links['rev_curr'];
		// $data['rev_srav'] = $settings_header_standart_links['rev_srav'];
		// $data['rev_wish'] = $settings_header_standart_links['rev_wish'];
		// $data['rev_acc'] = $settings_header_standart_links['rev_acc'];
		// $data['in_top3'] = $settings_header_standart_links['in_top3'];
		
		$style_tm = '';
		$setting_tm = $this->config->get('revtheme_header_menu');
		if ($setting_tm['icontype']) {
			if ($setting_tm['icon'] == 'fa none') {
				$style_tm = ' hidden';
			}
			$image = '<i class="'.$setting_tm['icon'].$style_tm.'"></i>';
		} else {
			if (!$setting_tm['image'] || $setting_tm['image'] == 'no_image.png') {
				$style_tm = ' hidden';
			}
			$image = '<span class="heading_ico_image'.$style_tm.'"><img src="'.$this->model_tool_image->resize($setting_tm['image'], 21, 21).'" alt=""/></span>';
		}
		$data['text_catalog_menu'] = ($image . $this->language->get('text_header_menu2_heading'));
		
		$data['microdata_status'] = $setting_all_settings['microdata_status'];
		$data['microdata_postcode'] = $setting_all_settings['microdata_postcode'];
		$data['microdata_city'] = $setting_all_settings['microdata_city'];
		$data['microdata_adress'] = $setting_all_settings['microdata_adress'];
		$data['microdata_email'] = $setting_all_settings['microdata_email'];
		if ($setting_all_settings['microdata_phones']) {
			$microdata_phones = explode(",", $setting_all_settings['microdata_phones']);
			$microdata_phones = array_map('trim',$microdata_phones);
			$data['microdata_phones'] = array_diff($microdata_phones, array(''));
		} else {
			$data['microdata_phones'] = false;
		}		
		if ($setting_all_settings['microdata_social']) {
			$microdata_social = explode(",", $setting_all_settings['microdata_social']);
			$microdata_social = array_map('trim',$microdata_social);
			$data['microdata_social'] = array_diff($microdata_social, array(''));
		} else {
			$data['microdata_social'] = false;
		}
		$data['setting_catalog_all'] = $this->config->get('revtheme_catalog_all');

		$dop_contact_status = $this->config->get('revtheme_header_dop_contacts_status');
		if ($dop_contact_status){
			$dop_contact = $this->config->get('revtheme_header_dop_contact');
			if (!empty($dop_contact)){
				foreach ($dop_contact as $result) {
					$style = '';
					if ($result['icontype']) {
						if ($result['icon'] == 'fa none') {
							$style = ' hidden';
						}
						$result_icon = '<i class="'.$result['icon'].$style.'"></i>';
					} else {
						if (!$result['image'] || $result['image'] == 'no_image.png') {
							$style = ' hidden';
						}
						$result_icon = '<span class="mask"></span><img class="'.$style.'" src="'.$this->model_tool_image->resize($result['image'], 21, 21).'" alt=""/>';
					}
					$number = $result['number'][$this->config->get('config_language_id')];
					if ($this->config->get('revtheme_geo_set')['status']) {
						foreach ($data['rev_geos'] as $rev_geo) {
							if ($number == $rev_geo['code']) {
								$number = $rev_geo['text'];
							}
						}
					}
					
					$data['dop_contacts'][] = array(
						'icon' 		=> $result_icon,
						'number' 	=> $number,
						'href' 		=> $result['href'][$this->config->get('config_language_id')],
						'sort'  	=> $result['sort']
					);
				}
				
				foreach ($data['dop_contacts'] as $key => $value) {
					$sort_dop_contacts[$key] = $value['sort'];
				}
				if (count($data['dop_contacts']) > 1) {
					array_multisort($sort_dop_contacts, SORT_ASC, $data['dop_contacts']);
				}

			} else {
				$data['dop_contacts'] = false;
			}
		} else {
			$data['dop_contacts'] = false;
		}
		$header_phone = $this->config->get('revtheme_header_phone');
		$data['header_phone_text'] = htmlspecialchars_decode($header_phone[$this->config->get('config_language_id')]['text'], true);
		if ($this->config->get('revtheme_geo_set')['status']) {
			foreach ($data['rev_geos'] as $rev_geo) {
				if ($data['header_phone_text'] == $rev_geo['code']) {
					$data['header_phone_text'] = $rev_geo['text'];
				}
			}
		}
		$data['header_phone_text2'] = htmlspecialchars_decode($header_phone[$this->config->get('config_language_id')]['text2'], true);
		if ($this->config->get('revtheme_geo_set')['status']) {
			foreach ($data['rev_geos'] as $rev_geo) {
				if ($data['header_phone_text2'] == $rev_geo['code']) {
					$data['header_phone_text2'] = $rev_geo['text'];
				}
			}
		}
		$data['header_phone_cod'] = $header_phone[$this->config->get('config_language_id')]['cod'];
		$data['header_phone_number'] = $header_phone[$this->config->get('config_language_id')]['number'];
		if ($this->config->get('revtheme_geo_set')['status']) {
			foreach ($data['rev_geos'] as $rev_geo) {
				if ($data['header_phone_number'] == $rev_geo['code']) {
					$data['header_phone_number'] = $rev_geo['text'];
				}
			}
		}
		$data['header_phone_cod2'] = $header_phone[$this->config->get('config_language_id')]['cod2'];
		$data['header_phone_number2'] = $header_phone[$this->config->get('config_language_id')]['number2'];
		if ($this->config->get('revtheme_geo_set')['status']) {
			foreach ($data['rev_geos'] as $rev_geo) {
				if ($data['header_phone_number2'] == $rev_geo['code']) {
					$data['header_phone_number2'] = $rev_geo['text'];
				}
			}
		}
		if ($header_phone['icontype']) {
			if ($header_phone['icon'] == 'fa none') {
				$data['header_phone_image'] = '';
			}
			$data['header_phone_image'] = '<i class="'.$header_phone['icon'].'"></i>';
		} else {
			if (!$header_phone['image'] || $header_phone['image'] == 'no_image.png') {
				$data['header_phone_image'] = '';
			}
			$data['header_phone_image'] = '<img src="'.$this->model_tool_image->resize($header_phone['image'], 32, 32).'" alt=""/>';
		}
		
		$this->load->model('catalog/information');

		$data['informations'] = array();
		$data['informations2'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['top']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
			if ($result['top2']) {
				$data['informations2'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$results_h_links = $this->config->get('revtheme_header_link');
		if (!empty($results_h_links)){
			foreach ($results_h_links as $result) {
				$data['revtheme_header_links'][] = array(
					'link'  => $result['link'][$this->config->get('config_language_id')],
					'title' => $result['title'][$this->config->get('config_language_id')],
					'sort'  => $result['sort']
				);
			}
		} else {
			$data['revtheme_header_links'] = false;
		}
		if (!empty($data['revtheme_header_links'])){
			foreach ($data['revtheme_header_links'] as $key => $value) {
				$sort[$key] = $value['sort'];
			}
			if (count($data['revtheme_header_links']) > 1) {
			array_multisort($sort, SORT_ASC, $data['revtheme_header_links']);
			}
		}
		
		$results_h_links2 = $this->config->get('revtheme_header_link2');
		if (!empty($results_h_links2)){
			foreach ($results_h_links2 as $result) {
				$style = '';
				if ($result['icontype']) {
					if ($result['icon'] == 'fa none') {
						$style = ' hidden';
					}
					$image = '<i class="hidden-md '.$result['icon'].$style.'"></i>';
				} else {
					if (!$result['image'] || $result['image'] == 'no_image.png') {
						$style = ' hidden';
					}
					// $image = '<span class="hidden-md '.$style.'"><img src="'.$this->model_tool_image->resize($result['image'], 21, 21).'" alt=""/></span>';
					$image = '<img src="'.$this->model_tool_image->resize($result['image'], 21, 21).'" alt=""/>';
				}
				$result_title = ($image . $result['title'][$this->config->get('config_language_id')]);
				
				$data['revtheme_header_links2'][] = array(
					'link'  => $result['link'][$this->config->get('config_language_id')],
					'title' => $result_title,
					'sort'  => $result['sort']
				);
			}
		} else {
			$data['revtheme_header_links2'] = false;
		}
		if (!empty($data['revtheme_header_links2'])){
			foreach ($data['revtheme_header_links2'] as $key => $value) {
				$sort2[$key] = $value['sort'];
			}
			if (count($data['revtheme_header_links2']) > 1) {
			array_multisort($sort2, SORT_ASC, $data['revtheme_header_links2']);
			}
		}

		$config_image_category_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
		$config_image_category_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
		$data['text_revmenu_manufs'] = $this->language->get('text_revmenu_manufs');
		$data['url_revmenu_manufs'] = $this->url->link('product/manufacturer');
		$results_amazon_links = $this->config->get('revtheme_header_menu_link');
		if ($setting_header_menu['type'] && !empty($results_amazon_links)){
			foreach ($results_amazon_links as $result) {
				$data['revtheme_header_menu_links'][] = array(
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
		if ($setting_all_settings['opacity_cont']) {
			$data['opacity_cont_class'] = 'opacity_minus';
		} else {
			$data['opacity_cont_class'] = 'opacity_minus_products';
		}
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
		$revtheme_dop_menu_2 = $this->config->get('revtheme_dop_menu_2');
		if (!empty($revtheme_dop_menu_2)){
			$data['revtheme_dop_menus_2'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu_2), true);
			$data['config_language_id'] = $this->config->get('config_language_id');
		} else {
			$data['revtheme_dop_menus_2'] = false;
		}
		$revtheme_dop_menu_3 = $this->config->get('revtheme_dop_menu_3');
		$data['image_in_ico_dop3'] = $this->config->get('revtheme_dop_menu_3_image_in_ico');
		if (!empty($revtheme_dop_menu_3)){
			$data['revtheme_dop_menus_3'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu_3), true);
			$data['config_language_id'] = $this->config->get('config_language_id');
			foreach($data['revtheme_dop_menus_3'] as $key => $val){
				if (!empty($data['revtheme_dop_menus_3'][$key]['dop_menu_image_3'])){
					$data['revtheme_dop_menus_3'][$key]['dop_menu_image_3'] = $this->model_tool_image->resize($data['revtheme_dop_menus_3'][$key]['dop_menu_image_3'], $config_image_category_width, $config_image_category_height);
				} else {
					$data['revtheme_dop_menus_3'][$key]['dop_menu_image_3'] = false;
				}
				if (isset($data['revtheme_dop_menus_3'][$key]['children'])){
					$children = $data['revtheme_dop_menus_3'][$key]['children'];
					foreach($children as $key2 => $val2){
						if (!empty($data['revtheme_dop_menus_3'][$key]['children'][$key2]['dop_menu_image_3'])){
							$data['revtheme_dop_menus_3'][$key]['children'][$key2]['dop_menu_image_3'] = $this->model_tool_image->resize($data['revtheme_dop_menus_3'][$key]['children'][$key2]['dop_menu_image_3'], $config_image_category_width, $config_image_category_height);
						} else {
							$data['revtheme_dop_menus_3'][$key]['children'][$key2]['dop_menu_image_3'] = false;
						}
					}
				}
				if (!empty($data['revtheme_dop_menus_3'][$key]['column'])){
					$data['revtheme_dop_menus_3'][$key]['column'] = $data['revtheme_dop_menus_3'][$key]['column'];
				} else {
					$data['revtheme_dop_menus_3'][$key]['column'] = 1;
				}
			}
		} else {
			$data['revtheme_dop_menus_3'] = false;
		}
		$data['phone_dop_text'] = html_entity_decode($header_phone[$this->config->get('config_language_id')]['doptext']);
		if ($this->config->get('revtheme_geo_set')['status']) {
			foreach ($data['rev_geos'] as $rev_geo) {
				if (strpos($data['phone_dop_text'], $rev_geo['code'])) {
					$data['phone_dop_text'] = html_entity_decode(str_replace($rev_geo['code'], $rev_geo['text'], $header_phone[$this->config->get('config_language_id')]['doptext2']));
				}
			}
		}
		$data['phone_dop_text2'] = html_entity_decode($header_phone[$this->config->get('config_language_id')]['doptext2']);
		if ($this->config->get('revtheme_geo_set')['status']) {
			foreach ($data['rev_geos'] as $rev_geo) {
				if (strpos($data['phone_dop_text2'], $rev_geo['code'])) {
					$data['phone_dop_text2'] = html_entity_decode(str_replace($rev_geo['code'], $rev_geo['text'], $header_phone[$this->config->get('config_language_id')]['doptext2']));
				}
			}
		}
		$data['revtheme_header_cart'] = $revtheme_header_cart = $this->config->get('revtheme_header_cart');
		if ($revtheme_header_cart['cart_size'] == 'small') {
			$data['cart_size_class_1'] = 'col-md-7';
			$data['cart_size_class_2'] = 'col-xs-6 col-md-2';
			$data['cart_size_class_3'] = 'col-md-10';
		} else if ($revtheme_header_cart['cart_size'] == 'mini') {
			$data['cart_size_class_1'] = 'col-md-8';
			$data['cart_size_class_2'] = 'col-xs-6 col-md-1';
			$data['cart_size_class_3'] = 'col-md-11';
		} else {
			$data['cart_size_class_1'] = 'col-md-6';
			$data['cart_size_class_2'] = 'col-xs-6 col-md-3';
			$data['cart_size_class_3'] = 'col-md-9';
		}
		if ($revtheme_header_cart['type'] == 'floating' || $revtheme_header_cart['cart_position']) {
			$data['cart_size_class_1'] = 'col-md-9';
			if (!$revtheme_header_cart['cart_position']) {
				$data['cart_size_class_2'] = 'col-xs-6 floating_hcart';
			} else {
				$data['cart_size_class_2'] = 'col-xs-6 hidden-md hidden-lg';
			}
			$data['cart_size_class_3'] = 'col-md-12';
		}
		
		$data['revblog_status'] = $setting_header_menu['revblog_status'];
		if ($setting_header_menu['revblog_status']){
			if (isset($setting_header_menu['image_in_ico_revblog']) && $setting_header_menu['image_in_ico_revblog']) {
				$data['image_in_ico_revblog'] = true;
			} else {
				$data['image_in_ico_revblog'] = false;
			}
			if (isset($setting_header_menu['revblog_in_amazon']) && $setting_header_menu['revblog_in_amazon']) {
				$data['revblog_in_amazon'] = true;
			} else {
				$data['revblog_in_amazon'] = false;
			}
			$data['text_revmenu_manufs'] = $this->language->get('text_revmenu_manufs');
			$data['url_revmenu_manufs'] = $this->url->link('product/manufacturer');
			$data['revblog_column'] = $setting_header_menu['revblog_column'];
			$this->load->model('revolution/revolution');
			$data['blog_categories'] = array();
			$blog_categories = $this->model_revolution_revolution->getBlogCategories();
			foreach ($blog_categories as $category) {
				$children_data = array();
				$children = $this->model_revolution_revolution->getBlogCategories($category['category_id']);
				foreach($children as $child) {
					if ($child['image']) {
						$thumb = $this->model_tool_image->resize($child['image'], $config_image_category_width, $config_image_category_height);
					} else {
						$thumb = $this->model_tool_image->resize('placeholder.png', $config_image_category_width, $config_image_category_height);
					}
					$children_data_level2 = array();
					if (!$setting_header_menu['image_in_ico'] || $setting_header_menu['tri_level']) {
						$children_level2 = $this->model_revolution_revolution->getBlogCategories($child['category_id']);
						foreach ($children_level2 as $child_level2) {
							$children_data_level2[] = array(
								'name'  =>  $child_level2['title'],
								'category_id' => $child_level2['category_id'],
								'href'  => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $child_level2['category_id'])
							);
						}
					}
					$children_data[] = array(
						'category_id' => $child['category_id'],
						'children'    => $children_data_level2,
						'name'  => $child['title'],
						'thumb' => $thumb,
						'href'  => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}
				$data['blog_categories'][] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['title'],
					'href'        => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id']),
					'children'    => $children_data
				);
			}
		}
		
		$revtheme_dop_menu_mob_menu = $this->config->get('revtheme_dop_menu_mob_menu');
		if (!empty($revtheme_dop_menu_mob_menu)){
			$data['revtheme_dop_menus_mob_menu'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu_mob_menu), true);
			foreach($data['revtheme_dop_menus_mob_menu'] as $key => $val){
				if (isset($data['revtheme_dop_menus_mob_menu'][$key]['children'])){
					$children = $data['revtheme_dop_menus_mob_menu'][$key]['children'];
				}
			}
		} else {
			$data['revtheme_dop_menus_mob_menu'] = false;
		}
		// $data['mob_menu_description'] = html_entity_decode($this->config->get('revtheme_all_settings')[$this->config->get('config_language_id')]['mob_menu_description'], ENT_QUOTES, 'UTF-8');
		// if ($this->config->get('revtheme_all_settings')['mobile_header'] == '3') {
		// 	$data['text_catalog_menu'] = ($image . $this->config->get('revtheme_all_settings')[$this->config->get('config_language_id')]['mob_menu_zag']);
		// }
		
		if ($settings_header_standart_links['rev_acc_zagolovok'] == 'name' && $this->customer->isLogged()) {
			$data['text_revlogged'] = sprintf('%s', $this->customer->getFirstName());
		} else if ($settings_header_standart_links['rev_acc_zagolovok'] == 'email' && $this->customer->isLogged()) {
			$data['text_revlogged'] = sprintf('%s', $this->customer->getEmail());
		} else {
			$this->load->language('common/header');
			$data['text_revlogged'] = $this->language->get('text_account');
		}
		# Revolution end

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['robots'] = $this->document->getRobots();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');
		
		$data['og_url'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		$data['og_image'] = false;
		
		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;
		if ($this->request->server['REQUEST_URI'] == '/') {
			$data['og_url'] = $this->url->link('common/home');
		} else {
			$data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		}
		
		$data['og_image'] = $this->document->getOgImage();
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['compare'] = $this->url->link('product/compare');
		$data['cart'] = $this->url->link('checkout/cart');
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		if ($this->config->get('configblog_blog_menu')) {
			$data['blog_menu'] = $this->load->controller('blog/menu');
		} else {
			$data['blog_menu'] = '';
		}
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		// $data['menu'] = $this->load->controller('common/menu');

		$data['categories'] = $this->categoriesTree($setting_header_menu);

		$output = $this->load->view('common/header', $data);
    $this->setCache($cache_key, $output);

		return $output;
	}
}
