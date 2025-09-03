<?php
//==============================================================================
// Redirect Manager v300.1 + Cache Optimization
// 
// Author: Clear Thinking, LLC + Optimization
//==============================================================================

require_once('catalog/controller/trait/cache.php');

class ModelExtensionModuleRedirectManager extends Model {
	use \CacheTrait;

  private $type = 'module';
  private $name = 'redirect_manager';
  private $redirects = [];
  private $settings = [];
  private $wildcard_redirects = [];
  private $cache_loaded = false;
  private $settings_loaded = false;
  
  public function redirect() {
    $settings = $this->getSettings();
    if (empty($settings['status'])) return;
      
    $this->loadRedirectsCache();
        
    $server = $this->request->server;
    
    $request_uri = (strpos('%', $server['REQUEST_URI'])) ? urldecode($server['REQUEST_URI']) : $server['REQUEST_URI'];
    $browser_url = explode('?', $request_uri);
    $query_string = (!empty($browser_url[1])) ? $browser_url[1] : '';
    
    $from = 'http' . (!empty($server['HTTPS']) && $server['HTTPS'] != 'off' ? 's' : '') . '://' . $server['HTTP_HOST'] . $browser_url[0];
    $from = strtolower($from);
    if (substr($from, -1) == '/') $from = substr($from, 0, -1);
      
    // Поиск редиректа в кеше
    $redirect_data = $this->findRedirect($from);
      
    if ($redirect_data) {
      // Обновляем статистику
      $this->updateRedirectStats($redirect_data[$this->name . '_id']);
      
      $to = $this->processRedirect($redirect_data, $from, $query_string);
      
      header('Location: ' . str_replace('&amp;', '&', $to), true, $redirect_data['response_code']);
      exit();
        
    } elseif (isset($this->request->get['route']) && $this->request->get['route'] == 'error/not_found' && !empty($settings['record']['404']['s'])) {
      $this->record404($server, $request_uri, $settings);
    }
  }
  
  //------------------------------------------------------------------------------
  // Cache Management
  //------------------------------------------------------------------------------
  
  private function loadRedirectsCache() {
    if ($this->cache_loaded) return;

    $cache_key = 'redirects.all.' . $this->name;
		$cache = $this->getCache($cache_key);
    
    if ($cache && !empty($cache['redirects'])) {
      $this->redirects = $cache['redirects'];
      $this->wildcard_redirects = $cache['wildcards'];
      $this->cache_loaded = true;
      return;
    }
      
    // Загрузка из базы данных
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $this->name . "` 
                              WHERE active = 1 
                              AND (date_start = '0000-00-00' OR date_start < NOW()) 
                              AND (date_end = '0000-00-00' OR date_end > NOW())");
      
    foreach ($query->rows as $row) {
      $from_url = strtolower($row['from_url']);
      $row['from_url'] = $from_url;
      
      if (strpos($from_url, '*') !== false || strpos($from_url, '?') !== false) {
        // Wildcard редиректы
        $this->wildcard_redirects[] = $row;
      } else {
        // Обычные редиректы
        $this->redirects[$from_url] = $row;
        
        // Добавляем вариант с trailing slash
        if (substr($from_url, -1) !== '/') {
          $this->redirects[$from_url . '/'] = $row;
        }
      }
    }
      
    // Сохраняем в кеш
    $this->setCache($cache_key, [
      'redirects' => $this->redirects,
      'wildcards' => $this->wildcard_redirects
		]);
    
    $this->cache_loaded = true;
  }
  
  public function clearCache() {
    $cache_key = 'redirects.all.' . $this->name;
    $this->cache->delete($cache_key);
    $this->redirects = [];
    $this->wildcard_redirects = [];
    $this->cache_loaded = false;
  }
  
  //------------------------------------------------------------------------------
  // Redirect Processing
  //------------------------------------------------------------------------------
  
  private function findRedirect($url) {
    $url = strtolower($url);
    
    // 1. Прямое совпадение
    if (isset($this->redirects[$url])) {
      return $this->redirects[$url];
    }
    
    // 2. Вариант с trailing slash
    if (substr($url, -1) !== '/' && isset($this->redirects[$url . '/'])) {
      return $this->redirects[$url . '/'];
    }
    
    // 3. Wildcard редиректы
    foreach ($this->wildcard_redirects as $redirect) {
      if ($this->matchWildcard($redirect['from_url'], $url)) {
        return $redirect;
      }
    }
    
    return null;
  }
  
