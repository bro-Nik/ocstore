<?php

require_once('catalog/controller/base/product_cart.php');

class ControllerExtensionModuleFeaturedProduct extends ControllerBaseProductCart {
	public function index($setting=[]) {
    $setting['name'] = $setting['name'] ?? 'Рекомендуемые товары';
    $setting['limit'] = $setting['limit'] ?? 20;
    $setting['status'] = $setting['status'] ?? 1;

		$results = array();
		
		$this->load->model('catalog/cms');
		
		if (isset($this->request->get['manufacturer_id'])) {
			$filter_data = array(
				'manufacturer_id'  => $this->request->get['manufacturer_id'],
				'limit' => $setting['limit']
			);
					
			$results = $this->model_catalog_cms->getProductRelatedByManufacturer($filter_data);
				
		} else {
			$path = (string)($this->request->get['path'] ?? '');
			$parts = $path ? explode('_', $path) : [];
					
			if(!empty($parts) && is_array($parts)) {
				$filter_data = array(
					'category_id'  => array_pop($parts),
					'limit' => $setting['limit']
				);
						
				$results = $this->model_catalog_cms->getProductRelatedByCategory($filter_data);			
			}
		}

    $data = $this->prepareProductsData($results, $setting);
		if ($data) {
			$data['id'] = 'slider_related_products';
			$data['title'] = $setting['name'];
			
			return $this->load->view('product/carousel_product', $data);
		}
	}
}
