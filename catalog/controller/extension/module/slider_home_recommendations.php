<?php
class ControllerExtensionModuleSliderHomeRecommendations extends Controller {

	public function index($settings) {
		if (!$settings['status']) {
			return false;
		}

		$this->load->model('tool/image');
		$data = $settings;
		$slides = $settings['slides'];
		$data['slides'] = array();
		
		if (!empty($slides)){
			foreach ($slides as $slide) {
				$data['slides'][] = array(
					'title'       		=> html_entity_decode($slide['title'], ENT_QUOTES, 'UTF-8'),
					'image'       		=> $this->model_tool_image->resize($slide['image'], $settings['width'], $settings['height']),
					'description' 		=> html_entity_decode($slide['description'], ENT_QUOTES, 'UTF-8'),
					'link'        		=> html_entity_decode($slide['link'], ENT_QUOTES, 'UTF-8'),
					'link_title'  		=> $slide['link_title'],
					'sort_order'      => $slide['sort_order']
				);
			}
		}

		if (!empty($data['slides'])){
			foreach ($data['slides'] as $key => $value) {
				$sort[$key] = $value['sort_order'];
			}
			array_multisort($sort, SORT_ASC, $data['slides']);
		}

		return $this->load->view('extension/module/slider_home_recommendations', $data);
		
	}
}
