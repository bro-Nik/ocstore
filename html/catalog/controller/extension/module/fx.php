<?php
class ControllerExtensionModuleFX extends Controller {

    private $error = array();
    private $total = '';
    private $total_default = 1;
    private $page = '';
    private $index_href = '';
    private $host = '';
    private $all = '';
	private $ocf = '';
	private $heading_title = '';
	private $append_footer = '';
	
	
    public function index($pre_data = array()) {
		
		$cancel = (float)VERSION < 1.9 ? $this->fuck15(array('data' => array())) : array('data' => array());
	
		if ($this->cancel()) return $cancel;
		
		if ((float)VERSION > 2.3 && ($pre_data == array() || !isset($pre_data['info']))) return $this->as_module();
		
		$data = $pre_data;

		$data['meta_description'] = $this->document->getDescription();
		$data['title'] = $this->document->getTitle();
		
		$data = $this->presetData($data);
		
		$out['route'] = $route = $this->route();
		
		$out['page'] = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 0;
		
		$pageurl = $rel_pageurl = $path_manufacturer_error = '';	

		$out['clear'] = $no_data = 0;
		
		$limit = $this->limit();

		$this->page = $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;		
		
		$rel_url = $this->relUrl();

		$fx['name'] = $data['name'] = $this->setName($data['info'], $route);
		$canonical = $this->config->get('fx_canonicals');
		$redirects = $this->config->get('fx_redirects');

		switch ($route){
			case 'product/category':
				$parts = explode('_', (string)$this->request->get['path']);
				$category_id = (int)array_pop($parts);
				$rel_pageurl = 'path=' . $category_id . $rel_url['prev_next'];
				$out['href'] = $this->url->link('product/category', 'path=' . $category_id . $rel_url['canonical'], 'SSL');
				break;
			case 'product/manufacturer/info':
				$rel_pageurl = 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $rel_url['prev_next'];
				$out['href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . (int)$this->request->get['manufacturer_id'] . $rel_url['canonical'], 'SSL');
				break;
			case 'product/special':
				$out['href'] = $this->url->link('product/special', $rel_url['canonical'], 'SSL');
				break;
			case 'product/product':				
				$rel_pageurl = 'product_id=' . $this->request->get['product_id'] . $rel_url['prev_next'];
				$out['href'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id'] . $rel_url['canonical'], 'SSL');
				$canonical = $this->config->get('fx_canonicals_p');
				$redirects = $this->config->get('fx_redirects_p');
				$no_data = 1;
				break;
			case 'information/information':
				$rel_pageurl = 'information_id=' . $this->request->get['information_id'] . $rel_url['prev_next'];
				$out['href'] = $this->url->link('information/information', 'information_id=' . (int)$this->request->get['information_id'] . $rel_url['canonical'], 'SSL');
				$canonical = $this->config->get('fx_canonicals_i');
				$redirects = $this->config->get('fx_redirects_i');
				$no_data = 1;		
				break;
			case 'common/home':
				$out['href'] = $this->url->link('common/home', '', 'SSL');				
				if (substr($out['href'], -27) == 'index.php?route=common/home') $out['href'] = str_replace('index.php?route=common/home', '', $out['href']);
				$canonical = $this->config->get('fx_canonicals_h');
				$redirects = $this->config->get('fx_redirects_h');
				$no_data = 1;
				break;
			default:
				$out['href'] = $this->url->link($route, '', 'SSL');
				$no_data = 1;
				break;
		}
		
		$this->index_href = $href = $out['href'];
		$href_no_page = $this->url->link($route, $rel_pageurl, 'SSL');
		
		$out['heading_title'] = $this->heading_title = $data['data']['heading_title'];
		if (isset($data['data']['seo_h1']) && !empty($data['data']['seo_h1'])) $out['heading_title'] = $this->heading_title = $data['data']['seo_h1'];
		$out['name'] = $data['name'];		
		$out['meta_description'] = ($data['meta_description'] != '') ? $data['meta_description'] : $data['name'];
		$out['title'] = (($data['title'] == '') || ($this->config->get('fx_title_to_name') && ($page > 1))) ? $data['name'] : $data['title'];
		
		$out['data']['manufacturer_mfp'] = $m_name = $ocf = '';
		
		if (($route == 'product/category') && !empty($this->request->get['manufacturer_id']) && !empty($this->request->get['path'])) {
			$mid = (int)$this->request->get['manufacturer_id'];
			$this->load->model('extension/module/fx');
			$cid = explode('_', $this->request->get['path']);
			$category_id = (int)array_pop($cid);
			
			$m_name = $this->model_extension_module_fx->getManufacturer($mid);
			
			//$out['data']['manufacturer_mfp'] = $this->getMFP($mid, $category_id);
			$out['data']['footer'] = $this->getMFP($mid, $category_id) . $data['data']['footer'];
			
			if ((float)VERSION < 3 ) {
				$c_m_url = $this->url->link('product/category', 'path=' . $category_id. '&manufacturer_id=' . $mid, true);
			} else {
				$m_full_url = parse_url($this->url->link('product/manufacturer/info', 'manufacturer_id=' . $mid, true));
				$m_url_path = ltrim(str_replace('/index.php', '', $m_full_url['path']), '/');
				$m_short_url = !empty($m_url_path) ? $m_url_path : '?manufacturer_id=' . $mid;
				$c_m_url = rtrim($this->url->link('product/category', 'path=' . $category_id, true), '/') . '/' . $m_short_url;
			}
			
			$out['man_breadcrumb'] = array(
				'text' => $m_name,
				'href' => $c_m_url
			);
			
			$out['clear'] = 1;			
			
			$out['title'] = str_replace($fx['name'], $fx['name'].$m_name, $out['title']);
			$out['meta_description'] = str_replace($fx['name'], $fx['name'].$m_name, $out['meta_description']);
			$out['heading_title'] = str_replace($fx['name'], $fx['name'].$m_name, $out['heading_title']);
			$out['name'] = str_replace($fx['name'], $fx['name'].$m_name, $out['name']);
			
			if ($this->config->get('fx_title_to_name')) $out['title'] = $fx['name'].$m_name;
			
			$c_m_num = array_keys($this->request->get);
			$path_num = (int)array_search('path', $c_m_num);
			$man_num = (int)array_search('manufacturer_id', $c_m_num);
			
			if ($man_num < $path_num) $path_manufacturer_error = 1;
			
			$data['data']['breadcrumbs'][] = $out['man_breadcrumb'];
		}		
		
		$this->total = $data['total'];
		$total_pages = strpos($rel_pageurl, 'limit=') == false ? $this->total_default : $data['total'];		
		
		if($this->config->get('fx_google')) {	
			if ($limit && ($total_pages > $page)){
				$out['next'] = $this->url->link($route, $rel_pageurl . '&page=' . ($page + 1), 'SSL');
			}
			if ($page > 1){
				$out['prev'] = $this->url->link($route, $rel_pageurl . ( $page == 2 ? '' : '&page='. ($page - 1)), 'SSL');
			}
		}		
		
		if ($this->multiTest($this->config->get('fx_no_pagination'))) $out['next'] = $out['prev'] = '';
		
		if ($this->testCanonical($canonical)) $out['canonical'] = $this->multiTest($this->config->get('fx_no_canonical')) ? '' : $href;
		
		if ($this->testWildcard($this->config->get('fx_redirect_pattern')) || $this->testRedirect($redirects, $href_no_page)) {
			$this->redirect_to($href);	
		}
		
		if ($this->testWildcard($this->config->get('fx_404_pattern'), $href) || $this->multiTest($this->config->get('fx_404_get'))) {
			$this->code404();
		}
		
		if ($this->config->get('fx_404_empty_page') && $this->page > 1 && $this->page > $this->total) {	
			$this->code404();
		}
		
		if ($this->testWildcard($this->config->get('fx_canonical_pattern'))) $out['canonical'] = $href;
		
		if (empty($out['canonical']) && $path_manufacturer_error) $out['canonical'] = $this->url->link('product/category', 'path=' . $category_id . '&manufacturer_id=' . (int)$this->request->get['manufacturer_id'], 'SSL');
		
		if ($this->ocfilter() === true) $out['canonical'] = $out['clear'] = '';
		
		if ($this->mfp()) $out['canonical'] = $href_no_page;
		
		if ($this->pageEmpty($data['data'], 'canonical')) $out['canonical'] = $href_no_page;
		
		if ($this->pageEmpty($data['data'], 'prev_next')) $out['next'] = $out['prev'] = '';
		
		$this->load->language('extension/module/fx');
	
		$after = '';
		$is_sort = isset($this->request->get['sort']) && isset($this->request->get['order']);
		$is_limit = isset($this->request->get['limit']);
		
		if ($this->config->get('fx_sortlimit') && ($is_sort || $is_limit) && isset($data['data']['sorts'])){
			$after = ' [';
			if (isset($this->request->get['sort'])){
				foreach ($data['data']['sorts'] as $sorts) {
					if ($sorts['value'] == $this->request->get['sort'].'-'.$this->request->get['order']) { 
						$after .= $sorts['text'];		
					}
				}
			}
			$after .= ($is_sort && $is_limit) ? ' | ' : '';
			$after .= $is_limit ? $this->request->get['limit'].']' : ']';
		}
		
		$after = $page < 2 ? '' : $after;
		
		$out['title'] = ($out['title'] == $data['title'] ) ? $out['title'].$m_name.$after : $out['title'].$after;
		$out['meta_description'] = ($out['meta_description'] == $data['meta_description'] ) ? $out['meta_description'].$m_name.$after : $out['meta_description'].$after;
		$out['heading_title'] = ($out['heading_title'] == $data['data']['heading_title'] ) ? $out['heading_title'].$m_name.$after : $out['heading_title'].$after;
			
		
		////***alpha→***////
			
			$pattern = $page > 1 ? $this->config->get('fx_pattern') : $this->config->get('fx_pattern_category');			
			if ($route == 'product/product') $pattern = $this->config->get('fx_pattern_product');			
			if ($route == 'product/manufacturer/info' && $page < 2) $pattern = $this->config->get('fx_pattern_brand');
			if ($route == 'product/manufacturer' || empty($pattern)) $pattern = '[h1]';
			
			$h1 = str_replace("[h1]", $out['heading_title'], $pattern);
			$h1 = str_replace("[name]", $data['name'], $h1);
			$h1 = str_replace("[page]", $this->language->get('text_page'), $h1);
			$h1 = str_replace("[out_of]", $this->language->get('text_out_of'), $h1);
			$h1 = str_replace("[n]", $page, $h1);
			$h1 = str_replace("[total]", $data['total'], $h1);			
			$h1 = str_replace(array("[total]", "[h1]", "[name]", "[page]", "[out_of]", "[n]", "[total]"), '', $h1);
			
			if ((strpos($h1, '[url]')) !== false) $h1 = html_entity_decode(str_replace("[url]", $href, $h1));
			
			
			$pattern = $page > 1 ? $this->config->get('fx_metapattern') : $this->config->get('fx_metapattern_category');			
			if ($route == 'product/product') $pattern = $this->config->get('fx_metapattern_product');			
			if ($route == 'product/manufacturer/info' && $page < 2) $pattern = $this->config->get('fx_metapattern_brand');
			if ($route == 'product/manufacturer' || empty($pattern)) $pattern = '[desc]';

			$metadesc = str_replace("[desc]", $out['meta_description'], $pattern);
			$metadesc = str_replace("[name]", $data['name'], $metadesc);
			$metadesc = str_replace("[h1]", $out['heading_title'], $metadesc);
			$metadesc = str_replace("[page]", $this->language->get('text_page'), $metadesc);
			$metadesc = str_replace("[out_of]", $this->language->get('text_out_of'), $metadesc);
			$metadesc = str_replace("[n]", $page, $metadesc);
			$metadesc = str_replace("[total]", $data['total'], $metadesc);			
			$metadesc = str_replace(array("[desc]", "[h1]", "[name]", "[page]", "[out_of]", "[n]", "[total]"), '', $metadesc);
			
			
			$pattern = $page > 1 ? $this->config->get('fx_title_pattern') : $this->config->get('fx_title_pattern_category');			
			if ($route == 'product/product') $pattern = $this->config->get('fx_title_pattern_product');			
			if ($route == 'product/manufacturer/info' && $page < 2) $pattern = $this->config->get('fx_title_pattern_brand');	
			if ($route == 'product/manufacturer' || empty($pattern)) $pattern = '[title]';		

			$title = str_replace("[title]", $out['title'], $pattern);
			$title = str_replace("[name]", $data['name'], $title);
			$title = str_replace("[h1]", $out['heading_title'], $title);
			$title = str_replace("[page]", $this->language->get('text_page'), $title);
			$title = str_replace("[out_of]", $this->language->get('text_out_of'), $title);
			$title = str_replace("[n]", $page, $title);
			$title = str_replace("[total]", $data['total'], $title);			
			$title = str_replace(array("[title]", "[h1]", "[name]", "[page]", "[out_of]", "[n]", "[total]"), '', $title);
		
			if ($h1 != '') $out['heading_title'] = $h1;
			if ($page > 1 && !$this->config->get('fx_description')) $out['clear'] = 1;
			if ($metadesc != '') $out['meta_description'] = $metadesc;
			if ($title != '') $out['title'] = $title;			

		////***←alpha***////

		//$out['data']['page'] = $out['page'];
		$out['data']['fx_span'] = $this->getH1($out['heading_title']);
		
		if ($this->config->get('fx_clear_sort_limit_doubles')) {
			if (isset($data['data']['limits'])) $out['data']['limits'] = $this->clear_limits($data['data']['limits']);
			if (isset($data['data']['sorts'])) $out['data']['sorts'] = $this->clear_sorts($data['data']);
		}
		
		if (isset($data['data']['pagination'])) $out['data']['pagination'] = $this->pagination($data['data']['pagination']);
		
		/*if (isset($data['data']['breadcrumbs'])) $out['data']['breadcrumbs'] = $this->bread($data['data']['breadcrumbs']); /**/
		$data['data'] = array_merge($data['data'], $out['data']);
		
		if (($page > 1) || !empty($m_name)) {
			$out['data'] = array_merge($out['data'], $this->hidden($data['data']));
		}		
		
		if ($this->isYandex() && $this->config->get('fx_yandex_canonical')) {
			$out['canonical'] = $href_no_page;
			$out['prev'] = $out['next'] = '';
		}
		
		if ($canonical) $this->delLinks($href, array ('prev','canonical','next'));
		if (!empty($out['prev']))	$this->document->addLink($out['prev'], 'prev');
		if (!empty($out['next']))	$this->document->addLink($out['next'], 'next');
		
		if (!empty($out['canonical'])) { 
			if ($this->cicleProtect($out['canonical'])) $this->document->addLink($out['canonical'], 'canonical');
			
			if ($out['canonical'] != $this->full_url()) $out['clear'] = $this->config->get('fx_description_canonical') ? 1 : $out['clear'];
		}
		
		if ($out['clear']) $out['data']['description'] = $out['data']['description_2'] = $out['data']['thumb'] = $out['data']['des2'] = $out['data']['ext_description'] = $out['data']['short_desc'] = $out['data']['faq'] = $out['data']['description_bottom'] = '';

		if ($no_data) $out['data'] = array();		
		
		if ($this->config->get('fx_tags_fix') && isset($data['data']['tags'])) $out['data']['tags'] = $this->safe_tags($data['data']['tags'], $this->full_url());
		if ($this->append_footer) $out['data']['footer'] = '<script>document.addEventListener("DOMContentLoaded", function(event) { $("a[data-link=→]").click(function() {  location = "index.php?route=product/search&tag=" + (this.hash).replace("#tag:", ""); }); });</script>' . $data['data']['footer'];
		
		if ($fx['name'] != '') {
		
			$out['data']['heading_title'] = $out['data']['seo_h1'] = $out['heading_title'];
			$this->document->setTitle($out['title']);
			$this->document->setDescription($out['meta_description']);
		
		}
		
		if ((float)VERSION < 1.9 ){
			$this->fuck15($out['data']);
		}
		
		return $out['data'];
    }
	
	public function test404() {	
	
		$uri = $this->request->server['REQUEST_URI'];		
		
		$preclear = explode('?', $uri);
		
		$clear = trim($preclear[0], '/');
		
		
		$parts = explode('/', $clear);
		
		while (!empty($parts)) {
			
			$prelast = explode('.', end($parts));
			
			$last = $prelast[0];
			
		
			$this->load->model('extension/module/fx');

			$query = $this->model_extension_module_fx->getQuery($last);
			
			
			if (!empty($query)) {

				preg_match('/(.*)_id=(\d*)/', $query, $match);
				
				
				switch ($match[1]){
					case 'category':
						$to = $this->url->link('product/category', 'path=' . $match[2], 'SSL');
						break;
					case 'manufacturer':
						$to = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $match[2], 'SSL');
						break;
					case 'product':
						$to = $this->url->link('product/product', 'product_id=' . $match[2], 'SSL');
						break;
					case 'information':
						$to = $this->url->link('information/information', 'information_id=' . $match[2], 'SSL');
						break;
				}
			
			}
			
			if (isset($to)) $this->response->redirect($to, 301);
			
			array_pop($parts);
			
		}		

    }
	
	public function pageEmpty($data, $attribute) {		
	
		if ($this->route() == 'product/product') return false;
	
		$count = (isset($data['products']) && is_array($data['products'])) ? count($data['products']) : 0;
	
		$canonical_to_first = $remove_prev_next = false;
		
		$get_list = 'mfp,filter';
		
		$list = array('product/category', 'product/manufacturer/info', 'product/special');
		
		$route_allow = in_array($this->route(), $list) ? true : false;
		
		if ($this->total < 1) $canonical_to_first = true;

		if ($route_allow && $this->testIssetGet($get_list) && !$count) $canonical_to_first = true;
		
		$max_page = $this->config->get('fx_max_page') ? (int)$this->config->get('fx_max_page') : $this->total_default;
		
		if ($this->page > $max_page) {
			$remove_prev_next = true;
			if ($this->page > 1) $canonical_to_first = true;
		}
		
		//$canonical_to_first = $this->multiTest($this->config->get('fx_no_canonical')) ? false : $canonical_to_first;
		
		$remove_prev_next = $this->config->get('fx_no_blank_pages') ? $remove_prev_next : false;
		
		if ($attribute == 'prev_next') return $remove_prev_next;
		if ($attribute == 'canonical') return $canonical_to_first;
		
		return false;

    }
	
	public function bread($breadcrumbs) {
		
		$last = array_pop($breadcrumbs);
		$last['href'] .= '#';
		
		$breadcrumbs[] = $last;
			
		return $breadcrumbs;

    }
	
	public function isAdmin() {
		
		if	((float)VERSION < 2.3 ) {
		
			if	(method_exists($this->load, 'library')) $this->load->library('user');

			$user = new User($this->registry);
			
		} else {
		
			$user = new Cart\User($this->registry);
		}
	
		return $user->isLogged();
    }
	
	public function cancel() {
		
		$route = $this->route();
		
		if (isset($this->request->get['filter_ocfilter']) && !$this->config->get('fx_ocfilter')) return true;
		
		if (empty($route)) return true;
	
		$list = $this->config->get('fx_routes_exclude') ? explode(',', $this->config->get('fx_routes_exclude')) : array('product/tags', 'product/compare', 'product/catalog');
		
		if (in_array($route, $list)) return true;
		
		$parts = array(
			'product/category' => 'path',
			'product/manufacturer/info' => 'manufacturer_id',
			'product/product' => 'product_id',
			'information/information' => 'information_id',
		);
		
		foreach ($parts as $key=>$value){
			if (($route == $key) && !$this->testIssetGet($value)) return true;
		}
		
		$list = $this->config->get('fx_gets_exclude') ? $this->config->get('fx_gets_exclude') : 'fx_disable';
		
		if ($this->multiTest($list)) return true;
		
		return false;

    }
	
	public function relUrl() {
	
		$rel_url['canonical'] = $rel_url['prev_next'] = '';
		
		$gets = explode(',', $this->config->get('fx_canonical_paht'));

		foreach ($gets as $get){
	
			$get = trim($get);
			
			if ($this->testIssetGet($get)){
				
				if ($get == 'page') {
					$rel_url['canonical'] .= ($this->page < 2) ? '' : '&page=' . $this->page;
				} else {
					$rel_url['canonical'] .= '&' . $get . '=' . $this->request->get[$get];
				}
			}
		}		
		
		if ($this->ocfilter() == 'seo') $rel_url['canonical'].= '&filter_ocfilter=' . $this->request->get['filter_ocfilter'];
		
		
		$gets = explode(',', $this->config->get('fx_prev_next_paht'));

		foreach ($gets as $get){	
			$get = trim($get);
			
			if ($this->testIssetGet($get) && ($get != 'page')) $rel_url['prev_next'] .= '&' . $get . '=' . $this->request->get[$get];
		}		
		
		if ($this->ocfilter() == 'seo') $rel_url['prev_next'] .= '&filter_ocfilter=' . $this->request->get['filter_ocfilter'];
		

		return $rel_url;

    }
	
	public function delLinks($href, $links, $test = false) {
	
		if (!method_exists($this->document, 'delLinks')) {
		
			if ($this->isAdmin() && !$test) echo '<pre>Full IndeX OCMOD Error...</pre>';
			
			return false;		
		}
		
		if ((float)VERSION < 1.9 ){ 
			$this->document->delLinks($href, $links);
		} else {
			$this->document->delLinks($links);
		}		

		return true;

    }
	
	public function limit($get = true) {

		$from_get = !empty($this->request->get['limit']) ? (int)$this->request->get['limit'] : 0;
		
		if ((float)VERSION < 2 ) {			
			$limit = $this->config->get('config_catalog_limit');
		} else if ((float)VERSION < 2.2 ) {			
			$limit = $this->config->get('config_product_limit');
		} else if ((float)VERSION < 3 ) {	
			$limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}
		
		return $from_get && $get ? $from_get : $limit;

    }
	
	public function code404() {
		
		if ((float)VERSION < 1.9 ){ 
			$this->getChild('error/not_found');
		} else {
			$this->load->controller('error/not_found');
		}

		return true;

    }
    
    public function isAjax() {	
        if (!empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            return true;
        }
        
        return false;
    }
   
    public function redirect_to($to, $code = 301) {
        
        $current = $this->full_url();
        
        if ($current == $to) return false;
        
        
        if((float)VERSION < 2) {
			$this->redirect($to, $code);
		}else{
			$this->response->redirect($to, $code);
		}
		
    }
	
	public function info($data='') {
		
		if (empty($data)) $data = $this->request->get['data'];
		
		//if (!empty($this->request->get['v'])) $data .= '&v=' . $this->request->get['v'];
		
		$parseurl = 'http://full-index.ru/messages/?data=' . $data;
		
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FX)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $parseurl);
		$pagecode = curl_exec( $ch );
		curl_close($ch);*/
		
