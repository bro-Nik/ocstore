<?php
class ControllerExtensionModuleStorereviews extends Controller {
    public function index($setting) {

        $setting = $setting['storereviews'];

        $this->language->load('product/extended_reviews');
        $data = $this->language->all();

        $this->load->model('tool/image');

        $this->load->model('catalog/ex_store_reviews');

        $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
        $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
        $this->document->addScript('catalog/view/javascript/ex-reviews/owl-carousel/owl.carousel.min.js');
        $this->document->addStyle('catalog/view/javascript/ex-reviews/owl-carousel/owl.carousel.css');
        $this->document->addStyle('catalog/view/javascript/ex-reviews/storereviews.css');

        $data['module'] = 'reviews';

        $data['module_header'] = $setting['module_header'][$this->config->get('config_language_id')];

    		$data['reviews_store_setting'] = $this->config->get('reviews_store_setting');

        $data['reviews'] = array();

        if (!$setting['limit']) {
			       $setting['limit'] = 4;
		         }
        $content = '1';
        if(isset($setting['only_photo']) && isset($setting['only_video'])){
          $content = 'media';
        }
        elseif(isset($setting['only_photo'])){
          $content = 'photo';
        }
        elseif(isset($setting['only_video'])){
          $content = 'video';
        }

        $results = $this->model_catalog_ex_store_reviews->getReviews(0, $setting['limit'], $setting['sort_order'], $content);
        $data['rating_store'] = $this->model_catalog_ex_store_reviews->getAverageRating();
        $data['total_reviews'] = $this->model_catalog_ex_store_reviews->getTotalReviews();
        $data['percent_rating'] = $this->model_catalog_ex_store_reviews->getPercentReviewsStore();

        foreach ($results as $result) {

          $images = array();

          if($result['images']){
          $review_images = $this->model_catalog_ex_store_reviews->getImagesByReviewId($result['review_id']);
          foreach ($review_images as $image){
      				$type = '0';

      			if($image['image'] != null){
      				$popup = 'image/' .$image['image'];
      				$thumb = $this->model_tool_image->resize($image['image'], 160, 160);
      			}
      			else if($image['image_href'] != null){
      				$popup = $image['image_href'];
      				$thumb = $image['image_href_t'];
      				$type = '1';
      			}

      				$images[] = array(
      					'thumb' => $thumb,
      					'popup' => $popup,
      					'image_id' => $image['review_image_id'],
      					'type'  => $type
      				);
      		}
        }

          $videos = null;
          if($result['videos']){
           $videos = explode(' ', $result['videos']);
         }

            $data['reviews'][] = array(
                'review_id'   => $result['review_id'],
                'rating'      => $result['rating'],
                'minus'       => $result['minus'],
                'plus'        => $result['plus'],
                'likes'       => $result['likes'],
                'dislikes'    => $result['dislikes'],
                'admin_reply' => $result['admin_reply'],
                'admin_name'  => $result['admin_name'],
				        'text'        => $result['text'],
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'author'      => $result['author'],
                'images'      => $images,
                'videos'      => $videos
            );
        }
    $data['link_all_reviews'] = $this->url->link('product/ex_store_reviews');
    $data['write_review'] = $this->url->link('product/ex_store_reviews');
    $data['text_all_reviews'] = $this->language->get('text_all_reviews');
    if(isset($setting['autoplay'])){
      $data['autoplay'] = true;
    }
    $data['autoplay_speed'] = $setting['autoplay_speed'];
		if($data['reviews']){
			if (VERSION >= 2.2) {
				return $this->load->view('extension/module/storereviews', $data);
			} else {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/module/storereviews.tpl')) {
					return $this->load->view($this->config->get('config_template') . '/template/extension/module/storereviews.tpl', $data);
				} else {
					return $this->load->view('default/template/extension/module/storereviews.tpl', $data);
				}
			}
		}
    }

}
