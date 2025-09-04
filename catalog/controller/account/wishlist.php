<?php
require_once('catalog/controller/trait/cookie.php');

class ControllerAccountWishList extends Controller {
	use \CookieTrait;

	public function index() {
		// $this->load->language('revolution/revolution');
		$this->load->language('account/wishlist');
		$this->load->model('account/wishlist');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');	
		$this->load->model('revolution/revolution');
		
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

		// $currency = $this->session->data['currency'];
		$config_image_wishlist_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width');
		$config_image_wishlist_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height');
		
		$data['text_empty'] = $this->language->get('text_empty');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_sku'] = $this->language->get('column_sku');
		$data['column_stock'] = $this->language->get('column_stock');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_remove'] = $this->language->get('button_remove');

		$data['products'] = array();
		$wishlist_data = $this->getCookie('wishlist') ?? [];
		$product_ids = array_map('intval', $wishlist_data);
    $products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);
		
		foreach ($products as $product_info) {
			
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $config_image_wishlist_width, $config_image_wishlist_height);
				} else {
					$image = false;
				}

				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'sku'        => $product_info['sku'],
					'price'      => $product_info['price'],
					'special'      => $product_info['special'],
					'mpn'        => $product_info['mpn'],
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id']),
					'price_number' => $product_info['price'],
					'quantity'   => $product_info['quantity']
				);
			}
			
		}
		
		$data['continue'] = $this->url->link('account/account', '', 'SSL');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('account/wishlist', $data));
	}
}