  private function matchWildcard($pattern, $url) {
    // Преобразуем wildcard в regex
    $regex_pattern = str_replace('\*', '.*', preg_quote($pattern, '/'));
    $regex_pattern = '/^' . $regex_pattern . '$/';
    
    return preg_match($regex_pattern, $url) || preg_match($regex_pattern, $url . '/');
  }
  
  private function processRedirect($redirect_data, $from, $query_string) {
    $to = $redirect_data['to_url'];
    
    // Обработка wildcards
    if (strpos($redirect_data['from_url'], '*') !== false) {
      $from_parts = explode('*', $redirect_data['from_url']);
      $to_parts = explode('*', $to);
      
      $wildcard_values = [];
      $temp_from = $from;
      
      foreach ($from_parts as $part) {
        if ($part === '') continue;
        $pos = strpos($temp_from, $part);
        if ($pos !== false) {
          $wildcard_values[] = substr($temp_from, 0, $pos);
          $temp_from = substr($temp_from, $pos + strlen($part));
        }
      }
      $wildcard_values[] = $temp_from;
      
      // Собираем конечный URL
      $to = '';
      foreach ($to_parts as $index => $part) {
          $to .= $part . ($wildcard_values[$index] ?? '');
      }
    }
      
    // Добавляем query string
    if ($query_string) {
      $to .= (strpos($to, '?') !== false) ? '&' . $query_string : '?' . $query_string;
    }
      
    // Убираем trailing slash
    if (substr($to, -1) === '/') {
      $to = substr($to, 0, -1);
    }
      
    return $to;
  }
  
  private function updateRedirectStats($redirect_id) {
    $this->db->query("UPDATE `" . DB_PREFIX . $this->name . "` 
                      SET times_used = times_used + 1 
                      WHERE " . $this->name . "_id = " . (int)$redirect_id);
  }
  
  //------------------------------------------------------------------------------
  // 404 Recording
  //------------------------------------------------------------------------------
  
  private function record404($server, $request_uri, $settings) {
    $ignore_ips = explode("\n", $settings['ignore_ips']);
    $ignore_ips = array_map('trim', $ignore_ips);
    
    $ip_match = false;
    $remote_ip = $server['REMOTE_ADDR'];
      
    foreach ($ignore_ips as $range) {
      $range = explode('-', $range);
      if (empty($range[0])) continue;
      if (empty($range[1])) $range[1] = $range[0];
      
      if (ip2long($remote_ip) >= ip2long($range[0]) && ip2long($remote_ip) <= ip2long($range[1])) {
        $ip_match = true;
        break;
      }
    }
      
    $ignore_user_agents = explode("\n", $settings['ignore_user_agents']);
    $ignore_user_agents = array_map('trim', $ignore_user_agents);
    
    $user_agent = $server['HTTP_USER_AGENT'] ?? '';
      
    if (!$ip_match && !in_array($user_agent, $ignore_user_agents)) {
      $from = 'http' . (!empty($server['HTTPS']) && $server['HTTPS'] != 'off' ? 's' : '') . '://' . $server['HTTP_HOST'] . $request_uri;
      $this->db->query("INSERT INTO `" . DB_PREFIX . $this->name . "_404` 
                      SET date_time = NOW(), 
                          url = '" . $this->db->escape($from) . "', 
                          ip = '" . $this->db->escape($remote_ip) . "', 
                          user_agent = '" . $this->db->escape($user_agent) . "'");
    }
  }
  
  //------------------------------------------------------------------------------
  // Settings and Utility Methods
  //------------------------------------------------------------------------------
  
  private function getSettings() {
    if ($this->settings_loaded) {
			return $this->settings;
		}

		$cache_key = 'redirects.settings';
		$cache = $this->getCache($cache_key);
    if ($cache !== false) {
			$this->settings_loaded = true;
      return $cache;
    }

    $code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
    
    $settings = [];
    $settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting 
                                      WHERE `code` = '" . $this->db->escape($code) . "' 
                                      ORDER BY `key` ASC");
      
    foreach ($settings_query->rows as $setting) {
      $value = $setting['value'];
      if ($setting['serialized']) {
        $value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
      }
      
      $split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
      
      if (count($split_key) == 1)     $settings[$split_key[0]] = $value;
      elseif (count($split_key) == 2) $settings[$split_key[0]][$split_key[1]] = $value;
      elseif (count($split_key) == 3) $settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
      elseif (count($split_key) == 4) $settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
      else                            $settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
    }
    
		$this->settings_loaded = true;
    $this->setCache($cache_key, $settings);
    return $settings;
  }
  
  //------------------------------------------------------------------------------
  // Public methods for admin operations
  //------------------------------------------------------------------------------
  
  public function onRedirectUpdate() {
    $this->clearCache();
  }
}
