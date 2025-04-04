<?php
class ControllerProductAllreviewspage extends Controller {
    public function index() {
        $this->document->addStyle('catalog/view/javascript/ex-reviews/extended_reviews.css');
        $this->document->addStyle('catalog/view/javascript/ex-reviews/owl-carousel/owl.carousel.css');
        $this->document->addScript('catalog/view/javascript/ex-reviews/owl-carousel/owl.carousel.min.js');
        $this->document->addStyle('catalog/view/javascript/ex-reviews/magnific/magnific-popup.css');
        $this->document->addScript('catalog/view/javascript/ex-reviews/magnific/jquery.magnific-popup.min.js');

        $this->load->model('catalog/product');

        $this->language->load('product/extended_reviews');

        $this->load->model('catalog/allreviewspage');

        $this->load->model('catalog/extended_reviews');

        $this->load->model('tool/image');

        $data = $this->language->all();

        $extended_reviews_settings = $this->config->get("extended_reviews_settings");

        $data['extended_reviews'] = $extended_reviews_settings;

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        if ($this->customer->isLogged()) {
          $data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
        } else {
          $data['customer_name'] = '';
        }

	      $data['show_review_plus'] = $this->config->get('config_show_review_plus');
		    $data['show_review_minus'] = $this->config->get('config_show_review_minus');
        $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
        if (!$extended_reviews_settings['review_access'] || $this->customer->isLogged()) {
          $data['review_guest'] = true;
        } else {
          $data['review_guest'] = false;
        }
        if (!$extended_reviews_settings['likes_access'] || $this->customer->isLogged()) {
          $data['likes_guest'] = true;
        } else {
          $data['likes_guest'] = false;
        }
        if (!$extended_reviews_settings['answer_access'] || $this->customer->isLogged()) {
          $data['answer_guest'] = true;
        } else {
          $data['answer_guest'] = false;
        }

        $data['module_header'] = $extended_reviews_settings['module_header'][$this->config->get('config_language_id')] != '' ? $extended_reviews_settings['module_header'][$this->config->get('config_language_id')] : $data['heading_title'];

        $limit = $this->config->get('config_product_limit') ? $this->config->get('config_product_limit') : 10;

        $this->document->setTitle($data['module_header']);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['breadcrumbs'][] = array(
            'text'      => $data['module_header'],
            'href'      => $this->url->link('product/allreviewspage', $url),
            'separator' => $this->language->get('text_separator')
        );

        $data['reviews'] = array();

        $reviews_total = $this->model_catalog_allreviewspage->getTotalCustomerReviews();

        $results = $this->model_catalog_allreviewspage->getAllCustomerReviews(($page - 1) * $limit, $limit);

        foreach ($results as $result) {
            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }

            $product_id = false;
            $prod_thumb = false;
            $prod_name = false;
            $prod_model = false;
            $prod_href = false;

            if ($result['product_id']) {
                $product = $this->model_catalog_product->getProduct($result['product_id']);

				if ($product['image']) {

						$prod_thumb = $this->model_tool_image->resize($product['image'], 350, 350);

				}
                $product_id = $product['product_id'];
                $prod_name = $product['name'];
                $prod_model = $product['model'];
                $prod_href = $this->url->link('product/product', 'product_id=' . $product['product_id']);
            }
            $review_images = $this->model_catalog_extended_reviews->getImagesByReviewId($result['review_id']);
            $children_reviews = $this->model_catalog_allreviewspage->getChildrenReviews($result['review_id']);
            $images = array();
            foreach ($review_images as $image){
            $type = '0';
            if($image['image'] != null){
            $popup = 'image/' .$image['image'];
            $thumb = $this->model_tool_image->resize($image['image'], $extended_reviews_settings['all_thumb_width']*2, $extended_reviews_settings['all_thumb_height']*2);
            }
            else if($image['image_href'] != null){
              $popup = $image['image_href'];
              $thumb = $image['image_href_t'];
              $type = '1';
            }
            $images[] = array(
    					'thumb' => $thumb,
    					'popup' => $popup,
              'type'  => $type
    				);
            }
            $videos = array();
            if(!empty($result['videos'])){
              $str = explode(' ', $result['videos']);
              foreach($str as $video){
                $videos[] = $video;
              }
            }
            $data['reviews'][] = array(
                'review_id'   => $result['review_id'],
                'rating'      => $rating,
                'description' => $result['text'],
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'author'      => $result['author'],
				        'purchased'   => $result['purchased'],
                'plus' 		  => $result['plus'],
                'minus' 	  => $result['minus'],
                'likes' 		  => $result['likes'],
                'dislikes' 	  => $result['dislikes'],
                'admin_name' => nl2br($result['admin_name']),
                'admin_reply'     => nl2br($result['admin_reply']),
                'children_reviews' => $children_reviews,
                'images'      => $images,
                'videos'      => $videos,
                'product_id'  => $product_id,
                'prod_thumb'  => $prod_thumb,
                'prod_name'   => $prod_name,
                'prod_model'  => $prod_model,
                'prod_href'   => $prod_href
            );
        }
        // Captcha
        if ($extended_reviews_settings['captcha']) {
          $data['captcha'] = $this->load->controller('extension/captcha/google_captcha');
        } else {
          $data['captcha'] = '';
        }
        $pagination = new Pagination();
        $pagination->total = $reviews_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('product/allreviewspage', '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($reviews_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($reviews_total - $limit)) ? $reviews_total : ((($page - 1) * $limit) + $limit), $reviews_total, ceil($reviews_total / $limit));

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
		if (VERSION >= 2.2) {
			$this->response->setOutput($this->load->view('product/allreviewspage', $data));
		} else {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/allreviewspage.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/product/allreviewspage.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('default/template/product/allreviewspage.tpl', $data));
			}
        }
    }
}
