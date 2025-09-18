<?php
require_once('catalog/controller/trait/cookie.php');
require_once('catalog/controller/base/products_list.php');

class ControllerAccountWishList extends ControllerBaseProductsList {
	use \CookieTrait;

	public function index() {
		$this->load->language('account/wishlist');
		$this->load->model('catalog/product');
		
		$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
		$data['predzakaz_button'] = $this->config->get('revtheme_predzakaz')['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');

		$this->document->setTitle($this->language->get('heading_title'));

		$basicBreadcrumbs = $this->load->controller('extension/module/breadcrumbs/getBasicBreadcrumbs');
		$data['breadcrumbs'] = array_merge($basicBreadcrumbs, [[
    	'text'  => $this->language->get('heading_title'),
    	'href'  => $this->url->link('account/wishlist')
		]]);
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_empty'] = $this->language->get('text_empty');

		$wishlist_data = $this->getCookie('wishlist') ?? [];
		$product_ids = array_map('intval', $wishlist_data);
    $products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);
    $data['products'] = $this->prepareProducts($products, []);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}
}
