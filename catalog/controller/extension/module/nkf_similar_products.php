<?php
/*
@author  nikifalex
@skype   logoffice1
@email    nikifalex@yandex.ru
@link https://opencartforum.com/files/file/4617-pohozhie-tovary/
*/

require_once('catalog/controller/base/product_cart.php');

class ControllerExtensionModuleNkfSimilarProducts extends ControllerBaseProductCart {
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
                $data['title'] = $title;
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
                $data = $this->prepareProductsData($results, $setting);

                if ($results) {
                    $data['id'] = 'product_related';
		            return $this->load->view('product/carousel_product', $data);
                }
            }
        }
    }
}
