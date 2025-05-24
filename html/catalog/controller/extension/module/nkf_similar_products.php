<?php
/*
@author  nikifalex
@skype   logoffice1
@email    nikifalex@yandex.ru
@link https://opencartforum.com/files/file/4617-pohozhie-tovary/
*/

class ControllerExtensionModuleNkfSimilarProducts extends Controller {
    public function index($setting) {

        if (isset($this->request->get['product_id']) && $this->request->get['product_id'] > 0) {

            $this->load->language('extension/module/nkf_similar_products');
            $data['text_tax'] = $this->language->get('text_tax');
            $data['button_cart'] = $this->language->get('button_cart');
            $data['button_wishlist'] = $this->language->get('button_wishlist');
            $data['button_compare'] = $this->language->get('button_compare');
            $data['entry_diff'] = $this->language->get('entry_diff');                  
            $this->load->model('extension/module/nkf_similar_products');
            $this->load->model('tool/image');
            $product_settings = $this->config->get('revtheme_product_all');
            $data['recalc_price'] = $product_settings['recalc_price'];
            $data['products'] = array();
            $filter_data = array(
                'module_id'           => $setting['module_id'],
                'use_cache'           => $setting['use_cache'],
                'limit'               => $setting['limit'],
                'use_category'        => $setting['use_category'],
                'use_manufacturer'    => $setting['use_manufacturer'],
                'delimiter'           => $setting['delimiter'],
                'use_price'           => $setting['use_price'],
                'price_percent'       => $setting['price_percent'],
                'use_quantity'        => $setting['use_quantity'],
                'cnt_diff'            => $setting['cnt_diff'],
                'use_excluded_attributes' => isset($setting['use_excluded_attributes']) ? $setting['use_excluded_attributes'] : 1,
                'excluded_attributes' => isset($setting['excluded_attributes']) ? $setting['excluded_attributes'] : array(),
                'product_id'          => $this->request->get['product_id'],
            );


            $settings_stikers = $this->config->get('revtheme_catalog_stiker');
            

            $lang = $this->config->get('config_language_id');
            $title = isset($setting['titles']['title'.$lang]) && $setting['titles']['title'.$lang] ? $setting['titles']['title'.$lang] : '';

            if (isset($title) && $title) {
                $data['heading_title'] = $title;
                $setting['title'] = $title;
            }

            if (isset($setting['use_featured_template']) && $setting['use_featured_template']) {
                $filter_data['only_id'] = true;
                $results = $this->model_extension_module_nkf_similar_products->getProductSimilar($filter_data);
                $setting2 = $setting;
                $setting2['product'] = $results;
                return $this->load->controller('extension/module/featured', $setting2);
            } else {
                $filter_data['only_id'] = false;
                $results = $this->model_extension_module_nkf_similar_products->getProductSimilar($filter_data);
                if ($results) {
                    foreach ($results as $result) {
                        if (isset($this->session->data['compare'])) {
                            if (in_array($result['product_id'], $this->session->data['compare'])) {
                                $compare_class = 'in-compare';
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
                                $wishlist_class = 'in-wishlist';
                                $button_wishlist = $this->language->get('button_wishlist_out');
                            } else {
                                $wishlist_class = '';
                                $button_wishlist = $this->language->get('button_wishlist');
                            }
                        } else {
                            $wishlist_class = '';
                            $button_wishlist = $this->language->get('button_wishlist');
                        }
                        if ($result['image']) {
                            $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
                        } else {
                            $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                        }
                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }
                        if ((float)$result['special']) {
                            $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $special = false;
                        }
				        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					        $price_number = $result['price'];
				        } else {
					        $price_number = false;
				        }
				        if ((float)$result['special']) {
					        $special_number = $result['special'];
				        } else {
					        $special_number = false;
				        }
                        if ($this->config->get('config_tax')) {
                            $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
                        } else {
                            $tax = false;
                        }
                        if ($this->config->get('config_review_status')) {
                            $rating = $result['rating'];
                        } else {
                            $rating = false;
                        }
                        if ($settings_stikers['spec_status']) {
                            $stiker_spec = true;
                        } else {
                            $stiker_spec = false;
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
                        if ($settings_stikers['mpn']) {
                            if ($settings_stikers['mpn_to_model']) {
                                $stiker_mpn = $product['model'];
                            } else {
                                $stiker_mpn = $product['mpn'];
                            }
                        } else {
                            $stiker_mpn = false;
                        }
                        $data['featured_products'][] = array(
                            'product_id'          => $result['product_id'],
                            'thumb'               => $image,
                            'name'                => $result['name'],
                            'description'         => $this->model_revolution_revolution->getAttrText($result['product_id']),
                            'price'               => $price,
                            'special'             => $special,
					        'price_number'        => $price_number,
					        'special_number'      => $special_number,
                            'tax'                 => $tax,
                            'diff_attributes'     => ($setting['add_diff_attributes'] == 0 ? array() : $result['diff']['attributes']),
                            'diff_attributes_str' => implode('<br/> ', $result['diff']['attributes']),
                            'rating'              => $rating,
                            'href'                => $this->url->link('product/product', 'product_id=' . $result['product_id']), 				            
				            'stiker_spec'         => $stiker_spec,				            
				            'stiker_upc'          => $stiker_upc,
				            'stiker_ean'          => $stiker_ean,
				            'stiker_jan'          => $stiker_jan,
				            'stiker_isbn'         => $stiker_isbn,
				            'stiker_mpn'          => $stiker_mpn,
					        'quantity'            => $result['quantity'],
                            'compare_class'       => $compare_class,
				            'wishlist_class'      => $wishlist_class,
				            'button_compare'      => $button_compare,
				            'button_wishlist'     => $button_wishlist,
                            'minimum'             => $result['minimum'] > 0 ? $result['minimum'] : 1,
                        );
                    }
                    return $this->load->view('extension/module/nkf_similar_products', $data);
                }
            }
        }
    }
}
