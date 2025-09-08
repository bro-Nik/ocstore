<?php
require_once('catalog/controller/trait/cookie.php');
require_once('catalog/controller/base/product_cart.php');

class ControllerRevolutionViewedProducts extends ControllerBaseProductCart {
    use \CookieTrait;

	public function index($settings = []) {
		
		$products = $this->getCookie('viewed');

		// Получаем ID текущего товара (если есть)
		$current_product_id = isset($this->request->get['revproduct_id']) ? (int)$this->request->get['revproduct_id'] : 0;

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
		$data['title'] = 'Вы недавно смотрели';

    $this->response->setOutput($this->load->view('product/carousel_product', $data));
	}
}
