<?php
class ControllerRevolutionRevpopupcartQuick extends Controller {
	public function index() {
		
		$data = array();

		$this->load->language('revolution/revolution');

		$settings = (array)$this->config->get('revtheme_catalog_popuporder');
		$data['revtheme_catalog_popuporder'] = $settings;
		$product_settings = $this->config->get('revtheme_product_all');
		$data['zakaz'] = $product_settings['zakaz'];
		$data['opt_price'] = $product_settings['opt_price'];
		$data['q_zavisimost'] = $product_settings['q_zavisimost'];
		
		if ($this->config->get('revtheme_all_settings')['pol_konf']) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('revtheme_all_settings')['pol_konf']);
			if ($information_info) {
				if ($this->config->get('revtheme_all_settings')['pol_konf_tvivod']) {
					$data['text_agree_pol_konf'] = sprintf($this->language->get('text_agree_pol_konf_st'), $this->url->link('information/information', 'information_id=' . $this->config->get('revtheme_all_settings')['pol_konf'], true), $information_info['title'], $information_info['title']);
				} else {
					$data['text_agree_pol_konf'] = sprintf($this->language->get('text_agree_pol_konf'), $this->url->link('information/information', 'information_id=' . $this->config->get('revtheme_all_settings')['pol_konf'], true), $information_info['title']);
				}
			} else {
				$data['text_agree_pol_konf'] = '';
			}
		} else {
			$data['text_agree_pol_konf'] = '';
		}
		
		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$data['error_warning'] = $this->language->get('error_stock');
		} elseif (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}
		
		$data['firstname'] = ($this->customer->isLogged()) ? $this->customer->getFirstName() : '';
		$data['email'] = ($this->customer->isLogged()) ? $this->customer->getEmail() : '';
		$data['telephone'] = ($this->customer->isLogged()) ? $this->customer->getTelephone() : '';
		$data['telephone_mask'] = $settings['telephone_mask'];
		$data['comment'] = '';

		$currency = $this->session->data['currency'];

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

		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$data['error_warning'] = $this->language->get('error_stock');
		} elseif (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
			$data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
		} else {
			$data['attention'] = '';
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
			$product_total = 0;

			foreach ($products as $product_in_cart) {
				if ($product_in_cart['product_id'] == $product['product_id']) {
					$product_total += $product_in_cart['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], 80, 80);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 80, 80);
			}
			
			$quantity_pr = $this->model_catalog_product->getProduct($product['product_id'])['quantity'];
		
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name' => $option['name'],
					'model' => (isset($option['model']) ? $option['model'] : false),
					'value' => (utf8_strlen($value) > 50 ? utf8_substr($value, 0, 50) . '..' : $value),
				);
				
				if (isset($option['opt_image'])) {
					if ($option['opt_image']) {
						$image = $option['opt_image'] ? $this->model_tool_image->resize($option['opt_image'], 80, 80) : '';
					} else {
						$image = $image;
					}
				}
				if ($option['quantity']) {
					$quantity_pr = $option['quantity'];
				}
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $currency);
			} else {
				$price = false;
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
				$total = $this->currency->format($unit_price * $product['quantity'], $currency);
			} else {
				$total = false;
			}

			$data['products'][] = array(
				'quantity_pr' => $quantity_pr,
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
				'price'       => $price,
				'total'       => $total,
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
				'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
			);
		}

		$data['heading_cartpopupquick_title'] = $this->language->get('heading_cartpopupquick_title');

		$this->response->setOutput($this->load->view('modals/cartquick', $data));

	}
	
	public function make_order() {
		
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


		if (isset($this->request->post['firstname'])) {
			if ((isset($settings['firstname']) && $settings['firstname'] == 2) && (utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$json['error']['field']['firstname'] = $this->language->get('error_firstname');
			}
		}

		if (isset($this->request->post['email'])) {
			if (isset($settings['email']) && $settings['email'] == 2) {
				if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
					$json['error']['field']['email'] = $this->language->get('error_email');
				}
			}
		}

		if (isset($this->request->post['telephone'])) {
			if ((isset($settings['telephone']) && $settings['telephone'] == 2) && (utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32) || (preg_match("[_]",$this->request->post['telephone']))) {
				$json['error']['field']['telephone'] = $this->language->get('error_telephone');
			}
		}

		if (isset($this->request->post['comment'])) {
			if ((isset($settings['comment']) && $settings['comment'] == 2) && (utf8_strlen($this->request->post['comment']) < 3) || (utf8_strlen($this->request->post['comment']) > 500)) {
				$json['error']['field']['comment'] = $this->language->get('error_comment');
			}
		}

		if ($this->config->get('revtheme_all_settings')['pol_konf']) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('revtheme_all_settings')['pol_konf']);
			if ($information_info && !isset($this->request->post['agree_pol_konf'])) {
				if ($this->config->get('revtheme_all_settings')['pol_konf_tvivod']) {
					$json['error']['field']['agree_pol_konf'] = sprintf($this->language->get('error_agree_pol_konf_st'), $information_info['title']);
				} else {
					$json['error']['field']['agree_pol_konf'] = $this->language->get('error_agree_pol_konf');
				}
			}
		}
		
		if (!isset($json['error'])) {
			
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
			
			$json['output'] = $this->language->get('text_success_order');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function status_cart() {
		
		$json = array();

		$this->load->language('revolution/revolution');
		$this->load->model('setting/extension');
		
		$currency = $this->session->data['currency'];

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

		$style = '';
		$this->load->model('tool/image');
		$data['setting_header_cart'] = $revtheme_header_cart = $this->config->get('revtheme_header_cart');
		if ($data['setting_header_cart']['icontype']) {
			if ($data['setting_header_cart']['icon'] == 'fa none') {
				$style = ' hidden';
			}
			$image = '<i class="'.$data['setting_header_cart']['icon'].$style.'"></i>';
		} else {
			if (!$data['setting_header_cart']['image'] || $data['setting_header_cart']['image'] == 'no_image.png') {
				$style = ' hidden';
			}
			$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($data['setting_header_cart']['image'], 21, 21).'" alt=""/></span>';
		}
		if ($revtheme_header_cart['cart_size'] == 'small') {
			$heading_title = ($image . $this->language->get('text_rev_items_small'));
		} else if ($revtheme_header_cart['cart_size'] == 'mini') {
			$heading_title = ($image . $this->language->get('text_rev_items_mini'));
		} else {
			$heading_title = ($image . $this->language->get('text_rev_items'));
		}
		
		$json['total'] = sprintf($heading_title, $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $currency));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
