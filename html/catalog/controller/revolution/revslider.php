<?php
class ControllerRevolutionRevslider extends Controller {
    protected $path = array();
	
	public function sliderstabs() {
		
		$revsliders_tab = $this->config->get('revtheme_home_all')['revsliders_tab'];
		if (!$revsliders_tab) {
			return false;
		}
		
		$this->load->language('revolution/revolution');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
		$this->load->model('revolution/revolution');
   
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
		$data['text_catalog_stiker_netu_2'] = $this->language->get('text_catalog_stiker_netu');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
		$data['popup_view'] = $setting_catalog_all['popup_view'];
		$data['img_slider'] = $setting_catalog_all['img_slider'];
		$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
		$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
		$data['ch_quantity'] = $setting_catalog_all['ch_quantity'];
		$data['chislo_ryad'] = $setting_catalog_all['chislo_ryad'];
		$data['text_catalog_stiker_last'] = $this->language->get('text_catalog_stiker_last');
		$data['text_catalog_stiker_best'] = $this->language->get('text_catalog_stiker_best');
		$data['text_catalog_revpopup_purchase'] = $this->language->get('text_catalog_revpopup_purchase');
		$data['text_catalog_revpopup_view'] = $this->language->get('text_catalog_revpopup_view');
		$data['text_catalog_price_na_zakaz'] = $this->language->get('text_catalog_price_na_zakaz');
		$description_options = $this->config->get('revtheme_cat_attributes');
		$data['description_options'] = $this->config->get('revtheme_cat_attributes');
		$data['text_select'] = $this->language->get('text_select');
		$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
		$data['text_option_option'] = $this->language->get('text_option_option');
		$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
		$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');
		$data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');
        $data['text_tax'] = $this->language->get('text_tax');
		$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
	
		$currency = $this->session->data['currency'];
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
		
		$setting_sliderstabs = $this->config->get('revtheme_home_all')['revsliders_tab'];
		if (!$setting_sliderstabs) {
			return false;
		}

		$setting_slider_1 = $this->config->get('revtheme_slider_1');
		$setting_slider_2 = $this->config->get('revtheme_slider_3');
		$setting_slider_3 = $this->config->get('revtheme_slider_4');
		$setting_slider_4 = $this->config->get('revtheme_slider_5');
		
		$data['slider_1_status'] = $setting_slider_1['status'];
		$data['slider_2_status'] = $setting_slider_2['status'];
		$data['slider_3_status'] = $setting_slider_3['status'];
		$data['slider_4_status'] = $setting_slider_4['status'];
		
		$data['slider_5_status'] = false;
		$data['slider_6_status'] = false;
		$data['slider_7_status'] = false;
		$data['slider_8_status'] = false;
		
		$this->load->model('tool/image');
		
		if ($setting_slider_1['icontype']) {
			$data['heading_title_slider_1'] = ('<i class="'.$setting_slider_1['icon'].'"></i>' . $setting_slider_1[$this->config->get('config_language_id')]['title']);
		} else {
			$image = $this->model_tool_image->resize($setting_slider_1['image'], 21, 21);
			$data['heading_title_slider_1'] = ('<span class="heading_ico_image"><span class="mask"></span><img src="'.$image.'" alt=""/></span>' . $setting_slider_1[$this->config->get('config_language_id')]['title']);
		}
		if ($setting_slider_2['icontype']) {
			$data['heading_title_slider_2'] = ('<i class="'.$setting_slider_2['icon'].'"></i>' . $setting_slider_2[$this->config->get('config_language_id')]['title']);
		} else {
			$image = $this->model_tool_image->resize($setting_slider_2['image'], 21, 21);
			$data['heading_title_slider_2'] = ('<span class="heading_ico_image"><span class="mask"></span><img src="'.$image.'" alt=""/></span>' . $setting_slider_2[$this->config->get('config_language_id')]['title']);
		}
		if ($setting_slider_3['icontype']) {
			$data['heading_title_slider_3'] = ('<i class="'.$setting_slider_3['icon'].'"></i>' . $setting_slider_3[$this->config->get('config_language_id')]['title']);
		} else {
			$image = $this->model_tool_image->resize($setting_slider_3['image'], 21, 21);
			$data['heading_title_slider_3'] = ('<span class="heading_ico_image"><span class="mask"></span><img src="'.$image.'" alt=""/></span>' . $setting_slider_3[$this->config->get('config_language_id')]['title']);
		}
		if ($setting_slider_4['icontype']) {
			$data['heading_title_slider_4'] = ('<i class="'.$setting_slider_4['icon'].'"></i>' . $setting_slider_4[$this->config->get('config_language_id')]['title']);
		} else {
			$image = $this->model_tool_image->resize($setting_slider_4['image'], 21, 21);
			$data['heading_title_slider_4'] = ('<span class="heading_ico_image"><span class="mask"></span><img src="'.$image.'" alt=""/></span>' . $setting_slider_4[$this->config->get('config_language_id')]['title']);
		}

		if ($setting_slider_1['status']) {
			$data['slider_1'] = $setting_slider_1['slider'];
			if ($setting_slider_1['autoscroll'] > 0) {
				$data['autoscroll_slider_1'] = $setting_slider_1['autoscroll'];
			} else {
				$data['autoscroll_slider_1'] = '0';
			}
			$data['products_slider_1'] = array();
			if ($setting_slider_1['category_id'] == 'featured') {
				$data['products_slider_1'] = $this->getFeaturedProducts($setting_slider_1);
			} else {
				$data['products_slider_1'] = $this->getCategoryProducts($setting_slider_1);
			}
		}
		if ($setting_slider_2['status']) {
			$data['slider_2'] = $setting_slider_2['slider'];
			if ($setting_slider_2['autoscroll'] > 0) {
				$data['autoscroll_slider_2'] = $setting_slider_2['autoscroll'];
			} else {
				$data['autoscroll_slider_2'] = '0';
			}
			$data['products_slider_2'] = array();
			if ($setting_slider_2['category_id'] == 'featured') {
				$data['products_slider_2'] = $this->getFeaturedProducts($setting_slider_2);
			} else {
				$data['products_slider_2'] = $this->getCategoryProducts($setting_slider_2);
			}
		}
		if ($setting_slider_3['status']) {
			$data['slider_3'] = $setting_slider_3['slider'];
			if ($setting_slider_3['autoscroll'] > 0) {
				$data['autoscroll_slider_3'] = $setting_slider_3['autoscroll'];
			} else {
				$data['autoscroll_slider_3'] = '0';
			}
			$data['products_slider_3'] = array();
			if ($setting_slider_3['category_id'] == 'featured') {
				$data['products_slider_3'] = $this->getFeaturedProducts($setting_slider_3);
			} else {
				$data['products_slider_3'] = $this->getCategoryProducts($setting_slider_3);
			}
		}
		if ($setting_slider_4['status']) {
			$data['slider_4'] = $setting_slider_4['slider'];
			if ($setting_slider_4['autoscroll'] > 0) {
				$data['autoscroll_slider_4'] = $setting_slider_4['autoscroll'];
			} else {
				$data['autoscroll_slider_4'] = '0';
			}
			$data['products_slider_4'] = array();
			if ($setting_slider_4['category_id'] == 'featured') {
				$data['products_slider_4'] = $this->getFeaturedProducts($setting_slider_4);
			} else {
				$data['products_slider_4'] = $this->getCategoryProducts($setting_slider_4);
			}
		}
		
		return $this->load->view('revolution/revslider_tabs', $data);

	}
	
