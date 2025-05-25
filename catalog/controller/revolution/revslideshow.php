<?php
class ControllerRevolutionRevslideshow extends Controller {
	public function index() {

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$data['banners'] = array();

		$setting = $this->config->get('revtheme_home_slideshow');
		
		$data['status'] = $setting['status'];
		$data['slides'] = $setting['slides'];
		// $data['slide_video'] = $setting['video'];
		$data['slide_video'] = $setting['video'] ?? '';
		if (isset($setting['video_width_img'])) {
			$data['video_width_img'] = $setting['video_width_img'];
		} else {
			$data['video_width_img'] = false;
		}
		$data['slide_width'] = $setting['width'].'px';
		$data['slide_height'] = $setting['height'].'px';

		if (!$setting['status'] || $setting['banner_id'] == '') {
			return false;
		}
		
		$data['autoscroll'] = $setting['autoscroll'];
		
		$results = $this->model_design_banner->getBanner($setting['banner_id']);
		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}
		
		$results_slideshows = $this->config->get('revtheme_home_slideshow_item');
		if (!empty($results_slideshows)){
			foreach ($results_slideshows as $result_slideshow) {
				$data['home_slideshows'][] = array(
					'title'       		=> html_entity_decode($result_slideshow['title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8'),
					'image'       		=> $this->model_tool_image->resize($result_slideshow['image'], $setting['width'], $setting['height']),
					'description' 		=> html_entity_decode($result_slideshow['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8'),
					'link'        		=> html_entity_decode($result_slideshow['link'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8'),
					'link_title'  		=> $result_slideshow['link_title'][$this->config->get('config_language_id')],
					'sort'        		=> $result_slideshow['sort'],
					'checkbox'    		=> isset($result_slideshow['checkbox']) ? 1 : 0,
					'title_color'       => $result_slideshow['title_color'],
					'description_color' => $result_slideshow['description_color'],
					'link_title_color'  => $result_slideshow['link_title_color'],
					'title_color'       => $result_slideshow['title_color'],
					'title_fsize'		=> $result_slideshow['title_fsize'],
					'title_xpos'		=> $result_slideshow['title_xpos'],
					'title_ypos'		=> $result_slideshow['title_ypos'],
					'description_fsize'	=> $result_slideshow['description_fsize'],
					'description_xpos'	=> $result_slideshow['description_xpos'],
					'description_ypos'	=> $result_slideshow['description_ypos'],
					'link_title_fsize'	=> $result_slideshow['link_title_fsize'],
					'link_title_xpos'	=> $result_slideshow['link_title_xpos'],
					'link_title_ypos'	=> $result_slideshow['link_title_ypos']
				);
			}
		} else {			
			$data['home_slideshows'] = false;
		}
		if (!empty($data['home_slideshows'])){
			foreach ($data['home_slideshows'] as $key => $value) {
				$sort[$key] = $value['sort'];
			}
			array_multisort($sort, SORT_ASC, $data['home_slideshows']);
		}

		return $this->load->view('revolution/revslideshow', $data);
		
	}
}
