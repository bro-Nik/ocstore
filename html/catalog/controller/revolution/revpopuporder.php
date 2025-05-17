<?php
class ControllerRevolutionRevpopuporder extends Controller {
	public function index() {

		$data = array();

		$this->load->model('catalog/product');
		$this->load->language('revolution/revolution');
		
		$settings = (array)$this->config->get('revtheme_catalog_popuporder');
		$data['revtheme_catalog_popuporder'] = $settings;
		$data['revtheme_product_all'] = $product_settings = $this->config->get('revtheme_product_all');
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
		
		if (isset($this->request->get['revproduct_id'])) {
			$product_id = (int)$this->request->get['revproduct_id'];
		} else {
			$product_id = 0;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);

		$data['product_id'] = $product_id;

		if ($product_info) {

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['quantity'] = $product_info['quantity'];
			
			$currency = $this->session->data['currency'];
			$data['currency_code'] = $currency;
			$var_currency = array();
			$var_currency['value'] = $this->currency->getValue($currency);
			$var_currency['symbol_left'] = $this->currency->getSymbolLeft($currency);
			$var_currency['symbol_right'] = $this->currency->getSymbolRight($currency);
			$var_currency['decimals'] = $this->currency->getDecimalPlace($currency);
			$var_currency['decimal_point'] = $this->language->get('decimal_point');
			$var_currency['thousand_point'] = $this->language->get('thousand_point');
			$data['currency'] = $var_currency;

			if ($product_info['quantity'] <= 0 && $this->config->get('config_stock_warning')) {
				$data['stock_warning'] = $this->language->get('text_zakaz_not') . $product_info['stock_status'] . '.';
			} else {
				$data['stock_warning'] = '';
			}

			$data['product_name'] = $product_info['name'];

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
			} else {
				$data['thumb'] = $this->model_tool_image->resize("placeholder.png", 100, 100);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
				$data['price_number'] = $product_info['price'];
			} else {
				$data['price'] = false;
				$data['price_number'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $currency);
			} else {
				$data['tax'] = false;
			}

			$data['points'] = $product_info['points'];

			$discounts = $this->model_catalog_product->getProductDiscounts($product_id);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency)
				);
			}

			$data['firstname'] = ($this->customer->isLogged()) ? $this->customer->getFirstName() : '';
			$data['email'] = ($this->customer->isLogged()) ? $this->customer->getEmail() : '';
			$data['telephone'] = ($this->customer->isLogged()) ? $this->customer->getTelephone() : '';
			$data['telephone_mask'] = $settings['telephone_mask'];
			$data['comment'] = '';

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['options'] = array();
			foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if ($option_value['quantity'] > 0 || $this->config->get('revtheme_product_all')['options_null_qw']) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $currency);
						} else {
							$price = false;
						}
						
						if ($option_value['price_prefix'] == '=') {
							$price_prefix = '';
						} else {
							$price_prefix = $option_value['price_prefix'];
						}
						
						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
							'price'                   => $price,
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
					'opt_image'            => $option['opt_image']
				);
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);

			$this->response->setOutput($this->load->view('revolution/revpopuporder', $data));
			
		} else {

			$this->response->redirect($this->url->link(isset($this->config->get('revtheme_all_settings')['revcheckout_status']) && $this->config->get('revtheme_all_settings')['revcheckout_status'] ? 'revolution/revcheckout' : 'checkout/checkout'));

		}
	}

	public function make_order() {
		$json = array();

		$this->language->load('revolution/revolution');
		$this->load->model('catalog/product');
		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->load->model('checkout/order');
		$this->load->model('checkout/marketing');

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['option'])) {
			$option = $this->request->post['option'];
		} else {
			$option = array();
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = (int)$this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$settings = $this->config->get('revtheme_catalog_popuporder');

		$product_info = $this->model_catalog_product->getProduct($product_id);
		$product_options = $this->model_catalog_product->getProductOptions($product_id);

		$order_status_id = (!empty($settings['order_status'])) ? (int)$settings['order_status'] : (int)$this->config->get('config_order_status_id');
		$customer_info = ($this->customer->isLogged()) ? $this->model_account_customer->getCustomer($this->customer->getId()) : FALSE;

		foreach ($product_options as $product_option) {
			if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
				$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_option'), $product_option['name']);
			}
		}

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
		
		$min_sum = !empty($this->config->get('revtheme_all_settings')['revcheckout_min_sum']) ? $this->config->get('revtheme_all_settings')['revcheckout_min_sum'] : 0;
		$cart_total_sum = 0;
		if (!empty($min_sum)) {
			$cart_total_sum = (int)preg_replace("/[^0-9]/", '', $this->request->post['price']) * $this->request->post['quantity'];
		}
		if (!empty($min_sum) && $min_sum > $cart_total_sum) {
			$json['error']['z_min_sum'] = sprintf($this->language->get('error_min_sum'), $this->currency->format($min_sum, $this->session->data['currency']));
		}

		if (!isset($json['error'])) {

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
			
			$json['output'] = $this->language->get('text_success_order');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
