<?php
class ControllerRevolutionRevsocok extends Controller {
	public function index() {

		$setting = $this->config->get('revtheme_home_socv');

		if (!$setting['status_ok']) {
			return false;
		}

		$data['width'] = $setting['width_ok'];
		$data['height'] = $setting['height_ok'];
		$data['id'] = $setting['id_ok'];

		return $this->load->view('revolution/revsocok', $data);

	}
}