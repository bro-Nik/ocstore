<?php
require_once(DIR_SYSTEM . 'library/trait/cache.php');

class ControllerCommonFooter extends Controller {
	use \CacheTrait;

	public function index() {
    $cache_key = 'footer';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) return $cache;

		$this->load->language('common/footer');
		$this->load->model('catalog/information');

		$data['revtheme_footer_all'] = $setting_footer_all = $this->config->get('revtheme_footer_all');
		$setting_footer_user_set = $this->config->get('revtheme_footer_user_set');
		$settings_all_settings = $this->config->get('revtheme_all_settings');
		$this->load->language('revolution/revolution');
		$data['text_loadmore'] = $this->language->get('text_loadmore');
		$data['description_options'] = $this->config->get('revtheme_cat_attributes');
		$data['setting_catalog_all'] = $this->config->get('revtheme_catalog_all');
		$data['revtheme_header_cart'] = $this->config->get('revtheme_header_cart');
		$data['setting_all_settings'] = $this->config->get('revtheme_all_settings');
		$data['revsubscribe'] = $this->load->controller('revolution/revsubscribe');
		$data['revtheme_filter'] = $this->config->get('revtheme_filter');

		$data['developer_mode'] = $this->config->get('developer_mode');
		if (!$data['developer_mode']) {
			// Читаем manifest.json, если он существует
			$manifest = [];
			if (file_exists('catalog/view/manifest.json')) {
    		$manifest = json_decode(file_get_contents('catalog/view/manifest.json'), true);
			}

			// Подключаем файлы из manifest.json
    	if (!empty($manifest['lazy.css'])) {
				$this->document->addStyle($manifest['lazy.css'], $rel = 'preload', $media = 'screen', $position = 'footer');
    	}
    	if (!empty($manifest['main.js'])) {
        $this->document->addScript($manifest['main.js'], 'footer');
    	}

			// Подключаем дополнительные чанки Webpack (если есть)
    	foreach ($manifest as $key => $path) {
        if (strpos($key, 'js/') === 0) { // Чанки начинаются с 'js/'
          $this->document->addScript($path, 'footer');
        }
    	}
		} else {
      $this->document->addScript('catalog/view/js/main.js', 'footer');
			$this->document->addStyle('catalog/view/css/lazy.css', $rel = 'preload', $media = 'screen', $position = 'footer');
		}

		$data['custom_footer'] = $footer_settings = $this->config->get('revtheme_custom_footer');
		$data['config_language_id'] = $this->config->get('config_language_id');
		$data['menus'] = array();
		$data['menus'][1] = $this->processMenuData('revtheme_dop_menu_cf', $footer_settings, 'cf_1_width', 'cf_1_description');
		$data['menus'][2] = $this->processMenuData('revtheme_dop_menu_cf_2', $footer_settings, 'cf_2_width', 'cf_2_description');
		$data['menus'][3] = $this->processMenuData('revtheme_dop_menu_cf_3', $footer_settings, 'cf_3_width', 'cf_3_description');
		$data['menus'][4] = $this->processMenuData('revtheme_dop_menu_cf_4', $footer_settings, 'cf_4_width', 'cf_4_description');
		$data['menus'][5] = $this->processMenuData('revtheme_dop_menu_cf_5', $footer_settings, 'cf_5_width', 'cf_5_description');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		// $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		if ($setting_footer_all['copy']) {
			$domen = $_SERVER['HTTP_HOST'];
			if(stristr($domen, 'xn--')) {
				require_once('catalog/controller/revolution/idna_convert.class.php');
				$idn = new idna_convert(array('idn_version'=>2008));
				$domen = (stripos($domen, 'xn--')!==false) ? $idn->decode($domen) : $idn->encode($domen);
			} else {					
				$domen = $_SERVER['HTTP_HOST'];
			}
			$data['powered'] = sprintf($this->language->get('text_powered_rev'), $domen, $this->config->get('config_name'), date('Y', time()));
		} else {
			$data['powered'] = $setting_footer_all[$this->config->get('config_language_id')]['copy_text'];
		}

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		
		$output = $this->load->view('common/footer', $data);
    $this->setCache($cache_key, $output);

		return $output;
	}

	private function processMenuData($config_key, $settings, $width_key, $description_key, ) {
    $menu_data = $this->config->get($config_key);
    
    $result = !empty($menu_data) ? json_decode(htmlspecialchars_decode($menu_data), true) : false;
    $width_attr = 'class="hidden-xs hidden-sm col-sm-12" style="width:' . $settings[$width_key] . '%"';
    $description = html_entity_decode($settings[$this->config->get('config_language_id')][$description_key], ENT_QUOTES, 'UTF-8');
    
    return [
      'menu' => $result,
      'width_attr' => $width_attr,
      'description' => $description
    ];
	}
}
