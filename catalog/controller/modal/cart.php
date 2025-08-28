<?php
require_once('catalog/controller/trait/cookie.php');
require_once('catalog/controller/trait/product.php');
require_once('catalog/controller/trait/form_handler.php');
require_once('catalog/controller/extension/module/validator.php');

class ControllerModalCart extends Controller {
	use \CookieTrait, \ProductInfo, \FormHandlerTrait, \ValidatorTrait;

	const FIELDS = ['name', 'email', 'phone', 'comment', 'address', 'privacy_policy_confirmation'];
  const REQUIRED_FIELDS = ['phone', 'privacy_policy_confirmation'];

	public function index() {
		$this->load->language('revolution/revolution');
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->load->model('catalog/product');

		$data = $this->getCommonData();
		$data['products'] = array();

		$cart_data = $this->getCookie('cart') ?? [];
		$product_ids = array_map('intval', array_keys($cart_data));
    $products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);

		foreach ($products as $product) {
			$thumb = $product['image'] ?? 'no_image.png';
			
			$data['products'][] = array(
				'minimum'     => $product['minimum'] > 0 ? $product['minimum'] : 1,
				'product_id'  => $product['product_id'],
				'thumb'       => $this->model_tool_image->resize($thumb, 150, 150),
				'name'        => $product['name'],
				'options'     => $this->prepareProductOptions($product['product_id']),
				'quantity'    => $cart_data[$product['product_id']]['quantity'],
				'price'       => $product['price'],
				'href'        => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		$data['checkout_link'] = $this->url->link('revolution/revcheckout');
		$data['heading'] = 'Корзина';

		$this->response->setOutput($this->load->view('modals/cart', $data));
	}

	public function send() {
		
		$json = array();

		$this->language->load('revolution/revolution');
		$this->load->model('catalog/product');
		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->load->model('checkout/order');
		$this->load->model('checkout/marketing');

		$order_status_id = (int)$this->config->get('config_order_status_id');

		$errors = $this->validateForm($this->request->post, self::REQUIRED_FIELDS, 'contact');
		
		if (!$errors) {
			$data['payment'] = '';
			$data['products'] = '';
			
			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				$order_data['store_url'] = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;
			}

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$forwarded_ip = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$forwarded_ip = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$forwarded_ip = '';
			}
		 
			$user_agent = isset($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : '';

			$order_products = array();
			$cart_data = $this->getCookie('cart') ?? [];
			$product_ids = array_map('intval', array_keys($cart_data));
    	$products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);
			foreach ($products as $product) {
			  $option_data = array();

				$all_options = $this->prepareProductOptions($product['product_id']);
				$product_cart = $cart_data[$product['product_id']];
				$product_options = array_map('intval', $product_cart['options'] ?? []);
				$product_quantity = $product_cart['quantity'] ?? 1;
				$product_sum = $product['price'];

			  foreach ($all_options as $option) {
			  	foreach ($option['product_option_value'] as $option_value) {
        		if (in_array($option_value['product_option_value_id'], $product_options)) {
							$product_sum += $option_value['price'];
							$option_data[] = array(
				  			'product_option_id'       => $option['product_option_id'],
				  			'product_option_value_id' => $option_value['product_option_value_id'],
				  			'option_id'               => $option['option_id'],
				  			'option_value_id'         => $option_value['option_value_id'],
				  			'name'                    => $option['name'],
				  			'value'                   => $option_value['name'],
				  			'type'                    => $option['type']
							);
        		}
					}
			  }

			  $order_products[] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'download'   => $product['download'],
				'quantity'   => $product_quantity,
				'subtract'   => $product['subtract'],
				'price'      => $product_sum,
				'total'      => $product_sum * $product_quantity,
				// 'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
				'reward'     => $product['reward']
				);
			}

			// // Totals
			$totals = array();
			// // $taxes = $this->cart->getTaxes();
			// $taxes = array();
			$total = 0;
			// 			
			// $total_data = array(
			// 	'totals' => &$totals,
			// 	'taxes'  => &$taxes,
			// 	'total'  => &$total
			// );
			// 
			// $sort_order = array();
			// $results = $this->model_setting_extension->getExtensions('total');
			//
			// foreach ($results as $key => $value) {
			// 	$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			// }
			//
			// array_multisort($sort_order, SORT_ASC, $results);
			//
			// foreach ($results as $result) {
			// 	if ($this->config->get('total_' . $result['code'] . '_status')) {
			// 		$this->load->model('extension/total/' . $result['code']);
			// 		
			// 		$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			// 	}
			// }
			//
			// $sort_order = array();
			//
			// foreach ($totals as $key => $value) {
			// 	$sort_order[$key] = $value['sort_order'];
			// }
			//
			// array_multisort($sort_order, SORT_ASC, $totals);
			
			$order_data = array(
				'invoice_prefix'          => $this->config->get('config_invoice_prefix'),
				'store_id'                => $store_id = (int)$this->config->get('config_store_id'),
				'store_name'              => $this->config->get('config_name'),
				'store_url'               => $store_id ? $this->config->get('config_url') : HTTP_SERVER,
				'customer_id'             => 0,
				'customer_group_id'       => $this->config->get('config_customer_group_id'),
				'firstname'               => $this->request->request['name'] ?? 'name',
				'lastname'                => '',
				'email'                   => $this->request->request['email'] ?? '-',
				'telephone'               => $this->request->request['phone'] ?? '-',
				'fax'                     => '',
				'shipping_city'           => '',
				'shipping_postcode'       => '',
				'shipping_country'        => '',
				'shipping_country_id'     => '',
				'shipping_zone_id'        => '',
				'shipping_zone'           => '',
				'shipping_address_format' => '',
				'shipping_firstname'      => $this->request->request['name'] ?? '-',
				'shipping_lastname'       => '',
				'shipping_company'        => '',
				'shipping_address_1'      => $this->request->request['address'] ?? '-',
				'shipping_address_2'      => '',
				'shipping_code'           => '',
				'shipping_method'         => '',
				'payment_city'            => '',
				'payment_postcode'        => '',
				'payment_country'         => '',
				'payment_country_id'      => '',
				'payment_zone'            => '',
				'payment_zone_id'         => '',
				'payment_address_format'  => '',
				'payment_firstname'       => $this->request->request['name'] ?? '-',
				'payment_lastname'        => '',
				'payment_company'         => '',
				'payment_address_1'       => '',
				'payment_address_2'       => '',
				'payment_company_id'      => '',
				'payment_tax_id'          => '',
				'payment_code'            => 'free_checkout',
				'payment_method'          => '-',
				'forwarded_ip'            => $forwarded_ip,
				'user_agent'              => $user_agent,
				'accept_language'         => '',
				'vouchers'                => array(),
				'comment'                 => $this->request->post['text'] ?? '-',
				'total'                   => $total,
				'reward'                  => '',
				'affiliate_id'            => 0,
				'tracking'                => '',
				'commission'              => 0,
				'marketing_id'            => 0,
				'language_id'             => $this->config->get('config_language_id'),
				'ip'                      => $this->request->server['REMOTE_ADDR'],
				'products'                => $order_products,
				'totals'                  => $totals
			);

			$order_id = $this->model_checkout_order->addOrder($order_data);
			$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . $order_id . "'");

			// $this->cart->clear();
			
			setcookie('cart', '');
			$json['html'] = $this->language->get('text_success_order');
		}
		
    $this->sendJsonResponse($json);
	}

}
