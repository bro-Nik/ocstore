<?php
class ControllerRevolutionRevsocfb extends Controller {
	public function index() {

		$setting = $this->config->get('revtheme_home_socv');

		if (!$setting['status_fb']) {
			return false;
		}

		$data['width'] = $setting['width_fb'];
		$data['height'] = $setting['height_fb'];
		$data['id'] = $setting['id_fb'];

		return $this->load->view('revolution/revsocfb', $data);

	}
}