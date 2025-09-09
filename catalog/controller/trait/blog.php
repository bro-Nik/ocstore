<?php
trait BlogTrait {
	public function columnLeft($blog_category_id) { 
		// Получаем все категории
		$this->load->model('blog/category');
    $categories = $this->model_blog_category->getCategories();

		// Передаем в шаблон
    $data['blog_category_id'] = $blog_category_id;
    $data['categories'] = array();
    
    foreach ($categories as $category) {
      $data['categories'][] = array(
        'category_id' => $category['blog_category_id'],
        'name'        => $category['name'],
        'href'        => $this->url->link('blog/category', 'blog_category_id=' . $category['blog_category_id']),
      );
    }
		return $this->load->view('blog/column_left', $data);
	}
}
