<?php
require_once('catalog/controller/extension/module/validator.php');
require_once('catalog/controller/trait/form_handler.php');

class ControllerModalCartquick extends Controller {
	use \ValidatorTrait, \FormHandlerTrait;

	const FIELDS = ['name', 'phone', 'comment', 'privacy_policy_confirmation'];
  const REQUIRED_FIELDS = ['phone', 'privacy_policy_confirmation'];

	public function index() {
		
		$this->load->language('revolution/revolution');
		$data = $this->getCommonData();

		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$data['error'] = $this->language->get('error_stock');
		} elseif (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}
		
		if ( isset( $this->request->request['remove'] ) ) {
			$this->cart->remove( $this->request->request['remove'] );
			unset( $this->session->data['vouchers'][$this->request->request['remove']] );
		}

		if ( isset( $this->request->request['update'] ) ) {
			$this->cart->update( $this->request->request['update'], $this->request->request['quantity'] );
		}

		if ( isset( $this->request->request['add'] ) ) {
			$this->cart->add( $this->request->request['add'], $this->request->request['quantity'] );
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->load->model('catalog/product');

		$data['products'] = array();
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			if ($product['minimum'] > $product['quantity']) {
				$data['warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], 80, 80);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 80, 80);
			}
			
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				}

				$option_data[] = array(
					'name' => $option['name'],
					'model' => (isset($option['model']) ? $option['model'] : false),
					'value' => (utf8_strlen($value) > 50 ? utf8_substr($value, 0, 50) . '..' : $value),
				);
			}

			$data['products'][] = array(
				'minimum'     => $product['minimum'] > 0 ? $product['minimum'] : 1,
				'key'         => $product['cart_id'],
				'product_id'  => $product['product_id'],
				'thumb'       => $image,
				'name'        => $product['name'],
				'model'       => $product['model'],
				'option'      => $option_data,
				'quantity'    => $product['quantity'],
				'stock'       => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'reward'      => ($product['reward'] ? sprintf($this->language->get('text_cartpopup_points'), $product['reward']) : ''),
				'price'       => $product['price'],
				'total'       => $product['price'] * $product['quantity'],
				'href'        => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		// Totals
		$this->load->model('setting/extension');

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

		$data['totals'] = array();

		foreach ($totals as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $total['value']
			);
		}

		$data['heading'] = $this->language->get('heading_cartpopupquick_title');
		$this->response->setOutput($this->load->view('modals/cartquick', $data));
	}

	protected function doAfterChecking() {
	}

	
	public function send() {
		
		$json = array();

		$this->language->load('revolution/revolution');
		$this->load->model('catalog/product');
		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->load->model('checkout/order');
		$this->load->model('checkout/marketing');

		$settings = $this->config->get('revtheme_catalog_popuporder');
		$customer_info = ($this->customer->isLogged()) ? $this->model_account_customer->getCustomer($this->customer->getId()) : FALSE;
		$order_status_id = (!empty($settings['order_status'])) ? (int)$settings['order_status'] : (int)$this->config->get('config_order_status_id');

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
				'ip'                      => $this->request->server['REMOTE_ADDR'],
				'products'                => $order_products,
				'totals'                  => $totals
			);

			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

			$order_id = (int)$this->session->data['order_id'];

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $order_status_id);

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . $order_id . "'");

			$this->cart->clear();
			
			$json['html'] = $this->language->get('text_success_order');
		}
		
    $this->sendJsonResponse($json);
	}

	public function status() {
		
		$json = array();

		$this->load->language('revolution/revolution');
		$this->load->model('setting/extension');
		
		$total = 0;
		$taxes = $this->cart->getTaxes();
		
		$totals = array();
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
		
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = array();
			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
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
		
    $this->sendJsonResponse($json);
	}
}
