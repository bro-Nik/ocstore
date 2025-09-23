<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');
require_once('catalog/controller/trait/category.php');

class ControllerCommonHeader extends Controller {
	use \CacheTrait, \CategoryTrait;

	public function index() {
		$cache_key = 'header_data';
		$cache = $this->getCache($cache_key);

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

    if ($cache === false) {
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


			$this->load->model('tool/image');
			$data['developer_mode'] = $this->config->get('developer_mode');

			$this->load->language('revolution/revolution');
			$data['revmenu'] = $this->load->controller('revolution/revmenu');
			$setting_all_settings = $this->config->get('revtheme_all_settings');
			$setting_header_menu = $this->config->get('revtheme_header_menu');
			
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


			$dop_contact_status = $this->config->get('revtheme_header_dop_contacts_status');
			if ($dop_contact_status){
				$dop_contact = $this->config->get('revtheme_header_dop_contact');
				if (!empty($dop_contact)){
					foreach ($dop_contact as $result) {
						$number = $result['number'][$this->config->get('config_language_id')];
						
						$data['dop_contacts'][] = array(
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
				}
			}

			$header_phone = $this->config->get('revtheme_header_phone');
			$data['header_phone_text'] = htmlspecialchars_decode($header_phone[$this->config->get('config_language_id')]['text'], true);
			$data['header_phone_text2'] = htmlspecialchars_decode($header_phone[$this->config->get('config_language_id')]['text2'], true);
			$data['header_phone_cod'] = $header_phone[$this->config->get('config_language_id')]['cod'];
			$data['header_phone_number'] = $header_phone[$this->config->get('config_language_id')]['number'];
			$data['header_phone_cod2'] = $header_phone[$this->config->get('config_language_id')]['cod2'];
			$data['header_phone_number2'] = $header_phone[$this->config->get('config_language_id')]['number2'];

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
					$data['revtheme_header_links2'][] = array(
						'link'  => $result['link'][$this->config->get('config_language_id')],
						'title' => $result['title'][$this->config->get('config_language_id')],
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
			$data['config_language_id'] = $this->config->get('config_language_id');
			$revtheme_dop_menu = $this->config->get('revtheme_dop_menu');
			if (!empty($revtheme_dop_menu)){
				$data['revtheme_dop_menus'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu), true);
			} else {
				$data['revtheme_dop_menus'] = false;
			}
			$revtheme_dop_menu_2 = $this->config->get('revtheme_dop_menu_2');
			if (!empty($revtheme_dop_menu_2)){
				$data['revtheme_dop_menus_2'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu_2), true);
			} else {
				$data['revtheme_dop_menus_2'] = false;
			}
			$revtheme_dop_menu_3 = $this->config->get('revtheme_dop_menu_3');
			// $data['image_in_ico_dop3'] = $this->config->get('revtheme_dop_menu_3_image_in_ico');
			if (!empty($revtheme_dop_menu_3)){
				$data['revtheme_dop_menus_3'] = json_decode(htmlspecialchars_decode($revtheme_dop_menu_3), true);
			} else {
				$data['revtheme_dop_menus_3'] = false;
			}

			$data['phone_dop_text'] = html_entity_decode($header_phone[$this->config->get('config_language_id')]['doptext']);
			$data['phone_dop_text2'] = html_entity_decode($header_phone[$this->config->get('config_language_id')]['doptext2']);
			
			$data['revblog_status'] = $setting_header_menu['revblog_status'];
			if ($setting_header_menu['revblog_status']){
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

			if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
				$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
			} else {
				$data['logo'] = '';
			}

			$data['home'] = $this->url->link('common/home');
			$data['wishlist'] = $this->url->link('account/wishlist', '', true);
			$data['compare'] = $this->url->link('product/compare');
			$data['contact'] = $this->url->link('information/contact');
			$data['telephone'] = $this->config->get('config_telephone');

			if ($this->config->get('configblog_blog_menu')) {
				$data['blog_menu'] = $this->load->controller('blog/menu');
			} else {
				$data['blog_menu'] = '';
			}
			$data['search'] = $this->load->controller('common/search');
			$data['categories'] = $this->categoriesTree($setting_header_menu);

    	$this->setCache($cache_key, $data);
		} else {
			$data = $cache;
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

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
			$this->document->addStyle('catalog/view/css/main.css');
		}

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

		$data['base'] = $server;
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['robots'] = $this->document->getRobots();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['name'] = $this->config->get('config_name');

		$this->load->language('common/header');
		
		// $data['og_url'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		
		$data['og_image'] = $this->document->getOgImage();
		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;
		if ($this->request->server['REQUEST_URI'] == '/') {
			$data['og_url'] = $this->url->link('common/home');
		} else {
			$data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		}

		return $this->load->view('common/header', $data);
	}
}
