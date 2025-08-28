<?php
require_once('catalog/controller/trait/cookie.php');

class ControllerProductCompare extends Controller {
	use \CookieTrait;

	public function index() {
		$this->load->language('revolution/revcompare');
		$this->load->language('revolution/revolution');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$compare_data = $this->getCookie('compare') ?? [];
		$product_ids = array_map('intval', $compare_data);
    $products = $this->model_catalog_product->getProductsByIds(['filter_product_ids' => $product_ids]);

		if ($this->config->get('revtheme_meta')['comp_meta_title'][(int)$this->config->get('config_language_id')]) {
			$this->document->setTitle($this->config->get('revtheme_meta')['comp_meta_title'][(int)$this->config->get('config_language_id')]);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}
		if ($this->config->get('revtheme_meta')['comp_meta_description'][(int)$this->config->get('config_language_id')]) {
			$this->document->setDescription($this->config->get('revtheme_meta')['comp_meta_description'][(int)$this->config->get('config_language_id')]);
		}
		if ($this->config->get('revtheme_meta')['comp_meta_keyword'][(int)$this->config->get('config_language_id')]) {
			$this->document->setKeywords($this->config->get('revtheme_meta')['comp_meta_keyword'][(int)$this->config->get('config_language_id')]);
		}

		$basicBreadcrumbs = $this->load->controller('extension/module/breadcrumbs/getBasicBreadcrumbs');
		$data['breadcrumbs'] = array_merge($basicBreadcrumbs, [[
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('product/compare')
		]]);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['text_empty'] = $this->language->get('text_empty');
		$data['review_status'] = $this->config->get('config_review_status');
		$settings = $this->config->get('revtheme_cat_compare');
		
		$data['cat_sort'] = $settings['cat_sort'];
		$data['main_cat'] = $settings['main_cat'];
		$data['compare_price'] = $settings['compare_price'];
		$data['compare_model'] = $settings['compare_model'];
		$data['compare_sku'] = $settings['compare_sku'];
		$data['compare_manuf'] = $settings['compare_manuf'];
		$data['compare_stock'] = $settings['compare_stock'];
		$data['compare_rate'] = $settings['compare_rate'];
		$data['compare_srtdesc'] = $settings['compare_srtdesc'];
		$data['compare_weight'] = $settings['compare_weight'];
		$data['compare_razmer'] = $settings['compare_razmer'];
		$data['compare_atrib'] = $settings['compare_atrib'];
		$data['compare_atribgr'] = $settings['compare_atribgr'];
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
		$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
		$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
		$data['text_catalog_stiker_netu_2'] = $this->language->get('text_catalog_stiker_netu');
		$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
		$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');

		$this->load->model('revolution/revolution');
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		
		$product_settings = $this->config->get('revtheme_product_all');
		$data['zakaz'] = $product_settings['zakaz'];
		$data['zakaz_price_null'] = $product_settings['zakaz_price_null'];
		$config_image_compare_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_width');
		$config_image_compare_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_height');
		$currency = $this->session->data['currency'];
		
		$data['products'] = array();
		$data['attribute_groups'] = array();

		if ($settings['cat_sort']) {
			foreach ($products as $product_info) {
				// $product_info = $this->model_catalog_product->getProduct($product_id);
				
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $config_image_compare_width, $config_image_compare_height);
						$data['image_width'] = $config_image_compare_width . 'px';
						$data['image_height'] = $config_image_compare_height . 'px';
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $config_image_compare_width, $config_image_compare_height);
						$data['image_width'] = false;
						$data['image_height'] = false;
					}
					
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
						$price_number = $product_info['price'];
					} else {
						$price = false;
						$price_number = false;
					}
					
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
						$special_number = $product_info['special'];
					} else {
						$special = false;
						$special_number = false;
					}
				
					if ($product_info['quantity'] <= 0) {
						$availability = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$availability = $product_info['quantity'];
					} else {
						$availability = $this->language->get('text_product_instock');
					}
					
					$attribute_data = array();
					
					$attribute_groups = $this->model_catalog_product->getProductAttributes($product_id);
					
					foreach ($attribute_groups as $attribute_group) {
						foreach ($attribute_group['attribute'] as $attribute) {
							$attribute_data[$attribute['attribute_id']] = $attribute['text'];
						}
					}					
					
					// if (isset($this->session->data['compare'])) {
					// 	if (in_array($product_id, $this->session->data['compare'])) {
					// 		$compare_class = 'in-compare';
					// 		$button_compare = $this->language->get('button_compare_out');
					// 	} else {
					// 		$compare_class = '';
					// 		$button_compare = $this->language->get('button_compare');
					// 	}
					// } else {
					// 	$compare_class = '';
					// 	$button_compare = $this->language->get('button_compare');
					// }
					// if (isset($this->session->data['wishlist'])) {
					// 	if (in_array($product_id, $this->session->data['wishlist'])) {
					// 		$wishlist_class = 'in-wishlist';
					// 		$button_wishlist = $this->language->get('button_wishlist_out');
					// 	} else {
					// 		$wishlist_class = '';
					// 		$button_wishlist = $this->language->get('button_wishlist');
					// 	}
					// } else {
					// 	$wishlist_class = '';
					// 	$button_wishlist = $this->language->get('button_wishlist');
					// }
					// if ($this->customer->isLogged()) {
					// 	$this->load->model('account/wishlist');
					// 	$wishlist_register = $this->model_account_wishlist->getWishlist();
					// 	if ($wishlist_register) {
					// 		$wishlist_register2 = array();
					// 		foreach ($wishlist_register as $result_wishlist_register_id) {
					// 			$wishlist_register_id[] = $result_wishlist_register_id['product_id'];
					// 		}
					// 		if (in_array($product_id, $wishlist_register_id)) {
					// 			$wishlist_class = 'in-wishlist';
					// 			$button_wishlist = $this->language->get('button_wishlist_out');
					// 		} else {
					// 			$wishlist_class = '';
					// 			$button_wishlist = $this->language->get('button_wishlist');
					// 		}
					// 	}
					// }
					
					
					if ($settings_stikers['upc']) {
						$stiker_upc = $product_info['upc'];
					} else {
						$stiker_upc = false;
					}
					if ($settings_stikers['ean']) {
						$stiker_ean = $product_info['ean'];
					} else {
						$stiker_ean = false;
					}
					if ($settings_stikers['jan']) {
						$stiker_jan = $product_info['jan'];
					} else {
						$stiker_jan = false;
					}
					if ($settings_stikers['isbn']) {
						$stiker_isbn = $product_info['isbn'];
					} else {
						$stiker_isbn = false;
					}
					if ($this->config->get('revtheme_product_all')['ed_izm']) {
						$ed_izm = $product_info['isbn'];
					} else {
						$ed_izm = false;
					}
					if ($settings_stikers['mpn']) {
						if ($settings_stikers['mpn_to_model']) {
							$stiker_mpn = $product_info['model'];
						} else {
							$stiker_mpn = $product_info['mpn'];
						}
					} else {
						$stiker_mpn = false;
					}
					if ($settings_stikers['stiker_netu_stock']) {
						$text_catalog_stiker_netu = $product_info['stock_status'];
					} else {
						$text_catalog_stiker_netu = $this->language->get('text_catalog_stiker_netu');
					}
					if ($settings_stikers['sklad_status']) {
						$stiker_sklad_status = $product_info['stock_status'];
					} else {
						$stiker_sklad_status = false;
					}
					
					$brand = $this->model_revolution_revolution->get_pr_brand($product_info['product_id']);
					
					$reviews_number = (int)$product_info['reviews'];
					$textcart_array = array('text_reviews_1', 'text_reviews_2', 'text_reviews_3');
					$textcart = $this->getcartword($reviews_number, $textcart_array);
					$reviews = sprintf($this->language->get($textcart), (int)$product_info['reviews']);
					
					$data['products'][$product_id] = array(
						'price_number' => $price_number,
						'special_number' => $special_number,
						'stiker_upc' => $stiker_upc,
						'stiker_ean' => $stiker_ean,
						'stiker_jan' => $stiker_jan,
						'stiker_isbn' => $stiker_isbn,
						'stiker_mpn' => $stiker_mpn,
						'text_catalog_stiker_netu' => $text_catalog_stiker_netu,
						'quantity' => $product_info['quantity'],
						'product_id'   => $product_info['product_id'],
						'name'         => $product_info['name'],
						'brand'    	   => $brand,
						'thumb'        => $image,
						'price'        => $price,
						'special'      => $special,
						'description'  => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
						'model'        => $product_info['model'],
						'sku'          => $product_info['sku'],
						'manufacturer' => $product_info['manufacturer'],
						'availability' => $availability,
						'minimum'      => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
						'rating'       => number_format($product_info['rating'], 1),
						'mpn'          => $product_info['mpn'],
						'reviews'      => $reviews,
						'weight'       => $this->weight->format($product_info['weight'], $product_info['weight_class_id']),
						'length'       => $this->length->format($product_info['length'], $product_info['length_class_id']),
						'width'        => $this->length->format($product_info['width'], $product_info['length_class_id']),
						'height'       => $this->length->format($product_info['height'], $product_info['length_class_id']),
						'attribute'    => $attribute_data,
						'href'         => $this->url->link('product/product', 'product_id=' . $product_id),
						'remove'       => $this->url->link('product/compare', 'remove=' . $product_id)
					);
					
					foreach ($attribute_groups as $attribute_group) {
						$data['attribute_groups'][$attribute_group['attribute_group_id']]['name'] = $attribute_group['name'];
						
						foreach ($attribute_group['attribute'] as $attribute) {
							$data['attribute_groups'][$attribute_group['attribute_group_id']]['attribute'][$attribute['attribute_id']]['name'] = $attribute['name'];
						}
					}
				}
			}
			$brands = array();
				foreach( $data['products'] as $product ) {
					if($product['brand']) {
						$brands[$product['brand']]['products'][] = $product;
					} else {
						$brands['No name']['products'][] = $product;
					}
				}
			uksort($brands, 'strcasecmp');
			$data['products'] = $brands;
		} else {
			foreach ($products as $product_info) {
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $config_image_compare_width, $config_image_compare_height);
						$data['image_width'] = $config_image_compare_width . 'px';
						$data['image_height'] = $config_image_compare_height . 'px';
					} else {
						$image = false;
						$data['image_width'] = false;
						$data['image_height'] = false;
					}
					
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
						$price_number = $product_info['price'];
					} else {
						$price = false;
						$price_number = false;
					}
					
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $currency);
						$special_number = $product_info['special'];
					} else {
						$special = false;
						$special_number = false;
					}
				
					if ($product_info['quantity'] <= 0) {
						$availability = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$availability = $product_info['quantity'];
					} else {
						$availability = $this->language->get('text_product_instock');
					}
					
					$attribute_data = array();
					
					$attribute_groups = $this->model_catalog_product->getProductAttributes($product_info['product_id']);
					
					foreach ($attribute_groups as $attribute_group) {
						foreach ($attribute_group['attribute'] as $attribute) {
							$attribute_data[$attribute['attribute_id']] = $attribute['text'];
						}
					}					
					
					if ($settings_stikers['upc']) {
						$stiker_upc = $product_info['upc'];
					} else {
						$stiker_upc = false;
					}
					if ($settings_stikers['ean']) {
						$stiker_ean = $product_info['ean'];
					} else {
						$stiker_ean = false;
					}
					if ($settings_stikers['jan']) {
						$stiker_jan = $product_info['jan'];
					} else {
						$stiker_jan = false;
					}
					if ($settings_stikers['isbn']) {
						$stiker_isbn = $product_info['isbn'];
					} else {
						$stiker_isbn = false;
					}
					if ($this->config->get('revtheme_product_all')['ed_izm']) {
						$ed_izm = $product_info['isbn'];
					} else {
						$ed_izm = false;
					}
					if ($settings_stikers['mpn']) {
						if ($settings_stikers['mpn_to_model']) {
							$stiker_mpn = $product_info['model'];
						} else {
							$stiker_mpn = $product_info['mpn'];
						}
					} else {
						$stiker_mpn = false;
					}
					if ($settings_stikers['stiker_netu_stock']) {
						$text_catalog_stiker_netu = $product_info['stock_status'];
					} else {
						$text_catalog_stiker_netu = $this->language->get('text_catalog_stiker_netu');
					}
					if ($settings_stikers['sklad_status']) {
						$stiker_sklad_status = $product_info['stock_status'];
					} else {
						$stiker_sklad_status = false;
					}
					
					$brand = $this->model_revolution_revolution->get_pr_brand($product_info['product_id']);
					
					$reviews_number = (int)$product_info['reviews'];
					$textcart_array = array('text_reviews_1', 'text_reviews_2', 'text_reviews_3');
					$textcart = $this->getcartword($reviews_number, $textcart_array);
					$reviews = sprintf($this->language->get($textcart), (int)$product_info['reviews']);
					
					$data['products'][$product_info['product_id']] = array(
						'price_number' => $price_number,
						'special_number' => $special_number,
						'stiker_upc' => $stiker_upc,
						'stiker_ean' => $stiker_ean,
						'stiker_jan' => $stiker_jan,
						'stiker_isbn' => $stiker_isbn,
						'stiker_mpn' => $stiker_mpn,
						'quantity' => $product_info['quantity'],
						'product_id'   => $product_info['product_id'],
						'name'         => $product_info['name'],
						'brand'    	   => $brand,
						'thumb'        => $image,
						'price'        => $price,
						'special'      => $special,
						'description'  => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
						'model'        => $product_info['model'],
						'sku'          => $product_info['sku'],
						'manufacturer' => $product_info['manufacturer'],
						'availability' => $availability,
						'minimum'      => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
						'rating'       => number_format($product_info['rating'], 1),
						'mpn'          => $product_info['mpn'],
						'reviews'      => $reviews,
						'weight'       => $this->weight->format($product_info['weight'], $product_info['weight_class_id']),
						'length'       => $this->length->format($product_info['length'], $product_info['length_class_id']),
						'width'        => $this->length->format($product_info['width'], $product_info['length_class_id']),
						'height'       => $this->length->format($product_info['height'], $product_info['length_class_id']),
						'attribute'    => $attribute_data,
						'href'         => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'remove'       => $this->url->link('product/compare', 'remove=' . $product_info['product_id']),
						'text_catalog_stiker_netu' => $text_catalog_stiker_netu
					);
					
					foreach ($attribute_groups as $attribute_group) {
						$data['attribute_groups'][$attribute_group['attribute_group_id']]['name'] = $attribute_group['name'];
						
						foreach ($attribute_group['attribute'] as $attribute) {
							$data['attribute_groups'][$attribute_group['attribute_group_id']]['attribute'][$attribute['attribute_id']]['name'] = $attribute['name'];
						}
					}
				}
			}	
		}
		
		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('product/compare', $data));
	}
	
	public function getcartword($number, $suffix) {
		$keys = array(2, 0, 1, 1, 1, 2);
		$mod = $number % 100;
		$suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
		return $suffix[$suffix_key];
	}
}