    public function slider1($setting) {
       
		$setting = $this->config->get('revtheme_slider_1');
		$revsliders_tab = $this->config->get('revtheme_home_all')['revsliders_tab'];
		
		if (!$setting['status'] || $revsliders_tab) {
			return false;
		}

        $this->load->language('revolution/revslider');
		$this->load->language('revolution/revolution');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
   
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
		$data['text_catalog_stiker_netu_2'] = $this->language->get('text_catalog_stiker_netu');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
		$data['popup_view'] = $setting_catalog_all['popup_view'];
		$data['img_slider'] = $setting_catalog_all['img_slider'];
		$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
		$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
		$data['ch_quantity'] = $setting_catalog_all['ch_quantity'];
		$data['chislo_ryad'] = $setting_catalog_all['chislo_ryad'];
		$data['text_catalog_stiker_last'] = $this->language->get('text_catalog_stiker_last');
		$data['text_catalog_stiker_best'] = $this->language->get('text_catalog_stiker_best');
		$data['text_catalog_revpopup_purchase'] = $this->language->get('text_catalog_revpopup_purchase');
		$data['text_catalog_revpopup_view'] = $this->language->get('text_catalog_revpopup_view');
		$data['text_catalog_price_na_zakaz'] = $this->language->get('text_catalog_price_na_zakaz');
		$description_options = $this->config->get('revtheme_cat_attributes');
		$data['description_options'] = $this->config->get('revtheme_cat_attributes');
		$data['text_select'] = $this->language->get('text_select');
		$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
		$data['text_option_option'] = $this->language->get('text_option_option');
		$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
		$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');
		$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
		$data['url_all'] = false;
		if ($setting[$this->config->get('config_language_id')]['url_all'] != '') {
			$data['url_all'] = sprintf($this->language->get('text_rev_url_all'), $setting[$this->config->get('config_language_id')]['url_all']);
		}
		
		$currency = $this->session->data['currency'];
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
		
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		if ($settings_stikers['status']) {
			$data['stikers_status'] = true;
		} else {
			$data['stikers_status'] = false;
		}
		
		if ($setting[$this->config->get('config_language_id')]['title']) {
			$style = '';
			if ($setting['icontype']) {
				if ($setting['icon'] == 'fa none') {
					$style = ' hidden';
				}
				$image = '<i class="'.$setting['icon'].$style.'"></i>';
			} else {
				if (!$setting['image'] || $setting['image'] == 'no_image.png') {
					$style = ' hidden';
				}
				$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($setting['image'], 25, 25).'" alt=""/></span>';
			}
			$data['heading_title'] = ($image . $setting[$this->config->get('config_language_id')]['title']);
		} else {
			$data['heading_title'] = '';
		}

		$data['slider_id'] = '1';
		$data['sort'] = $setting['sort'];
		$data['icon'] = $setting['icon'];
		$data['slider'] = $setting['slider'];

		if ($setting['autoscroll'] > 0) {
			$data['autoscroll'] = $setting['autoscroll'];
		} else {
			$data['autoscroll'] = '0';
		}

		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			$this->category_id = end($this->path);
		}

