<?php
class ControllerRevolutionRevsocvinsta extends Controller {
	public function index() {
		$setting = $this->config->get('revtheme_home_socv');
		if (!$setting['status_insta']) {
			return false;
		}
		$data['width'] = $setting['width_insta'];
        if (isset($setting['limit_insta']) && $setting['limit_insta'] != "") {
            $limit = $setting['limit_insta'];
        } else {
            $limit = 12;
        }
        $access_token = $setting['id_insta'];
        $data['username'] = $setting['name_insta'];
        $json_link="https://api.instagram.com/v1/users/self/media/recent/?access_token={$access_token}&count={$limit}";
		$data["json"] = $json = file_get_contents($json_link);
        $posts = json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $json), true);
	    $data["images"] = $this->cache->get('revinsta');
		if (!$data["images"]) {
			$data["images"] = [];
			foreach ($posts['data'] as $post) {
				array_push($data["images"], $post['images']['thumbnail']['url']);
			}
			$this->cache->set('revinsta', $data["images"]);
		}
		return $this->load->view('revolution/revsocvinsta', $data);
	}
}