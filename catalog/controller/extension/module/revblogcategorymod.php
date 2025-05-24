<?php
class ControllerExtensionModuleRevblogcategorymod extends Controller {
	public function index($setting) {
		static $module = 0;
		
		$this->load->model('revolution/revolution');
		$this->load->model('tool/image');

		if (!$setting['status']) {
			return false;
		}
		
		if (isset($this->request->get['revblog_category_id'])) {
			$parts = explode('_', (string)$this->request->get['revblog_category_id']);
		} else {
			$parts = array();
		}
		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}
		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}

		if ($setting['title']) {
			$data['heading_title'] = ('<i class="'.$setting['icon'].'"></i>' . $setting['title']);
		} else {
			$data['heading_title'] = false;
		}
		
		$data['blog_categories'] = array();

		if ($setting['blog_category_id']) {
			$filter_category_id = $setting['blog_category_id'];
		} else {
			$filter_category_id = 0;
		}
		
		$blog_categories = $this->model_revolution_revolution->getBlogCategories($filter_category_id);

		foreach ($blog_categories as $category) {

			$children_data = array();
			if ($category['category_id'] == $data['category_id']) {
				$children = $this->model_revolution_revolution->getBlogCategories($category['category_id']);
				foreach($children as $child) {
					$children_data[] = array(
						'category_id' => $child['category_id'],
						'title' => $child['title'],
						'href'  => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}
			}
			
			$data['blog_categories'][] = array(
				'category_id' => $category['category_id'],
				'title'       => $category['title'],
				'href'        => $this->url->link('revolution/revblog_category', 'revblog_category_id=' . $category['category_id']),
				'children'    => $children_data
			);
		}

		$data['module'] = $module++;
		
		if ($data['blog_categories']) {
			return $this->load->view('extension/module/revblogcategorymod', $data);
		}
	}
}