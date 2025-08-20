<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$canonical = $this->url->link('common/home');
			if ($this->config->get('config_seo_pro') && !$this->config->get('config_seopro_addslash')) {
				$canonical = rtrim($canonical, '/');
			}
			$this->document->addLink($canonical, 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

    $this->load->model('setting/setting');
    $settings = $this->model_setting_setting->getSetting('home');

		$data['slideshow'] = $this->load->controller('extension/module/slider_home_main');
		$data['recommendations'] = $this->load->controller('extension/module/slider_home_recommendations');

		// Загрузка рекомендуемых категорий с фильтрами
		$data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategories', 'homepage');

		$setting = $this->config->get('home');

    // Данные модулей
		$data['slider_tabs1'] = $this->load->controller('extension/module/slider_tabs', $settings['home_sliders1']);
		$data['slider_tabs2'] = $this->load->controller('extension/module/slider_tabs', $settings['home_sliders2']);
    $data['aboutstore'] = $this->prepareAboutStore($settings['home_aboutstore'] ?? []);

		// Revolution
		// $data['blog'] = $this->load->controller('revolution/revblogmod');
		$data['blog'] = $this->load->controller('blog/slider');
		$data['storereview'] = $this->load->controller('revolution/carousel_review', $settings['home_storereview']);
		// $data['viewed_products'] = $this->load->controller('revolution/viewed_products', $settings['home_viewed_products']);
		$main_settings = $setting['home_main'] ?? [];
		$data['h1'] = $main_settings['h1'] ?? [];

		$this->response->setOutput($this->load->view('common/home', $data));
	}

  protected function prepareAboutStore($aboutstore) {
		if (!$aboutstore['status']) {
			return;
		}

		$data['title'] = $aboutstore['title'];
		
		$data['html'] = html_entity_decode($aboutstore['description'], ENT_QUOTES, 'UTF-8');

		return $this->load->view('common/home/aboutstore', $data);
  }
}
