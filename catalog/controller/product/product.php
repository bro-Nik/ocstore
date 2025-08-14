<?php
require_once('catalog/controller/base/products_list.php');
require_once('catalog/controller/extension/module/validator.php');

class ControllerProductProduct extends ControllerBaseProductsList {
	use \ValidatorTrait;
	private $error = array();

	public function index() {
		$this->load->language('product/product');
		$this->load->language('revolution/revolution');
		$this->load->model('catalog/category');

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

    $this->load->model('revolution/revolution');

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			$data['breadcrumbs'] = $this->prepareBreadcrumbs($product_info);

			$this->setMetaData($product_info, 1);
			$this->noindexCheck($product_info);
			$this->setTitleDescription($data, $product_info, 1);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');

			$this->load->model('catalog/review');

			$data['review_count'] = (int)$product_info['reviews'];
			$data['answer_count'] = (int)$this->model_revolution_revolution->gettotalanswers($product_id);

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['sku'] = $product_info['sku'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);

			$this->load->model('tool/image');
			if ($product_info['image']) {
				$data['thumb']      = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
				$data['additional'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
				$data['popup'] 		  = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				$this->document->setOgImage($data['thumb']);
			} else {
				$data['thumb'] = '';
				$data['additional'] = '';
				$data['popup'] = '';
			}

			$thumb_small = $product_info['image'] ? $product_info['image'] : 'no_image.png';
			$data['thumb_small'] = $this->model_tool_image->resize($thumb_small, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
			$data['images'] = $this->prepareProductImages($product_info);
			$data['price'] = round($product_info['price'], 2);
			$data['special_price'] = $product_info['special'] ? round($product_info['special'] , 2) : false;

			$this->load->model('revolution/revolution');
			$special = $this->model_revolution_revolution->getProductSpecialData($this->request->get['product_id']);
			if (is_array($special) && isset($special['date_end']) && $special['date_end'] && time() < strtotime($special['date_end'])) {
				$this->load->language('revolution/revolution');
				$data['special_end'] = $special['date_end'];
				$data['text_countdown'] = $this->language->get('text_countdown');
			} else {
				$data['special_end'] = false;
			}

			$data['options'] = $this->prepareProductOptions($product_id);
			$data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;

			// REvolution start
			// $data['short_description'] = html_entity_decode($product_info['short_description'], ENT_QUOTES, 'UTF-8');
			$data['options_buy'] = $product_info['options_buy'];
			$data['brand'] = $this->model_revolution_revolution->get_pr_brand($product_id);
			$data['stiker_ean'] = $product_info['ean'];
			$data['stiker_jan'] = $product_info['jan'];
			$data['stiker_isbn'] = $product_info['isbn'];
			$data['sklad_status'] = $product_info['stock_status'];
			$data['quantity'] = $product_info['quantity'];
			$data['reviews_number'] = $reviews_number = (int)$product_info['reviews'];
			function getcartword($number, $suffix) {
				$keys = array(2, 0, 1, 1, 1, 2);
				$mod = $number % 100;
				$suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
				return $suffix[$suffix_key];
			}
			$textcart_array = array('text_reviews_1', 'text_reviews_2', 'text_reviews_3');
			$textcart = getcartword($reviews_number, $textcart_array);
			$data['reviews'] = sprintf($this->language->get($textcart), (int)$product_info['reviews']);
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page')) && $data['revanswers']) {
				$data['captcha2'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '_two');
			} else {
				$data['captcha2'] = '';
			}
			// Revolution end

			$data['rating'] = number_format($product_info['rating'], 1);

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['revtheme_product_all_attribute_group'] = $this->config->get('revtheme_product_all_attribute_group');
			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);
			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$related_products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
			$data['accessories'] = $this->prepareProducts($related_products);

			$data['viewed_products'] = $this->load->controller('revolution/viewed_products');

			$this->load->model('revolution/revolution');
			$data['tab_info'] = $this->model_revolution_revolution->getproductTabs($this->request->get['product_id']);
			$data['product_tabs'] = array();	
			$tabresults = $this->model_revolution_revolution->getproducttab($this->request->get['product_id']);
			foreach($tabresults as $result_tab){
				$data['product_tabs'][] = array(
					'product_tab_id' => $result_tab['product_tab_id'],
					'title'          => $result_tab['heading'],
					'description'    => html_entity_decode($result_tab['description'], ENT_QUOTES, 'UTF-8')
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);
			$data['mpn'] = $product_info['mpn'];

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			
			$data = $this->addCommonTemplateData($data);
    	
    	$this->response->setOutput($this->load->view('product/product', $data));
		} else {
			$this->ErrorPage();
		}
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function update_prices() {
		if (isset($this->request->request['product_id']) && isset($this->request->request['quantity'])) {
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			
			$product_id      = (int)$this->request->request['product_id'];
			$quantity        = (int)$this->request->request['quantity'];
			$product_info    = $this->model_catalog_product->getProduct($product_id);
			$product_options = $this->model_catalog_product->getProductOptions($product_id);
			$itog_weight     = $product_info['weight'];
			$option_weight	 = 0;
			$option_weight2	 = 0;
			$itog_reward     = $product_info['points'];
			$option_points 	 = 0;
			$option_points2  = 0;
			$option_price_pl = false;
			$option_price    = 0;
			$option_price2 	 = 0;
			$option_quantity = $product_info['quantity'];
			$opt_image 		 = false;
			$opt_model 		 = $product_info['model'];
			$all_settings    = $this->config->get('revtheme_product_all');

			if (!empty($this->request->request['option'])) {
				$option = $this->request->request['option'];
			} else {
				$option = array();
			}

			foreach ($product_options as $product_option) {
				if (is_array($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $option_value) {
					if(isset($option[$product_option['product_option_id']])) {
					if(($option[$product_option['product_option_id']] == $option_value['product_option_value_id']) || ((is_array($option[$product_option['product_option_id']])) && (in_array($option_value['product_option_value_id'], $option[$product_option['product_option_id']])))) {
						if ($option_value['price_prefix'] == '+') {
							$option_price += $option_value['price'];
						} elseif ($option_value['price_prefix'] == '-') {
						$option_price -= $option_value['price'];
						} elseif ($option_value['price_prefix'] == '=') {
						$option_price_pl = true;
						if ($all_settings['options_ravno_plus']) {
							$option_price2 += $option_value['price'];
						} else {
							$option_price2 = $option_value['price'];
						}
						}
						if ($option_value['weight_prefix'] == '+') {
						$option_weight += $option_value['weight'];
						} elseif ($option_value['weight_prefix'] == '-') {
						$option_weight -= $option_value['weight'];
						} elseif ($option_value['weight_prefix'] == '=') {
						$option_weight2 = $option_value['weight'];
						$itog_weight = 0;
						}
						if ($option_value['points_prefix'] == '+') {
						$option_points += $option_value['points'];
						} elseif ($option_value['points_prefix'] == '-') {
						$option_points -= $option_value['points'];
						} elseif ($option_value['points_prefix'] == '=') {
						if ($all_settings['options_ravno_plus']) {
							$option_points2 += $option_value['points'];
						} else {
							$option_points2 = $option_value['points'];
						}
						$itog_reward = 0;
						}
						$option_quantity = $option_value['quantity'];
						if ($all_settings['option_img_options']) {
							if($option_value['image']) {
							$opt_image = $option_value['image'];
							}
						} else {
							if($option_value['opt_image']) {
							$opt_image = $option_value['opt_image'];
							}
						}
						if($option_value['model']) {
						$opt_model = $option_value['model'];
						}
						}
					}
					}
				}
			}

			$json = array();
			
			$config_image_popup_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
			$config_image_popup_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
			$config_image_product_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
			$config_image_product_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
			$config_image_thumb_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width');
			$config_image_thumb_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height');
			$currency = $this->session->data['currency'];
			
			if ($opt_image) {
				$json['opt_image'] = $this->model_tool_image->resize($opt_image, $config_image_product_width, $config_image_product_height);
				$json['opt_image_2'] = $this->model_tool_image->resize($opt_image, $config_image_thumb_width, $config_image_thumb_height);
				$json['opt_image_2_big'] = $this->model_tool_image->resize($opt_image, $config_image_popup_width, $config_image_popup_height);
			} else {
				$json['opt_image'] = $this->model_tool_image->resize($product_info['image'], $config_image_product_width, $config_image_product_height);
				$json['opt_image_2'] = $this->model_tool_image->resize($product_info['image'], $config_image_thumb_width, $config_image_thumb_height);
				$json['opt_image_2_big'] = $this->model_tool_image->resize($product_info['image'], $config_image_popup_width, $config_image_popup_height);
			}
			
			if ($opt_model) {
				$json['opt_model'] = $opt_model;
			} else {
				$json['opt_model'] = $opt_model;
			}
			
			if ($option_weight2) {
				$option_weight = $itog_weight + $option_weight + $option_weight2;
				$json['weight'] = $option_weight * $quantity;
			} else {
				$json['weight'] = ($itog_weight + $option_weight) * $quantity;
			}
			
			if ($option_points2) {
				$option_points = $itog_reward + $option_points + $option_points2;
				$json['points'] = $option_points * $quantity;
			} else {
				$json['points'] = ($itog_reward + $option_points) * $quantity;
			}
			
			$json['option_quantity'] = $option_quantity;
			
			if ($option_price_pl) {
				$json['special_n'] = (float)($this->tax->calculate($option_price2 * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$json['special_n'] = (float)(($this->tax->calculate($this->update_discount($product_id, $quantity), $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
			}
			
			if ($option_price_pl) {
				$json['price_n'] = (float)($this->tax->calculate($option_price2 * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$json['price_n'] = (float)(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
			}
			
			$json['special'] = $this->currency->format(($this->tax->calculate($this->update_discount($product_id, $quantity), $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
			$json['price'] = $this->currency->format(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
			if ((float)$product_info['special'] && $all_settings['options_special']) {
				$special_koefficient = (float)$product_info['price'] / (float)$product_info['special'];
				$price = ($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity) + $this->tax->calculate($option_price * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax'));
				$special = $price / $special_koefficient;
				$json['special'] = $this->currency->format($special, $currency);
				$json['special_n'] = $json['price_n'] / $special_koefficient;
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function update_discount($product_id, $quantity) {
		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$customer_group_id = ($this->customer->isLogged()) ? (int)$this->customer->getGroupId() : (int)$this->config->get('config_customer_group_id');
		$price = $product_info['price'];
		$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
		if ($product_discount_query->num_rows) {
		$price = $product_discount_query->row['price'];
		}
		$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
		if ($product_special_query->num_rows) {
			$price = $product_special_query->row['price'];
		}       
		return $price;
	}

	protected function prepareBreadcrumbs($product_info) {
    $breadcrumbs = array();
    $breadcrumbs[] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/home')
    );

		if (isset($this->request->get['path'])) {
    	// Обработка path для категорий
    	$category_path = '';
    	
    	if (isset($this->request->get['path'])) {
      	$parts = explode('_', (string)$this->request->get['path']);
      	
      	$current_path = '';
      	foreach ($parts as $path_id) {
        	$current_path = $current_path ? $current_path . '_' . (int)$path_id : (int)$path_id;
        	$category = $this->model_catalog_category->getCategory($path_id);
        	
        	if ($category) {
          	$breadcrumbs[] = array(
            	'text' => $category['name'],
            	'href' => $this->url->link('product/category', 'path=' . $current_path . $this->buildUrl(['path']))
          	);
        	}
      	}
    	}
		}

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$this->load->model('catalog/manufacturer');

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		$url = '';

		if (isset($this->request->get['path'])) {
			$url .= '&path=' . $this->request->get['path'];
		}

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['manufacturer_id'])) {
			$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
		}

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}

		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . $this->request->get['tag'];
		}

		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		if (isset($this->request->get['sub_category'])) {
			$url .= '&sub_category=' . $this->request->get['sub_category'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$breadcrumbs[] = array(
			'text' => $product_info['name'],
			'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
		);

		return $breadcrumbs;
	}

	protected function prepareProductImages($product_info) {
    $images = [];
    $results = $this->model_catalog_product->getProductImages($product_info['product_id']);
    
    foreach ($results as $result) {
      $images[] = [
        'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
        'additional' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')),
        'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
        'video' => $result['video']
      ];
    }
    
    return $images;
	}
	protected function prepareProductOptions($product_id) {
		$options = array();
		foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
			$product_option_value_data = array();

			foreach ($option['product_option_value'] as $option_value) {
				if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
					if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
						$price = round($option_value['price'], 2);
					} else {
						$price = false;
					}

					$product_option_value_data[] = array(
						'product_option_value_id' => $option_value['product_option_value_id'],
						'option_value_id'         => $option_value['option_value_id'],
						'name'                    => $option_value['name'],
							'quantity' => $option_value['quantity'],
							'model' => $option_value['model'],
						'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
						'price'                   => $price,
						'price_prefix'            => $option_value['price_prefix']
					);
				}
			}

			$options[] = array(
				'product_option_id'    => $option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $option['option_id'],
				'name'                 => $option['name'],
				'type'                 => $option['type'],
				'value'                => $option['value'],
				'required'             => $option['required']
			);
		}
		return $options;
	}

	public function getReviews() {
		$this->load->language('product/product');
		$this->load->language('revolution/revolution');
		$this->load->model('catalog/review');

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$data['entry_answer'] = $this->language->get('entry_answer');
		$data['reviews'] = array();

		$total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);
		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'answer' => nl2br($result['answer']),
				'answer_from' => $result['answer_from'],
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($total - 5)) ? $total : ((($page - 1) * 5) + 5), $total, ceil($total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function getAnswers() {
		$this->load->language('revolution/revolution');
    $this->load->model('revolution/revolution');
		$product_id = $this->request->get['product_id'];

		$page = isset($this->request->get['page_answers']) ? $this->request->get['page_answers'] : 1;
    
		$data['entry_answer'] = $this->language->get('entry_answer');
		$data['answers'] = array();
    
    $total = $this->model_revolution_revolution->gettotalanswers($product_id);
    $answers = $this->model_revolution_revolution->getanswers($product_id, ($page - 1) * 10, 10);
    
    foreach ($answers as $answer) {
      $data['answers'][] = [
        'author' => $answer['author'],
        'text' => nl2br($answer['text']),
				'answer'     	=> html_entity_decode($answer['answer'], ENT_QUOTES, 'UTF-8'),
				'answer_from'   => $answer['answer_from'],
				'date_added' 	=> date($this->language->get('date_format_short'), strtotime($answer['date_added']))
      ];
    }
    
    $pagination = new Pagination();
    $pagination->total = $total;
    $pagination->page = $page;
    $pagination->limit = 10;
    $pagination->url = $this->url->link('product/product/answers', 'product_id='.$product_id.'&page_answers={page}');
    
    $data['pagination'] = $pagination->render();
    $data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($total - 10)) ? $total : ((($page - 1) * 10) + 10), $total, ceil($total / 10));
    
		$this->response->setOutput($this->load->view('product/answer', $data));
	}

	public function writeReview() {
		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$validForm = $this->validateForm($this->request->post, ['name', 'text', 'rating']);
			$validCaptcha = $this->validateCaptcha('review');
			if ($validForm && $validCaptcha) {
				$this->load->model('catalog/review');
				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);
				$json['success'] = 'Спасибо за ваш отзыв. Он появится после проверки на спам';
			} else {
				$json['error'] = 'Отправленные данные не корректны';
			}
		}
		ob_clean();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function writeAnswer() {
		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$validForm = $this->validateForm($this->request->post, ['name', 'text']);
			$validCaptcha = $this->validateCaptcha('answer');

			if ($validForm && $validCaptcha) {
    		$this->load->model('revolution/revolution');
				$this->model_revolution_revolution->addanswer($this->request->get['product_id'], $this->request->post);
				$json['success'] = 'Спасибо за ваш вопрос. Он появится после проверки на спам';
			} else {
				$json['error'] = 'Отправленные данные не корректны';
			}
		}
		ob_clean();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
