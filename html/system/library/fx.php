<?php
class FX {
	private $title;

	public function __construct($data = NULL) {
		
		
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}
	
	
    public function pagination($data) {
			
		$output = str_replace('={page}"', '=1"', $data);
		
		$output = str_replace(array('?page=1"', '&amp;page=1"'), '"', $output);
		$output = str_replace(array('?page=1&', '&amp;page=1&'), '&', $output);
		$output = str_replace('/page-1"', '"', $output);
		$output = str_replace('/page-1/"', '/"', $output);
		$output = str_replace('/page-1?', '?', $output);
		$output = str_replace('/page-1/?', '/?', $output);
		

		return $output;
    }
}
