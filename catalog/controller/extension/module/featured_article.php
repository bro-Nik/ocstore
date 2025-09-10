<?php

require_once(DIR_SYSTEM . 'library/trait/module_settings.php');

class ControllerExtensionModuleFeaturedArticle extends Controller {
  use TraitModuleSettings;

	public function index() {
		if (isset($this->request->get['product_id']) || isset($this->request->get['manufacturer_id']) || isset($this->request->get['path'])) {
			$this->load->language('extension/module/featured_article');
			$this->load->model('blog/article');
			$this->load->model('tool/image');

			$setting = $this->getSettings('featured_articles');

			$data['title'] = $setting['title'];
			$data['articles'] = array();
			$results = array();
			
			if (isset($this->request->get['product_id'])) {
				$filter_data = array(
					'product_id'  => $this->request->get['product_id'],
					'limit' => $setting['limit']
				);
					
				$results = $this->model_blog_article->getArticleRelatedByProduct($filter_data);
					
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$filter_data = array(
					'manufacturer_id'  => $this->request->get['manufacturer_id'],
					'limit' => $setting['limit']
				);
					
				$results = $this->model_blog_article->getArticleRelatedByManufacturer($filter_data);
			} else {
				$parts = explode('_', (string)$this->request->get['path']);

				if(!empty($parts) && is_array($parts)) {
					$filter_data = array(
						'category_id'  => array_pop($parts),
						'limit' => $setting['limit']
					);
					$results = $this->model_blog_article->getArticleRelatedByCategory($filter_data);			
				}
			}
	
			if ($results) {
				foreach ($results as $result) {
					// Получаем категорию статьи для хлебных крошек
					$article_categories = $this->model_blog_article->getCategories($result['article_id']);
					$blog_category_id = 0;
					
					if ($article_categories) {
						// Берем первую категорию
						$blog_category_id = $article_categories[0]['blog_category_id'];
					}

					$data['articles'][] = array(
						'article_id'  => $result['article_id'],
						'thumb'       => $this->model_tool_image->resize($result['image'] ?? 'placeholder.png', $setting['width'], $setting['height']),
						'name'        => $result['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('configblog_article_description_length')) . '..',
						'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'viewed'      => $result['viewed'],
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'rating'      => $this->config->get('configblog_review_status') ? $result['rating'] : false,
						// 'href'        => $this->url->link('blog/article', 'article_id=' . $result['article_id']),
						'href'        => $this->url->link('blog/article', 'blog_category_id=' . $blog_category_id . '&article_id=' . $result['article_id'])
					);
				}
				
				return $this->load->view('/module/article_slider', $data);
			}
		}
	}
}
