<?php
require_once('catalog/controller/base/products_list.php');
require_once('catalog/controller/extension/module/validator.php');
require_once('catalog/controller/trait/product.php');
require_once('catalog/controller/trait/template.php');

class ControllerProductProduct extends ControllerBaseProductsList {
	use \ValidatorTrait, \ProductInfo, \TemplateTrait;
	private $error = array();

	public function index() {
		$this->load->language('product/product');
		$this->load->language('revolution/revolution');

		$data['product_id'] = (int)$this->request->get['product_id'] ?? 0;
		$product_info = $this->model_catalog_product->getProduct($data['product_id']);

		if ($product_info) {
    	$this->load->model('revolution/revolution');
			$this->load->model('catalog/product');
			$data['breadcrumbs'] = $this->prepareBreadcrumbs($product_info);
			$data['captcha'] = $this->getCaptcha('review');
 			$data['product_variants'] = $this->load->controller('extension/module/prodvar'); 
      $data['featured_articles'] = $this->load->controller('extension/module/featured_article');

			$this->setMetaData($product_info, 1);
			$this->noindexCheck($product_info);
			$this->setTitleDescription($data, $product_info, 1);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');

			$this->prepareProductImages($product_info, $data);
			if ($data['thumb']) {
				$this->document->setOgImage($data['thumb']);
			}

			$data['options'] = $this->prepareProductOptions($data['product_id']);
			$this->prepareProductPrice($product_info, $data);
			$this->prepareProductStikers($product_info, $data);
			$this->prepareProductReviews($product_info, $data);
			$this->prepareProductTags($product_info, $data);
			$this->prepareProductOther($product_info, $data);
			$this->prepareProductTabs($product_info, $data);

			$related_products = $this->model_catalog_product->getProductRelated($data['product_id']);
			$data['accessories'] = $this->prepareProducts($related_products);

    	// OCFilter Start
    	if ($this->registry->get('ocfilter') && $this->ocfilter->startup()) {
      	$this->ocfilter->api->setProductItemControllerData($data);
    	}
    	// OCFilter End
			$this->addCommonTemplateData($data);
    	$this->response->setOutput($this->load->view('product/product', $data));
		} else {
			$this->ErrorPage();
		}
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		$product_id = $this->request->post['product_id'] ?? 0;
		$recurring_id = $this->request->post['recurring_id'] ?? 0;
		$quantity = $this->request->post['quantity'] ?? 1;

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
			$requiredFields = ['name', 'text', 'rating'];
			$errors = $this->validateForm($this->request->post, $requiredFields, 'review');
			if (!$errors) {
				$this->load->model('catalog/review');
				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);
				$json['success'] = 'Спасибо за ваш отзыв. Он появится после проверки на спам';
			} else {
				$json['toasts'] = $errors;
			}
		}
		ob_clean();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function writeAnswer() {
		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$requiredFields = ['name', 'text'];
			$errors = $this->validateForm($this->request->post, $requiredFields, 'answer');

			if (!$errors) {
    		$this->load->model('revolution/revolution');
				$this->model_revolution_revolution->addanswer($this->request->get['product_id'], $this->request->post);
				$json['success'] = 'Спасибо за ваш вопрос. Он появится после проверки на спам';
			} else {
				$json['toasts'] = $errors;
			}
		}
		ob_clean();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
