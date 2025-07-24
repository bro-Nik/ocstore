<?php
class ControllerBlogSlider extends Controller {

	public function index() {
		
		// $this->load->language('revolution/revblog');
		
		// $data['all_news'] = $this->url->link('revolution/revblog');

		// $this->load->model('revolution/revolution');
		$this->load->model('blog/article');
		$this->load->model('tool/image');

		$setting = $this->config->get('home_blog');
		
		if (!$setting['status']) {
			return false;
		}
		
		if ($setting['title']) {
			$data['heading_title'] = $setting['title'];
		} else {
			$data['heading_title'] = '';
		}
		
		$data['image_status'] = $setting['image_status'];
		$data['url_all'] = $setting['url_all'];
		$data['url_all_text'] = $setting['url_all_text'];
		
		$data['blogs'] = array();

		if (!$setting['news_limit']) {
			$setting['news_limit'] = 5;
		}

		if ($setting['blog_category_id']) {
			$filter_category_id = $setting['blog_category_id'];
		} else {
			$filter_category_id = false;
		}
		
		$data_sort = array(
			'start' => 0,
			'limit' => (int)$setting['news_limit'],
			'order' => 'DESC',
			'filter_blog_category_id' => $filter_category_id
		);

		$blogs = $this->model_blog_article->getArticles($data_sort);

		foreach ($blogs as $blog) {
			
			if ($blog['image']) {
				$image = $this->model_tool_image->resize($blog['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
			}
			// $description = utf8_substr(strip_tags(html_entity_decode($blog['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['desc_limit']) . '..';

			$data['blogs'][] = array(
				'name'       => $blog['name'],
				'image'       => $image,
				'date_added'  => date($this->language->get('date_format_short'), strtotime($blog['date_added'])),
				// 'description' => $description,
				'href'        => $this->url->link('blog/article', 'article_id=' . $blog['article_id'])
			);
		}

		if ($data['blogs']) {
			return $this->load->view('/module/home_blog', $data);
		}
	}
}
