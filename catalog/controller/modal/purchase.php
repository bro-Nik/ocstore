<?php
require_once('catalog/controller/extension/module/validator.php');
require_once('catalog/controller/trait/form_handler.php');

class ControllerModalPurchase extends Controller {
	use \ValidatorTrait, \FormHandlerTrait;

	const FIELDS = ['name', 'phone', 'comment', 'privacy_policy_confirmation'];
  const REQUIRED_FIELDS = ['phone', 'privacy_policy_confirmation'];

	public function index() {
		$this->load->language('revolution/revolution');
		$data = $this->getCommonData();
		
		$this->load->model('catalog/product');
		$product_id = (int)($this->request->get['revproduct_id'] ?? 0);
		$quantity = (int)($this->request->get['quantity'] ?? 0);
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$data['product_id'] = $product_id;

		if ($product_info) {

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['quantity'] = $product_info['quantity'];

			if ($product_info['quantity'] <= 0 && $this->config->get('config_stock_warning')) {
				$data['error'] = $this->language->get('text_zakaz_not') . $product_info['stock_status'] . '.';
			} else {
				$data['error'] = '';
			}

			$data['product_name'] = $product_info['name'];

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
			} else {
				$data['thumb'] = $this->model_tool_image->resize("placeholder.png", 100, 100);
			}

			$data['price'] = round($product_info['price'], 2);
			$data['special_price'] = $product_info['special'] ?? false;
			$data['minimum'] = $product_info['minimum'] ?? 1;

			$data['options'] = array();
			foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if ($option_value['quantity'] > 0 || $this->config->get('revtheme_product_all')['options_null_qw']) {
						
						if ($option_value['price_prefix'] == '=') {
							$price_prefix = '';
						} else {
							$price_prefix = $option_value['price_prefix'];
						}
						
						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'price'                   => round($option_value['price'], 2),
							'price_prefix'            => $price_prefix,
							'option_value_disabled'   => ($option_value['quantity'] > 0) ? false : true
						);
					}
				}
				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required'],
				);
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);
			$this->response->setOutput($this->load->view('modals/purchase', $data));
			
		} else {
			$this->response->redirect($this->url->link(isset($this->config->get('revtheme_all_settings')['revcheckout_status']) && $this->config->get('revtheme_all_settings')['revcheckout_status'] ? 'revolution/revcheckout' : 'checkout/checkout'));
		}
	}

	public function send() {
		return $this->handleFormRequest($this->request->post, self::REQUIRED_FIELDS);
	}

	protected function success(&$json, $data) {
		$this->language->load('revolution/revolution');
		$this->load->model('catalog/product');
		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->load->model('checkout/order');
		$this->load->model('checkout/marketing');

		$settings = $this->config->get('revtheme_catalog_popuporder');

		$product_id = $data['product_id'];
		$product_info = $this->model_catalog_product->getProduct($product_id);

		$order_status_id = (!empty($settings['order_status'])) ? (int)$settings['order_status'] : (int)$this->config->get('config_order_status_id');

		$order_data = array();
		$cart_product_key = (int)$product_id;
		$old_cart_product_id = $this->cart->getProducts();

		if (isset($old_cart_product_id[$cart_product_key])) {
			$this->cart->remove($cart_product_key);
		}
		
		$cart = $this->cart->getProducts();
		$this->cart->clear();
		
		$this->cart->add($product_id, $quantity, $option);

		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$forwarded_ip = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$forwarded_ip = $this->request->server['HTTP_CLIENT_IP'];
		} else {
			$forwarded_ip = '';
		}
		
		$user_agent = isset($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : '';
		$accept_language = isset($this->request->server['HTTP_ACCEPT_LANGUAGE']) ? $this->request->server['HTTP_ACCEPT_LANGUAGE'] : '';

		if (isset($this->request->cookie['tracking'])) {
			$tracking = $this->request->cookie['tracking'];
			$subtotal = $this->cart->getSubTotal();

			// Affiliate
			$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

			if ($affiliate_info) {
				$affiliate_id = $affiliate_info['customer_id'];
				$commission = ($subtotal / 100) * $affiliate_info['commission'];
			} else {
				$affiliate_id = 0;
				$commission = 0;
			}

			// Marketing
			$this->load->model('checkout/marketing');

			$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

			if ($marketing_info) {
				$marketing_id = $marketing_info['marketing_id'];
			} else {
				$marketing_id = 0;
			}
		} else {
			$affiliate_id = 0;
			$commission = 0;
			$marketing_id = 0;
			$tracking = '';
		}

		$order_products = array();

		foreach ($this->cart->getProducts() as $product) {
			$option_data = array();

			foreach ($product['option'] as $option) {
			$option_data[] = array(
				'product_option_id'       => $option['product_option_id'],
				'product_option_value_id' => $option['product_option_value_id'],
				'option_id'               => $option['option_id'],
				'option_value_id'         => $option['option_value_id'],
				'name'                    => $option['name'],
				'value'                   => $option['value'],
				'type'                    => $option['type']
			);
			}

			$order_products[] = array(
			'product_id' => $product['product_id'],
			'name'       => $product['name'],
			'model'      => $product['model'],
			'option'     => $option_data,
			'download'   => $product['download'],
			'quantity'   => $product['quantity'],
			'subtract'   => $product['subtract'],
			'price'      => $product['price'],
			'total'      => $product['total'],
			'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
			'reward'     => $product['reward']
			);
		}

		// Totals
		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;
					
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
		
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);
					
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
		}
			
		$order_data = array(
			'invoice_prefix'          => $this->config->get('config_invoice_prefix'),
			'store_id'                => $store_id = (int)$this->config->get('config_store_id'),
			'store_name'              => $this->config->get('config_name'),
			'store_url'               => $store_id ? $this->config->get('config_url') : HTTP_SERVER,
			'customer_id'             => $this->customer->isLogged() ? $this->customer->getId() : 0,
			'customer_group_id'       => $this->customer->isLogged() ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id'),
			'firstname'               => (isset($this->request->request['firstname'])) ? $this->request->request['firstname'] : $this->language->get('heading_title'),
			'lastname'                => '',
			'email'                   => (isset($this->request->request['email']) && !empty($this->request->request['email'])) ? $this->request->request['email'] : 'localhost@localhost.com',
			'telephone'               => (isset($this->request->request['telephone'])) ? $this->request->request['telephone'] : '',
			'fax'                     => '',
			'shipping_city'           => '',
			'shipping_postcode'       => '',
			'shipping_country'        => '',
			'shipping_country_id'     => '',
			'shipping_zone_id'        => '',
			'shipping_zone'           => '',
			'shipping_address_format' => '',
			'shipping_firstname'      => (isset($this->request->request['firstname'])) ? $this->request->request['firstname'] : $this->language->get('heading_title'),
			'shipping_lastname'       => '',
			'shipping_company'        => '',
			'shipping_address_1'      => '',
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
			'payment_firstname'       => (isset($this->request->request['firstname'])) ? $this->request->request['firstname'] : $this->language->get('heading_title'),
			'payment_lastname'        => '',
			'payment_company'         => '',
			'payment_address_1'       => '',
			'payment_address_2'       => '',
			'payment_company_id'      => '',
			'payment_tax_id'          => '',
			'payment_code'            => 'free_checkout',
			'payment_method'          => '--',
			'forwarded_ip'            => $forwarded_ip,
			'user_agent'              => $user_agent,
			'accept_language'         => $accept_language,
			'vouchers'                => array(),
			'comment'                 => (isset($this->request->post['comment'])) ? $this->request->post['comment'] : '',
			'total'                   => $total,
			'reward'                  => '',
			'affiliate_id'            => $affiliate_id,
			'tracking'                => $tracking,
			'commission'              => $commission,
			'marketing_id'            => $marketing_id,
			'language_id'             => $this->config->get('config_language_id'),
			'currency_id'             => $this->currency->getId($this->session->data['currency']),
			'currency_code'           => $this->session->data['currency'],
			'currency_value'          => $this->currency->getValue($this->session->data['currency']),
			'ip'                      => $this->request->server['REMOTE_ADDR'],
			'products'                => $order_products,
			'totals'                  => $totals
		);

		$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

		$order_id = (int)$this->session->data['order_id'];

		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $order_status_id);

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . $order_id . "'");

		$this->cart->clear();
		if ($cart) {
			foreach ($cart as $value) {
				$this->cart->add($value['product_id'], $value['quantity'], $value['option']);
			}
		}
		
		$json['html'] = $this->language->get('text_success_order');

		return $json;
	}
}
