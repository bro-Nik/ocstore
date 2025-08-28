<?php 
require_once('catalog/controller/trait/template.php');
require_once('catalog/controller/trait/cookie.php');
require_once('catalog/controller/extension/module/validator.php');
require_once('catalog/controller/trait/form_handler.php');

class ControllerRevolutionRevcheckout extends Controller {
	use \TemplateTrait, \CookieTrait, \ValidatorTrait, \FormHandlerTrait;

	const FIELDS = ['name', 'phone', 'comment'];
  const REQUIRED_FIELDS = ['phone'];
	
	public function index() {
		$data = $this->getCommonData();
		// $data = array();
		
		$this->load->language('checkout/cart');
		$this->load->language('checkout/checkout');
		$this->load->language('revolution/revolution');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/revcheckout', '', 'SSL')
		);

		$cart_data = $this->getCookie('cart') ?? [];
		
		if (!$cart_data) {
			unset($this->session->data['success']);
      $this->addCommonTemplateData($data);

			$this->response->setOutput($this->load->view('error/not_found', $data));
			
		} else {
			// new
			$product_ids = array_map('intval', array_keys($cart_data));
    	$products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);

		foreach ($products as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], 150, 150);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 150, 150);
			}
			
			$option_data = array();

			// foreach ($product['option'] as $option) {
			// 	if ($option['type'] != 'file') {
			// 		$value = $option['value'];
			// 	} else {
			// 		$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
			//
			// 		if ($upload_info) {
			// 			$value = $upload_info['name'];
			// 		} else {
			// 			$value = '';
			// 		}
			// 	}
			//
			// 	$option_data[] = array(
			// 		'name' => $option['name'],
			// 		'model' => (isset($option['model']) ? $option['model'] : false),
			// 		'value' => (utf8_strlen($value) > 50 ? utf8_substr($value, 0, 50) . '..' : $value),
			// 	);
			// 	
			// 	if (isset($option['opt_image'])) {
			// 		if ($option['opt_image']) {
			// 			$image = $option['opt_image'] ? $this->model_tool_image->resize($option['opt_image'], 80, 80) : '';
			// 		} else {
			// 			$image = $image;
			// 		}
			// 	}
			// 	if ($option['quantity']) {
			// 		$quantity_pr = $option['quantity'];
			// 	}
			// }

			$data['products'][] = array(
				// 'quantity_pr' => $quantity_pr,
				'minimum'     => $product['minimum'] > 0 ? $product['minimum'] : 1,
				// 'key'         => $product['cart_id'],
				'product_id'  => $product['product_id'],
				'thumb'       => $image,
				'name'        => $product['name'],
				// 'model'       => $product['model'],
				'option'      => $option_data,
				'quantity'    => $cart_data[$product['product_id']]['quantity'],
				// 'stock'       => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				// 'reward'      => ($product['reward'] ? sprintf($this->language->get('text_cartpopup_points'), $product['reward']) : ''),
				'price'       => $product['price'],
				// 'total'       => $total,
				'href'        => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

			$data['sticky'] = $this->config->get('revtheme_header_menu')['sticky'];
			
			$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
			$language_id = $this->config->get('config_language_id');
			
			$data['descript'] = html_entity_decode($settings[$language_id]['revcheckout_description']);
			// if ($this->config->get('revtheme_geo_set')['status']) {
			// 	require_once(DIR_SYSTEM . 'library/revolution/SxGeo.php');
			// 	$SxGeo = new SxGeo();
			// 	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			// 		$ip = $_SERVER['HTTP_CLIENT_IP'];
			// 	} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// 		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			// 	} else {
			// 		$ip = $_SERVER['REMOTE_ADDR'];
			// 	}
			// 	$ip_city = $SxGeo->getCity($ip)['city']['name_ru'];
			// 	$ip_region = $SxGeo->getCityFull($ip)['region']['name_ru'];
			// 	$rev_geo_data = $this->config->get('revtheme_geo');
			// 	$data['rev_geos'] = array();
			// 	if (!empty($rev_geo_data)){
			// 		foreach ($rev_geo_data as $rev_geo) {
			// 			if ($ip_city == $rev_geo['city'] || $ip_region == $rev_geo['city']) {
			// 				$data['rev_geos'][] = array(
			// 					'code' => $rev_geo['code'],
			// 					'text' => $rev_geo['text'][$this->config->get('config_language_id')]
			// 				);
			// 			}
			// 		}
			// 	}
			// 	foreach ($data['rev_geos'] as $rev_geo) {
			// 		if (strpos($data['descript'], $rev_geo['code'])) {
			// 			$data['descript'] = html_entity_decode(str_replace($rev_geo['code'], $rev_geo['text'], $data['descript']));
			// 		}
			// 	}
			// }
			$data['logged'] = $this->customer->isLogged();

			// $data['user'] = $this->user();
			$data['address'] = $this->address();
			$data['shipping_method'] = $this->shipping_method();
			$data['payment_method'] = $this->payment_method();
			$data['cart'] = $this->korzina();
			
			if (isset($this->session->data['agree'])) { 
				$data['agree'] = $this->session->data['agree'];
			} else {
				$data['agree'] = true;
			}
			
			if ($this->config->get('config_checkout_id')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
				if ($information_info) {
					$data['text_agree'] = sprintf($this->language->get('text_agree_pol_konf_st_popup'), $this->url->link('information/information', 'information_id=' . $information_info['information_id'], true), $information_info['title'], $information_info['title']);
				} else {
					$data['text_agree'] = '';
				}
			} else {
				if ($this->config->get('revtheme_all_settings')['pol_konf']) {
					$this->load->model('catalog/information');
					$information_info = $this->model_catalog_information->getInformation($this->config->get('revtheme_all_settings')['pol_konf']);
					if ($information_info) {
						if ($this->config->get('revtheme_all_settings')['pol_konf_tvivod']) {
							$data['text_agree'] = sprintf($this->language->get('text_agree_pol_konf_st_popup'), $this->url->link('information/information', 'information_id=' . $this->config->get('revtheme_all_settings')['pol_konf'], true), $information_info['title'], $information_info['title']);
						} else {
							$data['text_agree'] = sprintf($this->language->get('text_agree_pol_konf_popup'), $this->url->link('information/information', 'information_id=' . $this->config->get('revtheme_all_settings')['pol_konf'], true), $information_info['title']);
						}
					} else {
						$data['text_agree'] = '';
					}
				} else {
					$data['text_agree'] = '';
				}
			}
			
			$data['checkout_guest'] = $this->config->get('config_checkout_guest');
			
			if(isset($this->session->data['shipping_address_id']))	{
				unset($this->session->data['shipping_address_id']);
			}
			
			if ($this->customer->isLogged()) {
				$customer_group_id = $this->customer->getGroupId();
			} else {
				if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['customer_group_id'];
				} else {
					$customer_group_id = $this->config->get('config_customer_group_id');
				}
			}
			$this->load->model('revolution/revolution');
			$data['custom_fields'] = $this->model_revolution_revolution->geRevcheckoutCustomFields($customer_group_id);


			// User

		$this->load->language('checkout/cart');
		$this->load->language('checkout/checkout');
		$this->load->language('revolution/revolution');
		
		$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
		$language_id = $this->config->get('config_language_id');
		
		
		$this->load->model('account/customer_group');
		$data['customer_groups'] = array();
		if (is_array($this->config->get('config_customer_group_display'))) {
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}
		
		$data['checkout_guest'] = $this->config->get('config_checkout_guest');
		$data['customer_group_id'] = $customer_group_id = isset($this->request->post['customer_group_id']) ? $this->request->post['customer_group_id'] : $this->config->get('config_customer_group_id');
		$data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
		$data['register'] = isset($this->request->post['register']) ? $this->request->post['register'] : 0;
		
		$this->load->model('revolution/revolution');
		$data['custom_fields'] = $this->model_revolution_revolution->geRevcheckoutCustomFields($customer_group_id);

      $this->addCommonTemplateData($data);
			
			$this->response->setOutput($this->load->view('revolution/checkout/checkout', $data));
			// $this->response->setOutput($this->load->view('revolution/checkout/cart', $data));
		}
  	}
	
	public function validate() {
		$this->load->language('checkout/cart');
		$this->load->language('checkout/checkout');
		$this->load->language('revolution/revolution');
		
		$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
		$language_id = $this->config->get('config_language_id');
		
		$this->load->model('revolution/revolution');
		$this->load->model('account/customer');
		$this->load->model('account/customer_group');
		
		$json = array();
		
		if (!$this->cart->getProducts() || !$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$json['error']['error_warning'] = $this->language->get('error_stock');
		}
	
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getGroupId();
		} else {
			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
		}
		
		if ($settings['revcheckout_name'] == '2' && (isset($this->request->post['firstname']) && ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)))) {
			$json['error']['firstname'] = $this->language->get('error_revcheckout_firstname');
		}
		if ($settings['revcheckout_family'] == '2' && (isset($this->request->post['lastname']) && ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)))) {
			$json['error']['lastname'] = $this->language->get('error_revcheckout_lastname');
		}
		if (($settings['revcheckout_mail'] == '2') || ($settings['revcheckout_register'] == '1' && !$this->customer->isLogged() && isset($this->request->post['register'])) || ($settings['revcheckout_register'] == '2' && !$this->customer->isLogged())) {
			if (isset($this->request->post['email']) && ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL))) {
				$json['error']['email'] = $this->language->get('error_revcheckout_email');
			}
			//if (isset($this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email']) && !$this->customer->isLogged()) {
			//	$this->load->language('account/register');
			//	$json['error']['email'] = $this->language->get('error_exists');
			//}
		}
		if ((!$this->customer->isLogged() && isset($this->request->post['register'])) || $settings['revcheckout_register'] == '2') {
			if (isset($this->request->post['password']) && ((utf8_strlen($this->request->post['password']) < 3) || (utf8_strlen($this->request->post['password']) > 40))) {
				$json['error']['password'] = $this->language->get('error_revcheckout_password');
			}
		}
		if ($settings['revcheckout_telephone'] == '2' && (isset($this->request->post['telephone']) && ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)))) {
			$json['error']['telephone'] = $this->language->get('error_revcheckout_telephone');
		}
		
		$shipping_payment = array();
		$shipping_payment = $this->config->get('revtheme_revcheckoutshippay');
		$session_shipping_methods = isset($this->request->post['shipping_method']) ? preg_replace('/\..+$/','',$this->request->post['shipping_method']) : '';
		
		if ($this->cart->hasShipping() && ((!$this->customer->isLogged() && isset($this->request->post['shipping_method'])) || ($this->customer->isLogged() && isset($this->request->post['address']) && $this->request->post['address'] == 'new'))) {
			if ($shipping_payment && $session_shipping_methods && in_array('adres_on', $shipping_payment[$session_shipping_methods])) {
				$this->load->model('localisation/country');
				$country_info = array();
				if (isset($this->request->post['country_id'])) $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
				if ($settings['revcheckout_index'] == '2') {
					if ($country_info && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
						$json['error']['postcode'] = $this->language->get('error_revcheckout_postcode');
					}
				}
				if ($settings['revcheckout_country'] == '2') {
					if (isset($this->request->post['country_id']) && $this->request->post['country_id'] == '') {
						$json['error']['country'] = $this->language->get('error_country');
						$json['error']['country_id'] = $this->language->get('error_revcheckout_country');
					}
				}
				if ($settings['revcheckout_region'] == '2') {
					if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
						$json['error']['zone'] = $this->language->get('error_zone');
						$json['error']['zone_id'] = $this->language->get('error_revcheckout_zone');
					}
				}
				if ($settings['revcheckout_city'] == '2') {
					if (isset($this->request->post['city']) && ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128))) {
						$json['error']['city'] = $this->language->get('error_revcheckout_city');
					}
				}
				if ($settings['revcheckout_adres'] == '2') {
					if (isset($this->request->post['address_1']) && ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128))) {
						$json['error']['address_1'] = $this->language->get('error_revcheckout_address_1');
					}
				}
			}
		}
		$shipping_code = isset($this->request->post['shipping_method']) ? $this->request->post['shipping_method'] : 0;
		$custom_fields = $this->model_revolution_revolution->geRevcheckoutCustomFields($customer_group_id, $shipping_code);
		$custom_fields_a = $this->model_revolution_revolution->geRevcheckoutCustomFields($customer_group_id);
		if ($this->cart->hasShipping() && ((!$this->customer->isLogged() || (isset($this->request->post['address']) && $this->request->post['address'] == 'new') || ($this->customer->isLogged() && !$settings['revcheckout_reg_adres'])) && isset($this->request->post['shipping_method']))) {
			if ($shipping_payment && $session_shipping_methods && in_array('adres_on', $shipping_payment[$session_shipping_methods])) {
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address' && $custom_field['required'] && empty($this->request->post['custom_field']['address'][$custom_field['custom_field_id']])) {
						if ($custom_field['type'] == 'checkbox') {
							$json['error']['custom_field[address][' . $custom_field['custom_field_id'] . '][]'] = sprintf($this->language->get('error_revcheckout_custom_field'), $custom_field['name']);
						} else {
							$json['error']['custom_field[address][' . $custom_field['custom_field_id'] . ']'] = sprintf($this->language->get('error_revcheckout_custom_field'), $custom_field['name']);
						}
					}
				}
			}
		}
		foreach ($custom_fields_a as $custom_field) {
			if ($custom_field['location'] == 'account' && $custom_field['required'] && empty($this->request->post['custom_field']['account'][$custom_field['custom_field_id']])) {
					$json['error']['custom_field[account][' . $custom_field['custom_field_id'] . ']'] = sprintf($this->language->get('error_revcheckout_custom_field'), $custom_field['name']);
			}
		}
		
		if ($this->config->get('config_checkout_id') || $this->config->get('revtheme_all_settings')['pol_konf']) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			if ($information_info && !isset($this->request->post['agree'])) {
				$json['error']['agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			} else if ($this->config->get('revtheme_all_settings')['pol_konf'] && !isset($this->request->post['agree'])) {
				$json['error']['agree'] = $this->language->get('error_agree_pol_konf');
			}
		}
		

		if ($this->cart->hasProducts() && $this->cart->hasShipping()) {
			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);
				if (!isset($shipping[0]) || !isset($shipping[1])) {
					$json['error']['warning'] = $this->language->get('error_shipping');
				}
			}						
		}
				
		$min_sum = !empty($settings['revcheckout_min_sum']) ? $settings['revcheckout_min_sum'] : 0;
		$cart_total_sum = 0;
		if (!empty($min_sum)) {
			$cart_total_sum = $this->cart->getTotal();
		}
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$cart_total_sum += $voucher['amount'];
			}
		}
		if (!empty($min_sum) && $min_sum > $cart_total_sum) {
			$json['error']['z_min_sum'] = sprintf($this->language->get('error_min_sum'), $this->currency->format($min_sum, $this->session->data['currency']));
		}

		if (!$json) {
			$this->session->data['firstname'] = isset($this->request->post['firstname']) ? strip_tags($this->request->post['firstname']) : '';
			$this->session->data['lastname'] = isset($this->request->post['lastname']) ? strip_tags($this->request->post['lastname']) : '';
			$this->session->data['email'] = isset($this->request->post['email']) ? strip_tags($this->request->post['email']) : 'localhost@localhost.com';
			$this->session->data['telephone'] = isset($this->request->post['telephone']) ? strip_tags($this->request->post['telephone']) : '';
		}
		
		if (!$json && !$this->customer->isLogged()) {
			$this->session->data['account'] = 'guest';
			$this->session->data['guest']['firstname'] = $this->session->data['firstname'];
			$this->session->data['guest']['lastname'] = $this->session->data['lastname'];
			$this->session->data['guest']['email'] = $this->session->data['email'];
			$this->session->data['guest']['telephone'] = $this->session->data['telephone'];
			$this->session->data['guest']['customer_group_id'] = $customer_group_id;
			$this->session->data['guest']['fax'] = isset($this->request->post['fax']) ? $this->request->post['fax'] : '';
		}
		
		$data['config_country_id'] = $this->config->get('config_country_id');
		if (!$json && $this->customer->isLogged() && isset($this->request->post['address']) && $this->request->post['address'] == 'new') {
			$this->load->model('account/address');
			$address_id = isset($this->request->post['address_id']) ? $this->request->post['address_id'] : $this->customer->getAddressId();
			$this->model_account_address->editAddress($address_id, $this->request->post);
		}
		
		if ((!$json && !$this->customer->isLogged() && isset($this->request->post['register'])) || (!$json && !$this->customer->isLogged() && $settings['revcheckout_register'] == '2')) {
			$this->load->model('account/customer');
			$this->session->data['account'] = 'register';
			$this->session->data['checkout_customer_id'] = true;
			$customer_id = $this->model_account_customer->addCustomer($this->request->post);
			$this->load->model('account/address');
			$this->model_account_address->addAddress($customer_id, $this->request->post);
			$this->customer->login($this->request->post['email'], $this->request->post['password']);
			$customer_info = $this->model_account_customer->getCustomer($customer_id);
			$this->session->data['customer_id'] = $customer_id;
			$this->session->data['customer_group_id'] = $customer_info['customer_group_id'];
			$this->load->model('account/activity');
			$activity_data = array(
				'customer_id' => $customer_id,
				'name'        => $this->request->post['firstname'].' '.$this->request->post['lastname']
			);
			$this->model_account_activity->addActivity('register', $activity_data);
			unset($this->session->data['guest']);
			$json['validate_login'] = true;
		}
		
		if (!$json && $this->cart->hasProducts()) {
			$this->address();
			$json['success'] = $this->confirm();
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}	

	public function address() {
		$data = array();
		$this->load->language('checkout/cart');
		$this->load->language('checkout/checkout');
		$this->load->language('revolution/revolution');

		$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
		$language_id = $this->config->get('config_language_id');
		
		$data['config_country_id'] = $this->config->get('config_country_id');
		
		$customer_group_id = isset($this->request->post['customer_group_id']) ? $this->request->post['customer_group_id'] : $this->config->get('config_customer_group_id');
		$shipping_code = isset($this->request->post['shipping_method']) ? $this->request->post['shipping_method'] : 0;
		
		$this->load->model('revolution/revolution');
		$data['custom_fields'] = $this->model_revolution_revolution->geRevcheckoutCustomFields($customer_group_id, $shipping_code);
		
		if (isset($this->request->post['custom_field']['address'])) {
			if (is_array($this->request->post['custom_field']['address'])) {
				foreach ($this->request->post['custom_field']['address'] as $key => $value) {
					foreach ($data['custom_fields'] as $key2 => $value2) {
						if ($value2['type'] == 'select' || $value2['type'] == 'radio') {
							if ($value2['custom_field_id'] == $key) {
								foreach ($value2['custom_field_value'] as $key3 => $value3) {
									if ($value2['name'].': '.$value3['name'] == $value) {
										$data['custom_fields'][$key2]['custom_field_value'][$key3]['chek'] = true;
									}
								}
							}
						} else if ($value2['type'] == 'checkbox') {
							if ($value2['custom_field_id'] == $key) {
								foreach ($value2['custom_field_value'] as $key3 => $value3) {
									if (in_array($value2['name'].': '.$value3['name'], $value)) {
										$data['custom_fields'][$key2]['custom_field_value'][$key3]['chek'] = true;
									}
								}
							}
						} else {
							if ($value2['custom_field_id'] == $key) {
								$data['custom_fields'][$key2]['value'] = $value;
							}
						}
					}
				}
			}
		}
		
		$data['new_address'] = $new_address = isset($this->request->post['address']) && $this->request->post['address'] == 'new' ? true : false;
		
		if (!$this->customer->isLogged() || $new_address || !$settings['revcheckout_reg_adres']) {
		
			$address['address_1'] = isset($this->request->post['address_1']) ? $this->request->post['address_1'] : '';
			$address['address_2'] = isset($this->request->post['address_2']) ? $this->request->post['address_2'] : '';
			$address['company'] = isset($this->request->post['company']) ? $this->request->post['company'] : '';
			$address['postcode'] = isset($this->request->post['postcode']) ? $this->request->post['postcode'] : '';
			
			if(isset($this->request->post['city'])) {
				$address['city'] = $address['shipping_city'] = $this->request->post['city'];
			} elseif (isset($this->session->data['payment_address']['city'])) {
				$address['city'] = $address['shipping_city'] = $this->session->data['payment_address']['city'];
			} else {
				$address['city'] = $address['shipping_city'] = '';
			}
			
			if(isset($this->request->post['zone_id'])) {
				$address['zone_id'] = $address['zone_country_id'] = $this->request->post['zone_id'];
			} else {
				$address['zone_id'] = $address['zone_country_id'] = $this->config->get('config_zone_id');
			}
			
			if(isset($this->request->post['country_id'])) {
				$address['country_id'] = $address['shipping_country_id'] = $this->request->post['country_id'];
			} else {
				$address['country_id'] = $address['shipping_country_id'] = $this->config->get('config_country_id');
			}

			$address['zone'] = '';
			if ($address['zone_id']) {
				$this->load->model('localisation/zone');	
				$zone_info = $this->model_localisation_zone->getZone($address['zone_id']);
				
				$address['zone'] = isset($zone_info) ? $zone_info['name'] : '';
				$address['zone_code'] = isset($zone_info) ? $zone_info['code'] : '';
			}

			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($address['country_id']);
			
			$address['country'] = $country_info ? $country_info['name'] : '';
			$address['iso_code_2'] = $country_info ? $country_info['iso_code_2'] : '';
			$address['iso_code_3'] = $country_info ? $country_info['iso_code_3'] : '';
			$address['address_format'] = $country_info ? $country_info['address_format'] : '';
		
		}
		
		$existing_address = isset($this->request->post['address']) && $this->request->post['address'] == 'existing' ? true : false;
		
		if (($this->customer->isLogged() && !$new_address && !$existing_address && $settings['revcheckout_reg_adres']) || ($this->customer->isLogged() && $existing_address)) {
			$this->load->model('account/address');	
			$data['address_id'] = $address_id = isset($this->request->post['address_id']) ? $this->request->post['address_id'] : $this->customer->getAddressId();
			$this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'] = $address_id;
			$address = $this->model_account_address->getAddress($address_id);
		}
		
		$this->session->data['shipping_address'] = $this->session->data['payment_address'] = $address;
		$this->session->data['ship_meth'] = isset($this->request->post['shipping_method']) ? $this->request->post['shipping_method'] : '';
		$this->session->data['pay_meth'] = isset($this->request->post['payment_method']) ? $this->request->post['payment_method'] : '';
		$this->session->data['comment'] = isset($this->request->post['comment']) ? $this->request->post['comment'] : '';
		
		$data['customer_id'] = $this->customer->isLogged() ? $this->customer->getId() : '';
		$data['is_shipping'] = $this->cart->hasShipping() ? true : false;
		
		$data['addresses'] = array();
		$this->load->model('account/address');
		$data['addresses'] = $this->model_account_address->getAddresses();
		
		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();
		
		$data['city'] = trim($address['city']);
		$data['postcode'] = trim($address['postcode']);
		$data['address_1'] = trim($address['address_1']);
		$data['country'] = trim($address['country']);
		$data['country_id'] = trim($address['country_id']);
		$data['zone'] = trim($address['zone']);
		$data['zone_id'] = trim($address['zone_id']);
		$data['iso_code_2'] = trim($address['iso_code_2']);
		$data['iso_code_3'] = trim($address['iso_code_3']);
		$data['address_format'] = trim($address['address_format']);
		
		$shipping_payment = array();
		$shipping_payment = $this->config->get('revtheme_revcheckoutshippay');
		$session_shipping_methods = isset($this->request->post['shipping_method']) ? preg_replace('/\..+$/','',$this->request->post['shipping_method']) : '';
		if ($shipping_payment && $session_shipping_methods && !in_array('adres_on', $shipping_payment[$session_shipping_methods])) {
			$data['is_shipping'] = false;
		}
		
		$result = $this->load->view('revolution/checkout/address', $data);

		if (isset($this->request->get['ajax'])) {
			$this->response->setOutput($result);
		} else {
			return $result;
		}
	}

	public function shipping_method() {
		$data = array();
		
		$this->load->language('revolution/revolution');
		$data['text_revcheckout_shipping_method'] = $this->language->get('text_revcheckout_shipping_method');
		$this->load->language('checkout/checkout');

		if (isset($this->session->data['shipping_address'])) {
			$method_data = array();
			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('shipping');
			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);
					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);
					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}
			$sort_order = array();
			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
			array_multisort($sort_order, SORT_ASC, $method_data);
			// $this->session->data['shipping_methods'] = $method_data;
		}
		
		// $data['shipping_methods'] = $this->session->data['shipping_methods'];
		
		$shipping = explode('.', $this->session->data['ship_meth']);
		
		if(isset($shipping[0]) && isset($shipping[1]) && isset($method_data[$shipping[0]]['quote'][$shipping[1]])) {
			$this->session->data['shipping_method'] = $method_data[$shipping[0]]['quote'][$shipping[1]];
		}
		
		$data['code'] = isset($this->session->data['shipping_method']['code']) ? $this->session->data['shipping_method']['code'] : '';
		
		$data['error_warning'] = (empty($this->session->data['shipping_methods'])) ? sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact')) : '';

		$result = $this->load->view('revolution/checkout/shipping', $data);
		if ($this->cart->hasShipping()) {
			if (isset($this->request->get['ajax'])) {
				$this->response->setOutput($result);
			} else {
				return $result;
			}
		} else {
			return '';
		}
  	}
  	
  	public function payment_method() {
		$data = array();

		if (isset($this->session->data['payment_address'])) {
			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);
			$this->load->model('setting/extension');
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
			$method_data = array();
			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('payment');
			$recurring = $this->cart->hasRecurringProducts();
			$shipping_payment = array();
			$shipping_payment = $this->config->get('revtheme_revcheckoutshippay');
			$session_shipping_methods = isset($this->request->post['shipping_method']) ? preg_replace('/\..+$/','',$this->request->post['shipping_method']) : '';
			foreach ($results as $result) {
				if ($this->config->get('payment_' . $result['code'] . '_status')) {
					$this->load->model('extension/payment/' . $result['code']);
					$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);
					if ($method) {
						if ($recurring) {
							if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
								$method_data[$result['code']] = $method;
							}
						} else {
							$method_data[$result['code']] = $method;
						}
					}
				}
			}
			$sort_order = array();
			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
			array_multisort($sort_order, SORT_ASC, $method_data);
		}

		foreach ($method_data as $key => $value) {
			if ($shipping_payment && $session_shipping_methods && !in_array($key, $shipping_payment[$session_shipping_methods])) {
				unset($method_data[$key]);
			}
		}
		
		$this->session->data['payment_methods'] = $method_data;
		
		$data['payment_methods'] = $this->session->data['payment_methods'];
		
		if (isset($this->session->data['pay_meth']) && $this->session->data['pay_meth'] != '' && isset($method_data[$this->session->data['pay_meth']])) {
			$this->session->data['payment_method']['code'] = $this->session->data['pay_meth'];
			$this->session->data['payment_method'] = $method_data[$this->session->data['pay_meth']];
		}
		
		$data['code'] = isset($this->session->data['payment_method']['code']) ? $this->session->data['payment_method']['code'] : '';
   
		$data['error_warning'] = empty($this->session->data['payment_methods']) ? sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact')) : '';
		
		$result = $this->load->view('revolution/checkout/payment', $data);
		
		if (isset($this->request->get['ajax'])) {
			$this->response->setOutput($result);
		} else {
			return $result;
		}
  	}
	
	public function korzina(){
		$data = array();
		$this->load->language('product/product');
		$this->load->language('checkout/cart');
		$this->load->language('revolution/revolution');

		$settings = $data['settings'] = $this->config->get('revtheme_all_settings');
		$language_id = $this->config->get('config_language_id');
		
		$product_settings = $this->config->get('revtheme_product_all');
		$data['q_zavisimost'] = $product_settings['q_zavisimost'];
        
		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}
			
		$points = $this->customer->getRewardPoints();
		$points_total = 0;
			
		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}		
		$data['text_use_reward'] = sprintf($this->language->get('text_use_reward'), $points);
		
		$data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
      		$data['error_warning'] = $this->language->get('error_stock');		
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
		
		$currency = $this->session->data['currency'];
		
		$data['weight'] = false;
		if ($this->cart->getWeight() > 0 && $this->config->get('config_cart_weight')) {
			$data['weight'] = number_format($this->cart->getWeight(), 2);
			$data['weight_format'] = $this->weight->getUnit($this->config->get('config_weight_class_id'));
		}
		
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

        $data['products'] = array();
				$cart_data = $this->getCookie('cart') ?? [];
				$product_ids = array_map('intval', array_keys($cart_data));
    		$products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);

        // $products = $this->cart->getProducts();
			
        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

            if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
			} else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
            }
			
			$quantity_pr = $this->model_catalog_product->getProduct($product['product_id'])['quantity'];

            $option_data = array();

			$image_cart_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width');
			$image_cart_width = $image_cart_width ? $image_cart_width : 40;
			$image_cart_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height');
			$image_cart_height = $image_cart_height ? $image_cart_height : 40;
			
			$this->load->model('tool/upload');

    //         foreach ($product['option'] as $option) {
    //             if ($option['type'] != 'file') {
				// 	if (isset($option['option_value']))	{
				// 		$value = $option['option_value'];
				// 	} else if (isset($option['value']))	{
				// 		$value = $option['value'];
				// 	} else {
				// 		$value = '';
				// 	}
    //             } else {
    //                 $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
				// 	if ($upload_info) {
				// 		$value = $upload_info['name'];
				// 	} else {
				// 		$value = '';
				// 	}
    //             }
				// 
				// if (isset($option['opt_image'])) {
				// 	if ($option['opt_image']) {
				// 		$image = $this->model_tool_image->resize($option['opt_image'], $image_cart_width,  $image_cart_height);
				// 	} else {
				// 		if ($product['image']) {
				// 			$image = $this->model_tool_image->resize($product['image'], $image_cart_width, $image_cart_height);
				// 		} else {
				// 			$image = $this->model_tool_image->resize('no_image.png', $image_cart_width, $image_cart_height);
				// 		}
				// 	}
				// } else {
				// 	if ($product['image']) {
				// 		$image = $this->model_tool_image->resize($product['image'], $image_cart_width, $image_cart_height);
				// 	} else {
				// 		$image = $this->model_tool_image->resize('no_image.png', $image_cart_width, $image_cart_height);
				// 	}
				// }
				//
    //             $option_data[] = array(
    //                 'name'  => $option['name'],
    //                 'value' => (utf8_strlen($value) > 50 ? utf8_substr($value, 0, 50) . '..' : $value),
				// 	'model' => (isset($option['model']) ? $option['model'] : false)
    //             );
				// if ($option['quantity']) {
				// 	$quantity_pr = $option['quantity'];
				// }
    //         }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $currency);
            } else {
                $price = false;
            }

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $currency);
            } else {
                $total = false;
            }
                
            $profile_description = '';
                
            $recurring = '';

			// if ($product['recurring']) {
			// 	$frequencies = array(
			// 		'day'        => $this->language->get('text_day'),
			// 		'week'       => $this->language->get('text_week'),
			// 		'semi_month' => $this->language->get('text_semi_month'),
			// 		'month'      => $this->language->get('text_month'),
			// 		'year'       => $this->language->get('text_year')
			// 	);
			//
			// 	if ($product['recurring']['trial']) {
			// 		$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
			// 	}
			//
			// 	if ($product['recurring']['duration']) {
			// 		$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
			// 	} else {
			// 		$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
			// 	}
			// }
			
			if ($this->config->get('revtheme_product_all')['ed_izm']) {
				$ed_izm = $product['isbn'];
			} else {
				$ed_izm = false;
			}

            $data['products'][] = array(
				// 'ed_izm'              => $ed_izm,
				// 'cart_id'  			  => $product['cart_id'],
                'product_id'          => $product['product_id'],
                'thumb'               => $image,
                'name'                => $product['name'],
                'model'               => $product['model'],
				'minimum'  			  => $product['minimum'],
				'quantity_pr'  		  => $quantity_pr,
				'sku'                 => $product['sku'],
                'option'              => $option_data,
                'quantity'            => $product['quantity'],
                // 'stock'               => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
                'reward'              => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
                'price'               => $price,
                'total'               => $total,
                'href'                => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'remove'              => $this->url->link('checkout/cart', 'remove=' . $product['product_id']),
                'recurring'           => $recurring,
                'profile_name'        => isset($product['profile_name'])?$product['profile_name']:'',
                'profile_description' => $profile_description,
            );
		}

		$data['vouchers'] = array();
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount']),
					'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)   
				);
			}
		}
		
		$data['coupon_status'] = $this->config->get('total_coupon_status');
		if (isset($this->request->post['coupon'])) {
			$data['coupon'] = $this->request->post['coupon'];
		} elseif (isset($this->session->data['coupon'])) {
			$data['coupon'] = $this->session->data['coupon'];
		} else {
			$data['coupon'] = '';
		}
			
		$data['voucher_status'] = $this->config->get('total_voucher_status');
		if (isset($this->request->post['voucher'])) {
			$data['voucher'] = $this->request->post['voucher'];				
		} elseif (isset($this->session->data['voucher'])) {
			$data['voucher'] = $this->session->data['voucher'];
		} else {
			$data['voucher'] = '';
		}

		$data['reward_status'] = ($points && $points_total && $this->config->get('total_reward_status'));
		if (isset($this->request->post['reward'])) {
			$data['reward'] = $this->request->post['reward'];
		} elseif (isset($this->session->data['reward'])) {
			$data['reward'] = $this->session->data['reward'];
		} else {
			$data['reward'] = '';
		}
		
		// Totals
		$this->load->model('setting/extension');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;
		
		// Because __call can not keep var references so we put them into an array. 			
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
		
		// Display prices
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
					
					// We have to put the totals in an array so that they pass by reference.
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
		
		$result = $this->load->view('revolution/checkout/cart', $data);
		
		if (isset($this->request->get['ajax'])) {
			$this->response->setOutput($result);
		} else {
			return $result;
		}
	}
	
	public function cart_edit() {
		$json = array();
		if (!empty($this->request->post['quantity'])) {
			foreach ($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		
	private function confirm() {
		$data['payment'] = '';
		$data['products'] = '';
		$redirect = '';

		$this->load->language('checkout/checkout');
		
		$data['text_cart'] = $this->language->get('text_cart');

		if (!$this->cart->hasShipping()) {
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		}
		
		$currency = $this->session->data['currency'];

		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			$product_total = 0;
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$redirect = $this->url->link('checkout/cart');
				break;
			}
		}

		$order_data = array();

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);

		$this->load->model('setting/extension');

		$sort_order = array();

		$results = $this->model_setting_extension->getExtensions('total');

		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get('total_' . $result['code'] . '_status')) {
				$this->load->model('extension/total/' . $result['code']);

				// We have to put the totals in an array so that they pass by reference.
				$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			}
		}

		$sort_order = array();

		foreach ($totals as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $totals);

		$order_data['totals'] = $totals;

		$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		$order_data['store_id'] = $this->config->get('config_store_id');
		$order_data['store_name'] = $this->config->get('config_name');

		if ($order_data['store_id']) {
			$order_data['store_url'] = $this->config->get('config_url');
		} else {
			$order_data['store_url'] = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;
		}
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post != '') {
			$order_data['firstname'] = $this->session->data['firstname'];
			$order_data['lastname'] = $this->session->data['lastname'];
			$order_data['email'] = $this->session->data['email'];
			$order_data['email'] = (isset($this->session->data['email']) && !empty($this->session->data['email'])) ? $this->session->data['email'] : 'localhost@localhost.com';
			$order_data['telephone'] = $this->session->data['telephone'];
		
			if ($this->customer->isLogged()) {
				$this->load->model('account/customer');
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$order_data['customer_id'] = $this->customer->getId();
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
				$order_data['fax'] = $customer_info['fax'];
				$order_data['custom_field'] = array();
			} else if (isset($this->session->data['guest'])) {
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = isset($this->session->data['guest']['customer_group_id'])?$this->session->data['guest']['customer_group_id']:$this->config->get('config_customer_group_id');
				$order_data['fax'] = isset($this->session->data['guest']['fax']) ? $this->session->data['guest']['fax'] : '';
				$order_data['custom_field'] = array();
			}

			$order_data['payment_firstname'] = $order_data['firstname'];
			$order_data['payment_lastname'] = $order_data['lastname'];
			$order_data['payment_company'] = $this->session->data['payment_address']['company'];
			$order_data['payment_city'] = $this->session->data['payment_address']['city'];
			$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
			$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
			$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
			$order_data['payment_country'] = $this->session->data['payment_address']['country'];
			$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
			$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
			$order_data['payment_custom_field'] = array();
			$order_data['payment_address_1'] =  $this->session->data['payment_address']['address_1'];
			$order_data['payment_address_2'] =  $this->session->data['payment_address']['address_2'];

			$order_data['payment_method'] = isset($this->session->data['payment_method']['title']) ? $this->session->data['payment_method']['title'] : '';
			$order_data['payment_code'] = isset($this->session->data['payment_method']['code']) ? $this->session->data['payment_method']['code'] : '';

			if ($this->cart->hasShipping()) {
				$order_data['shipping_firstname'] = $order_data['firstname'];
				$order_data['shipping_lastname'] = $order_data['lastname'];
				$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
				$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
				$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
				$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
				$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
				$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
				$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
				$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
				$order_data['shipping_custom_field'] = array();
				$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
				$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
				$order_data['shipping_method'] = isset($this->session->data['shipping_method']['title']) ? $this->session->data['shipping_method']['title'] : '';
				$order_data['shipping_code'] = isset($this->session->data['shipping_method']['code']) ? $this->session->data['shipping_method']['code'] : '';
			} else {
				$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_custom_field'] = array();
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
			}

			$order_data['products'] = array();

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

				$order_data['products'][] = array(
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

			$order_data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$order_data['vouchers'][] = array(
						'description'      => $voucher['description'],
						'code'             => substr(md5(mt_rand()), 0, 10),
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $voucher['amount']
					);
				}
			}

			$this->session->data['comment'] = '';
			if (isset($this->request->post['custom_field']['account']) && !empty($this->request->post['custom_field']['account'])) {
				$custom_fields_account = $this->request->post['custom_field']['account'];
				$implode = array();
				foreach ($custom_fields_account as $custom_field_account) {
					if (is_array($custom_field_account)) {
						foreach ($custom_field_account as $custom_field_ac) {
							if ($custom_field_ac != '') {
								$implode[0] = $custom_field_ac;
							}
							if ($implode) {
								$this->session->data['comment'] .= implode(', ', $implode) . "\n";
							}
						}
					} else {
						$custom_field_acc = $custom_field_account;
						$this->session->data['comment'] .= $custom_field_acc . "\n";
					}
				}
			}
			if (isset($this->request->post['custom_field']['address']) && !empty($this->request->post['custom_field']['address'])) {
				$custom_fields_address = $this->request->post['custom_field']['address'];
				$implode = array();
				foreach ($custom_fields_address as $custom_field_address) {
					if (is_array($custom_field_address)) {
						foreach ($custom_field_address as $custom_field_adr) {
							if ($custom_field_adr != '') {
								$implode[0] = $custom_field_adr;
							}
							if ($implode) {
								$this->session->data['comment'] .= implode(', ', $implode) . "\n";
							}
						}
					} else {
						$custom_field_addre = $custom_field_address;
						$this->session->data['comment'] .= $custom_field_addre . "\n";
					}
				}
			}
			if ($this->session->data['comment'] != '' && isset($this->request->post['comment']) && $this->request->post['comment'] != '') {
				$this->session->data['comment'] .= "<br/>";
			}
			$this->session->data['comment'] .= (isset($this->request->post['comment'])) ? strip_tags($this->request->post['comment']) : '';
			$order_data['comment'] = $this->session->data['comment'];
	
			$order_data['total'] = $total;

			if (isset($this->request->cookie['tracking'])) {
				$this->load->model('account/customer');
				$order_data['tracking'] = $this->request->cookie['tracking'];

				$subtotal = $this->cart->getSubTotal();

				// Affiliate
				$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['customer_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}

				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} else if (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$this->load->model('checkout/order');
			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

			$data['text_recurring_item'] = $this->language->get('text_recurring_item');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
		}
		
		$this->session->data['payment_method']['code'] = isset($this->session->data['payment_method']['code']) ? $this->session->data['payment_method']['code'] : $this->request->post['payment_method'];
		
		$code = explode('.', $this->session->data['payment_method']['code']);
		
		header('Content-Type: text/html; charset=UTF-8');		
		return $this->load->controller('extension/payment/'.$code[0]);
  	}
	
	// public function country() {
	// 	$json = array();
	// 	$this->load->model('localisation/country');
 //    	$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
	// 	if ($country_info) {
	// 		$this->load->model('localisation/zone');
	// 		$json = array(
	// 			'country_id'        => $country_info['country_id'],
	// 			'name'              => $country_info['name'],
	// 			'iso_code_2'        => $country_info['iso_code_2'],
	// 			'iso_code_3'        => $country_info['iso_code_3'],
	// 			'address_format'    => $country_info['address_format'],
	// 			'postcode_required' => $country_info['postcode_required'],
	// 			'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
	// 			'status'            => $country_info['status']		
	// 		);
	// 	}		
	// 	$this->response->setOutput(json_encode($json));
	// }
	
}
