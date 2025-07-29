<?php
class ControllerExtensionModuleSlideshow extends Controller {

	public function index($setting) {

		if (!$setting['status']) {
			return false;
		}

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$data = $setting;

		$slides = $setting['slides'];
		$data['slides'] = array();
		
		if (!empty($slides)){
			foreach ($slides as $slide) {
				$data['slides'][] = array(
					'title'       		=> html_entity_decode($slide['title'], ENT_QUOTES, 'UTF-8'),
					'image'       		=> $this->model_tool_image->resize($slide['image'], $setting['width'], $setting['height']),
					'description' 		=> html_entity_decode($slide['description'], ENT_QUOTES, 'UTF-8'),
					'link'        		=> html_entity_decode($slide['link'], ENT_QUOTES, 'UTF-8'),
					'link_title'  		=> $slide['link_title'],
					'sort_order'      => $slide['sort_order']
				);
			}
		} else {			
			$data['slides'] = false;
		}
		if (!empty($data['slides'])){
			foreach ($data['slides'] as $key => $value) {
				$sort[$key] = $value['sort_order'];
			}
			array_multisort($sort, SORT_ASC, $data['slides']);
		}

		return $this->load->view('extension/module/slideshow', $data);
		
	}
}
