<?php
class ControllerRevolutionRevfilter extends Controller {

	public function index() {
		
		$data['settings'] = $setting = $this->config->get('revtheme_filter');
		if (!$setting['status'] || ($this->request->get['route'] != 'product/category')) {
			return false;
		}

		$this->load->language('revolution/revolution');
		$this->document->addStyle('/catalog/view/theme/revolution/stylesheet/revfilter.css');
		$this->document->addScript('/catalog/view/javascript/revolution/ion.rangeslider.min.js');
		$data['title'] = $setting[$this->config->get('config_language_id')]['title'];
		$data['filter_instock_vid'] = $setting['filter_instock_vid'];
		$data['filter_instock'] = $setting['filter_instock'];
		$data['filter_subcategories'] = $setting['filter_subcategories'];
		$data['filter_subcategories_vid'] = $setting['filter_subcategories_vid'];
		$data['filter_gr'] = $setting['filter_gr'];
		$data['filter_gr_vid'] = $setting['filter_gr_vid'];
		$data['text_price'] = preg_replace('/:/', '', $this->language->get('text_price'));
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_find'] = $this->language->get('text_find');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_instock'] = $this->language->get('text_instock');
		$data['text_instock_on'] = $this->language->get('text_instock_on');
		$data['text_subcategories'] = $this->language->get('text_subcategories');
		$data['text_revf_group'] = $this->language->get('text_revf_group');
		$data['text_filter_mobile'] = $this->language->get('text_filter_mobile');
		$data['text_filter_sort'] = $this->language->get('text_filter_sort');
		$data['full_path'] = $this->url->link('product/category', 'path=' . $this->request->get['path']);
		$data['filter_refreshbutton'] = isset($setting['filter_refreshbutton']) ? $setting['filter_refreshbutton'] : false;
		$data['text_refresh'] = $this->language->get('text_refresh');

		$parts = explode('_', (string)$this->request->get['path']);
		$category_id = (int)array_pop($parts);
		if (isset($setting['filter_categories']) && (in_array($category_id, $setting['filter_categories']))) {
			return false;
		}

		$filter_data = array(
			'filter_category_id' => $category_id,
			'init_filter' => true
		);
		$this->load->model('revolution/revolution');
		$this->load->model('catalog/product');
		$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
		if ($product_total < 2) {
			return false;
		}
		list($data['allfilters'], $data['filter_price_min'], $data['filter_price_max']) = $this->model_revolution_revolution->initfilter($filter_data);
		if ($data['filter_gr'] != 'false') {
			list($data['allfilters_gr']) = $this->model_revolution_revolution->initfilter_gr($filter_data);
		}
		$data['filter_price_status'] = $setting['filter_price'];
		$currency = $this->session->data['currency'];
		$data['prefix'] = $this->currency->getSymbolLeft($currency);
		$data['postfix'] = $this->currency->getSymbolRight($currency);
		$setting_sort_default = $this->config->get('revtheme_catalog_sorts_category');
		$config_product_limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		$data['sort'] = isset($this->request->get['sort']) ? $this->request->get['sort'] : $setting_sort_default['sort_default'];
		$data['order'] = isset($this->request->get['order']) ? $this->request->get['order'] : $setting_sort_default['sort_default_adesc'];
		$data['limit'] = isset($this->request->get['limit']) ? $this->request->get['limit'] : $config_product_limit;
		$data['path'] = isset($this->request->get['path']) ? $this->request->get['path'] : false;
		$data['filter_instock_chek'] = isset($this->request->get['filter_instock']) ? $this->request->get['filter_instock'] : false;
		$data['filter_price_min_from'] = isset($this->request->get['filter_price']) && $this->request->get['filter_price'] != '' ? strtok($this->request->get['filter_price'], ";") : $data['filter_price_min'];
		$data['filter_price_max_to'] = isset($this->request->get['filter_price']) && $this->request->get['filter_price'] != '' ? substr($this->request->get['filter_price'],strpos($this->request->get['filter_price'], ';')+1) : $data['filter_price_max'];
		
		$data['filter_data'] = array();
		$filters = array();
		if (isset($data['allfilters']) && is_array($data['allfilters'])) {
			foreach ($data['allfilters'] as $key => $filter) {
				if (isset($filter['values']) && is_array($filter['values'])) {
					$values = array();
					foreach ($filter['values'] as $value) {
						$values[] = $value['id'];
					}
					$filters[$filter['id']] = $values;
				}
			}
		}
		
		if (isset($this->request->get['filter_manufactures'])) {
			if (is_array($this->request->get['filter_manufactures'])) {
				foreach ($data['allfilters']['manufs']['values'] as $key => $value) {
					if (in_array($value['val'], $this->request->get['filter_manufactures'])) {
						$data['allfilters']['manufs']['values'][$key]['chek'] = true;
					}
				}
			} else {
				foreach ($data['allfilters']['manufs']['values'] as $key => $value) {
					if ($value['val'] == $this->request->get['filter_manufactures']) {
						$data['allfilters']['manufs']['values'][$key]['chek'] = true;
					}
				}
			}
		}
		
		if (isset($this->request->get['filter_attributes'])) {
			foreach ($this->request->get['filter_attributes'] as $key => $value) {
				$fakey = 'fa'.$key;
				$fa_array = ($data['allfilters'][$fakey]['values']);
				foreach ($fa_array as $key2 => $value) {
					if (is_array($this->request->get['filter_attributes'][$key])) {
						if (in_array($value['val'], $this->request->get['filter_attributes'][$key])) {
							$data['allfilters'][$fakey]['values'][$key2]['chek'] = true;
						}
					} else {
						if ($value['val'] == $this->request->get['filter_attributes'][$key]) {
							$data['allfilters'][$fakey]['values'][$key2]['chek'] = true;
						}
					}
				}
			}
		}
		
		if (isset($this->request->get['filter_options'])) {
			foreach ($this->request->get['filter_options'] as $key => $value) {
				$fakey = 'fo'.$key;
				$fa_array = ($data['allfilters'][$fakey]['values']);
				foreach ($fa_array as $key2 => $value) {
					if (is_array($this->request->get['filter_options'][$key])) {
						if (in_array($value['val'], $this->request->get['filter_options'][$key])) {
							$data['allfilters'][$fakey]['values'][$key2]['chek'] = true;
						}
					} else {
						if ($value['val'] == $this->request->get['filter_options'][$key]) {
							$data['allfilters'][$fakey]['values'][$key2]['chek'] = true;
						}
					}
				}
			}
		}
		
		$data['filter_data'] = json_encode($filters);
		$data['subcategories'] = $this->model_revolution_revolution->getSubCategoriesByCategoryId($category_id);
		
		if (isset($this->request->get['filter_categories'])) {
			foreach ($data['subcategories'] as $key => $value) {
				if (is_array($this->request->get['filter_categories'])) {
					if (in_array($value['category_id'], $this->request->get['filter_categories'])) {
						$data['subcategories'][$key]['chek'] = 1;
					}
				} else {
					if ($value['category_id'] == $this->request->get['filter_categories']) {
						$data['subcategories'][$key]['chek'] = 1;
					}
				}
			}
		}
		
		return $this->load->view('revolution/revfilter', $data);
	}
}