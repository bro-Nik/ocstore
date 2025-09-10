<?php
trait CacheTrait {
  public function getCache($key) {
		if ($this->config->get('developer_theme')) {
			$cache = $this->cache->get($key);
    	if ($cache !== false && $cache !== null) {
		    // print_r('Взято из кэша. Key = ' . $key . '<br>');
      	return $cache;
    	}
		}
    return false;
  }

  public function setCache($key, $output, $sec = null) {
		if ($this->config->get('developer_theme')) {
		  // print_r('Сохранено в кэш. Key = ' . $key . '<br>');
      $this->cache->set($key, $output);
		}
  }
}
