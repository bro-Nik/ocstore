<?php
require_once('catalog/controller/base/product_cart.php');

class ControllerRevolutionViewedProducts extends ControllerBaseProductCart {
	public function index($settings) {
		
		$products = array();

		if (isset($this->request->cookie['viewed'])) {
			$products = explode(',', $this->request->cookie['viewed']);
		} else if (isset($this->session->data['viewed'])) {
			$products = $this->session->data['viewed'];
		}

		// Получаем ID текущего товара (если есть)
		$current_product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

		// Фильтруем массив, сразу убирая текущий товар (если он есть)
		$products = array_filter($products, function($product_id) use ($current_product_id) {
    		return (int)$product_id !== $current_product_id;
		});

		$limit = !empty($settings['limit']) ? (int)$settings['limit'] : 8;
		$products_ids = array_slice($products, 0, $limit);

    
    $this->load->model('catalog/product');
    $products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $products_ids]);
		
    $data = $this->prepareProductsData($products, $settings);
		$data['id'] = 'slider_viewed_products';

		return $this->load->view('product/carousel_product', $data);

	}
}
