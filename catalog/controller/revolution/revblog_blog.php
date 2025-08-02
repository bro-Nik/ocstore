<?php
require_once('catalog/controller/base/product_cart.php');

class ControllerRevolutionRevblogBlog extends ControllerBaseProductCart {
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

			// $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			// $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			
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
				foreach ($results as $result) {
					$data['images'][] = array(
						'original'	=> HTTP_SERVER.'image/'.$result['image'],
						'popup' 	=> $this->model_tool_image->resize($result['image'], $config_image_popup_width, $config_image_popup_height),
						'thumb' 	=> $this->model_tool_image->resize($result['image'], $img_w2, $img_h2)
					);
				}
			}

			$data['continue'] = $this->url->link('common/home');
			
			$products_results = $this->model_revolution_revolution->getBlogProducts($revblog_id);
    	$data_products = $this->prepareProductsData($products_results, $setting2);
			$data['products'] = array();
			if ($data_products) {
				$data['products'] = data_products['products'];
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
