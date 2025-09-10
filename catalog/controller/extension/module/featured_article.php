<?php

require_once(DIR_SYSTEM . 'library/trait/module_settings.php');

class ControllerExtensionModuleFeaturedArticle extends Controller {
  use TraitModuleSettings;

	public function index($external_settings = []) {
		$this->load->language('extension/module/featured_article');
		$this->load->model('blog/article');
		$this->load->model('tool/image');

		$setting = $this->getSettings('featured_articles');

		$data['title'] = $setting['title'];
		// $data['url_all'] = $setting['url_all'] ?? '';
		// $data['url_all_text'] = $setting['url_all_text'] ?? '';
		$data['articles'] = array();
		$results = array();
		
		$ids = $external_settings && $external_settings['status'] ? $external_settings['blog_category_id'] : false;
		if ($ids) {
			$data_sort = array(
				'start' => 0,
				'limit' => (int)$setting['limit'] ?? 5,
				'order' => 'DESC',
				'filter_blog_category_id' => $ids ?? false
			);

			$results = $this->model_blog_article->getArticles($data_sort);
		} elseif (isset($this->request->get['product_id'])) {
			$results = $this->model_blog_article->getArticleRelatedByProduct([
				'product_id' => $this->request->get['product_id'],
				'limit' => $setting['limit']
			]);
		} elseif (isset($this->request->get['manufacturer_id'])) {
			$results = $this->model_blog_article->getArticleRelatedByManufacturer([
				'manufacturer_id'  => $this->request->get['manufacturer_id'],
				'limit' => $setting['limit']
			]);
		} elseif (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
			if(!empty($parts) && is_array($parts)) {
				$results = $this->model_blog_article->getArticleRelatedByCategory([
					'category_id'  => array_pop($parts),
					'limit' => $setting['limit']
				]);			
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
