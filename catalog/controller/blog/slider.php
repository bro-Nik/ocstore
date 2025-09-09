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
		
		$data['heading_title'] = $setting['title'] ?? '';
		$data['image_status'] = $setting['image_status'];
		$data['url_all'] = $setting['url_all'];
		$data['url_all_text'] = $setting['url_all_text'];
		$data['articles'] = array();

		$data_sort = array(
			'start' => 0,
			'limit' => (int)$setting['news_limit'] ?? 5,
			'order' => 'DESC',
			'filter_blog_category_id' => $setting['blog_category_id'] ?? false
		);

		$blogs = $this->model_blog_article->getArticles($data_sort);

		foreach ($blogs as $blog) {
			
			if ($blog['image']) {
				$image = $this->model_tool_image->resize($blog['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
			}

			$data['articles'][] = array(
				'name'       => $blog['name'],
				'thumb'       => $image,
				'date_added'  => date($this->language->get('date_format_short'), strtotime($blog['date_added'])),
				'href'        => $this->url->link('blog/article', 'article_id=' . $blog['article_id'])
			);
		}

		if ($data['articles']) {
			return $this->load->view('/module/article_slider', $data);
		}
	}
}
