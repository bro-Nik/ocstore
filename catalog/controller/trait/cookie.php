<?php
trait CookieTrait {
  public function getCookie($key) {
		if (isset($this->request->cookie[$key])) {
    	$cookie = $this->request->cookie[$key];
			$cookie = html_entity_decode($cookie, ENT_QUOTES, 'UTF-8');

      if (strpos($cookie, '{') === 0 || strpos($cookie, '[') === 0) {
        return json_decode($cookie, true);
      }

			return explode(',', $cookie);
		}
  }
}
