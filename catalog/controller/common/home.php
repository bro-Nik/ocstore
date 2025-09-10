<?php
require_once('catalog/controller/trait/template.php');
require_once(DIR_SYSTEM . 'library/trait/module_settings.php');

class ControllerCommonHome extends Controller {
	use \TemplateTrait, TraitModuleSettings;

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


		$settings = $this->getSettingsByPrefix('home');

		$data['slideshow'] = $this->load->controller('extension/module/slideshow', $settings['home_slideshow']);
		$data['recommendations'] = $this->load->controller('extension/module/slider_home_recommendations', $settings['home_recommendations']);

		// Загрузка рекомендуемых категорий с фильтрами
		$data['related_categories'] = $this->load->controller('extension/module/related_categories/getRelatedCategories', 'homepage');

    // Данные модулей
		$data['slider_tabs1'] = $this->load->controller('extension/module/slider_tabs', $settings['home_sliders_1']);
		$data['slider_tabs2'] = $this->load->controller('extension/module/slider_tabs', $settings['home_sliders_2']);
    $data['aboutstore'] = $this->prepareAboutStore($settings['home_aboutstore'] ?? []);
    $data['brands'] = $this->prepareBrands();

    $data['blog'] = $this->load->controller('extension/module/featured_article', $settings['home_blog']);
		$data['storereview'] = $this->load->controller('revolution/carousel_review', $settings['home_storereview']);
		$main_settings = $settings['home_main'] ?? [];
		$data['h1'] = $main_settings['h1'] ?? '';

    $this->addCommonTemplateData($data);
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

  protected function prepareBrands() {
		$this->load->model('catalog/manufacturer');
    $this->load->model('tool/image');

    $data = [];
    $data['brands'] = [];
    $manufacturers = $this->model_catalog_manufacturer->getManufacturersToBrandSlider();
    
		foreach ($manufacturers as $manufacturer) {
      if ($manufacturer['image']) {
      	$data['brands'][] = [
          	'name'  => $manufacturer['name'],
          	'image' => $this->model_tool_image->resize($manufacturer['image'], 100, 50),
          	'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
      	];
			}
    }

		return $this->load->view('common/home/slider_brands', $data);
  }
}
