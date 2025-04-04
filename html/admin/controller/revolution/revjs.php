<?php
$js = array();
$revtheme_all_settings = $this->request->post['revtheme_all_settings'];
$setting_catalog_all = $this->request->post['revtheme_catalog_all'];
$revtheme_header_menu = $this->request->post['revtheme_header_menu'];
$revtheme_product_all = $this->request->post['revtheme_product_all'];
$description_options = $this->request->post['revtheme_cat_attributes'];
$revtheme_header_cart = $this->request->post['revtheme_header_cart'];
$revtheme_filter = $this->config->get('revtheme_filter');
$revtheme_footer_all = $this->request->post['revtheme_footer_all'];
$revtheme_home_all = $this->request->post['revtheme_home_all'];
if (VERSION >= 2.2) {
	$catalog_img_width = $this->config->get($this->config->get('config_theme') . '_image_product_width');
} else {
	$catalog_img_width = $this->config->get('config_image_product_width');
}
if ($revtheme_all_settings['mobile_header'] == '2') {
	if (isset($this->request->post['revtheme_footer_soc'])) {
		$revtheme_footer_socs = $this->request->post['revtheme_footer_soc'];
	} else {
		$revtheme_footer_socs = false;
	}
	$js[] = '
	one_sch = $(\'.mobsearch\').html(),
	$(\'.mobsearch\').html(\'\');
	$(\'.mobsearch_two\').html(one_sch);
	$(\'.mobsearch_two .search-button\').on(\'click\', function() {
	url = $(\'base\').attr(\'href\') + \'index.php?route=product/search\';
	var value = $(\'.mobsearch_two input[name=\"search\"]\').val();
	if (value) {url += \'&search=\' + encodeURIComponent(value);}
	var category_id = $(\'.mobsearch_two input[name=\"category_id\"]\').prop(\'value\');
	if (category_id > 0) {url += \'&category_id=\' + encodeURIComponent(category_id) + \'&sub_category=true\';}
	location = url;
	});
	$(\'.mobsearch_two .search input[name=\"search\"]\').on(\'keydown\', function(e) {
	if (e.keyCode == 13) {$(\'.mobsearch_two .search-button\').trigger(\'click\');}
	});
	$(document).ready(function() {
		$(\'nav#mobil_mmenu\').mmenu({
			"extensions": ["theme-dark", "pagedim-black"],
			"counters": true,
			"navbars": [
				{
				   "position": "top",
				   "type": "tabs",
				   "content": [
					  "<a href=\'#panel-menu\'><i class=\'fa fa-bars\'></i></a>",
					  "<a href=\'#panel-language\'><i class=\'fa fa-info\'></i></a>"
				   ]
				},
				'; if ($revtheme_footer_socs) { $js[] = '
				{
				   "position": "bottom",
				   "content": [
						'; foreach ($revtheme_footer_socs as $revtheme_footer_soc) { $js[] = '
							"<a class=\''.$revtheme_footer_soc['image'].'\' href=\''.$revtheme_footer_soc['link'][$this->config->get('config_language_id')].'\' rel=\'nofollow\'></a>",
						'; } $js[] = '
				   ]
				}
				'; } $js[] = '
			],
			"navbar": {
				"title": \'\'
			}
		  });
		$("nav#mobil_mmenu").removeClass(\'dnone\');
	});	
'; } else if ($revtheme_all_settings['mobile_header'] == '3') { $js[] = '
	$(document).ready(function() {
		$(\'nav#mobil_mmenu\').mmenu({
			"extensions": ["theme-white", "pagedim", "shadow-page"],
			"counters": true,
			"navbar": {
				"title": \'\'
			}
		  });
		$("nav#mobil_mmenu").removeClass(\'dnone\');
	});	
'; } $js[] = '
'; if ($revtheme_product_all['options_null_qw']) { $js[] = '
	$(\'input.disabled_option + label\').append("<svg><line x1=\'0\' y1=\'100%\' x2=\'100%\' y2=\'0\' stroke-width=\'1\' stroke=\'rgb(221,221,221)\'></line></svg>").append("<svg><line x1=\'0\' y1=\'0\' x2=\'100%\' y2=\'100%\' stroke-width=\'1\' stroke=\'rgb(221,221,221)\'></line></svg>");
'; } $js[] = '
if (!localStorage.getItem(\'display\')) {
'; if ($setting_catalog_all['vid_default'] == 'vid_price') { $js[] = '
	localStorage.setItem(\'display\', \'price\');
'; } else if ($setting_catalog_all['vid_default'] == 'vid_list') { $js[] = '
	localStorage.setItem(\'display\', \'list\');
'; } else { $js[] = '
	localStorage.setItem(\'display\', \'grid\');
'; } $js[] = '
}
'; if ($revtheme_header_menu['zadergka']) { $js[] = '
	$("#top3 #menu .nav").removeClass(\'dblock_zadergkaoff\').addClass(\'dblock_zadergka\');
	var global_menu2_button;
	$("#top3 #menu .nav > li").hover(function(){
		var this_li = $(this);
		global_menu2_button = setTimeout(function() {
		this_li.find(\'.mmmenu .dropdown-menu\').addClass(\'dblockdr\');
	}, 250)
	},function(){
		$(this).find(\'.mmmenu .dropdown-menu\').removeClass(\'dblockdr\');
		clearTimeout(global_menu2_button);
	});
'; } $js[] = '
'; if ($revtheme_header_menu['onclick']) { $js[] = '
	$(\'#menu2_button\').click(function(){$(\'#menu2\').toggleClass(\'dblock\');});
'; } $js[] = '
$(function () {
  $("#menu .nav > li .mmmenu").mouseenter(function(){
		$(\'#pagefader\').fadeIn(70);
		$(\'body\').addClass(\'razmiv\');
   });
	$("#menu .nav > li .mmmenu").mouseleave(function(){
		$(\'#pagefader\').fadeOut(70);
		$(\'body\').removeClass(\'razmiv\');
   });
});
'; if (!$setting_catalog_all['category_desc']) { $js[] = '
	$(\'.footer-category\').append($(\'.category_description\'));
	$(\'.category_description\').removeClass(\'dnone\');
'; } $js[] = '
'; if ($setting_catalog_all['product_rating'] == '2' && $revtheme_product_all['pr_tabs']) { $js[] = '
$(\'.pr_reviews_count\').on(\'click\', function(e) {
	url = $(this).attr(\'data-href\');
	location = url;
});
if (window.location.hash === "#tab-review") {
	setTimeout(function() {
		$(\'a[href=\\"#tab-review\\"]\').trigger(\'click\');
		$(\'html, body\').animate({ scrollTop: $(\'a[href=\\"#tab-review\\"]\').offset().top - 100}, 250);
		var no_hash_url = window.location.href.replace(/#.*$/, \'\');
		window.history.replaceState(\'\', document.title, no_hash_url);
	}, 150);
}
'; } else if ($setting_catalog_all['product_rating'] == '2' && !$revtheme_product_all['pr_tabs']) { $js[] = '
$(\'.pr_reviews_count\').on(\'click\', function(e) {
	url = $(this).attr(\'data-href\');
	location = url;
});
if (window.location.hash === "#tab-review") {
	setTimeout(function() {
		$(\'html, body\').animate({ scrollTop: $(\'.tab-review\').offset().top - 100}, 250);
		var no_hash_url = window.location.href.replace(/#.*$/, \'\');
		window.history.replaceState(\'\', document.title, no_hash_url);
	}, 150);
}
'; } $js[] = '
'; if ($setting_catalog_all['img_slider']) { $js[] = '
	function podgon_img(){
		$(\'.owlproduct\').each(function(indx, element){
			var data = $(element).data(\'owlCarousel\');
			data && data.reinit({navigation: true})
		});
	'; if ($setting_catalog_all['img_slider'] == '2') { $js[] = '
		if($(window).width() > 974) {
			$(\'.owl-item:eq(0)\', \'.image.owlproduct\').mouseover(function(){
				if ($(this).parent().find(\'.owl-item:eq(1)\').length > 0) {
					$(this).hide(0);
					$(this).parent().find(\'.owl-item:eq(1)\').mouseleave(function(){
						$(this).parent().find(\'.owl-item:eq(0)\').show(0);
					});
					$(\'.product-thumb\').mouseleave(function(){
						$(this).parent().find(\'.owl-item:eq(0)\').show(0);
					});
				}
			});
		}
	'; } else if ($setting_catalog_all['img_slider'] == '3') { $js[] = '
		function thumb_hover_images() {
			if($(window).width() < 767) return;
			$(\'.product_thumb_hover_images img\').each(function () {
				if($(this).data(\'images_additional\')) {
					let image = $(this),
						image_src = $(this).attr(\'src\'),
						array_images = $(this).data(\'images_additional\').split(\', \'),
						parent_elem = image.parent(),
						html = \'\';
					if(typeof(image_src) == \'undefined\') image_src = $(this).data(\'src\');
					array_images.unshift(image_src);
					image.data(\'images_additional\', false)
					html += \'<div class="hover_images_block">\';
					html += \'<div class="hover_images_block_slide">\';
					for(i in array_images) {
						html += \'<div class="hover_images_block_slide_img\'+(i == 0 ? \' active\' : \'\')+\'" newsrc="\'+array_images[i]+\'"></div>\';
					}
					html += \'</div>\';
					html += \'</div>\';
					image.after(html);
					const slide_img = parent_elem.find(\'.hover_images_block_slide_img\');
					$(\'.hover_images_block\').addClass(\'visible\');
					slide_img.on(\'mouseenter\', function(e) {
						image.attr(\'src\', $(this).attr(\'newsrc\'));
						slide_img.removeClass(\'active\').eq($(this).index()).addClass(\'active\');
					});
					parent_elem.on(\'mouseleave\', () => {
						//image.attr(\'src\', image_src);
						slide_img.removeClass(\'active\').first().addClass(\'active\');
						var slide_img_act = parent_elem.find(\'.hover_images_block_slide_img.active\');
						image.attr(\'src\', slide_img_act.attr(\'newsrc\'));
					});
				}
			});
		};
		$(function() {
			thumb_hover_images();
			$(document).ajaxStop(thumb_hover_images);
		});
	'; } $js[] = '
	}
'; } $js[] = '
'; if ($setting_catalog_all['img_slider']) { $js[] = '
	$(\'#content .owlproduct\').owlCarousel({
		beforeInit: true,
		items: 1,
		singleItem: true,
		mouseDrag: false,
		autoPlay: false,
		navigation: true,
		navigationText: [\'<i class="fa fa-chevron-left fa-3x"></i>\', \'<i class="fa fa-chevron-right fa-3x"></i>\'],
		pagination: false
	});
'; } else { $js[] = '
	$(\'.owl-carousel.owlproduct\').remove();
'; } $js[] = '
function list_view(){
	$(\'#content .products_category .product-grid > .clearfix\').remove();
	$(\'#content .products_category .product-grid, #content .products_category .product-price\').attr(\'class\', \'product-layout product-list col-xs-12\');
	$(\'#content .product-list .cart > a\').attr(\'data-toggle\', \'none\');
	$(\'#content .product-list .cart > a\').attr(\'title\', \'\');
	$(document).ready(function() {
		var w_list_img = $(\'.product-list .product-thumb .image\').outerWidth();
		'; if ($setting_catalog_all['img_slider']) { $js[] = '
			$(\'.product-layout .product-thumb > .image\').css(\'width\', \''.$catalog_img_width.'px\');
			podgon_img();
		'; } $js[] = '
	});
	$(\'.product-list .product-thumb h4\').css(\'height\', \'initial\');
	$(\'.product-list .product-thumb .product_buttons\').css(\'height\', \'initial\');
	$(\'.product-list .product-thumb .caption\').css(\'margin-left\', \''.$catalog_img_width.'px\');
	$(\'.product-list .product-thumb .description_options\').addClass(\'view_list_options\');
	$(\'.product-list .product-thumb .description_options\').css(\'height\', \'initial\');
	$(\'.product-layout.product-list\').css(\'height\', \'initial\');
	$(\'#grid-view, #price-view\').removeClass(\'active\');
	$(\'#list-view\').addClass(\'active\');
	localStorage.setItem(\'display\', \'list\');
}
function grid_view(){
	cols = $(\'#column-right, #column-left\').length;
	if (cols == 2) {
		$(\'#content .product-list, #content .product-price\').attr(\'class\', \'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12\');
	} else if (cols == 1) {
		$(\'#content .product-list, #content .product-price\').attr(\'class\', \'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12\');
	} else {
		$(\'#content .product-list, #content .product-price\').attr(\'class\', \'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12\');
	}
	'; if ($revtheme_all_settings['mobil_two']) { $js[] = '
		if ($(window).width() > 294 && $(window).width() < 975) {
			$(\'#content .product-layout.product-grid\').attr(\'class\', \'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6\');
		}
	'; } $js[] = '
	$(\'.product-grid .product-thumb .caption\').css(\'margin-left\', \'initial\');
	$(\'.product-grid .product-thumb .description_options\').removeClass(\'view_list_options\');
	var product_grid_width = $(\'.product-layout .product-thumb\').outerWidth();
	var product_item_width = $(\'.rev_slider .item .product-thumb\').outerWidth();
	if (product_grid_width < 240) {
		$(\'.product-layout\').addClass(\'new_line\');
		$(\'.rev_slider .item\').addClass(\'new_line\');
	} else {
		$(\'.product-layout\').removeClass(\'new_line\');
		$(\'.rev_slider .item\').removeClass(\'new_line\');
	}
	if (product_item_width < 240) {
		$(\'.rev_slider .item\').addClass(\'new_line\');
	} else {
		$(\'.rev_slider .item\').removeClass(\'new_line\');
	}
	'; if ($setting_catalog_all['img_slider']) { $js[] = '
		$(\'.product-layout .product-thumb > .image\').css(\'width\', \'100%\');
		var product_grid_width = $(\'.product-layout .product-thumb\').outerWidth();
		if (localStorage.getItem(\'display\') == \'price\') {
			if (product_grid_width < 240) {window.setTimeout(function() {podgon_img();},150)} else {podgon_img();}
		} else {
			podgon_img();
		}
	'; } $js[] = '
	max_height_div(\'.product-grid .product-thumb h4\');
	max_height_div(\'.product-grid .product-thumb .price\');
	max_height_div(\'.product-grid .product-thumb .product_buttons\');
	'; if (!$revtheme_all_settings['cat_opis_opt']) { $js[] = '
	setTimeout(function() {
		max_height_div(\'.product-grid .product-thumb .description_options\');
	}, 300);
	'; } else { $js[] = '
	max_height_div(\'.product-layout.product-grid\');
	'; } $js[] = '
	$(\'#list-view, #price-view\').removeClass(\'active\');
	$(\'#grid-view\').addClass(\'active\');
	localStorage.setItem(\'display\', \'grid\');
}
function price_view(){
	$(\'#content .products_category .product-grid > .clearfix\').remove();
	$(\'#content .products_category .product-list, #content .products_category .product-grid\').attr(\'class\', \'product-layout product-price col-xs-12\');
	$(\'#content .product-view .cart > a\').attr(\'data-toggle\', \'none\');
	$(\'#content .product-view .cart > a\').attr(\'title\', \'\');
	$(\'.product-price .product-thumb h4\').css(\'height\', \'initial\');
	$(\'.product-price .product-thumb .caption\').css(\'margin-left\', \'initial\');
	$(\'.product-price .product-thumb .product_buttons\').css(\'height\', \'initial\');
	$(\'.product-price .product-thumb .description_options\').removeClass(\'view_list_options\');
	$(\'.product-price .product-thumb .description_options\').css(\'height\', \'initial\');
	$(\'.product-layout.product-price\').css(\'height\', \'initial\');
	$(\'#list-view, #grid-view\').removeClass(\'active\');
	$(\'#price-view\').addClass(\'active\');
	localStorage.setItem(\'display\', \'price\');
}
$(\'#list-view\').click(function() {
	list_view();
});
$(\'#grid-view\').click(function() {
	grid_view();
});
$(\'#price-view\').click(function() {
	price_view();
});
'; if ($revtheme_all_settings['opacity_cont']) { $js[] = '
	$(\'html\').removeClass(\'opacity_minus\').addClass(\'opacity_plus\');
	'; if (!$revtheme_all_settings['n_progres']) { $js[] = '
		$(window).load(function() {
			$(\'html\').removeClass(\'opacity_plus\');
		});
	'; } $js[] = '
'; } else { $js[] = '
	$(\'html\').removeClass(\'opacity_minus_products\').addClass(\'opacity_plus_products\');
'; } $js[] = '
'; if ($revtheme_footer_all['callbtn']) {
$revtheme_dop_menu_callbtn = $this->request->post['revtheme_dop_menu_callbtn'];
$revtheme_dop_menus_callbtn = json_decode(htmlspecialchars_decode($revtheme_dop_menu_callbtn), true); $js[] = '
	$(document).ready(function(){
	var triggered = false;
	$(".triggerbtn").click(function(){
		if(triggered == false){
			$(this).toggleClass(\'animbuttonsh\');
			$(this).html(\'<i class=\"fa fa-close\" aria-hidden=\"true\"></i>\');
			var id = 6;
			'; foreach ($revtheme_dop_menus_callbtn as $revtheme_dop_menu) { $js[] = '
			$(\'.share_icon_callbtn.'.$revtheme_dop_menu['sort'].'\').animate({bottom: id+\'em\'}, 150);
			id+=4;
			'; } $js[] = '
			triggered = true;
		} else {
			$(this).toggleClass(\'animbuttonsh\');
			$(this).html(\'<i class=\"fa fa-comments-o fa-lg\" aria-hidden=\"true\"></i>\');
			$(\'.share_icon_callbtn\').animate({bottom: \'2em\'}, 150);
			triggered = false;
		}
	})
})
'; } $js[] = '
'; if ($revtheme_all_settings['n_progres']) { $js[] = '
	NProgress.start();
	$(window).load(function() {
		NProgress.done();
		$(\'html\').removeClass(\'opacity_plus\');
	});
'; } $js[] = '
'; if ($revtheme_header_menu['sticky']) { $js[] = '
	if($(window).width() > 768) {
		$(\'#top3\').affix({
			offset: {
				'; if (!$revtheme_all_settings['all_document_width'] && $revtheme_all_settings['all_document_margin'] && !$revtheme_all_settings['all_document_h_f_width']) { $js[] = '
					top: $(\'#top\').outerHeight()+$(\'#top2\').outerHeight()+$(\'html.common-home #menu2.inhome\').outerHeight()+20
				'; } else { $js[] = '
					top: $(\'#top\').outerHeight()+$(\'#top2\').outerHeight()+$(\'html.common-home #menu2.inhome\').outerHeight()
				'; } $js[] = '
			}
		});
	}
	'; if (!$revtheme_all_settings['all_document_width'] && $revtheme_all_settings['all_document_margin'] && $revtheme_all_settings['all_document_h_f_width']) { $js[] = '
		if($(window).width() > 974) {
			$(\'#all_document\').css(\'margin-top\', \'60px\');
		}
	'; } $js[] = '
	var win_shopcart = $(window).height();
	var win_shopcart2 = $(\'#top\').outerHeight()+$(\'#top2\').outerHeight()+$(\'#top3\').outerHeight();
	//$(\'#cart .dropdown-menu > li\').css(\'max-height\', win_shopcart-win_shopcart2).css(\'overflow-y\', \'auto\');
	$(\'#top3 #menu2 .child-box\').css(\'max-height\', win_shopcart-win_shopcart2).css(\'overflow-y\', \'auto\');
'; } $js[] = '
$(function() {
	if (localStorage.getItem(\'display\') == \'list\') {
		list_view();
	} else if (localStorage.getItem(\'display\') == \'price\') {
		price_view();
	} else if (localStorage.getItem(\'display\') == \'grid\') {
		grid_view();
	} else {
		'; if ($setting_catalog_all['vid_default'] == 'vid_price') { $js[] = '
			price_view();
		'; } else if ($setting_catalog_all['vid_default'] == 'vid_list') { $js[] = '
			list_view();
		'; } else if ($setting_catalog_all['vid_default'] == 'vid_grid') { $js[] = '
			grid_view();
		'; } $js[] = '
	}
	'; if ($revtheme_product_all['option_f_auto']) { $js[] = '
		var pr_opts_pr = $(\'.product-info .options_no_buy .form-group\');
		pr_opts_pr.find(\'input[type=\"checkbox\"]:first, input[type=\"radio\"]:first\').each(function() {
			this.checked = true;
			this.onchange();
		});
		pr_opts_pr.find(\'select:first\').each(function() {
			if ($(this).find(\'option:selected\').length < 1) {
				this.options[1].selected=true;
				this.onchange();
			}
		});
		var pr_opts_cat = $(\'.description_options .product-info .form-group\');
		pr_opts_cat.find(\'input[type=\"checkbox\"]:first, .radio:first-child input[type=\"radio\"]\').each(function() {
			this.checked = true;
			this.onchange();
		});
		pr_opts_cat.find(\'select:first\').each(function() {
			if ($(this).find(\'option:selected\').length < 1) {
				this.options[1].selected=true;
				this.onchange();
			}
		});
		var pr_opts_cat_mf = $(\'#slider_m_f .product-info .form-group\');
		pr_opts_cat_mf.find(\'input[type=\"checkbox\"]:first, .radio:first-child input[type=\"radio\"]\').each(function() {
			this.checked = true;
			this.onchange();
		});
		pr_opts_cat_mf.find(\'select:first\').each(function() {
			if ($(this).find(\'option:selected\').length < 1) {
				this.options[1].selected=true;
				this.onchange();
			}
		});
	'; } $js[] = '
	podgon_fona();
	$(window).resize(podgon_fona);
});
function podgon_fona() {
	toggle_ellipses();
	var h_top5 = $(\'.inhome #menu2\').outerHeight();
	if (h_top5) {
		$(\'#top5\').css(\'min-height\', h_top5+20);
	}
	'; if ($revtheme_header_menu['up_menu_height']) { $js[] = '
		var h_top4 = $(\'#top4\').outerHeight();
		$(\'html.common-home #menu2.inhome\').css(\'min-height\', h_top4+50);
	'; } $js[] = '
	var m2inh = $(\'html.common-home #menu2.inhome\').outerHeight();
	$(\'html.common-home #menu2.inhome .podmenu2\').css(\'height\', m2inh);
	var m2inhw = $(\'html.common-home #menu2_button\').outerWidth();
	$(\'html.common-home #menu2.inhome .podmenu2\').css(\'min-width\', m2inhw-0.5);
	'; if ($revtheme_header_menu['sticky'] && $revtheme_header_menu['up_menu_height']) { $js[] = '
		$(\'html.common-home #top3.affix #menu2.inhome\').css(\'min-height\', \'initial\');
		var m2inh = $(\'html.common-home #menu2.inhome\').outerHeight();
		$(\'html.common-home #top3.affix #menu2.inhome .podmenu2\').css(\'height\', m2inh);
		$(document).on(\'scroll\', function(){
			var home_amazon_height = $(\'#top\').outerHeight()+$(\'#top2\').outerHeight()+$(\'html.common-home #top3 #menu2.inhome\').outerHeight();
			if ($(window).scrollTop() > home_amazon_height) {
				$(\'html.common-home #top3.affix #menu2.inhome\').css(\'min-height\', \'initial\');
				$(\'html.common-home #top3.affix #menu2.inhome .podmenu2\').css(\'min-height\', \'initial\');
			} else {
				var h_top4 = $(\'#top4\').outerHeight();
				$(\'html.common-home #top3 #menu2.inhome\').css(\'min-height\', h_top4+50);
				$(\'html.common-home #top3 #menu2.inhome .podmenu2\').css(\'min-height\', h_top4+50);
			}
		});
	'; } $js[] = '
	'; if ($revtheme_header_menu['sticky']) { $js[] = '
		var h_top3 = $(\'#top3\').outerHeight();
		'; if (!$revtheme_all_settings['all_document_width'] && $revtheme_all_settings['all_document_h_f_width'] && $revtheme_all_settings['all_document_margin']) { $js[] = '
		'; } else { $js[] = '
		$(\'.main-content\').css(\'padding-top\', h_top3+25);
		'; } $js[] = '
	'; } $js[] = '
	'; if (($revtheme_all_settings['all_document_width']) || (!$revtheme_all_settings['all_document_width'] && $revtheme_all_settings['all_document_h_f_width'])) { $js[] = '
		$(\'#top3\').addClass(\'absolutpo\');
	'; } $js[] = '
	'; if ($setting_catalog_all['vid_default'] == 'vid_grid') { $js[] = '
		if($(window).width() < 767) {
			grid_view();
		}
	'; } $js[] = '
	'; if ($revtheme_all_settings['mobil_two']) { $js[] = '
		if ($(window).width() > 294 && $(window).width() < 975) {
			$(\'#content .product-layout.product-grid\').attr(\'class\', \'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6\');
		}
	'; } $js[] = '
	'; if ($revtheme_header_menu['image_in_ico'] && !$revtheme_header_menu['type']) { $js[] = '
		var menu_ico_width = $(\'.image_in_ico_row\').outerWidth();
		$(\'#menu .nav > li .dropdown-menu\').css(\'width\', menu_ico_width-30);
	'; } $js[] = '

	var product_grid_width = $(\'.product-layout .product-thumb\').outerWidth();
	var product_item_width = $(\'.rev_slider .item .product-thumb\').outerWidth();
	if (product_grid_width < 240) {
		$(\'.product-layout\').addClass(\'new_line\');
		$(\'.rev_slider .item\').addClass(\'new_line\');
	} else {
		$(\'.product-layout\').removeClass(\'new_line\');
		$(\'.rev_slider .item\').removeClass(\'new_line\');
	}
	if (product_item_width < 240) {
		$(\'.rev_slider .item\').addClass(\'new_line\');
	} else {
		$(\'.rev_slider .item\').removeClass(\'new_line\');
	}
	max_height_div(\'.product-grid .product-thumb h4\');
	max_height_div(\'.product-grid .product-thumb .price\');
	max_height_div(\'.product-grid .product-thumb .product_buttons\');
	'; if (!$revtheme_all_settings['cat_opis_opt']) { $js[] = '
	setTimeout(function() {
		max_height_div(\'.product-grid .product-thumb .description_options\');
	}, 300);
	'; } else { $js[] = '
	$(\'.product-grid .product-thumb\').toggleClass(\'not_vivod_do\');
	setTimeout(function() {
		max_height_div(\'.product-layout.product-grid\');
		max_height_div(\'.rev_slider .owl-carousel .product-layout.col-xs-12\');
		$(\'.product-grid .product-thumb\').toggleClass(\'not_vivod_do\');
	}, 50);
	'; } $js[] = '
	max_height_div(\'#content .refine_categories.clearfix a > span\');
}
function toggle_ellipses() {
  var ellipses1 = $(\'.br_ellipses\');
  var howlong = $(\'.breadcrumb li:hidden\').length;
  if ($(\'.breadcrumb li:hidden\').length > 1) {
    ellipses1.show().css(\'display\', \'inline\');
  } else {
    ellipses1.hide();
  }
}

$(document).on(\'scroll\', function() {
	if ($(window).scrollTop() > 100) {
		$(\'.scroll-top-wrapper\').addClass(\'show\');
	} else {
		$(\'.scroll-top-wrapper\').removeClass(\'show\');
	}
});

$(\'.scroll-top-wrapper\').on(\'click\', scrollToTop);
$(\'.popup-phone-wrapper\').on(\'click\', get_revpopup_phone);
function scrollToTop() {
	verticalOffset = typeof(verticalOffset) != \'undefined\' ? verticalOffset : 0;
	element = $(\'body\');
	offset = element.offset();
	offsetTop = offset.top;
	$(\'html, body\').animate({scrollTop: offsetTop}, 200, \'linear\');
};
function get_revpopup_notification(m_class, m_header, message) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$(\'.tooltip\').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = \'mfp-zoom-in\';
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			}, 
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
			}
		},
		tLoading: \'\',
		items: {
			src: $(\'<div class="popup_notification"><div class="popup_notification_heading \'+m_class+\'">\'+m_header+\'</div><div class="popup_notification_message">\'+message+\'</div></div>\'),
			type: \'inline\'
		}
	});
}
function get_revpopup_phone() {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$(\'.tooltip\').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
				this.st.mainClass = \'mfp-zoom-in\';
				$(\'.dropdown-menu.dop_contss\').fadeOut(70);
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			}, 
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
				$(\'.dropdown-menu.dop_contss\').css(\'display\', \'\');
			}
		},
		tLoading: \'\',
		items: {
			src: \'index.php?route=revolution/revpopupphone\',
			type: \'ajax\'
		}
	});
}
function get_revpopup_view(product_id) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$(\'.tooltip\').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = \'mfp-zoom-in\';
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			},
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
			}
		},
		tLoading: \'\',
		items: {
			src: \'index.php?route=revolution/revpopupview&revproduct_id=\'+product_id,
			type: \'ajax\'
		}
	});
}
function get_revpopup_purchase(product_id) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$(\'.tooltip\').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = \'mfp-zoom-in\';
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			}, 
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
			}
		},
		tLoading: \'\',
		items: {
			src: \'index.php?route=revolution/revpopuporder&revproduct_id=\'+product_id,
			type: \'ajax\'
		}
	});
}
function get_revpopup_cartquick() {
	$(\'#cart .dropdown-menu\').css(\'display\', \'none\');
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$(\'.tooltip\').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = \'mfp-zoom-in\';
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			}, 
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
				$(\'#cart .dropdown-menu\').css(\'display\', \'\');
			}
		},
		tLoading: \'\',
		items: {
			src: \'index.php?route=revolution/revpopupcartquick\',
			type: \'ajax\'
		}
	});
}
function get_revpopup_cart( product_id, action, quantity, block_id ) {
	$(\'.tooltip\').hide();
	quantity = typeof(quantity) != \'undefined\' ? quantity : 1;
	if ( action == "catalog" ) {
		data = $(\'.products_category .product_\'+product_id+\' .options input[type=\"text\"], .products_category .product_\'+product_id+\' .options input[type=\"hidden\"], .products_category .product_\'+product_id+\' .options input[type=\"radio\"]:checked, .products_category .product_\'+product_id+\' .options input[type=\"checkbox\"]:checked, .products_category .product_\'+product_id+\' .options select\');
		$.ajax({
			url: \'index.php?route=checkout/cart/add\',
			type: \'post\',
			data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
			dataType: \'json\',
			success: function( json ) {
				$(\'.alert, .text-danger\').remove();
				$(\'.form-group\').removeClass(\'has-error\');
				$(\'.success, .warning, .attention, information, .error\').remove();
				'; if (!$description_options['options_in_cat']) { $js[] = '
				if ( json[\'redirect\'] ) {
					location = json[\'redirect\'];
				}
				'; } else { $js[] = '
				if (localStorage.getItem(\'display\') == \'price\' || block_id) {
					if ( json[\'redirect\'] ) {
						location = json[\'redirect\'];
					}
				'; if (!$description_options['options_in_grid']) { $js[] = '
				} else if (localStorage.getItem(\'display\') == \'grid\') {
					if ( json[\'redirect\'] ) {
						location = json[\'redirect\'];
					}
				'; } $js[] = '
				} else {
					$(\'.products_category .form-group.required\').removeClass(\'opt_required\');
					if (json[\'error\']) {
						$(\'body\').removeClass(\'razmiv2\');
						$(\'#pagefader2\').fadeOut(70);
						$(\'#top3.absolutpo\').css(\'right\', \'initial\');
						if ($(window).width() < 768) {
							$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
						}
						if (json[\'error\'][\'option\']) {
							if ($(window).width() < 768) {
								if (json[\'redirect\']) {
									location = json[\'redirect\'];
								}
							} else {
								for (i in json[\'error\'][\'option\']) {
									$(\'.products_category #input-option\' + i).parent().addClass(\'opt_required\');
								}
							}
						}
					}
				}
				'; } $js[] = '
				if ( json[\'success\'] ) {
					'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
						if (document.body.scrollHeight > document.body.offsetHeight) {
							$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
							if ($(window).width() < 768) {
								$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = \'mfp-zoom-in\';
							},
							close: function() {
								$(\'body\').removeClass(\'razmiv2\');
								$(\'#pagefader2\').fadeOut(70);
								$(\'#top3.absolutpo\').css(\'right\', \'initial\');
								if ($(window).width() < 768) {
									$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
								}
							}
						},
						tLoading: \'\',
						items: {
							src: \'index.php?route=revolution/revpopupcart\',
							type: \'ajax\'
						}
						});
					'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
					'; } else { $js[] = '
						'; if ($setting_catalog_all['img_slider']) { $js[] = '
						tmp_img = $(\'.products_category .product_\'+product_id+\' .image .owl-item:first-child img\');
						'; } else { $js[] = '
						tmp_img = $(\'.products_category .product_\'+product_id+\' .image img\');
						'; } $js[] = '
						'; if ($revtheme_all_settings['mobile_header']) { $js[] = '
							if ($(window).width() < 768) {
								header_cart_element = "#top #cart_mobi";
							} else {
								'; if ($revtheme_header_cart['cart_position']) { $js[] = '
									header_cart_element = "#top2 #cart";
								'; } else { $js[] = '
									header_cart_element = "#top3 #cart";
								'; } $js[] = '
							}
						'; } else { $js[] = '
							'; if ($revtheme_header_cart['cart_position']) { $js[] = '
								header_cart_element = "#top2 #cart";
							'; } else { $js[] = '
								header_cart_element = "#top3 #cart";
							'; } $js[] = '
						'; } $js[] = '
						$(tmp_img)
							.clone()
							.css({width : $(\'.product-price .image\').outerWidth(), \'position\' : \'absolute\', \'z-index\' : \'999\', top: $(\'.products_category .product_\'+product_id+\' .image img\').offset().top, left: $(\'.products_category .product_\'+product_id+\' .image img\').offset().left})
							.appendTo("body")
							.animate({opacity: 0.3,
								left: $(header_cart_element).offset()[\'left\'],
								top: $(header_cart_element).offset()[\'top\']+15,
								width: 10}, 800, function() {
								$(this).remove();
							});
					'; } $js[] = '
					$(\'#top #cart-total_mobi\').html(json[\'total\']);
					$(\'#top3 #cart-total\').html(json[\'total\']);
					$(\'#top2 #cart-total\').html(json[\'total\']);
					$(\'#top3 #cart-total-popup\').html(json[\'total\']);
					$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
					'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
						setTimeout(function() {
							$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
							$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
						}, 300);
					'; } $js[] = '
				}
			}
		});
	}
	if ( action == "catalog_mod" ) {
		data = $(\'.products_category .product_\'+product_id+\' .options input[type=\"text\"], .products_category .product_\'+product_id+\' .options input[type=\"hidden\"], .products_category .product_\'+product_id+\' .options input[type=\"radio\"]:checked, .products_category .product_\'+product_id+\' .options input[type=\"checkbox\"]:checked, .products_category .product_\'+product_id+\' .options select\');
		$.ajax({
			url: \'index.php?route=checkout/cart/add\',
			type: \'post\',
			data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
			dataType: \'json\',
			success: function( json ) {
				$(\'.alert, .text-danger\').remove();
				$(\'.form-group\').removeClass(\'has-error\');
				$(\'.success, .warning, .attention, information, .error\').remove();

				if ( json[\'redirect\'] ) {
					location = json[\'redirect\'];
				}
				
				if ( json[\'success\'] ) {
					'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
						if (document.body.scrollHeight > document.body.offsetHeight) {
							$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
							if ($(window).width() < 768) {
								$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = \'mfp-zoom-in\';
							},
							close: function() {
								$(\'body\').removeClass(\'razmiv2\');
								$(\'#pagefader2\').fadeOut(70);
								$(\'#top3.absolutpo\').css(\'right\', \'initial\');
								if ($(window).width() < 768) {
									$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
								}
							}
						},
						tLoading: \'\',
						items: {
							src: \'index.php?route=revolution/revpopupcart\',
							type: \'ajax\'
						}
						});
					'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
					'; } else { $js[] = '
						'; if ($revtheme_all_settings['mobile_header']) { $js[] = '
							if ($(window).width() < 768) {
								header_cart_element = "#top #cart_mobi";
							} else {
								'; if ($revtheme_header_cart['cart_position']) { $js[] = '
									header_cart_element = "#top2 #cart";
								'; } else { $js[] = '
									header_cart_element = "#top3 #cart";
								'; } $js[] = '
							}
						'; } else { $js[] = '
							'; if ($revtheme_header_cart['cart_position']) { $js[] = '
								header_cart_element = "#top2 #cart";
							'; } else { $js[] = '
								header_cart_element = "#top3 #cart";
							'; } $js[] = '
						'; } $js[] = '
						tmp_img = $(\'.products_category .product_\'+product_id+\' .image img\');
						$(tmp_img)
							.clone()
							.css({width : $(\'.products_category .product_\'+product_id+\' .image img\').outerWidth(), \'position\' : \'absolute\', \'z-index\' : \'999\', top: $(\'.products_category .product_\'+product_id+\' .image img\').offset().top, left: $(\'.products_category .product_\'+product_id+\' .image img\').offset().left})
							.appendTo("body")
							.animate({opacity: 0.3,
								left: $(header_cart_element).offset()[\'left\'],
								top: $(header_cart_element).offset()[\'top\']+15,
								width: 10}, 800, function() {
								$(this).remove();
							});
					'; } $js[] = '
					$(\'#top #cart-total_mobi\').html(json[\'total\']);
					$(\'#top3 #cart-total\').html(json[\'total\']);
					$(\'#top2 #cart-total\').html(json[\'total\']);
					$(\'#top3 #cart-total-popup\').html(json[\'total\']);
					$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
					'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
						setTimeout(function() {
							$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
							$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
						}, 300);
					'; } $js[] = '
				}
			}
		});
	}
	if ( action == "product" ) {
		data = $(\'.product_informationss .product-info input[type=\"text\"], .product_informationss .product-info input[type=\"hidden\"], .product_informationss .product-info input[type=\"radio\"]:checked, .product_informationss .product-info input[type=\"checkbox\"]:checked, .product_informationss .product-info select, .product_informationss .product-info textarea\'),
		$.ajax({
			url: \'index.php?route=checkout/cart/add\',
			type: \'post\',
			data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
			dataType: \'json\',
			'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
			beforeSend: function(){
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			},
			'; } $js[] = '
			success: function( json ) {
				$(\'.alert, .text-danger\').remove();
				$(\'.form-group\').removeClass(\'has-error\');
				$(\'.success, .warning, .attention, information, .error\').remove();
				if (json[\'error\']) {
					$(\'body\').removeClass(\'razmiv2\');
					$(\'#pagefader2\').fadeOut(70);
					$(\'#top3.absolutpo\').css(\'right\', \'initial\');
					if ($(window).width() < 768) {
						$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
					}
					if (json[\'error\'][\'option\']) {
						for (i in json[\'error\'][\'option\']) {
							$(\'#input-option\' + i).before(\'<span class="error bg-danger">\' + json[\'error\'][\'option\'][i] + \'</span>\');
							if ($(window).width() < 768) {
								$(\'html, body\').animate({scrollTop:$(\'.error\').offset().top - 40}, \'linear\');
							}
						}
					}
				}
				if ( json[\'success\'] ) {
					'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
						if (document.body.scrollHeight > document.body.offsetHeight) {
							$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
							if ($(window).width() < 768) {
								$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
							}
						}
						$.magnificPopup.open({
							removalDelay: 170,
							callbacks: {
								beforeOpen: function() {
								   this.st.mainClass = \'mfp-zoom-in\';
								},
								close: function() {
									$(\'body\').removeClass(\'razmiv2\');
									$(\'#pagefader2\').fadeOut(70);
									$(\'#top3.absolutpo\').css(\'right\', \'initial\');
									if ($(window).width() < 768) {
										$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
									}
								}
							},
							tLoading: \'\',
							items: {
								src: \'index.php?route=revolution/revpopupcart\',
								type: \'ajax\'
							}
						});
					'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
					'; } else { $js[] = '
						'; if ($revtheme_all_settings['mobile_header']) { $js[] = '
							if ($(window).width() < 768) {
								header_cart_element = "#top #cart_mobi";
							} else {
								'; if ($revtheme_header_cart['cart_position']) { $js[] = '
									header_cart_element = "#top2 #cart";
								'; } else { $js[] = '
									header_cart_element = "#top3 #cart";
								'; } $js[] = '
							}
						'; } else { $js[] = '
							'; if ($revtheme_header_cart['cart_position']) { $js[] = '
								header_cart_element = "#top2 #cart";
							'; } else { $js[] = '
								header_cart_element = "#top3 #cart";
							'; } $js[] = '
						'; } $js[] = '
						tmp_img = $(\'.main-image img\')
						$(tmp_img)
							.clone()
							.css({\'position\' : \'absolute\', \'z-index\' : \'999\', top: $(\'.main-image img\').offset().top, left: $(\'.main-image img\').offset().left})
							.appendTo("body")
							.animate({opacity: 0.3,
								left: $(header_cart_element).offset()[\'left\'],
								top: $(header_cart_element).offset()[\'top\']+15,
								width: 10}, 800, function() {
								$(this).remove();
							});
					'; } $js[] = '
					$(\'#top #cart-total_mobi\').html(json[\'total\']);
					$(\'#top3 #cart-total\').html(json[\'total\']);
					$(\'#top2 #cart-total\').html(json[\'total\']);
					$(\'#top3 #cart-total-popup\').html(json[\'total\']);
					$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
					'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
						setTimeout(function() {
							$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
							$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
						}, 300);
					'; } $js[] = '
				}
			}
		});
	}
	if ( action == "popup_product" ) {
		data = $(\'#popup-view-wrapper .product-info input[type=\"text\"], #popup-view-wrapper .product-info input[type=\"hidden\"], #popup-view-wrapper .product-info input[type=\"radio\"]:checked, #popup-view-wrapper .product-info input[type=\"checkbox\"]:checked, #popup-view-wrapper .product-info select, #popup-view-wrapper .product-info textarea\');
		$.ajax({
			url: \'index.php?route=checkout/cart/add\',
			type: \'post\',
			data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
			dataType: \'json\',
			'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
			beforeSend: function(){
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			},
			'; } $js[] = '
			success: function( json ) {
				$(\'.alert, .text-danger\').remove();
				$(\'.form-group\').removeClass(\'has-error\');
				$(\'.success, .warning, .attention, information, .error\').remove();			
				if (json[\'error\']) {
					$(\'body\').removeClass(\'razmiv2\');
					$(\'#pagefader2\').fadeOut(70);
					$(\'#top3.absolutpo\').css(\'right\', \'initial\');
					if ($(window).width() < 768) {
						$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
					}
					if (json[\'error\'][\'option\']) {
						if ($(window).width() < 768) {
							if (json[\'redirect\']) {
								location = json[\'redirect\'];
							}
						} else {
							for (i in json[\'error\'][\'option\']) {
								$(\'#input-option\' + i).before(\'<span class="error bg-danger">\' + json[\'error\'][\'option\'][i] + \'</span>\');
							}
						}
					}
				}
				if ( json[\'success\'] ) {
					'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
						if (document.body.scrollHeight > document.body.offsetHeight) {
							$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
							if ($(window).width() < 768) {
								$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
							}
						}
						$.magnificPopup.open({
							removalDelay: 170,
							callbacks: {
								beforeOpen: function() {
								   this.st.mainClass = \'mfp-zoom-in\';
								},
								close: function() {
									$(\'body\').removeClass(\'razmiv2\');
									$(\'#pagefader2\').fadeOut(70);
									$(\'#top3.absolutpo\').css(\'right\', \'initial\');
									if ($(window).width() < 768) {
										$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
									}
								}
							},
							tLoading: \'\',
							items: {
								src: \'index.php?route=revolution/revpopupcart\',
								type: \'ajax\'
							}
						});
					'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
					'; } else { $js[] = '
						$.magnificPopup.close();
					'; } $js[] = '	
					$(\'#top #cart-total_mobi\').html(json[\'total\']);
					$(\'#top3 #cart-total\').html(json[\'total\']);
					$(\'#top2 #cart-total\').html(json[\'total\']);
					$(\'#top3 #cart-total-popup\').html(json[\'total\']);
					$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
					'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
						setTimeout(function() {
							$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
							$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
						}, 300);
					'; } $js[] = '
				}
			}
		});
	}
	if ( action == "show_cart" ) {
		if (document.body.scrollHeight > document.body.offsetHeight) {
			$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
			if ($(window).width() < 768) {
				$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
			}
		}
		$.magnificPopup.open({
			removalDelay: 170,
			callbacks: {
				beforeOpen: function() {
				   this.st.mainClass = \'mfp-zoom-in\';
				},
				open: function() {
					$(\'body\').addClass(\'razmiv2\');
					$(\'#pagefader2\').fadeIn(70);
				}, 
				close: function() {
					$(\'body\').removeClass(\'razmiv2\');
					$(\'#pagefader2\').fadeOut(70);
					$(\'#top3.absolutpo\').css(\'right\', \'initial\');
					if ($(window).width() < 768) {
						$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
					}
				}
			},
			tLoading: \'\',
			items: {
				src: \'index.php?route=revolution/revpopupcart\',
				type: \'ajax\'
			}
		});
	}
	if ( action == "redirect_cart" ) {
		  window.location.href = "/index.php?route=checkout/checkout"
	}
	if ( action == "module" ) {
		quantity = typeof(quantity) != \'undefined\' ? quantity : 1;
		data = $(\'#\'+block_id+\' .product_\'+product_id+\' .options input[type=\"text\"], #\'+block_id+\' .product_\'+product_id+\' .options input[type=\"hidden\"], #\'+block_id+\' .product_\'+product_id+\' .options input[type=\"radio\"]:checked, #\'+block_id+\' .product_\'+product_id+\' .options input[type=\"checkbox\"]:checked, #\'+block_id+\' .product_\'+product_id+\' .options select\');
		$.ajax({
			url: \'index.php?route=checkout/cart/add\',
			type: \'post\',
			data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
			dataType: \'json\',
			success: function( json ) {
				$(\'.alert, .text-danger\').remove();
				$(\'.form-group\').removeClass(\'has-error\');
				$(\'.success, .warning, .attention, information, .error\').remove();
				'; if (!$description_options['options_in_cat'] || !$description_options['options_in_grid'] || !$revtheme_home_all['pr_opisanie']) { $js[] = '
				if ( json[\'redirect\'] ) {
					location = json[\'redirect\'];
				}
				'; } else { $js[] = '
				$(\'#\'+block_id+\' .form-group.required\').removeClass(\'opt_required\');
				if (json[\'error\']) {
					$(\'body\').removeClass(\'razmiv2\');
					$(\'#pagefader2\').fadeOut(70);
					$(\'#top3.absolutpo\').css(\'right\', \'initial\');
					if ($(window).width() < 768) {
						$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
					}
					if (json[\'error\'][\'option\']) {
						if ($(window).width() < 768) {
							if (json[\'redirect\']) {
								location = json[\'redirect\'];
							}
						} else {
							for (i in json[\'error\'][\'option\']) {
								$(\'#\'+block_id+\' #input-option\' + i).parent().addClass(\'opt_required\');
							}
						}
					}
				}
				'; } $js[] = '
				if ( json[\'success\'] ) {
					'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
						if (document.body.scrollHeight > document.body.offsetHeight) {
							$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
							if ($(window).width() < 768) {
								$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = \'mfp-zoom-in\';
							},
							close: function() {
								$(\'body\').removeClass(\'razmiv2\');
								$(\'#pagefader2\').fadeOut(70);
								$(\'#top3.absolutpo\').css(\'right\', \'initial\');
								if ($(window).width() < 768) {
									$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
								}
							}
						},
						tLoading: \'\',
						items: {
							src: \'index.php?route=revolution/revpopupcart\',
							type: \'ajax\'
						}
						});
					'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
					'; } else { $js[] = '
						'; if ($setting_catalog_all['img_slider']) { $js[] = '
						tmp_img = $(\'#\'+block_id+\' .product_\'+product_id+\' .image .owl-item:first-child img\');
						'; } else { $js[] = '
						tmp_img = $(\'#\'+block_id+\' .product_\'+product_id+\' .image img\');
						'; } $js[] = '
						'; if ($revtheme_all_settings['mobile_header']) { $js[] = '
							if ($(window).width() < 768) {
								header_cart_element = "#top #cart_mobi";
							} else {
								'; if ($revtheme_header_cart['cart_position']) { $js[] = '
									header_cart_element = "#top2 #cart";
								'; } else { $js[] = '
									header_cart_element = "#top3 #cart";
								'; } $js[] = '
							}
						'; } else { $js[] = '
							'; if ($revtheme_header_cart['cart_position']) { $js[] = '
								header_cart_element = "#top2 #cart";
							'; } else { $js[] = '
								header_cart_element = "#top3 #cart";
							'; } $js[] = '
						'; } $js[] = '
						$(tmp_img)
							.clone()
							.css({\'position\' : \'absolute\', \'z-index\' : \'999\', top: $(\'#\'+block_id+\' .product_\'+product_id+\' .image img\').offset().top, left: $(\'#\'+block_id+\' .product_\'+product_id+\' .image img\').offset().left})
							.appendTo("body")
							.animate({opacity: 0.3,
								left: $(header_cart_element).offset()[\'left\'],
								top: $(header_cart_element).offset()[\'top\']+15,
								width: 10}, 800, function() {
								$(this).remove();
							});
					'; } $js[] = '
					$(\'#top #cart-total_mobi\').html(json[\'total\']);
					$(\'#top3 #cart-total\').html(json[\'total\']);
					$(\'#top2 #cart-total\').html(json[\'total\']);
					$(\'#top3 #cart-total-popup\').html(json[\'total\']);
					$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
					'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
						setTimeout(function() {
							$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
							$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
						}, 300);
					'; } $js[] = '
				}
			}
		});
	}
	if ( action == "module_in_product" ) {
		quantity = typeof(quantity) != \'undefined\' ? quantity : 1;
		data = $(\'#\'+block_id+\' .product_\'+product_id+\' .options input[type=\"text\"], #\'+block_id+\' .product_\'+product_id+\' .options input[type=\"hidden\"], #\'+block_id+\' .product_\'+product_id+\' .options input[type=\"radio\"]:checked, #\'+block_id+\' .product_\'+product_id+\' .options input[type=\"checkbox\"]:checked, #\'+block_id+\' .product_\'+product_id+\' .options select\');
		$.ajax({
			url: \'index.php?route=checkout/cart/add\',
			type: \'post\',
			data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
			dataType: \'json\',
			'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
			beforeSend: function(){
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			},
			'; } $js[] = '
			success: function( json ) {
				$(\'.alert, .text-danger\').remove();
				$(\'.form-group\').removeClass(\'has-error\');
				$(\'.success, .warning, .attention, information, .error\').remove();
				'; if (!$description_options['options_in_cat'] || !$description_options['options_in_grid']) { $js[] = '
				if ( json[\'redirect\'] ) {
					location = json[\'redirect\'];
				}
				'; } else { $js[] = '
				$(\'#\'+block_id+\' .form-group.required\').removeClass(\'opt_required\');
				if (json[\'error\']) {
					$(\'body\').removeClass(\'razmiv2\');
					$(\'#pagefader2\').fadeOut(70);
					$(\'#top3.absolutpo\').css(\'right\', \'initial\');
					if ($(window).width() < 768) {
						$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
					}
					if (json[\'error\'][\'option\']) {
						if ($(window).width() < 768) {
							if (json[\'redirect\']) {
								location = json[\'redirect\'];
							}
						} else {
							for (i in json[\'error\'][\'option\']) {
								$(\'#\'+block_id+\' #input-option\' + i).parent().addClass(\'opt_required\');
							}
						}
					}
				}
				'; } $js[] = '
				if ( json[\'success\'] ) {
					'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
						if (document.body.scrollHeight > document.body.offsetHeight) {
							$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
							if ($(window).width() < 768) {
								$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = \'mfp-zoom-in\';
							},
							close: function() {
								$(\'body\').removeClass(\'razmiv2\');
								$(\'#pagefader2\').fadeOut(70);
								$(\'#top3.absolutpo\').css(\'right\', \'initial\');
								if ($(window).width() < 768) {
									$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
								}
							}
						},
						tLoading: \'\',
						items: {
							src: \'index.php?route=revolution/revpopupcart\',
							type: \'ajax\'
						}
						});
					'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
					'; } else { $js[] = '
						'; if ($setting_catalog_all['img_slider']) { $js[] = '
						tmp_img = $(\'#\'+block_id+\' .product_\'+product_id+\' .image .owl-item:first-child img\');
						'; } else { $js[] = '
						tmp_img = $(\'#\'+block_id+\' .product_\'+product_id+\' .image img\');
						'; } $js[] = '
						'; if ($revtheme_all_settings['mobile_header']) { $js[] = '
							if ($(window).width() < 768) {
								header_cart_element = "#top #cart_mobi";
							} else {
								'; if ($revtheme_header_cart['cart_position']) { $js[] = '
									header_cart_element = "#top2 #cart";
								'; } else { $js[] = '
									header_cart_element = "#top3 #cart";
								'; } $js[] = '
							}
						'; } else { $js[] = '
							'; if ($revtheme_header_cart['cart_position']) { $js[] = '
								header_cart_element = "#top2 #cart";
							'; } else { $js[] = '
								header_cart_element = "#top3 #cart";
							'; } $js[] = '
						'; } $js[] = '
						$(tmp_img)
							.clone()
							.css({\'position\' : \'absolute\', \'z-index\' : \'999\', top: $(\'#\'+block_id+\' .product_\'+product_id+\' .image img\').offset().top, left: $(\'#\'+block_id+\' .product_\'+product_id+\' .image img\').offset().left})
							.appendTo("body")
							.animate({opacity: 0.3,
								left: $(header_cart_element).offset()[\'left\'],
								top: $(header_cart_element).offset()[\'top\']+15,
								width: 10}, 800, function() {
								$(this).remove();
							});
					'; } $js[] = '
					$(\'#top #cart-total_mobi\').html(json[\'total\']);
					$(\'#top3 #cart-total\').html(json[\'total\']);
					$(\'#top2 #cart-total\').html(json[\'total\']);
					$(\'#top3 #cart-total-popup\').html(json[\'total\']);
					$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
					'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
						setTimeout(function() {
							$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
							$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
						}, 300);
					'; } $js[] = '
				}
			}
		});
	}
}
function get_revpopup_cart_option (opt_id, option, quantity, product_id) {
	$(\'.tooltip\').hide();
	$(\'.options_buy .pro_\'+option+\' input[name=\"option[\'+opt_id+\']\"]\').val(option);
	data = $(\'.product-info .options_buy .pro_\'+option+\' input[type=\"text\"], .product-info .options_buy .pro_\'+option+\' input[type=\"hidden\"], .product-info .options_buy .pro_\'+option+\' input[type=\"radio\"]:checked, .product-info .options_buy .pro_\'+option+\' input[type=\"checkbox\"]:checked, .product-info .options_buy .pro_\'+option+\' select, .product-info .options_buy .pro_\'+option+\' textarea\');
    $.ajax({
        url: \'index.php?route=checkout/cart/add\',
        type: \'post\',
		data: data.serialize() + \'&product_id=\' + product_id + \'&quantity=\' + quantity,
        dataType: \'json\',
		'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
			beforeSend: function(){
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			},
		'; } $js[] = '
        success: function( json ) {
			$(\'.alert, .text-danger\').remove();
			$(\'.form-group\').removeClass(\'has-error\');
			$(\'.success, .warning, .attention, information, .error\').remove();
			if (json[\'error\']) {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
			}
			if ( json[\'success\'] ) {
				'; if ($revtheme_header_cart['cart_vspl'] == '1') { $js[] = '
					if (document.body.scrollHeight > document.body.offsetHeight) {
						$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
						if ($(window).width() < 768) {
							$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
						}
					}
					$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = \'mfp-zoom-in\';
							},
							close: function() {
								$(\'body\').removeClass(\'razmiv2\');
								$(\'#pagefader2\').fadeOut(70);
								$(\'#top3.absolutpo\').css(\'right\', \'initial\');
								if ($(window).width() < 768) {
									$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
								}
							}
						},
						tLoading: \'\',
						items: {
							src: \'index.php?route=revolution/revpopupcart\',
							type: \'ajax\'
						}
					});
				'; } else if ($revtheme_header_cart['cart_vspl'] == '2') { $js[] = '
						location = json[\'redirect_cart\'];
				'; } else { $js[] = '
					'; if ($revtheme_all_settings['mobile_header']) { $js[] = '
						if ($(window).width() < 768) {
							header_cart_element = "#top #cart_mobi";
						} else {
							'; if ($revtheme_header_cart['cart_position']) { $js[] = '
								header_cart_element = "#top2 #cart";
							'; } else { $js[] = '
								header_cart_element = "#top3 #cart";
							'; } $js[] = '
						}
					'; } else { $js[] = '
						'; if ($revtheme_header_cart['cart_position']) { $js[] = '
							header_cart_element = "#top2 #cart";
						'; } else { $js[] = '
							header_cart_element = "#top3 #cart";
						'; } $js[] = '
					'; } $js[] = '
					tmp_img = $(\'.main-image img\')
					$(tmp_img)
						.clone()
						.css({\'position\' : \'absolute\', \'z-index\' : \'999\', top: $(\'.main-image img\').offset().top, left: $(\'.main-image img\').offset().left})
						.appendTo("body")
						.animate({opacity: 0.3,
							left: $(header_cart_element).offset()[\'left\'],
							top: $(header_cart_element).offset()[\'top\']+15,
							width: 10}, 800, function() {
							$(this).remove();
						});
				'; } $js[] = '
				$(\'#top #cart-total_mobi\').html(json[\'total\']);
				$(\'#top3 #cart-total\').html(json[\'total\']);
				$(\'#top2 #cart-total\').html(json[\'total\']);
				$(\'#top3 #cart-total-popup\').html(json[\'total\']);
				$(\'#cart > ul\').load(\'index.php?route=common/cart/info ul li\');
				'; if ($setting_catalog_all['product_in_cart']) { $js[] = '
					setTimeout(function() {
						$(\'.product-thumb.product_\'+ product_id +\' .image .pr_in_cart_i\').remove();
						$(\'.product-thumb.product_\'+ product_id +\' .image\').append(\'<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>\');
					}, 300);
				'; } $js[] = '
			}
		}
    });
}
function get_revpopup_login() {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$(\'.tooltip\').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = \'mfp-zoom-in\';
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			}, 
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
			}
		},
		tLoading: \'\',
		items: {
			src: \'index.php?route=revolution/revpopuplogin\',
			type: \'ajax\'
		}
	});
}
$(document).on(\'click\', \'.tel .dropdown-menu\', function (e) {
	$(this).hasClass(\'dropdown-menu-right\') && e.stopPropagation();
});

'; if ($revtheme_all_settings['modal_status']) { $js[] = '
	function getModalButtons() {
		'; if ($revtheme_all_settings['modal_buttons']) { $js[] = '
			return modal = true;
		'; } else { $js[] = '
			return modal = false;
		'; } $js[] = '
	}
	function getCookie(name) {
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, \'\\$1\') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
	}
	if (!getCookie(\'revmodal\')) {
		$(document).ready(setTimeout(function() {
			if (document.body.scrollHeight > document.body.offsetHeight) {
				$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
				}
			}
			$.magnificPopup.open({
				removalDelay: 170,
				modal: getModalButtons(),
				callbacks: {
					beforeOpen: function() {
						this.st.mainClass = \'mfp-zoom-in\';
						$(\'.dropdown-menu.dop_contss\').fadeOut(70);
					},
					open: function() {
						$(\'body\').addClass(\'razmiv2\');
						$(\'#pagefader2\').fadeIn(70);
					}, 
					close: function() {
						$(\'body\').removeClass(\'razmiv2\');
						$(\'#pagefader2\').fadeOut(70);
						$(\'#top3.absolutpo\').css(\'right\', \'initial\');
						if ($(window).width() < 768) {
							$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
						}
						$(\'.dropdown-menu.dop_contss\').css(\'display\', \'\');
					}
				},
				tLoading: \'\',
				items: {
					src: \'index.php?route=revolution/revmodal\',
					type: \'ajax\'
				}
			});
			$(document).on(\'click\', \'.popup-modal-dismiss\', function (e) {
				e.preventDefault();
				$.magnificPopup.close();
			});
		}, 1000));
	}
'; } $js[] = '
'; if ($revtheme_footer_all['f_map']) { $js[] = '
	'; if (!$revtheme_all_settings['yamap']) { $js[] = '
		var block_show = null;
		function scrollTracking(){
			var wt = $(window).scrollTop();
			var wh = $(window).height();
			var et = $(\'#map-wrapper\').offset().top;
			var eh = $(\'#map-wrapper\').outerHeight();
			if (wt + wh >= et && wt + wh - eh * 2 <= et + (wh - eh)){
				if (block_show == null || block_show == false) {
					$.getScript("//api-maps.yandex.ru/2.1/?lang=ru_RU").done(function() {
						ymaps.ready(init_map);
						function init_map(){
							var address = \''.$this->config->get('config_address').'\';
							var geocoder = ymaps.geocode(address);
							geocoder.then(
								function (res) {
									var coordinates = res.geoObjects.get(0).geometry.getCoordinates();
									var map = new ymaps.Map("yamap", {
										center: coordinates,
										zoom: 15,
										controls: [
											\'typeSelector\',
											\'fullscreenControl\'
										]
									});			
									map.geoObjects.add(new ymaps.Placemark(
										coordinates,
										{
											\'hintContent\': address,
											\'balloonContent\': \''.$this->config->get('config_name').'\'
										},
										{
											\'preset\': \'islands#redDotIcon\'
										}
									));
								}
							);
						}
					});
				}
				block_show = true;
			}
		}
		$(window).scroll(function(){
			scrollTracking();
		});
		$(document).ready(function(){
			scrollTracking();
			var height_f_conts = $(\'#map-wrapper .contact-info\').outerHeight();
			$(\'#map-wrapper #yamap\').css(\'height\', height_f_conts);
			$(\'#map-wrapper #yamap ymaps\').css(\'height\', height_f_conts);
			$(\'#map-wrapper .contact-info\').css(\'position\', \'absolute\');
		});
	'; } $js[] = '
'; } $js[] = '
function get_revpopup_predzakaz(product_id) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$(\'#top3.absolutpo\').css(\'right\', \'8.5px\');
		if ($(window).width() < 768) {
			$(\'#top #cart_mobi\').css(\'margin-right\', \'17px\');
		}
	}
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = \'mfp-zoom-in\';
			},
			open: function() {
				$(\'body\').addClass(\'razmiv2\');
				$(\'#pagefader2\').fadeIn(70);
			}, 
			close: function() {
				$(\'body\').removeClass(\'razmiv2\');
				$(\'#pagefader2\').fadeOut(70);
				$(\'#top3.absolutpo\').css(\'right\', \'initial\');
				if ($(window).width() < 768) {
					$(\'#top #cart_mobi\').css(\'margin-right\', \'initial\');
				}
			}
		},
		tLoading: \'\',
		items: {
			src: \'index.php?route=revolution/revpopuppredzakaz&revproduct_id=\'+product_id,
			type: \'ajax\'
		}
	});
}
'; /* if ($revtheme_filter['status']) { $js[] = '
	(function($){
		$.fn.revFilter = function(f) {
			var g = this.selector;
			var h = $(g).attr(\'action\');
			$(document).ready(function() {
				init_revfilter();
			});
			$(document).on(\'submit\', g, function(e) {
				e.preventDefault();
				var a = $(this).serialize();
				loadProds(h,a,f.revload);
			});
			$(document).on(\'click\', \'#\'+f.reset_id, function(e) {
				$(g+\' input, \'+g+\' select\').not(\'[type=hidden]\').each(function(a) {
					if ($(this).hasClass(\'irs-hidden-input\')) {
						var b = $(this).data(\'ionRangeSlider\');
						b.reset();
						}
					if ($(this).is(\':checkbox\') || $(this).is(\':radio\')) {
						$(this).removeAttr("checked");
					} else {
						$(this).val(\'\');
					}
				});
				var c = $(g).serialize();
				loadProds(h,c,f.revload);
			});
			if (f.mode == \'auto\') {
				$(document).on(\'change\', g+\' input:not([type=hidden]):not(.irs-hidden-input), \'+g+\' select\', function() {
					$(g).submit();
				})
			}
			function init_revfilter() {
				'; if ($setting_catalog_all['pagination'] == 'knopka') { $js[] = '
					$(\'.pagpages\').addClass(\'dnone\');
				'; } $js[] = '
				'; if ($setting_catalog_all['pagination'] == 'standart_knopka' || $setting_catalog_all['pagination'] == 'knopka') { $js[] = '
					var a = $(\'#load_more\').html();
					$(\'.pagination\').parent().parent().before(a);
				'; } $js[] = '
				$(\'#input-sort\').removeAttr(\'onchange\');
				$(\'#input-limit\').removeAttr(\'onchange\');
				$(f.selector).addClass(\'revcontainer\');
				if (localStorage.getItem(\'display\') == \'list\') {
					list_view();
				} else if (localStorage.getItem(\'display\') == \'price\') {
					price_view();
				} else if (localStorage.getItem(\'display\') == \'grid\') {
					grid_view();
				} else {
					'; if ($setting_catalog_all['vid_default'] == 'vid_price') { $js[] = '
						price_view();
					'; } else if ($setting_catalog_all['vid_default'] == 'vid_list') { $js[] = '
						list_view();
					'; } else if ($setting_catalog_all['vid_default'] == 'vid_grid') { $js[] = '
						grid_view();
					'; } $js[] = '
				}
				'; if ($setting_catalog_all['img_slider']) { $js[] = '
					$(\'#content .owlproduct\').owlCarousel( {
						beforeInit: true,
						items: 1,
						singleItem: true,
						mouseDrag: false,
						autoPlay: false,
						navigation: true,
						navigationText: [\'<i class="fa fa-chevron-left fa-3x"></i>\',\'<i class="fa fa-chevron-right fa-3x"></i>\'],
						pagination: false
					});
					'; if (!$setting_catalog_all['chislo_ryad']) { $js[] = '
						if (localStorage.getItem(\'display\')==\'grid\') {
							$(\'.product-thumb > .image\').css(\'width\',\'initial\');
						}
					'; } $js[] = '
					podgon_img();
				'; } else { $js[] = '
					$(\'.owl-carousel.owlproduct\').remove();
				'; } $js[] = '
				podgon_fona();
				$(\'#column-left #revfilter_box .mobil_wellsm .well.well-sm\').remove();
				if ($(window).width() < 991) {
					$(\'#column-left #revfilter_box .mobil_wellsm .collapsible\').append($(\'.revfilter_container > .well.well-sm\'));
				}
				'.(isset($revtheme_filter['scripts']) ? $revtheme_filter['scripts'] : "").'
			}
			function loadProds(c,d,e) {
				d = d || \'\';
				e = e || false;
				filterurl = c + \'&isrevfilter=1\';
				$.ajax({
					url: filterurl,
					type: \'get\',
					data: d,
					processData: false,
					dataType: e ? \'json\' : \'html\',
					beforeSend: function() {
						$(g+\' button\').button(\'loading\');
						masked(\'.products_category > .product-layout > .product-thumb\',true);
						$(\'.load_more .fa-refresh\').addClass(\'fa-spin\');
					},
					success: function(a) {
						var b = $.parseHTML((e && (typeof a.html != \'undefined\')) ? a.html : a);
						$(f.selector).children().remove();
						$(f.selector).append($(b).find(f.selector).children());
						init_revfilter();
					},
					complete: function() {
						setTimeout(function() {
							masked(\'.products_category > .product-layout > .product-thumb\',false);
							autoscroll_loading = false;
							$(g+\' button\').button(\'reset\');
							var pr_opts_cat = $(\'.products_category .options_buy\')
							pr_opts_cat.find(\'select:first\').each(function() {
								this.onchange();
							});
						},250);
						if (f.mode == \'manual\' && $(window).width() > 767) {
							element = $(\'.breadcrumb\');
							offset = element.offset();
							offsetTop = offset.top;
							//$(\'html, body\').animate({scrollTop:offsetTop}, 250, \'linear\');
						};
						$(\'.load_more .fa-refresh\').removeClass(\'fa-spin\').css(\'hover\');
						'; if ($setting_catalog_all['pagination'] == 'auto') { $js[] = '
							$(\'.pagpages .pagination\').hide();
						'; } $js[] = '
						'; if (isset($revtheme_filter['filter_brstroka']) && $revtheme_filter['filter_brstroka']) { $js[] = '
							var urlfull = c + (d ? ((c.indexOf(\'?\') > 0 ? \'&\' : \'?\') + d) : \'\');
							urlfull = decodeURIComponent(urlfull);
							history.pushState(\'\', \'\', urlfull);
						'; } $js[] = '
					}
				})
			}
			$(document).on(\'click\', \'.pagination a\', function(e) {
				loadProds($(this).attr(\'href\'), null, true);
				element = $(\'.breadcrumb\');
				offset = element.offset();
				offsetTop = offset.top;
				$(\'html, body\').animate({scrollTop:offsetTop}, 250, \'linear\');
				return false;
			});
			$(document).on(\'change\', \'#input-sort\', function(e) {
				var a = $(this).val();
				sort = a.match(\'sort=([A-Za-z.]+)\');
				$(\'input[name="sort"]\').val(sort[1]);
				order = a.match(\'order=([A-Z]+)\');
				$(\'input[name="order"]\').val(order[1]);
				$(g).submit();
			});
			$(document).on(\'change\', \'#input-limit\', function(e) {
				var a = $(this).val();
				if (a) {
					limit = a.match(\'limit=([0-9]+)\');
					$(\'input[name="limit"]\').val(limit[1]);
				}
				$(g).submit();
			});
			'; if ($setting_catalog_all['pagination'] == 'standart_knopka' || $setting_catalog_all['pagination'] == 'knopka') { $js[] = '
				var i = $(\'#input-limit\').val();
				if (i) {
					limit = i.match(\'limit=([0-9]+)\');
					$i = limit[1];
				}
				$(document).on(\'click\', \'.load_more\', function(e) {
					e.preventDefault();
					var a = $(\'#input-limit\').val();
					if (a) {
						limit = a.match(\'limit=([0-9]+)\');
					}
					limit3 = $(\'#revfilter input[name="limit"]\').val();
					if (limit3) {
						limit21 = limit3;
					} else {
						limit21 = limit[1];
						$(\'#revfilter input[name="limit"]\').val(limit21);
					}
					limit2 = Number(limit21)+Number($i);
					limitnumber = \'limit=\'+limit21;
					a = a.replace(\'limit=\'+$i,\'\');
					a = a.replace(limitnumber,\'\');
					var b = a+\'limit=\'+limit2;
					$(\'#revfilter input[name="limit"]\').val(limit2);
					$(g).submit();
				});
			'; } $js[] = '
			'; if ($setting_catalog_all['pagination'] == 'auto') { $js[] = '
				var i = $(\'#input-limit\').val();
				limit = i.match(\'limit=([0-9]+)\');
				$i = limit[1];
				autoscroll_loading = false;
				$(\'.pagpages .pagination\').hide();
				$(window).scroll(function() {
					if (inZone(\'.pagpages\') && !autoscroll_loading) {
						autoscroll_loading = true;
						var c = $(".pagpages .pagination li.active").next("li").children("a");
						if (c.length==0) return;
						setTimeout(function() {
							var a = $(\'#input-limit\').val();
							limit = a.match(\'limit=([0-9]+)\');
							limit3 = $(\'#revfilter input[name="limit"]\').val();
							if (limit3) {
								limit21 = limit3;
							} else {
								limit21 = limit[1];
								$(\'#revfilter input[name="limit"]\').val(limit21);
							}
							limit2 = Number(limit21)+Number($i);
							limitnumber = \'limit=\'+limit21;
							a = a.replace(\'limit=\'+$i,\'\');
							a = a.replace(limitnumber,\'\');
							var b = a+\'limit=\'+limit2;
							$(\'#revfilter input[name="limit"]\').val(limit2);
							$(g).submit();
						}, 250);
					}
				});
			'; } $js[] = '
			function inZone(a) {
				if ($(a).length) {
				var b = $(window).scrollTop();
				var c = $(window).height();
				var d = $(a).offset();
				if (b<=d.top&&($(a).height()+d.top)<(b+c)) return true
				};
				return false;
			}
			$(document).on(\'click\',\'#list-view\',function() {
				list_view();
			});
			$(document).on(\'click\', \'#grid-view\', function() {
				grid_view();
			});
			$(document).on(\'click\', \'#price-view\', function() {
				price_view();
			});
		}
	})(jQuery);
'; } $js[] = '
'; if ($revtheme_all_settings['cookies']) { $js[] = '
	var Cookie = {
		set: function(name, value, days) {
			var domain, domainParts, date, expires, host;
			if (days) {
				date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				expires = "; expires="+date.toGMTString();
			} else {
				expires = "";
			}
			host = location.host;
			if (host.split(".").length === 1) {
				document.cookie = name+"="+value+expires+"; path=/";
			} else {
				domainParts = host.split(".");
				domainParts.shift();
				domain = "."+domainParts.join(".");
				document.cookie = name+"="+value+expires+"; path=/";
				if (Cookie.get(name) == null || Cookie.get(name) != value) {
					domain = "."+host;
					document.cookie = name+"="+value+expires+"; path=/";
				}
			}
			return domain;
		},
		get: function(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(";");
			for (var i=0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0)==" ") {
					c = c.substring(1,c.length);
				}
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		}
	};
	if(!Cookie.get(\'revcookie\')) {
		setTimeout("document.querySelector(\'.bottom_cookie_block\').style.display=\'block\'", 500);
	}
	$(\'.bottom_cookie_block_ok\').click(function(){
		$(\'.bottom_cookie_block\').fadeOut();
		Cookie.set(\'revcookie\', true, 120);
	});
'; } */ $js[] = '
';
$tjs = implode("\r\n", $js);