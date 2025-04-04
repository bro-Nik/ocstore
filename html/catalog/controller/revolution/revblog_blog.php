<?php
class ControllerRevolutionRevblogBlog extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('revolution/revolution');
		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		
		$setting = $this->config->get('revtheme_home_blog');
		$setting2 = $this->config->get('revblog_settings');
		$data['share_status'] = $setting2['share_status'];
		$data['share_status_code'] = html_entity_decode($this->config->get('revtheme_product_all')['share_status_code'], ENT_QUOTES, 'UTF-8');
		$data['main_image_status'] = isset($setting2['main_image_status']) ? $setting2['main_image_status'] : true;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$category_info = false;

		if (isset($this->request->get['revblog_category_id'])) {
			$revblog_category_id = '';

			$parts = explode('_', (string)$this->request->get['revblog_category_id']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $blog_category) {

				if (!$revblog_category_id) {
					$revblog_category_id = $blog_category;
				} else {
					$revblog_category_id .= '_' . $blog_category;
				}

				$category_info = $this->model_revolution_revolution->getBlogCategory($blog_category);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['title'],
						'href' => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $revblog_category_id)
					);
				}
			}

			$category_info = $this->model_revolution_revolution->getBlogCategory($category_id);

			if ($category_info) {
				$data['breadcrumbs'][] = array(
					'text' => $category_info['title'],
					'href' => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $this->request->get['revblog_category_id'])
				);
			}
		}

		if (isset($this->request->get['revblog_id'])) {
			$data['revblog_id'] = $revblog_id = (int)$this->request->get['revblog_id'];
		} else {
			$data['revblog_id'] = $revblog_id = 0;
		}

		$blog_info = $this->model_revolution_revolution->getBlog($revblog_id);
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
		$data['logo_width'] = '200';
		$data['logo_height'] = '200';
		$data['microdata_author'] = $data['microdata_name'] = $this->config->get('config_name');
		$data['microdata_date_info'] = '';
		$data['microdata_url_info'] = $this->url->link('revolution/revblog_blog', 'revblog_id=' . $revblog_id);

		if ($blog_info) {
			
			if ($blog_info['image']) {
				$data['logo'] = $server . 'image/' . $blog_info['image'];
			} else {
				if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
					$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
				} else {
					$data['logo'] = '';
				}
			}
			$data['logo_width'] = '200';
			$data['logo_height'] = '200';
			$data['microdata_author'] = $data['microdata_name'] = $this->config->get('config_name');
			$data['microdata_date_info'] = date('Y-m-d', strtotime($blog_info['date_available']));
			$data['microdata_url_info'] = $this->url->link('revolution/revblog_blog', 'revblog_id=' . $revblog_id);
			
			$url = '';

			if (isset($this->request->get['revblog_category_id'])) {
				$url .= '&revblog_category_id=' . $this->request->get['revblog_category_id'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $blog_info['title'],
				'href' => $this->url->link('revolution/revblog_blog', $url . '&revblog_id=' . $revblog_id)
			);

			if ($blog_info['title_pr'] != '') {
				$data['heading_products_title'] = $blog_info['title_pr'];
			} else {
				$data['heading_products_title'] = $this->language->get('heading_products_title_blog');
			}
			
			if ($blog_info['meta_title']) {
				$this->document->setTitle($blog_info['meta_title']);
			} else {
				$this->document->setTitle($blog_info['title']);
			}
			$this->document->setDescription($blog_info['meta_description']);
			$this->document->setKeywords($blog_info['meta_keyword']);
			$this->document->addLink($this->url->link('revolution/revblog_blog', 'revblog_id=' . $revblog_id), 'canonical');

			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			
			$data['description'] = html_entity_decode($blog_info['description'], ENT_QUOTES, 'UTF-8');
			if ($this->config->get('revtheme_geo_set')['status']) {
				require_once(DIR_SYSTEM . 'library/revolution/SxGeo.php');
				$SxGeo = new SxGeo();
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				$ip_city = $SxGeo->getCity($ip)['city']['name_ru'];
				$ip_region = $SxGeo->getCityFull($ip)['region']['name_ru'];
				$rev_geo_data = $this->config->get('revtheme_geo');
				$data['rev_geos'] = array();
				if (!empty($rev_geo_data)){
					foreach ($rev_geo_data as $rev_geo) {
						if ($ip_city == $rev_geo['city'] || $ip_region == $rev_geo['city']) {
							$data['rev_geos'][] = array(
								'code' => $rev_geo['code'],
								'text' => $rev_geo['text'][$this->config->get('config_language_id')]
							);
						}
					}
				}
				foreach ($data['rev_geos'] as $rev_geo) {
					if (strpos($blog_info['description'], $rev_geo['code'])) {
						$data['description'] = html_entity_decode(str_replace($rev_geo['code'], $rev_geo['text'], $blog_info['description']), ENT_QUOTES, 'UTF-8');
					}
				}
			}
			$data['data_added'] = date($this->language->get('date_format_short'), strtotime($blog_info['date_available']));
			$data['revpopuporder_settings'] = $revpopuporder_settings = $this->config->get('revtheme_catalog_popuporder');
			$data['revpopuporder'] = $revpopuporder_settings['status'];

			$data['heading_title'] = $blog_info['title'];
			
			$config_image_popup_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
			$config_image_popup_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
			$config_image_product_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
			$config_image_product_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
			$currency = $this->session->data['currency'];
			$config_product_description_length = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length');

			$img_w = $setting2['form_image_width'];
			$img_h = $setting2['form_image_height'];
			
			$img_w2 = $setting2['images_image_width'];
			$img_h2 = $setting2['images_image_height'];

			if (!empty($img_w)) {
				$image_width = $img_w;
			} else {
				$image_width = 228;
			}
			
			if (!empty($img_h)) {
				$image_height = $img_h;
			} else {
				$image_height = 228;
			}

			if ($blog_info['image']) {
				$data['image'] = $this->model_tool_image->resize($blog_info['image'], $image_width, $image_height);
			} else {
				$data['image'] = false;
			}
			
			$data['images'] = array();
			$results = $this->model_revolution_revolution->getBlogImages($revblog_id);
			if ($results) {
				$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
				$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
				foreach ($results as $result) {
					$data['images'][] = array(
						'original'	=> HTTP_SERVER.'image/'.$result['image'],
						'popup' 	=> $this->model_tool_image->resize($result['image'], $config_image_popup_width, $config_image_popup_height),
						'thumb' 	=> $this->model_tool_image->resize($result['image'], $img_w2, $img_h2)
					);
				}
			}

			$data['continue'] = $this->url->link('common/home');
			
			$data['products'] = array();
			$products_results = $this->model_revolution_revolution->getBlogProducts($revblog_id);
			if ($products_results) {
				$settings_stikers = $this->config->get('revtheme_catalog_stiker');
				if ($settings_stikers['status']) {
					$data['stikers_status'] = true;

					if ($settings_stikers['new_status']) {
						$settings_last = $this->config->get('revtheme_catalog_last');
						if ($settings_last['last_products'] != '') {
							$date_added = array_flip(explode(',', $settings_last['last_products']));
						} else {
							if ($settings_last['filter_day']) {
								$filter_day = date('Y-m-d H:i:s', strtotime('now - ' . $settings_last['filter_day'] . ' days'));
							} else {
								$filter_day = false;
							}
							$data_last = array(
								'sort'  => 'p.date_added',
								'order' => 'DESC',
								'start' => 0,
								'limit' => $settings_last['limit'],
								'filter_day' => $filter_day
								);
							$date_added = $this->model_revolution_revolution->getLastProducts($data_last);
						}
					}

					if ($settings_stikers['best_status']) {
						$settings_best = $this->config->get('revtheme_catalog_best');
						if ($settings_best['best_products'] != '') {
							$best_seller = array_flip(explode(',', $settings_best['best_products']));
						} else {
							$data_best = array(
								'sort'  => 'p.sales',
								'order' => 'DESC',
								'start' => 0,
								'limit' => $settings_best['limit'],
								'filter_buy' => $settings_best['filter_buy']
							);
							$best_seller = $this->model_revolution_revolution->getBestProducts($data_best);
						}
					}
				} else {
					$data['stikers_status'] = false;
				}
				
				$data['setting_all_settings'] = $this->config->get('revtheme_all_settings');
				$data['revpopuporder_settings'] = $revpopuporder_settings = $this->config->get('revtheme_catalog_popuporder');
				$data['revpopuporder'] = $revpopuporder_settings['status'];
				$product_settings = $this->config->get('revtheme_product_all');
				$data['zakaz'] = $product_settings['zakaz'];
				$data['zakaz_price_null'] = $product_settings['zakaz_price_null'];
				$data['recalc_price'] = $product_settings['recalc_price'];
				$data['q_zavisimost'] = $product_settings['q_zavisimost'];
				$data['opt_price'] = $product_settings['opt_price'];
				$data['zakaz_price_null'] = $product_settings['zakaz_price_null'];
				$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
				$data['popup_view'] = $setting_catalog_all['popup_view'];
				$data['img_slider'] = $setting_catalog_all['img_slider'];
				$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
				$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
				$data['ch_quantity'] = $setting_catalog_all['ch_quantity'];
				$data['chislo_ryad'] = $setting_catalog_all['chislo_ryad'];
				$description_options = $this->config->get('revtheme_cat_attributes');
				$data['description_options'] = $this->config->get('revtheme_cat_attributes');
				$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
				$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
				$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
				$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');
				$revtheme_home_all = $this->config->get('revtheme_home_all');
				$data['currency_code'] = $this->session->data['currency'];
				$var_currency = array();
				$var_currency['value'] = $this->currency->getValue($currency);
				$var_currency['symbol_left'] = $this->currency->getSymbolLeft($currency);
				$var_currency['symbol_right'] = $this->currency->getSymbolRight($currency);
				$var_currency['decimals'] = $this->currency->getDecimalPlace($currency);
				$var_currency['decimal_point'] = $this->language->get('decimal_point');
				$var_currency['thousand_point'] = $this->language->get('thousand_point');
				$data['currency'] = $var_currency;
				if ($this->config->get('revtheme_all_settings')['mobile_on']) {
					$is_mobile = $data['is_mobile'] = $this->mobiledetect->isMobile();
					$is_desctope = $data['is_desctope'] = !$this->mobiledetect->isMobile() || $this->mobiledetect->isTablet();
				} else {
					$is_mobile = $data['is_mobile'] = true;
					$is_desctope = $data['is_desctope'] = true;
				}

				foreach ($products_results as $result) {
					
					if (isset($this->session->data['compare'])) {
						if (in_array($result['product_id'], $this->session->data['compare'])) {
							$compare_class = 'in_compare';
							$button_compare = $this->language->get('button_compare_out');
						} else {
							$compare_class = '';
							$button_compare = $this->language->get('button_compare');
						}
					} else {
						$compare_class = '';
						$button_compare = $this->language->get('button_compare');
					}
					if (isset($this->session->data['wishlist'])) {
						if (in_array($result['product_id'], $this->session->data['wishlist'])) {
							$wishlist_class = 'in_wishlist';
							$button_wishlist = $this->language->get('button_wishlist_out');
						} else {
							$wishlist_class = '';
							$button_wishlist = $this->language->get('button_wishlist');
						}
					} else {
						$wishlist_class = '';
						$button_wishlist = $this->language->get('button_wishlist');
					}
					
					if ($this->customer->isLogged()) {
						$this->load->model('account/wishlist');
						$wishlist_register = $this->model_account_wishlist->getWishlist();
						if ($wishlist_register) {
							$wishlist_register2 = array();
							foreach ($wishlist_register as $result_wishlist_register_id) {
								$wishlist_register_id[] = $result_wishlist_register_id['product_id'];
							}
							if (in_array($result['product_id'], $wishlist_register_id)) {
								$wishlist_class = 'in_wishlist';
								$button_wishlist = $this->language->get('button_wishlist_out');
							} else {
								$wishlist_class = '';
								$button_wishlist = $this->language->get('button_wishlist');
							}
						}
					}
					
					if ($settings_stikers['new_status']) {
						if (isset($date_added[$result['product_id']])) {
							$stiker_last = true;
						} else {
							$stiker_last = false;
						}
					} else {
						$stiker_last = false;
					}
					
					if ($settings_stikers['best_status']) {
						if (isset($best_seller[$result['product_id']])) {
							$stiker_best = true;	
						} else {
							$stiker_best = false;
						}
					} else {
						$stiker_best = false;
					}
					
					if ($settings_stikers['spec_status']) {
						$stiker_spec = true;
					} else {
						$stiker_spec = false;
					}
					
					if ($settings_stikers['stock_status']) {
						$stiker_stock = true;
					} else {
						$stiker_stock = false;
					}
					
					if ($settings_stikers['upc']) {
						$stiker_upc = $result['upc'];
					} else {
						$stiker_upc = false;
					}
					if ($settings_stikers['ean']) {
						$stiker_ean = $result['ean'];
					} else {
						$stiker_ean = false;
					}
					if ($settings_stikers['jan']) {
						$stiker_jan = $result['jan'];
					} else {
						$stiker_jan = false;
					}
					if ($settings_stikers['isbn']) {
						$stiker_isbn = $result['isbn'];
					} else {
						$stiker_isbn = false;
					}
					if ($this->config->get('revtheme_product_all')['ed_izm']) {
						$ed_izm = $result['isbn'];
					} else {
						$ed_izm = false;
					}
					if ($settings_stikers['mpn']) {
						if ($settings_stikers['mpn_to_model']) {
							$stiker_mpn = $result['model'];
						} else {
							$stiker_mpn = $result['mpn'];
						}
					} else {
						$stiker_mpn = false;
					}
					if ($settings_stikers['stiker_netu_stock']) {
						$text_catalog_stiker_netu = $result['stock_status'];
					} else {
						$text_catalog_stiker_netu = $this->language->get('text_catalog_stiker_netu');
					}
					if ($settings_stikers['sklad_status']) {
						$stiker_sklad_status = $result['stock_status'];
					} else {
						$stiker_sklad_status = false;
					}
					
					$brand = $this->model_revolution_revolution->get_pr_brand($result['product_id']);
					
					if ($result['image']) {
						if ($setting_catalog_all['img_slider'] && $is_desctope) {
							$images = array();
							$this->load->model('catalog/product');
							$add_images = $this->model_catalog_product->getProductImages($result['product_id']);
							if ($setting_catalog_all['img_slider'] == '2') {
								$add_images = array_slice($add_images, 0, 1);
							}
							foreach ($add_images as $add_image) {
								$images[] = array(
									'thumb' => $this->model_tool_image->resize($add_image['image'], $config_image_product_width, $config_image_product_height)
								);
							}
						} else {
							$images = false;
						}
						$image = $this->model_tool_image->resize($result['image'], $config_image_product_width, $config_image_product_height);
					} else {
						$images = false;
						$image = $this->model_tool_image->resize('placeholder.png', $config_image_product_width, $config_image_product_height);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $currency);
						$price_number = $result['price'];
					} else {
						$price = false;
						$price_number = false;
					}

					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $currency);
						$special_number = $result['special'];
					} else {
						$special = false;
						$special_number = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $currency);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					
					$description = '';
					$options = false;
					if ($description_options['description_on']) {
						if ($description_options['zamena_description']) {
							$description = $this->model_revolution_revolution->getAttrText($result['product_id']);
						} else {
							$description = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $config_product_description_length) . '...';
						}
					}
					if ($description_options['options_in_grid']) {
						$options = array();
						$cache = 'options_data.' . crc32((int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$result['product_id']);
						$pr_options = $this->cache->get($cache);
						if (!$pr_options) {
							$pr_options = $this->model_catalog_product->getProductOptions($result['product_id']);
							if ($this->config->get('revtheme_all_settings')['cache_on']) {$this->cache->set($cache, $pr_options);}
						}
					
						foreach ($pr_options as $option) {
							$product_option_value_data = array();

							foreach ($option['product_option_value'] as $option_value) {
								if ($option_value['quantity'] > 0 || $this->config->get('revtheme_product_all')['options_null_qw']) {
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $currency);
									} else {
										$option_price = false;
									}

									if ($option_value['price_prefix'] == '=') {
										$price_prefix = '';
									} else {
										$price_prefix = $option_value['price_prefix'];
									}
									
									$product_option_value_data[] = array(
										'product_option_value_id' => $option_value['product_option_value_id'],
										'quantity' 				  => $option_value['quantity'], 
										'model' 				  => $option_value['model'],
										'option_value_id'         => $option_value['option_value_id'],
										'name'                    => $option_value['name'],
										'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
										'price'                   => $option_price,
										'price_prefix'            => $price_prefix,
										'option_value_disabled'   => ($option_value['quantity'] > 0) ? false : true
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
								'required'             => $option['required'],
								'opt_image'            => $option['opt_image']
							);
						}
					}
					$product_in_cart = false;
					if ($setting_catalog_all['product_in_cart']) {
						$products_in_cart = $this->cart->getProducts();
						foreach ($products_in_cart as $product_cart) {
							if ($product_cart['product_id'] == $result['product_id']) {
								$product_in_cart = true;
							}
						}
					}

					$data['products'][] = array(
						'reviews' => $this->config->get('config_review_status') ? (int)$result['reviews'] : false,
						'ed_izm' => $ed_izm,
						'price_number' => $price_number,
						'special_number' => $special_number,
						'compare_class' => $compare_class,
						'wishlist_class' => $wishlist_class,
						'button_compare' => $button_compare,
						'button_wishlist' => $button_wishlist,
						'stiker_last' => $stiker_last,
						'stiker_best' => $stiker_best,
						'stiker_spec' => $stiker_spec,
						'stiker_stock' => $stiker_stock,
						'stiker_sklad_status' => $stiker_sklad_status,
						'stiker_upc' => $stiker_upc,
						'stiker_ean' => $stiker_ean,
						'stiker_jan' => $stiker_jan,
						'stiker_isbn' => $stiker_isbn,
						'stiker_mpn' => $stiker_mpn,
						'text_catalog_stiker_netu' => $text_catalog_stiker_netu,
						'brand' => $brand,
						'images' => $images,
						'options' => $options,
						'quantity' => $result['quantity'],
						'options_buy' => $result['options_buy'],
						'model' => $result['model'],
						'short_description' => html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8'),
						'product_in_cart' => $product_in_cart,
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						'name'        => $result['name'],
						'description' => $description,
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
						'rating'      => number_format($result['rating'], 1),
						'href'        => $this->url->link('product/product', '&product_id=' . $result['product_id'])
					);
				}
				
				if ($setting2['not_view_null'] && isset($data['products'])) {
					$product_settings = $this->config->get('revtheme_product_all');
					$zakaz = $product_settings['zakaz'];
					$zakaz_price_null = $product_settings['zakaz_price_null'];
					if (!$zakaz || !$zakaz_price_null) {
						foreach ($data['products'] as $key => $value) {
							if (!$zakaz) {
								$quantity[$key] = $value['quantity'];
							} else {
								$quantity[$key] = 2;
							}
							if (!$zakaz_price_null && $value['price_number'] == 0) {
								$price_number[$key] = 0;
							} else {
								$price_number[$key] = 1;
							}
							if ($quantity[$key] < 1 || $price_number[$key] == 0) {
								unset($data['products'][$key]);
							}
						}
					}
				}
			}
			
			$data['category_blog_grid'] = isset($setting2['category_blog_grid']) && $setting2['category_blog_grid'] ? $setting2['category_blog_grid'] : false;
			$data['blog_date_status'] = isset($setting2['blog_date_status']) && $setting2['blog_date_status'] ? $setting2['blog_date_status'] : false;
			$data['related_left_status'] = isset($setting2['related_left_status']) && $setting2['related_left_status'] ? $setting2['related_left_status'] : false;
			$data['revblog_id'] = $revblog_id;
			$data['blog_relateds'] = array();
			$blog_related_results = $this->model_revolution_revolution->getBlogRelated($revblog_id);
			if (!empty($setting2['list_desc_limit'])) {
				$description_limit = $setting2['list_desc_limit'];
			} else {
				$description_limit = 400;
			}
			if ($blog_related_results) {
				if ($this->config->get('revtheme_geo_set')['status']) {
					require_once(DIR_SYSTEM . 'library/revolution/SxGeo.php');
					$SxGeo = new SxGeo();
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ip = $_SERVER['HTTP_CLIENT_IP'];
					} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
					} else {
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					$ip_city = $SxGeo->getCity($ip)['city']['name_ru'];
					$ip_region = $SxGeo->getCityFull($ip)['region']['name_ru'];
					$rev_geo_data = $this->config->get('revtheme_geo');
					$data['rev_geos'] = array();
					if (!empty($rev_geo_data)){
						foreach ($rev_geo_data as $rev_geo) {
							if ($ip_city == $rev_geo['city'] || $ip_region == $rev_geo['city']) {
								$data['rev_geos'][] = array(
									'code' => $rev_geo['code'],
									'text' => $rev_geo['text'][$this->config->get('config_language_id')]
								);
							}
						}
					}
				}
				foreach ($blog_related_results as $blog_related_result) {
					if ($blog_related_result['image']) {
						$thumb = $this->model_tool_image->resize($blog_related_result['image'], $setting2['list_image_width'], $setting2['list_image_height']);
					} else {
						$thumb = $this->model_tool_image->resize('placeholder.png', $setting2['list_image_width'], $setting2['list_image_height']);
					}
					$description = utf8_substr(strip_tags(html_entity_decode($blog_related_result['description'], ENT_QUOTES, 'UTF-8')), 0, $description_limit) . '..';
					if ($this->config->get('revtheme_geo_set')['status']) {
						foreach ($data['rev_geos'] as $rev_geo) {
							if (strpos($blog_related_result['description'], $rev_geo['code'])) {
								$description = str_replace($rev_geo['code'], $rev_geo['text'], $blog_related_result['description']);
								$description = utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, $description_limit) . '..';
							}
						}
					}
					
					if (isset($this->request->get['revblog_category_id'])) {
						$url .= '&revblog_category_id=' . $this->request->get['revblog_category_id'];
					}
					
					$data['blog_relateds'][] = array(
						'revblog_id'  => $blog_related_result['revblog_id'],
						'description' => $description,
						'thumb'       => $thumb,
						'title'       => $blog_related_result['title'],
						'href'        => $this->url->link('revolution/revblog_blog', $url . '&revblog_id=' . $blog_related_result['revblog_id']),
						'data_added'  => isset($setting2['category_blog_grid']) && $setting2['category_blog_grid'] ? date('d.m', strtotime($blog_related_result['date_available'])) : date($this->language->get('date_format_short'), strtotime($blog_related_result['date_available']))
					);
				}
			}
			
			$data['review_status'] = isset($setting2['review_status']) && $setting2['review_status'] ? $setting2['review_status'] : false;
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}
			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$this->load->language('product/product');
				$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
				$data['review_guest'] = false;
			}
			
			$data['revblog_reviews'] = array();
			$data['review_total'] = $this->model_revolution_revolution->getTotalReviewsByBlogId($this->request->get['revblog_id']);
			$results_reviews = $this->model_revolution_revolution->getReviewsByBlogId($this->request->get['revblog_id']);
			foreach ($results_reviews as $result_review) {
				$revblog_parent_reviews = $this->model_revolution_revolution->getParentReviewsByBlogId($this->request->get['revblog_id'], $result_review['review_id']);
				$parent_reviews = array();
				foreach ($revblog_parent_reviews as $revblog_parent_review) {
					$parent_reviews[] = array(
						'review_id'				 => (int)$revblog_parent_review['review_id'],
						'author'           		 => $revblog_parent_review['author'],
						'text'       	   		 => nl2br($revblog_parent_review['text']),
						'date_added'       		 => date($this->language->get('date_format_short'), strtotime($revblog_parent_review['date_added']))
					);
				}
				$data['revblog_reviews'][] = array(
					'review_id'				 => (int)$result_review['review_id'],
					'revblog_parent_reviews' => $parent_reviews,
					'author'           		 => $result_review['author'],
					'text'       	   		 => nl2br($result_review['text']),
					'parent_review_id' 		 => (int)$result_review['parent_review_id'],
					'date_added'       		 => date($this->language->get('date_format_short'), strtotime($result_review['date_added']))
				);
			}


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('revolution/revblog_blog', $data));
			
		} else {
			$this->load->language('error/not_found');
			
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('revolution/revblog_blog', '&revblog_id=' . $revblog_id)
			);

			$this->document->setTitle($this->language->get('text_error'));
			
			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));

		}
	}
	
	public function write_review() {
		$this->load->language('product/product');
		$this->load->language('revolution/revolution');
		$json = array();
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_text_blog_name');
			}
			if ((utf8_strlen($this->request->post['text']) < 10) || (utf8_strlen($this->request->post['text']) > 2000)) {
				$json['error'] = $this->language->get('error_text_blog_review');
			}
			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				if (VERSION >= 2.2) {
					$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				} else {
					$captcha = $this->load->controller('captcha/' . $this->config->get('config_captcha') . '/validate');
				}
				if ($captcha) {
					$json['error'] = $captcha;
				}
			}
			if (!isset($json['error'])) {
				$this->load->model('revolution/revolution');
				$this->model_revolution_revolution->addBlogReview($this->request->get['revblog_id'], $this->request->post);
				$json['success'] = $this->language->get('text_blog_success');
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function related_left() {
		
		$setting2 = $this->config->get('revblog_settings');
		if (!isset($setting2['related_left_status']) || !$setting2['related_left_status']) {
			return false;
		}
		
		$this->load->language('revolution/revolution');
		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');
		
		if (isset($this->request->get['revblog_id'])) {
			$revblog_id = (int)$this->request->get['revblog_id'];
		} else {
			$revblog_id = 0;
		}
		
		$data['category_blog_grid'] = isset($setting2['category_blog_grid']) && $setting2['category_blog_grid'] ? $setting2['category_blog_grid'] : false;
		$data['blog_date_status'] = isset($setting2['blog_date_status']) && $setting2['blog_date_status'] ? $setting2['blog_date_status'] : false;
		$data['related_left_status'] = isset($setting2['related_left_status']) && $setting2['related_left_status'] ? $setting2['related_left_status'] : false;
		$data['revblog_id'] = $revblog_id;
		$data['blogs'] = array();
		$blog_related_results = $this->model_revolution_revolution->getBlogRelated($revblog_id);
		if (!empty($setting2['list_desc_limit'])) {
			$description_limit = $setting2['list_desc_limit'];
		} else {
			$description_limit = 400;
		}
		if ($blog_related_results) {
			foreach ($blog_related_results as $blog_related_result) {
				if ($blog_related_result['image']) {
					$thumb = $this->model_tool_image->resize($blog_related_result['image'], $setting2['list_image_width'], $setting2['list_image_height']);
				} else {
					$thumb = $this->model_tool_image->resize('placeholder.png', $setting2['list_image_width'], $setting2['list_image_height']);
				}
				$url = '';
				if (isset($this->request->get['revblog_category_id'])) {
					$url .= '&revblog_category_id=' . $this->request->get['revblog_category_id'];
				}
				$data['blogs'][] = array(
					'revblog_id'  => $blog_related_result['revblog_id'],
					'description' => false,
					'thumb'       => $thumb,
					'title'       => $blog_related_result['title'],
					'href'        => $this->url->link('revolution/revblog_blog', $url . '&revblog_id=' . $blog_related_result['revblog_id']),
					'data_added'  => isset($setting2['category_blog_grid']) && $setting2['category_blog_grid'] ? date('d.m', strtotime($blog_related_result['date_available'])) : date($this->language->get('date_format_short'), strtotime($blog_related_result['date_available']))
				);
			}
			if (!empty($data['blogs'])){
				foreach ($data['blogs'] as $key => $value) {
					$sort_t[$key] = $value['data_added'];
				}
				array_multisort($sort_t, SORT_DESC, $data['blogs']);
			}
		}
		$data['module'] = 'revbl_rel';
		$data['slider'] = false;
		$data['image_status'] = true;
		$data['data_status'] = true;
		$data['heading_title'] = $this->language->get('text_blog_related');
		if ($data['blogs']) {
			return $this->load->view('revolution/revblog_mod', $data);
		}
	}
	
}