		$url = '';

		$data['products'] = array();

		if ($setting['category_id'] == 'featured') {
			$data['products'] = $this->getFeaturedProducts($setting);
		} else {
			$data['products'] = $this->getCategoryProducts($setting);
		}

        $sort_order = array();

        $data['button_cart']        = $this->language->get('button_cart');
        $data['button_wishlist']    = $this->language->get('button_wishlist');
        $data['button_compare']     = $this->language->get('button_compare');
        $data['text_tax']           = $this->language->get('text_tax');

		return $this->load->view('revolution/revslider', $data);
        
    }

	public function slider2($setting) {
       
		$setting = $this->config->get('revtheme_slider_3');
		$revsliders_tab = $this->config->get('revtheme_home_all')['revsliders_tab'];
		
		if (!$setting['status'] || $revsliders_tab) {
			return false;
		}
		
        $this->load->language('revolution/revslider');
		$this->load->language('revolution/revolution');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
		
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
		$data['text_catalog_stiker_netu_2'] = $this->language->get('text_catalog_stiker_netu');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
		$data['popup_view'] = $setting_catalog_all['popup_view'];
		$data['img_slider'] = $setting_catalog_all['img_slider'];
		$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
		$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
		$data['ch_quantity'] = $setting_catalog_all['ch_quantity'];
		$data['chislo_ryad'] = $setting_catalog_all['chislo_ryad'];
		$data['text_catalog_stiker_last'] = $this->language->get('text_catalog_stiker_last');
		$data['text_catalog_stiker_best'] = $this->language->get('text_catalog_stiker_best');
		$data['text_catalog_revpopup_purchase'] = $this->language->get('text_catalog_revpopup_purchase');
		$data['text_catalog_revpopup_view'] = $this->language->get('text_catalog_revpopup_view');
		$data['text_catalog_price_na_zakaz'] = $this->language->get('text_catalog_price_na_zakaz');
		$description_options = $this->config->get('revtheme_cat_attributes');
		$data['description_options'] = $this->config->get('revtheme_cat_attributes');
		$data['text_select'] = $this->language->get('text_select');
		$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
		$data['text_option_option'] = $this->language->get('text_option_option');
		$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
		$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');
		$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
		$data['url_all'] = false;
		if ($setting[$this->config->get('config_language_id')]['url_all'] != '') {
			$data['url_all'] = sprintf($this->language->get('text_rev_url_all'), $setting[$this->config->get('config_language_id')]['url_all']);
		}
		
		$currency = $this->session->data['currency'];
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
		
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		if ($settings_stikers['status']) {
			$data['stikers_status'] = true;
		} else {
			$data['stikers_status'] = false;
		}
       
		if ($setting[$this->config->get('config_language_id')]['title']) {
			$style = '';
			if ($setting['icontype']) {
				if ($setting['icon'] == 'fa none') {
					$style = ' hidden';
				}
				$image = '<i class="'.$setting['icon'].$style.'"></i>';
			} else {
				if (!$setting['image'] || $setting['image'] == 'no_image.png') {
					$style = ' hidden';
				}
				$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($setting['image'], 25, 25).'" alt=""/></span>';
			}
			$data['heading_title'] = ($image . $setting[$this->config->get('config_language_id')]['title']);
		} else {
			$data['heading_title'] = '';
		}

		$data['slider_id'] = '2';
		$data['sort'] = $setting['sort'];
		$data['icon'] = $setting['icon'];
		$data['slider'] = $setting['slider'];

		if ($setting['autoscroll'] > 0) {
			$data['autoscroll'] = $setting['autoscroll'];}
			else {$data['autoscroll'] = '0';
		}

		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			$this->category_id = end($this->path);
		}

		$url = '';

		$data['products'] = array();

		if ($setting['category_id'] == 'featured') {
			$data['products'] = $this->getFeaturedProducts($setting);
		} else {
			$data['products'] = $this->getCategoryProducts($setting);
		}

        $sort_order = array();

        $data['button_cart']        = $this->language->get('button_cart');
        $data['button_wishlist']    = $this->language->get('button_wishlist');
        $data['button_compare']     = $this->language->get('button_compare');
        $data['text_tax']           = $this->language->get('text_tax');

		return $this->load->view('revolution/revslider', $data);
    }
	
	public function slider3($setting) {
       
		$setting = $this->config->get('revtheme_slider_4');
		$revsliders_tab = $this->config->get('revtheme_home_all')['revsliders_tab'];
		
		if (!$setting['status'] || $revsliders_tab) {
			return false;
		}
		
        $this->load->language('revolution/revslider');
		$this->load->language('revolution/revolution');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
		
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
		$data['text_catalog_stiker_netu_2'] = $this->language->get('text_catalog_stiker_netu');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
		$data['popup_view'] = $setting_catalog_all['popup_view'];
		$data['img_slider'] = $setting_catalog_all['img_slider'];
		$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
		$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
		$data['ch_quantity'] = $setting_catalog_all['ch_quantity'];
		$data['chislo_ryad'] = $setting_catalog_all['chislo_ryad'];
		$data['text_catalog_stiker_last'] = $this->language->get('text_catalog_stiker_last');
		$data['text_catalog_stiker_best'] = $this->language->get('text_catalog_stiker_best');
		$data['text_catalog_revpopup_purchase'] = $this->language->get('text_catalog_revpopup_purchase');
		$data['text_catalog_revpopup_view'] = $this->language->get('text_catalog_revpopup_view');
		$data['text_catalog_price_na_zakaz'] = $this->language->get('text_catalog_price_na_zakaz');
		$description_options = $this->config->get('revtheme_cat_attributes');
		$data['description_options'] = $this->config->get('revtheme_cat_attributes');
		$data['text_select'] = $this->language->get('text_select');
		$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
		$data['text_option_option'] = $this->language->get('text_option_option');
		$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
		$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');
		$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
		$data['url_all'] = false;
		if ($setting[$this->config->get('config_language_id')]['url_all'] != '') {
			$data['url_all'] = sprintf($this->language->get('text_rev_url_all'), $setting[$this->config->get('config_language_id')]['url_all']);
		}
		
		$currency = $this->session->data['currency'];
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
		
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		if ($settings_stikers['status']) {
			$data['stikers_status'] = true;
		} else {
			$data['stikers_status'] = false;
		}
		
		if ($setting[$this->config->get('config_language_id')]['title']) {
			$style = '';
			if ($setting['icontype']) {
				if ($setting['icon'] == 'fa none') {
					$style = ' hidden';
				}
				$image = '<i class="'.$setting['icon'].$style.'"></i>';
			} else {
				if (!$setting['image'] || $setting['image'] == 'no_image.png') {
					$style = ' hidden';
				}
				$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($setting['image'], 25, 25).'" alt=""/></span>';
			}
			$data['heading_title'] = ($image . $setting[$this->config->get('config_language_id')]['title']);
		} else {
			$data['heading_title'] = '';
		}

		$data['slider_id'] = '3';
		$data['sort'] = $setting['sort'];
		$data['slider'] = $setting['slider'];

		if ($setting['autoscroll'] > 0) {
			$data['autoscroll'] = $setting['autoscroll'];}
			else {$data['autoscroll'] = '0';
		}

		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			$this->category_id = end($this->path);
		}

		$url = '';

		$data['products'] = array();

		if ($setting['category_id'] == 'featured') {
			$data['products'] = $this->getFeaturedProducts($setting);
		} else {
			$data['products'] = $this->getCategoryProducts($setting);
		}

        $sort_order = array();

        $data['button_cart']        = $this->language->get('button_cart');
        $data['button_wishlist']    = $this->language->get('button_wishlist');
        $data['button_compare']     = $this->language->get('button_compare');
        $data['text_tax']           = $this->language->get('text_tax');

		return $this->load->view('revolution/revslider', $data);
    }
	
	public function slider4($setting) {
       
		$setting = $this->config->get('revtheme_slider_5');
		$revsliders_tab = $this->config->get('revtheme_home_all')['revsliders_tab'];
		
		if (!$setting['status'] || $revsliders_tab) {
			return false;
		}
		
        $this->load->language('revolution/revslider');
		$this->load->language('revolution/revolution');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
		
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
		$data['text_catalog_stiker_netu_2'] = $this->language->get('text_catalog_stiker_netu');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
		$data['popup_view'] = $setting_catalog_all['popup_view'];
		$data['img_slider'] = $setting_catalog_all['img_slider'];
		$data['rev_srav_prod'] = $setting_catalog_all['rev_srav_prod'];
		$data['rev_wish_prod'] = $setting_catalog_all['rev_wish_prod'];
		$data['ch_quantity'] = $setting_catalog_all['ch_quantity'];
		$data['chislo_ryad'] = $setting_catalog_all['chislo_ryad'];
		$data['text_catalog_stiker_last'] = $this->language->get('text_catalog_stiker_last');
		$data['text_catalog_stiker_best'] = $this->language->get('text_catalog_stiker_best');
		$data['text_catalog_revpopup_purchase'] = $this->language->get('text_catalog_revpopup_purchase');
		$data['text_catalog_revpopup_view'] = $this->language->get('text_catalog_revpopup_view');
		$data['text_catalog_price_na_zakaz'] = $this->language->get('text_catalog_price_na_zakaz');
		$description_options = $this->config->get('revtheme_cat_attributes');
		$data['description_options'] = $this->config->get('revtheme_cat_attributes');
		$data['text_select'] = $this->language->get('text_select');
		$data['revtheme_product_all'] = $this->config->get('revtheme_product_all');
		$data['text_option_option'] = $this->language->get('text_option_option');
		$revpopuppredzakaz_settings = $this->config->get('revtheme_predzakaz');
		$data['predzakaz_button'] = $revpopuppredzakaz_settings['status'];
		$data['text_predzakaz'] = $this->config->get('revtheme_predzakaz')['notify_status'] ? $this->language->get('text_predzakaz_notify') : $this->language->get('text_predzakaz');
		$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
		$data['url_all'] = false;
		if ($setting[$this->config->get('config_language_id')]['url_all'] != '') {
			$data['url_all'] = sprintf($this->language->get('text_rev_url_all'), $setting[$this->config->get('config_language_id')]['url_all']);
		}
		
		$currency = $this->session->data['currency'];
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
		
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		if ($settings_stikers['status']) {
			$data['stikers_status'] = true;
		} else {
			$data['stikers_status'] = false;
		}
		
		if ($setting[$this->config->get('config_language_id')]['title']) {
			$style = '';
			if ($setting['icontype']) {
				if ($setting['icon'] == 'fa none') {
					$style = ' hidden';
				}
				$image = '<i class="'.$setting['icon'].$style.'"></i>';
			} else {
				if (!$setting['image'] || $setting['image'] == 'no_image.png') {
					$style = ' hidden';
				}
				$image = '<span class="heading_ico_image'.$style.'"><img src="'.$this->model_tool_image->resize($setting['image'], 25, 25).'" alt=""/></span>';
			}
			$data['heading_title'] = ($image . $setting[$this->config->get('config_language_id')]['title']);
		} else {
			$data['heading_title'] = '';
		}

		$data['slider_id'] = '4';
		$data['sort'] = $setting['sort'];
		$data['slider'] = $setting['slider'];

		if ($setting['autoscroll'] > 0) {
			$data['autoscroll'] = $setting['autoscroll'];}
			else {$data['autoscroll'] = '0';
		}

		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');

		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			$this->category_id = end($this->path);
		}

		$url = '';

		$data['products'] = array();

		if ($setting['category_id'] == 'featured') {
			$data['products'] = $this->getFeaturedProducts($setting);
		} else {
			$data['products'] = $this->getCategoryProducts($setting);
		}

        $sort_order = array();

        $data['button_cart']        = $this->language->get('button_cart');
        $data['button_wishlist']    = $this->language->get('button_wishlist');
        $data['button_compare']     = $this->language->get('button_compare');
        $data['text_tax']           = $this->language->get('text_tax');

		return $this->load->view('revolution/revslider', $data);
    }
	
    public function getCategoryProducts($setting) {
        $result = array();

        $data = array(
            'filter_category_id'     => $setting['category_id'],
            'filter_manufacturer_id' => $setting['manufacturer_id'],
            'filter_sub_category'    => true,
            'sort'                   => $setting['sort'],
            'order'                  => $setting['sort'] == 'p.sort_order' ? 'ASC' : 'DESC',
            'start'                  => '0',
            'limit'                  => $setting['count']
        );
				
		$currency = $this->session->data['currency'];
		$config_product_description_length = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length');

		if ($this->config->get('revtheme_all_settings')['mobile_on']) {
			$is_mobile = $data['is_mobile'] = $this->mobiledetect->isMobile();
			$is_desctope = $data['is_desctope'] = !$this->mobiledetect->isMobile() || $this->mobiledetect->isTablet();
		} else {
			$is_mobile = $data['is_mobile'] = true;
			$is_desctope = $data['is_desctope'] = true;
		}
		
		$this->load->model('revolution/revolution');
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
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
		
        $products = $this->model_revolution_revolution->getProducts($data);

        foreach ($products as $product) {
			
			if (isset($this->session->data['compare'])) {
				if (in_array($product['product_id'], $this->session->data['compare'])) {
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
				if (in_array($product['product_id'], $this->session->data['wishlist'])) {
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
					if (in_array($product['product_id'], $wishlist_register_id)) {
						$wishlist_class = 'in_wishlist';
						$button_wishlist = $this->language->get('button_wishlist_out');
					} else {
						$wishlist_class = '';
						$button_wishlist = $this->language->get('button_wishlist');
					}
				}
			}			
			
			if ($settings_stikers['new_status']) {
				if (isset($date_added[$product['product_id']])) {
					$stiker_last = true;
				} else {
					$stiker_last = false;
				}
			} else {
				$stiker_last = false;
			}
			
			if ($settings_stikers['best_status']) {
				if (isset($best_seller[$product['product_id']])) {
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
				$stiker_upc = $product['upc'];
			} else {
				$stiker_upc = false;
			}
			if ($settings_stikers['ean']) {
				$stiker_ean = $product['ean'];
			} else {
				$stiker_ean = false;
			}
			if ($settings_stikers['jan']) {
				$stiker_jan = $product['jan'];
			} else {
				$stiker_jan = false;
			}
			if ($settings_stikers['isbn']) {
				$stiker_isbn = $product['isbn'];
			} else {
				$stiker_isbn = false;
			}
			if ($this->config->get('revtheme_product_all')['ed_izm']) {
				$ed_izm = $product['isbn'];
			} else {
				$ed_izm = false;
			}
			if ($settings_stikers['mpn']) {
				if ($settings_stikers['mpn_to_model']) {
					$stiker_mpn = $product['model'];
				} else {
					$stiker_mpn = $product['mpn'];
				}
			} else {
				$stiker_mpn = false;
			}
			if ($settings_stikers['stiker_netu_stock']) {
				$text_catalog_stiker_netu = $product['stock_status'];
			} else {
				$text_catalog_stiker_netu = $this->language->get('text_catalog_stiker_netu');
			}
			if ($settings_stikers['sklad_status']) {
				$stiker_sklad_status = $product['stock_status'];
			} else {
				$stiker_sklad_status = false;
			}
			
            if ($product['image']) {
				if ($setting_catalog_all['img_slider'] && $is_desctope) {
					$images = array();
					$this->load->model('catalog/product');
					$add_images = $this->model_catalog_product->getProductImages($product['product_id']);
					if ($setting_catalog_all['img_slider'] == '2') {
						$add_images = array_slice($add_images, 0, 1);
					}
					foreach ($add_images as $add_image) {
						$images[] = array(
							'thumb' => $this->model_tool_image->resize($add_image['image'], $setting['image_width'], $setting['image_height'])
						);
					}
				} else {
					$images = false;
				}
				$image = $this->model_tool_image->resize($product['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$images = false;
                $image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
            }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $currency);
				$price_number = $product['price'];
            } else {
                $price = false;
				$price_number = false;
            }

            if ((float)$product['special']) {
                $special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $currency);
				$special_number = $product['special'];
            } else {
                $special = false;
				$special_number = false;
            }

            if ($this->config->get('config_tax')) {
                $tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $currency);
            } else {
                $tax = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $product['rating'];
            } else {
                $rating = false;
            }

			$brand = $this->model_revolution_revolution->get_pr_brand($product['product_id']);
			
			$description_options = $this->config->get('revtheme_cat_attributes');
			$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
			$description = '';
			$options = false;
			if ($revtheme_home_all['pr_opisanie']) {
				if ($description_options['description_on']) {
					if ($description_options['zamena_description']) {
						$description = $this->model_revolution_revolution->getAttrText($product['product_id']);
					} else {
						$description = utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, $config_product_description_length) . '...';
					}
				}
				if ($description_options['options_in_grid']) {
					$options = array();
					$cache = 'options_data.' . crc32((int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$product['product_id']);
					$pr_options = $this->cache->get($cache);
					if (!$pr_options) {
						$pr_options = $this->model_catalog_product->getProductOptions($product['product_id']);
						if ($this->config->get('revtheme_all_settings')['cache_on']) {$this->cache->set($cache, $pr_options);}
					}
				
					foreach ($pr_options as $option) {
						$product_option_value_data = array();

						foreach ($option['product_option_value'] as $option_value) {
							if ($option_value['quantity'] > 0 || $this->config->get('revtheme_product_all')['options_null_qw']) {
								if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
									$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $currency);
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
			}
			$product_in_cart = false;
			if ($setting_catalog_all['product_in_cart']) {
				$products_in_cart = $this->cart->getProducts();
				foreach ($products_in_cart as $product_cart) {
					if ($product_cart['product_id'] == $product['product_id']) {
						$product_in_cart = true;
					}
				}
			}
			
            $result[] = array(
				'reviews' => $this->config->get('config_review_status') ? (int)$product['reviews'] : false,
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
				'quantity' => $product['quantity'],
				'options_buy' => $product['options_buy'],
				'model' => $product['model'],
				'short_description' => html_entity_decode($product['short_description'], ENT_QUOTES, 'UTF-8'),
				'product_in_cart' => $product_in_cart,
                'product_id' => $product['product_id'],
                'thumb' => $image,
                'name' => $product['name'],
                'description' => $description,
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'price' => $price,
                'special' => $special,
                'tax' => $tax,
                'rating' => number_format($rating, 1),
                //'reviews' => sprintf($this->language->get('text_reviews'), (int)$product['reviews']),
				'minimum' => $product['minimum'] > 0 ? $product['minimum'] : 1
            );
        }
		if ($setting['not_view_null'] && isset($result)) {
			$product_settings = $this->config->get('revtheme_product_all');
			$zakaz = $product_settings['zakaz'];
			$zakaz_price_null = $product_settings['zakaz_price_null'];
			if (!$zakaz || !$zakaz_price_null) {
				foreach ($result as $key => $value) {
					if (!$zakaz) {
						$quantity[$key] = $value['quantity'];
					} else {
						$quantity[$key] = 2;
					}
					if (!$zakaz_price_null) {
						$price_number[$key] = $value['price_number'];
					} else {
						$price_number[$key] = 1;
					}
					if ($quantity[$key] < 1 || $price_number[$key] == 0) {
						unset($result[$key]);
					}
				}
			}
		}
        return $result;
    }

    public function getFeaturedProducts($setting){
        $result = array();

        $products = explode(',', $setting['featured']);

        if (empty($setting['count'])) {
            $setting['count'] = 5;
        }

		$currency = $this->session->data['currency'];
		$config_product_description_length = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length');
		
		if ($this->config->get('revtheme_all_settings')['mobile_on']) {
			$is_mobile = $data['is_mobile'] = $this->mobiledetect->isMobile();
			$is_desctope = $data['is_desctope'] = !$this->mobiledetect->isMobile() || $this->mobiledetect->isTablet();
		} else {
			$is_mobile = $data['is_mobile'] = true;
			$is_desctope = $data['is_desctope'] = true;
		}
		
		$this->load->model('revolution/revolution');
		$settings_stikers = $this->config->get('revtheme_catalog_stiker');
		$data['setting_catalog_all'] = $setting_catalog_all = $this->config->get('revtheme_catalog_all');
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
		
        $products = array_slice($products, 0, (int)$setting['count']);

        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
				
				if (isset($this->session->data['compare'])) {
				if (in_array($product_info['product_id'], $this->session->data['compare'])) {
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
					if (in_array($product_info['product_id'], $this->session->data['wishlist'])) {
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
						if (in_array($product_info['product_id'], $wishlist_register_id)) {
							$wishlist_class = 'in_wishlist';
							$button_wishlist = $this->language->get('button_wishlist_out');
						} else {
							$wishlist_class = '';
							$button_wishlist = $this->language->get('button_wishlist');
						}
					}
				}				
				
				if ($settings_stikers['new_status']) {
					if (isset($date_added[$product_info['product_id']])) {
						$stiker_last = true;
					} else {
						$stiker_last = false;
					}
				} else {
					$stiker_last = false;
				}
				
				if ($settings_stikers['best_status']) {
					if (isset($best_seller[$product_info['product_id']])) {
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
				
                if ($product_info['image']) {
					if ($setting_catalog_all['img_slider'] && $is_desctope) {
						$images = array();
						$this->load->model('catalog/product');
						$add_images = $this->model_catalog_product->getProductImages($product_info['product_id']);
						if ($setting_catalog_all['img_slider'] == '2') {
							$add_images = array_slice($add_images, 0, 1);
						}
						foreach ($add_images as $add_image) {
							$images[] = array(
								'thumb' => $this->model_tool_image->resize($add_image['image'], $setting['image_width'], $setting['image_height'])
							);
						}
					} else {
						$images = false;
					}
					$image = $this->model_tool_image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
				} else {
					$images = false;
                    $image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
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

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $currency);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $product_info['rating'];
                } else {
                    $rating = false;
                }
				
				$brand = $this->model_revolution_revolution->get_pr_brand($product_info['product_id']);
				
				$description_options = $this->config->get('revtheme_cat_attributes');
				$data['revtheme_home_all'] = $revtheme_home_all = $this->config->get('revtheme_home_all');
				$description = '';
				$options = false;
				if ($revtheme_home_all['pr_opisanie']) {
					if ($description_options['description_on']) {
						if ($description_options['zamena_description']) {
							$description = $this->model_revolution_revolution->getAttrText($product_info['product_id']);
						} else {
							$description = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $config_product_description_length) . '...';
						}
					}
					if ($description_options['options_in_grid']) {
						$options = array();
						$cache = 'options_data.' . crc32((int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$product_info['product_id']);
						$pr_options = $this->cache->get($cache);
						if (!$pr_options) {
							$pr_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
							if ($this->config->get('revtheme_all_settings')['cache_on']) {$this->cache->set($cache, $pr_options);}
						}
					
						foreach ($pr_options as $option) {
							$product_option_value_data = array();

							foreach ($option['product_option_value'] as $option_value) {
								if ($option_value['quantity'] > 0 || $this->config->get('revtheme_product_all')['options_null_qw']) {
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $currency);
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
				}
				$product_in_cart = false;
				if ($setting_catalog_all['product_in_cart']) {
					$products_in_cart = $this->cart->getProducts();
					foreach ($products_in_cart as $product_cart) {
						if ($product_cart['product_id'] == $product_info['product_id']) {
							$product_in_cart = true;
						}
					}
				}
				
                $result[] = array(
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
					'quantity' => $product_info['quantity'],
					'options_buy' => $product_info['options_buy'],
					'model' => $product_info['model'],
					'short_description' => html_entity_decode($product_info['short_description'], ENT_QUOTES, 'UTF-8'),
					'product_in_cart' => $product_in_cart,
                    'product_id' => $product_info['product_id'],
                    'thumb' => $image,
                    'name' => $product_info['name'],
                    'description' => $description,
                    'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                    'price' => $price,
                    'special' => $special,
                    'tax' => $tax,
                    'rating' => number_format($rating, 1),
                    'reviews' => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
					'minimum' => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1
                );
            }
        }
		if ($setting['not_view_null'] && isset($result)) {
			$product_settings = $this->config->get('revtheme_product_all');
			$zakaz = $product_settings['zakaz'];
			$zakaz_price_null = $product_settings['zakaz_price_null'];
			if (!$zakaz || !$zakaz_price_null) {
				foreach ($result as $key => $value) {
					if (!$zakaz) {
						$quantity[$key] = $value['quantity'];
					} else {
						$quantity[$key] = 2;
					}
					// if (!$zakaz_price_null) {
					// 	$price_number[$key] = $value['price_number'];
					// } else {
					$price_number[$key] = 1;
					// }
					if ($quantity[$key] < 1 || $price_number[$key] == 0) {
						unset($result[$key]);
					}
				}
			}
		}
		return $result;
    }
}