		ini_set('default_socket_timeout', 10);
		
		$headers = @get_headers($parseurl);
		
		$this->response->addHeader('Content-Type: text/html; charset=utf-8');
		
		if($headers !== false){
			
			$pagecode = @file_get_contents($parseurl);			
			
			$this->response->setOutput($pagecode);
		} else {
		
			$this->response->setOutput('Сonnecrion to full-index.ru failed... You can see info directly on <a href="http://full-index.ru/messages/">site</a>');
			
		}

    }
	
	public function fuck15($data) {

		$this->data['fx'] = json_encode($data);

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/fx.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/fx.tpl';
		} else {
			$this->template = 'default/template/module/fx.tpl';
		}
		
		$this->render();

    }
	
	public function getMFP($mid, $cid) {	

		$script = '<script>
			
		function fx_back(){
			$(location).prop("href", "' . $this->url->link("product/category", "path=" . $cid , 'SSL') . '");
		}
		
		function fx_check(){
			$("#mfilter-opts-attribs-1-manufacturers-'. $mid .'").prop("checked", true);
		}
		
		$( document ).ready(function() {
			$(".mfilter-manufacturers .mfilter-heading").removeClass( "mfilter-collapsed" );
			$(".mfilter-manufacturers .mfilter-content-opts").show();
		
			fx_check();			
		});

		$("#mfilter-opts-attribs-1-manufacturers-'.$mid.'").on("click", function() { fx_back();});				
		$("#mfilter-opts-attribs-1-manufacturers-'.$mid.'").parent().parent().on("click", ".mfilter-close", function() { fx_back();});		
			
			</script>';

		return $script;

    }
	
	public function ocfilter() { // true = убрать canonical
	
		if ($this->registry->has('ocfilter') && method_exists($this->ocfilter, 'getPageInfo')) {
		  $page_info = $this->ocfilter->getPageInfo();
		  if ($page_info) $this->ocf = $page_info;
		}

		$gets = $this->request->get;		
		unset($gets['_route_']);
		$test = isset($gets['filter_ocfilter']) ? $gets['filter_ocfilter'] : false;
		
		$canonicals = 'page,sort,limit,order';
		$clear = !($this->multiTest($canonicals) || (count($gets) > 3));
		
		if (!empty($this->ocf)) return 'seo'; // посадочная
		
		if (!$clear || !$test) return false;
		
		if ($this->testWildcard($this->config->get('fx_canonical_pattern'))) return false;

		//if ((substr_count($test, ',')+substr_count($test, ';')) + 1 <= $this->config->get('fx_ocfilter')) return true;
		
		return false;
    }
	
	public function mfp() {

		$gets = $this->request->get;
		unset($gets['_route_']);
		
		if (!isset($gets['mfp'])) return false;
		
		if (count($gets) > 3) return true;
		
		return false;
    }
	
	public function filter_vier() {
	
		$gets = $this->request->get;
		
		$list = 'manufs,attrb,optv,qnts,nows,psp,prs,chc';
		
		if ($this->multiTest($list)) return true;	
		
		return false;
    }
	
	public function rewiews() {
		
		$limit = 5;
	
		$out = $this->config->get('fx_review_page') ? 999 : $limit;
		
		if ($this->route() == 'product/product/review' && !$this->isAjax() && $this->config->get('fx_review_301')) {
			$this->redirect_to($this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id'], 'SSL'));
		}
		
		if ((float)VERSION < 1.9 ){
			$this->fuck15($out);
		}
		
		return $out;

    }


	public function route() {
		
		$route = '';
		
		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];
		} else {
			$uri = ltrim($this->request->server['REQUEST_URI'], '/');
			if ($uri == '' || $uri[0] == '?' || $uri == 'index.php'|| $uri == 'index.html') $route = 'common/home';
			//$route = 'common/home';
		}

		return $route;
    }

	public function testWildcard($wildcards, $href = '') {
		
		$uri = $this->request->server['REQUEST_URI'];

		$wilds = explode(',', $wildcards);

		foreach ($wilds as $wild){
			
			$wild = trim($wild);

			$test = str_replace('*', '☺', $wild);	$test = str_ireplace('/', '☻', $test);

			$test = preg_quote(strtolower($test));

			$test = str_replace('☺', '.*', $test);	$test = str_ireplace('☻', '\/', $test);

			if (preg_match('/^'.$test.'$/', $uri)) return true;

		}

		return false;

    }

	public function testCanonical($canonicals, $href='') {	

		if (isset($this->request->get['product_id']) && (isset($this->request->get['manufacturer_id']) || isset($this->request->get['page']))) return true;

		if (trim($canonicals) == 'all') return true;
		
		if ($this->multiTest($canonicals)) return true;

		return false;
    }

	public function cicleProtect($href = '') {
		
        $current = $this->full_url();
		
        //print($this->full_url() . '   | cicleProtect <br>');
		
		if (($href == $current) && $this->config->get('fx_canonical_protection')) return false;

		return true;
    }
	
	

	public function multiTest($str, $href = '') {
		
		$elements = explode(',', $str);

		foreach ($elements as $element_){
		
			$element = str_replace('&amp;', '&', $element_);
			
			if ((strpos($element, '&')) !== false) {
				
				$test = true;
				
				$sub_elements = explode('&', $element);
				
				foreach ($sub_elements as $get_){
				
					$get = trim($get_);
				
					$sub_test = (substr($get, 0, 1) == '-') ? !$this->testIssetGet(substr($get, 1)) : $this->testIssetGet($get);
					
					$test &= $sub_test;
				}
				
				
				if ($test) return true;
				
			} else {
				
				if ($this->testIssetGet(trim($element))) return true;
				
			}
		
		
		}

		return false;
    }
	
	public function host() {
	
		if ($this->host) return $this->host;
		
		$https = ($this->config->get('config_secure') || substr(HTTP_SERVER, 0, 5) == 'https');
		
		$host = $https ? str_replace('http:/', 'https:/', $this->config->get('config_ssl')) : $this->config->get('config_url');
		
		if (substr_count($host, '/') > 3){		
			$ssl = $https ? 'https://' : 'http://';
			$host = $ssl . $this->request->server['HTTP_HOST'];
		}
		
		$this->host = $host;

		return $host;

    }	
	
	public function full_url() {
	
		$host = $this->host();
		
		$uri = $this->request->server['REQUEST_URI'];
		
		if ($uri[0] != '/')  $uri = '/' . $uri;
		
		$full = rtrim($host, '/') . $uri;
		
		return $full;
    }

	public function setName($info, $route) {	
	
		if (!isset($info['name']) || ((float)VERSION < 1.9 && $route == 'common/home')) return '';
	
		$list = array(
			'product/category',
			'product/manufacturer/info',
			'product/product',
			'information/information',
		);
		
		if (in_array($route, $list)) return $info['name'];
		
		$this->load->language($route);
		
		$name = $this->language->get('heading_title');
		
		$parts = explode('/', $route);	
		
		if ($name == 'heading_title') $name = array_pop($parts);
		
		return $name;
    }
	
	public function clear_limits($limits) {
		
		if (empty($limits)) return $limits;
		
		if ((float)VERSION < 2 ) {			
			$default = $this->config->get('config_catalog_limit');
		} else if ((float)VERSION < 2.2 ) {			
			$default = $this->config->get('config_product_limit');
		} else if ((float)VERSION < 3 ) {	
			$default = $this->config->get($this->config->get('config_theme') . '_product_limit');
		} else {
			$default = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}
		
		$limits[0]['href'] = preg_replace('/(\?|&|&amp;)limit=' . $default . '$/', '', $limits[0]['href']);
		
		$limits[0]['href'] = str_replace(array('/&amp;limit=', '/&limit='), '/?limit=', $limits[0]['href']);
		$limits[0]['href'] = str_replace(array('/&amp;sort=', '/&sort='), '/?sort=', $limits[0]['href']);
		
		return $limits;

    }
	
	public function clear_sorts($data) {
		
		if (empty($data['sorts'])) return $data['sorts'];
		
		$data['sort'] = $this->config->get('fx_clear_sort') ? $this->config->get('fx_clear_sort') : 'p.sort_order';
		$data['order'] = $this->config->get('fx_clear_order') ? $this->config->get('fx_clear_order') : 'ASC';
		
		$replace = array('?sort='.$data['sort'], '&sort='.$data['sort'], '&amp;sort='.$data['sort'],'&amp;order='.$data['order'], '?order='.$data['order'], '&order='.$data['order']);

		foreach($data['sorts'] as &$sort){			
			
			$temp = str_replace($replace, '', $sort['href'], $n);
			$temp = str_replace(array('/&amp;sort=', '/&sort='), '/?sort=', $temp);
			$temp = str_replace(array('/&amp;limit=', '/&limit='), '/?limit=', $temp);
			
			if ($n > 1) $sort['href'] = $temp;
		}
		
		return $data['sorts'];

    }
	
	public function getH1($h1) {
	
		$style = (strlen($this->config->get('fx_style')) > 3 ) ? ' style="'.$this->config->get('fx_style').'"' : '';
		
		$span = '<span'. $style . '>' . $h1 . '</span>';

		
		if (($this->page > 1)&&($this->config->get('fx_hide_h1'))) { 
			return '<!--hidden h1-->';
		}
		
		if (($this->page > 1) && $this->config->get('fx_span')) { 
			return $span;
		}
		
		return '';

    }	

	public function hidden($out) {
		
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		
		//$out['content_top'] = $out['column_left'] = $out['column_right'] = $out['content_bottom'] = $out['categories'] = '';
		
		if ($this->config->get('fx_noindex_addon_top')) $out['content_top'] = '<!--noindex-->' . $out['content_top'] . '<!--/noindex-->';
		if ($this->config->get('fx_noindex_addon_left')) $out['column_left'] = '<!--noindex-->' . $out['column_left'] . '<!--/noindex-->';
		if ($this->config->get('fx_noindex_addon_right')) $out['column_right'] = '<!--noindex-->' . $out['column_right'] . '<!--/noindex-->';
		if ($this->config->get('fx_noindex_addon_bottom')) $out['content_bottom'] = '<!--noindex-->' . $out['content_bottom'] . '<!--/noindex-->';
		
		if ($this->config->get('fx_noindex_addon_cats_list')) {
		
			if (isset($out['categories'])){			
				foreach($out['categories'] as &$category){					
					$category['name'] = '<!--noindex-->' . $category['name'] . '<!--/noindex-->';					
				}
			}
			
			if (isset($out['text_refine']))  $out['text_refine'] = '<!--noindex-->' . $out['text_refine'] . '<!--/noindex-->';
			
			
			if (isset($out['sorts'])){		
				foreach($out['sorts'] as &$sort){	
					$sort['text'] = '<!--noindex-->' . $sort['text'] . '<!--/noindex-->';
				}
			}
			
			$out['footer'] = '<!--noindex-->' . $out['footer'] . '<!--/noindex-->';
			
		}
		
		if (!isset($out['content_top'])) $out['content_top'] = '';
		if (!isset($out['column_left'])) $out['column_left'] = '';
		if (!isset($out['column_right'])) $out['column_right'] = '';
		if (!isset($out['content_bottom'])) $out['content_bottom'] = '';

		if ($this->config->get('fx_hide_content_top') || ($out['content_top'] == '<!--noindex--><!--/noindex-->')) $out['content_top'] ='';
		if ($this->config->get('fx_hide_column_left') || ($out['column_left'] == '<!--noindex--><!--/noindex-->')) $out['column_left'] ='';
		if ($this->config->get('fx_hide_column_right') || ($out['column_right'] == '<!--noindex--><!--/noindex-->')) $out['column_right'] ='';
		if ($this->config->get('fx_hide_content_bottom') || ($out['content_bottom'] == '<!--noindex--><!--/noindex-->')) $out['content_bottom'] ='';
		if ($this->config->get('fx_hide_cats_list')) $out['categories'] = array();

		
		return $out;

    }
	
	public function hidden15($out) {
		
		$hide = false;
		
		if (($this->request->get['route'] == 'product/category') && !empty($this->request->get['manufacturer_id']) && !empty($this->request->get['path'])) $hide = true;
		
		if ($this->page > 1) $hide = true;

		if (!$hide) return $out;
		
		if (!isset($out['content_top'])) $out['common/content_top'] = '';
		if (!isset($out['column_left'])) $out['common/column_left'] = '';
		if (!isset($out['column_right'])) $out['common/column_right'] = '';
		if (!isset($out['content_bottom'])) $out['common/content_bottom'] = '';
			
		if ($this->config->get('fx_hide_content_top') || ($out['common/content_top'] == '<!--noindex--><!--/noindex-->')) $out['common/content_top'] ='';
		if ($this->config->get('fx_hide_column_left') || ($out['common/column_left'] == '<!--noindex--><!--/noindex-->')) $out['common/column_left'] ='';
		if ($this->config->get('fx_hide_column_right') || ($out['common/column_right'] == '<!--noindex--><!--/noindex-->')) $out['common/column_right'] ='';
		if ($this->config->get('fx_hide_content_bottom') || ($out['common/content_bottom'] == '<!--noindex--><!--/noindex-->')) $out['common/content_bottom'] ='';

		
		if ((float)VERSION < 1.9 ){
			$this->fuck15($out);
		}
		return $out;

    }
	
	public function testIssetGet($str) {
	
		$gets = explode(',', $str);		

		foreach ($gets as $get){
			if (isset($this->request->get[$get])) return true;
		}


		return false;

    }	
	
	public function isYandex() {
	
		if (!isset($this->request->server['HTTP_USER_AGENT'])) return false;
		
		$agent = $this->request->server['HTTP_USER_AGENT'];
		
		if (((strpos($agent, 'yandex')) !== false) && ((strpos($agent, 'bot')) !== false)) return true;

		return false;

    }
	
	public function seo_pro() {
	
		if ($this->config->get('config_seo_url_type') && ($this->config->get('config_seo_url_type') == 'seo_pro')) return true;

		return false;

    }	
	
	public function seopro_tags($tmp = array()) {
	
		$out = array();
	
		$allowed = explode(',', $this->config->get('fx_seopro_tags'));
		foreach ($allowed as $allow){
			if (isset($tmp[$allow])) {$out[$allow] = $tmp[$allow];}
		}

		return $out;

    }
	
	public function layout() {
	
		if ($this->route() == 'product/category' && isset($this->request->get['page'])) return $this->config->get('fx_layout');

		return false;

    }
	
	public function clear_host($url) {
	
		$host = $this->host();
		
		$url = '/'. ltrim(str_replace($host, '', $url), '/');
		
		return $url;
    }

	public function testRedirect($redirects, $to = '') {

		$uri = $this->request->server['REQUEST_URI'];
		if (!$to) $to = $this->index_href;
		
		$to = $this->clear_host($to);
		
		if ($to == $uri) return false;
		
		if ($to == '/index.php?route=common/home' && $uri == '/') return false;
		
		$code = (int)$this->config->get('fx_redirect_empty_page');
		
		if (($this->page > 1) && ($this->page > $this->total) && ($code > 300) && ($code < 600))  $this->redirect_to($to, $code);

		if ($this->config->get('fx_301')){
		
			$intlist = array('product_id', 'manufacturer_id', 'information_id', 'page', 'limit', 'filter_manufacturer_id', 'filter_category_id');
		
			foreach ($intlist as $iget){

				if ( $this->testGet($iget)) return true;
				
			}
			
			if ( $this->findSlash(array('sort','order','mfp', 'path')) ) return true;

		}

		if ($redirects == '') return false;

		$redirs = explode(',', $redirects);	

		foreach ($redirs as $redir){
			
			$redir = trim($redir);

			if ($redir == 'manufacturer_id' && isset($this->request->get['path'])) return true;

			if (isset($this->request->get[$redir]) || (($redir == '//') && (strpos($uri,'//') !== false ))) return true;		

		}
		
		return false;

    }

	public function presetData($mod_data = array()) {
	
		if (!$mod_data['info']) $mod_data['info'] = array();

		if (!isset($mod_data['info']['name'])) $mod_data['info']['name'] = '';
		if (!isset($mod_data['ocfilter'])) $mod_data['ocfilter'] = false;
		if (!isset($mod_data['total'])) $mod_data['total'] = 0;
		
		$this->total_default = ceil((int)$mod_data['total'] / $this->limit(false));
		
		$mod_data['total'] = ceil($mod_data['total'] / $this->limit());
		
		if (!isset($mod_data['data']['limits'])) $mod_data['data']['limits'] = array();
		
		if (!isset($mod_data['data']['heading_title'])) $mod_data['data']['heading_title'] = '';
		if (!isset($mod_data['data']['content_top'])) $mod_data['data']['content_top'] = '';
		if (!isset($mod_data['data']['column_left'])) $mod_data['data']['column_left'] = '';
		if (!isset($mod_data['data']['column_right'])) $mod_data['data']['column_right'] = '';
		if (!isset($mod_data['data']['content_bottom'])) $mod_data['data']['content_bottom'] = '';
		if (!isset($mod_data['data']['footer'])) $mod_data['data']['footer'] = '';
		
		return $mod_data;

    }
	
	public function data() {
	
		if (!isset($data['name'])) $data['name'] = '';
		if (!isset($data['ocfilter'])) $data['ocfilter'] = false;
		if (!isset($data['total'])) $data['total'] = 0;
		$data['total'] = ceil($data['total']);
		$href = $this->request->server['REQUEST_URI'];
		$this->load->model('extension/module/fx');
		$data = $this->model_extension_module_fx->data();
		return $data;
    }
	
	public function m_filter($data) {	

		$out = $data;
		
		if (isset($this->request->get['manufacturer_id'])) $out['filter_manufacturer_id'] = (int)$this->request->get['manufacturer_id'];
		
		return $out;
    }

	public function Redirects() {
	

		if ($this->config->get('fx_redirect_list') == '') return false;

		$redirects = explode(PHP_EOL, $this->config->get('fx_redirect_list'));

		$uri = $this->request->server['REQUEST_URI'];

		$uri = ltrim($uri, '/ ');


		foreach ($redirects as $redirect){

			$temp = explode('→', $redirect);

			if (str_replace($host, '', $temp[0]) == $uri) {

				if((float)VERSION < 2) {
					$this->redirect($host.str_replace($host, '', $temp[1]), 301);
				}else{
					$this->redirect_to($host.str_replace($host, '', $temp[1]));
				}

				return true;

			}

		}

		return false;

    }

	public function testManuficturer($mid = 0) {
		
		$route = $this->request->get['route'];

		if (($route == 'product/category') && isset($this->request->get['manufacturer_id'])) {
				$mid = $filter_data['filter_manufacturer_id'] = $data['filter_manufacturer_id'] = (int)$this->request->get['manufacturer_id'];
				$this->load->model('extension/module/fx');
				$man['name'] = $this->model_extension_module_fx->getManufacturer($mid);
				$man['manufacturer_mfp'] = $this->getMFP($mid, $category_info['category_id']);
				$this->man = $man;
				return $man;
		}
		
		return null;

    }

	public function testGet($get, $n = 1) {
	
		if (!isset($this->request->get[$get])) return false;
		
		$val = (string)$this->request->get[$get];

		if (isset($this->request->get['oct_filter'])) $n = 0;
		
		if (!ctype_digit($val) || $val[0] == '0' || (int)$val <= $n ) return true;

		return false;

    }

	public function findSlash($uget) {

		foreach ($uget as $get){

			if (!isset($this->request->get[$get])) continue;

			if (substr($this->request->get[$get], -1) == '/') return true;

			if (($get == 'order') && (!in_array($this->request->get[$get], array('ASC', 'DESC', 'asc', 'desc')))) return true;

		}	

		return false;

    }

    public function test() {
	
		$host = $this->host();
		
		$this->load->language('extension/module/fx');
		
		$this->title = 'test';
		
		$errors = array();
		
		if (!$this->delLinks('/', array(), true)) $errors[] = $this->language->get('error_ocmod');
	
		$robots = file_get_contents ($this->request->server['DOCUMENT_ROOT'] . '/robots.txt');
		$result = strpos ($robots, '?page=') || strpos ($robots, '&page=');
		
		if ($result) $errors[] = $this->language->get('error_robots');
		
		$this->load->model('extension/module/fx');
		
		$category_id = $this->model_extension_module_fx->getCat();

		$parseurl = str_replace('&amp;','&', $this->url->link('product/category', 'path=' . $category_id . '&page=2', 'SSL'));
		
		$headers = array('HTTP_ACCEPT: Something', 'HTTP_ACCEPT_LANGUAGE: ru, en, da, nl', 'HTTP_CONNECTION: Something');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; FX)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $parseurl);
		$pagecode = curl_exec( $ch );
		curl_close($ch);
		
		if ($pagecode != ''){
			$code = explode('head>', $pagecode ); 
			$pagecode = str_replace('<','{',$code[1]); 
		}
		$pagecode = substr($pagecode, 0, -2);				
		if ((strpos($pagecode, 'robots') || strpos($pagecode, 'yandex') || strpos($pagecode, '"google"')) && strpos($pagecode, 'noindex'))  { 
			$errors[] = $this->language->get('error_noindex');
		}

		$data = implode('<br>', $errors);
		
		if ($data != ''){		
			$this->response->addHeader('Content-Type: text/html');
			$this->response->setOutput($data);
		}
    }

    public function scan($encode=true) {
	
		$json = array();
		
		$this->load->model('extension/module/fx');
		
		$json['scan'] = $this->model_extension_module_fx->scanData('fx');
		
		$json['debug'] = $this->model_extension_module_fx->debugData('fx');
		
		$json['seo_pro'] = $this->seo_pro();		
		
		$json['version'] = round((float)VERSION, 1);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

    }
	
    public function decode($encode = '/') {
		
		$this->url->addRewrite($this);
		
		//print($this->request->get['_route_']);

		$this->response->addHeader('Content-Type: text/html');
		$this->response->setOutput($print);

    }

    public function debug() {
	
		$html = '';
		
		$this->load->model('extension/module/fx');
		$data = $this->model_extension_module_fx->debugData('fx');
				
		foreach ($data as $row){
		
			if (!strpos($row['key'], '_redirect_list')) $html .= '<b>"' . $row['key'] . '"</b><span style = "color:#088">&#09;=>&#09;</span>"' . $row['value'] . '",<br>';
		}
		
		$this->response->addHeader('Content-Type: text/html');
		$this->response->setOutput($html);

    }

    public function safe_tags($data, $p_url) {		

		$empty = $this->url->link('product/search', 'tag=');
		
		
		
		$tags = array();

		foreach ($data as $tag) {
		
		
			$url = str_replace($empty, '', $tag['href'], $n);
			
			$url = $n ? $p_url . '#tag:' . $url . '" data-link="→'  : $tag['href'];
		
			$tags[] = array(
				'tag'  => $tag['tag'],
				'href' => $url
			);
		}
		
		$this->append_footer = '<script>$(a[data-link=→]).click(function() {  alert("777"); });</script>';

		return $tags;
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
	
	public function event(){
	
		
		$cancel = (float)VERSION < 1.9 ? $this->fuck15(array()) : array();
	
		if ($this->cancel()) return $cancel;
	
		switch ($this->route()){
			case 'information/information':
				$href = $this->url->link('information/information', 'information_id=' . (int)$this->request->get['information_id'], 'SSL');
				$canonical = $this->config->get('fx_canonicals_i');
				$redirects = $this->config->get('fx_redirects_i');
				break;
			case 'common/home':
				$href = $this->url->link('common/home', '', 'SSL');
				if (substr($href, -27) == 'index.php?route=common/home') $href = str_replace('index.php?route=common/home', '', $href);
				$canonical = $this->config->get('fx_canonicals_h');
				$redirects = $this->config->get('fx_redirects_h');
				break;
			default:
				$href = $this->url->link($this->route(), '', 'SSL');
				return;
				break;
		}
		
		if ($this->testCanonical($canonical, $href)) $out['canonical'] = $href;
		
		if ($this->testWildcard($this->config->get('fx_redirect_pattern'), $href) || $this->testRedirect($redirects, $href)) {
			$this->redirect_to($href);
		}
		
		if ($this->testWildcard($this->config->get('fx_404_pattern'), $href) || $this->multiTest($this->config->get('fx_404_get'), $href)) {
			$this->code404();
		}
		
		if ($this->testWildcard($this->config->get('fx_canonical_pattern'))) $out['canonical'] = $href;
		
		if ($canonical) $this->delLinks($href, array('canonical'));
		
		if (!empty($out['canonical']) && $this->cicleProtect($out['canonical'])) $this->document->addLink($out['canonical'], 'canonical');
		
		return;
	}
	
	public function header_old($data) {
		
		
		//$out['title'] = $this->fx->getTitle();
		
		if (empty($out['title'])) $out['title'] = $data['title'];
		
		$out['page'] = $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 0;		
		
		$after = (($page > 1) && $this->config->get('fx_page')) ? ' - '. 'страница' .' '.$page : '';		
		
		$out['title'] .= $after;		
		
		if ($this->config->get('fx_block_noindex')) $out['robots'] = $out['noindex'] = false;
		
		//$out['title'] .= '[fx]';
		
		$out['fx_robots'] = '';
		
		if ($page == 1) { 
			$out['fx_robots'] = '<meta name="robots" content="noindex, nofollow"/>';
		} else if (($page > 1) && $this->config->get('fx_yandex')) {
			$out['fx_robots'] = '<meta name="yandex" content="noindex' . ($this->config->get('fx_follow') ? '"/>' : ', nofollow"/>') ;
		} elseif ($this->multiTest($this->config->get('fx_noindex_get')) || $this->testWildcard($this->config->get('fx_noindex_mask'))) {
			$out['fx_robots'] = '<meta name="'.$this->config->get('fx_noindex_name').'" content="noindex' . ($this->config->get('fx_follow') ? '"/>' : ', nofollow"/>') ;
		}

		return $out;

    }
	
	public function header($data) {		
		
		$cancel = (float)VERSION < 1.9 ? $this->fuck15(array()) : array();
	
		if ($this->cancel()) return $cancel;
		
		//$out['title'] = $this->fx->getTitle();
		
		$out['title'] = empty($data['title']) ? '' : $data['title'];
		
		$out['description'] = empty($data['description']) ? '' : $data['description'];
		
		$this->load->language('extension/module/fx');		
		
		$out['page'] = $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 0;		
		
		$after = (($page > 1) && $this->config->get('fx_page')) && !$this->config->get('fx_title_pattern') ? ' - '. $this->language->get('text_page') .' '. $page : '';			
		
		$out['title'] .= $after;	
				
		if ($this->config->get('fx_block_noindex') && !$this->multiTest($this->config->get('fx_noindex_exceptions_get'))) {
			$out['robots'] = $out['noindex'] = $out['show_meta_robots'] = false;
			if (isset($data['keywords']) && strpos($data['keywords'], 'noindex')) $out['keywords'] = '';
			if (isset($data['description']) && strpos($data['description'], 'noindex')) $out['description'] = '';
		}
		
		//$out['title'] .= '[fx]';
		
		$out['fx_robots'] = '';
		
		if ($page == 1) $out['fx_robots'] = '" />'.PHP_EOL.'<meta name="robots" content="noindex, nofollow';

		if ($this->config->get('fx_noindex') && ($this->multiTest($this->config->get('fx_noindex_get')) || $this->testWildcard($this->config->get('fx_noindex_mask')))) {
			$out['fx_robots'] = '" />'.PHP_EOL.'<meta name="'.$this->config->get('fx_noindex_name').'" content="noindex' . ($this->config->get('fx_follow') ? '' : ', nofollow');
		}
		
		$out['description'] .= $out['fx_robots'];
		
		$out['fx_robots'] = '';

		if ((float)VERSION < 1.9 ){
			$this->fuck15($out);
		} else {
			return $out;
		}
		
		return $out;

    }
	
    public function as_module() {
	
		if (!isset($this->request->get['page'])) return '';	
	
		$this->load->language('extension/module/fx');
	
		$link = '';
		
		switch ($this->route()){
			case 'product/category':
				$parts = explode('_', (string)$this->request->get['path']);
				$category_id = (int)array_pop($parts);
				$rel_pageurl = 'path=' . $category_id;
				$link = $this->url->link('product/category', 'path=' . $category_id, 'SSL');
				break;
			case 'product/manufacturer/info':
				$rel_pageurl = 'manufacturer_id=' . $this->request->get['manufacturer_id'];
				$out['href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . (int)$this->request->get['manufacturer_id'], 'SSL');
				break;
			case 'product/special':
				$out['href'] = $this->url->link('product/special', '', 'SSL');
				break;
		}
		
		if ($link) $link = '<a class="fx-back" href="' . $link . '">' . $this->language->get('text_back') . '</a>';
		
		return $link;
    }
}