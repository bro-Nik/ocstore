// mobile-menu
document.addEventListener('DOMContentLoaded', function() {
    var menu = new MmenuLight(document.querySelector("#mobil_mmenu"), "all");

    var navigator = menu.navigation({
        // selectedClass: 'Selected',
        // slidingSubmenus: true,
        title: 'Меню'
    });

    var drawer = menu.offcanvas({
        // position: 'left'
    });

    // Open the menu.
    var menuButton = document.querySelector('a[href="#mobil_mmenu"]');
    if (menuButton) {
        menuButton.addEventListener("click", function(evnt) {
            evnt.preventDefault();
            drawer.open();
        });
    }
});

if (!localStorage.getItem('display')) {
	localStorage.setItem('display', 'grid');
}

	$('#menu2_button').click(function(){$('#menu2').toggleClass('dblock');});

	$('.footer-category').append($('.category_description'));
	$('.category_description').removeClass('dnone');

	$('.owl-carousel.owlproduct').remove();

function list_view(){
	$('#content .products_category .product-grid > .clearfix').remove();
	$('#content .products_category .product-grid, #content .products_category .product-price').attr('class', 'product-layout product-list col-xs-12');
	$('#content .product-list .cart > a').attr('data-toggle', 'none');
	$('#content .product-list .cart > a').attr('title', '');
	$(document).ready(function() {
		var w_list_img = $('.product-list .product-thumb .image').outerWidth();

	});
	$('.product-list .product-thumb h4').css('height', 'initial');
	$('.product-list .product-thumb .product_buttons').css('height', 'initial');
	$('.product-list .product-thumb .caption').css('margin-left', 'px');
	$('.product-list .product-thumb .description_options').addClass('view_list_options');
	$('.product-list .product-thumb .description_options').css('height', 'initial');
	$('.product-layout.product-list').css('height', 'initial');
	$('#grid-view, #price-view').removeClass('active');
	$('#list-view').addClass('active');
	localStorage.setItem('display', 'list');
}
function grid_view(){
	cols = $('#column-right, #column-left').length;
	if (cols == 2) {
		$('#content .product-list, #content .product-price').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
	} else if (cols == 1) {
		$('#content .product-list, #content .product-price').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
	} else {
		$('#content .product-list, #content .product-price').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
	}
	

		if ($(window).width() > 294 && $(window).width() < 975) {
			$('#content .product-layout.product-grid').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6');
		}
	

	$('.product-grid .product-thumb .caption').css('margin-left', 'initial');
	$('.product-grid .product-thumb .description_options').removeClass('view_list_options');
	var product_grid_width = $('.product-layout .product-thumb').outerWidth();
	var product_item_width = $('.rev_slider .item .product-thumb').outerWidth();
	if (product_grid_width < 240) {
		$('.product-layout').addClass('new_line');
		$('.rev_slider .item').addClass('new_line');
	} else {
		$('.product-layout').removeClass('new_line');
		$('.rev_slider .item').removeClass('new_line');
	}
	if (product_item_width < 240) {
		$('.rev_slider .item').addClass('new_line');
	} else {
		$('.rev_slider .item').removeClass('new_line');
	}
	

	max_height_div('.product-grid .product-thumb h4');
	max_height_div('.product-grid .product-thumb .price');
	max_height_div('.product-grid .product-thumb .product_buttons');
	

	setTimeout(function() {
		max_height_div('.product-grid .product-thumb .description_options');
	}, 300);
	

	$('#list-view, #price-view').removeClass('active');
	$('#grid-view').addClass('active');
	localStorage.setItem('display', 'grid');
}
function price_view(){
	$('#content .products_category .product-grid > .clearfix').remove();
	$('#content .products_category .product-list, #content .products_category .product-grid').attr('class', 'product-layout product-price col-xs-12');
	$('#content .product-view .cart > a').attr('data-toggle', 'none');
	$('#content .product-view .cart > a').attr('title', '');
	$('.product-price .product-thumb h4').css('height', 'initial');
	$('.product-price .product-thumb .caption').css('margin-left', 'initial');
	$('.product-price .product-thumb .product_buttons').css('height', 'initial');
	$('.product-price .product-thumb .description_options').removeClass('view_list_options');
	$('.product-price .product-thumb .description_options').css('height', 'initial');
	$('.product-layout.product-price').css('height', 'initial');
	$('#list-view, #grid-view').removeClass('active');
	$('#price-view').addClass('active');
	localStorage.setItem('display', 'price');
}
$('#list-view').click(function() {
	list_view();
});
$('#grid-view').click(function() {
	grid_view();
});
$('#price-view').click(function() {
	price_view();
});


	$('html').removeClass('opacity_minus').addClass('opacity_plus');

	$(document).ready(function(){
	var triggered = false;
	$(".triggerbtn").click(function(){
		if(triggered == false){
			$(this).toggleClass('animbuttonsh');
			$(this).html('<i class=\"fa fa-close\" aria-hidden=\"true\"></i>');
			var id = 6;
			

			triggered = true;
		} else {
			$(this).toggleClass('animbuttonsh');
			$(this).html('<i class=\"fa fa-comments-o fa-lg\" aria-hidden=\"true\"></i>');
			$('.share_icon_callbtn').animate({bottom: '2em'}, 150);
			triggered = false;
		}
	})
})

	NProgress.start();
	$(window).load(function() {
		NProgress.done();
		$('html').removeClass('opacity_plus');
	});




	if($(window).width() > 768) {
		$('#top3').affix({
			offset: {
				

					top: $('#top').outerHeight()+$('#top2').outerHeight()+$('html.common-home #menu2.inhome').outerHeight()
				

			}
		});
	}
	

	var win_shopcart = $(window).height();
	var win_shopcart2 = $('#top').outerHeight()+$('#top2').outerHeight()+$('#top3').outerHeight();
	//$('#cart .dropdown-menu > li').css('max-height', win_shopcart-win_shopcart2).css('overflow-y', 'auto');
	$('#top3 #menu2 .child-box').css('max-height', win_shopcart-win_shopcart2).css('overflow-y', 'auto');


$(function() {
	if (localStorage.getItem('display') == 'list') {
		list_view();
	} else if (localStorage.getItem('display') == 'price') {
		price_view();
	} else if (localStorage.getItem('display') == 'grid') {
		grid_view();
	} else {
		

			grid_view();
		

	}
	

	podgon_fona();
	$(window).resize(podgon_fona);
});
function podgon_fona() {
	toggle_ellipses();
	var h_top5 = $('.inhome #menu2').outerHeight();
	if (h_top5) {
		$('#top5').css('min-height', h_top5+20);
	}
	

	var m2inh = $('html.common-home #menu2.inhome').outerHeight();
	$('html.common-home #menu2.inhome .podmenu2').css('height', m2inh);
	var m2inhw = $('html.common-home #menu2_button').outerWidth();
	$('html.common-home #menu2.inhome .podmenu2').css('min-width', m2inhw-0.5);

		var h_top3 = $('#top3').outerHeight();

		$('.main-content').css('padding-top', h_top3+25);

		$('#top3').addClass('absolutpo');

		if($(window).width() < 767) {
			grid_view();
		}

		if ($(window).width() > 294 && $(window).width() < 975) {
			$('#content .product-layout.product-grid').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-4 col-xs-6');
		}

	var product_grid_width = $('.product-layout .product-thumb').outerWidth();
	var product_item_width = $('.rev_slider .item .product-thumb').outerWidth();
	if (product_grid_width < 240) {
		$('.product-layout').addClass('new_line');
		$('.rev_slider .item').addClass('new_line');
	} else {
		$('.product-layout').removeClass('new_line');
		$('.rev_slider .item').removeClass('new_line');
	}
	if (product_item_width < 240) {
		$('.rev_slider .item').addClass('new_line');
	} else {
		$('.rev_slider .item').removeClass('new_line');
	}
	max_height_div('.product-grid .product-thumb h4');
	max_height_div('.product-grid .product-thumb .price');
	max_height_div('.product-grid .product-thumb .product_buttons');
	

	setTimeout(function() {
		max_height_div('.product-grid .product-thumb .description_options');
	}, 300);
	

	max_height_div('#content .refine_categories.clearfix a > span');
}
function toggle_ellipses() {
  var ellipses1 = $('.br_ellipses');
  var howlong = $('.breadcrumb li:hidden').length;
  if ($('.breadcrumb li:hidden').length > 1) {
    ellipses1.show().css('display', 'inline');
  } else {
    ellipses1.hide();
  }
}

$(document).on('scroll', function() {
	if ($(window).scrollTop() > 100) {
		$('.scroll-top-wrapper').addClass('show');
	} else {
		$('.scroll-top-wrapper').removeClass('show');
	}
});

$('.scroll-top-wrapper').on('click', scrollToTop);
$('.popup-phone-wrapper').on('click', get_revpopup_phone);
function scrollToTop() {
	verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
	element = $('body');
	offset = element.offset();
	offsetTop = offset.top;
	$('html, body').animate({scrollTop: offsetTop}, 200, 'linear');
};
function get_revpopup_notification(m_class, m_header, message) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$('.tooltip').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = 'mfp-zoom-in';
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			}, 
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
			}
		},
		tLoading: '',
		items: {
			src: $('<div class="popup_notification"><div class="popup_notification_heading '+m_class+'">'+m_header+'</div><div class="popup_notification_message">'+message+'</div></div>'),
			type: 'inline'
		}
	});
}
function get_revpopup_phone() {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$('.tooltip').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
				this.st.mainClass = 'mfp-zoom-in';
				$('.dropdown-menu.dop_contss').fadeOut(70);
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			}, 
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
				$('.dropdown-menu.dop_contss').css('display', '');
			}
		},
		tLoading: '',
		items: {
			src: 'index.php?route=revolution/revpopupphone',
			type: 'ajax'
		}
	});
}
function get_revpopup_view(product_id) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$('.tooltip').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = 'mfp-zoom-in';
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			},
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
			}
		},
		tLoading: '',
		items: {
			src: 'index.php?route=revolution/revpopupview&revproduct_id='+product_id,
			type: 'ajax'
		}
	});
}
function get_revpopup_purchase(product_id) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$('.tooltip').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = 'mfp-zoom-in';
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			}, 
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
			}
		},
		tLoading: '',
		items: {
			src: 'index.php?route=revolution/revpopuporder&revproduct_id='+product_id,
			type: 'ajax'
		}
	});
}
function get_revpopup_cartquick() {
	$('#cart .dropdown-menu').css('display', 'none');
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$('.tooltip').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = 'mfp-zoom-in';
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			}, 
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
				$('#cart .dropdown-menu').css('display', '');
			}
		},
		tLoading: '',
		items: {
			src: 'index.php?route=revolution/revpopupcartquick',
			type: 'ajax'
		}
	});
}
function get_revpopup_cart( product_id, action, quantity, block_id ) {
	$('.tooltip').hide();
	quantity = typeof(quantity) != 'undefined' ? quantity : 1;
	if ( action == "catalog" ) {
		data = $('.products_category .product_'+product_id+' .options input[type=\"text\"], .products_category .product_'+product_id+' .options input[type=\"hidden\"], .products_category .product_'+product_id+' .options input[type=\"radio\"]:checked, .products_category .product_'+product_id+' .options input[type=\"checkbox\"]:checked, .products_category .product_'+product_id+' .options select');
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			success: function( json ) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');
				$('.success, .warning, .attention, information, .error').remove();
				

				if ( json['redirect'] ) {
					location = json['redirect'];
				}
				

				if ( json['success'] ) {
					

						if (document.body.scrollHeight > document.body.offsetHeight) {
							$('#top3.absolutpo').css('right', '8.5px');
							if ($(window).width() < 768) {
								$('#top #cart_mobi').css('margin-right', '17px');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = 'mfp-zoom-in';
							},
							close: function() {
								$('body').removeClass('razmiv2');
								$('#pagefader2').fadeOut(70);
								$('#top3.absolutpo').css('right', 'initial');
								if ($(window).width() < 768) {
									$('#top #cart_mobi').css('margin-right', 'initial');
								}
							}
						},
						tLoading: '',
						items: {
							src: 'index.php?route=revolution/revpopupcart',
							type: 'ajax'
						}
						});
					

					$('#top #cart-total_mobi').html(json['total']);
					$('#top3 #cart-total').html(json['total']);
					$('#top2 #cart-total').html(json['total']);
					$('#top3 #cart-total-popup').html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					

						setTimeout(function() {
							$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
							$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
						}, 300);
					

				}
			}
		});
	}
	if ( action == "catalog_mod" ) {
		data = $('.products_category .product_'+product_id+' .options input[type=\"text\"], .products_category .product_'+product_id+' .options input[type=\"hidden\"], .products_category .product_'+product_id+' .options input[type=\"radio\"]:checked, .products_category .product_'+product_id+' .options input[type=\"checkbox\"]:checked, .products_category .product_'+product_id+' .options select');
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			success: function( json ) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');
				$('.success, .warning, .attention, information, .error').remove();

				if ( json['redirect'] ) {
					location = json['redirect'];
				}
				
				if ( json['success'] ) {
					

						if (document.body.scrollHeight > document.body.offsetHeight) {
							$('#top3.absolutpo').css('right', '8.5px');
							if ($(window).width() < 768) {
								$('#top #cart_mobi').css('margin-right', '17px');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = 'mfp-zoom-in';
							},
							close: function() {
								$('body').removeClass('razmiv2');
								$('#pagefader2').fadeOut(70);
								$('#top3.absolutpo').css('right', 'initial');
								if ($(window).width() < 768) {
									$('#top #cart_mobi').css('margin-right', 'initial');
								}
							}
						},
						tLoading: '',
						items: {
							src: 'index.php?route=revolution/revpopupcart',
							type: 'ajax'
						}
						});
					

					$('#top #cart-total_mobi').html(json['total']);
					$('#top3 #cart-total').html(json['total']);
					$('#top2 #cart-total').html(json['total']);
					$('#top3 #cart-total-popup').html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					

						setTimeout(function() {
							$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
							$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
						}, 300);
					

				}
			}
		});
	}
	if ( action == "product" ) {
		data = $('.product_informationss .product-info input[type=\"text\"], .product_informationss .product-info input[type=\"hidden\"], .product_informationss .product-info input[type=\"radio\"]:checked, .product_informationss .product-info input[type=\"checkbox\"]:checked, .product_informationss .product-info select, .product_informationss .product-info textarea'),
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			

			beforeSend: function(){
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			},
			

			success: function( json ) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');
				$('.success, .warning, .attention, information, .error').remove();
				if (json['error']) {
					$('body').removeClass('razmiv2');
					$('#pagefader2').fadeOut(70);
					$('#top3.absolutpo').css('right', 'initial');
					if ($(window).width() < 768) {
						$('#top #cart_mobi').css('margin-right', 'initial');
					}
					if (json['error']['option']) {
						for (i in json['error']['option']) {
							$('#input-option' + i).before('<span class="error bg-danger">' + json['error']['option'][i] + '</span>');
							if ($(window).width() < 768) {
								$('html, body').animate({scrollTop:$('.error').offset().top - 40}, 'linear');
							}
						}
					}
				}
				if ( json['success'] ) {
					

						if (document.body.scrollHeight > document.body.offsetHeight) {
							$('#top3.absolutpo').css('right', '8.5px');
							if ($(window).width() < 768) {
								$('#top #cart_mobi').css('margin-right', '17px');
							}
						}
						$.magnificPopup.open({
							removalDelay: 170,
							callbacks: {
								beforeOpen: function() {
								   this.st.mainClass = 'mfp-zoom-in';
								},
								close: function() {
									$('body').removeClass('razmiv2');
									$('#pagefader2').fadeOut(70);
									$('#top3.absolutpo').css('right', 'initial');
									if ($(window).width() < 768) {
										$('#top #cart_mobi').css('margin-right', 'initial');
									}
								}
							},
							tLoading: '',
							items: {
								src: 'index.php?route=revolution/revpopupcart',
								type: 'ajax'
							}
						});
					

					$('#top #cart-total_mobi').html(json['total']);
					$('#top3 #cart-total').html(json['total']);
					$('#top2 #cart-total').html(json['total']);
					$('#top3 #cart-total-popup').html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					

						setTimeout(function() {
							$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
							$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
						}, 300);
					

				}
			}
		});
	}
	if ( action == "popup_product" ) {
		data = $('#popup-view-wrapper .product-info input[type=\"text\"], #popup-view-wrapper .product-info input[type=\"hidden\"], #popup-view-wrapper .product-info input[type=\"radio\"]:checked, #popup-view-wrapper .product-info input[type=\"checkbox\"]:checked, #popup-view-wrapper .product-info select, #popup-view-wrapper .product-info textarea');
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			

			beforeSend: function(){
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			},
			

			success: function( json ) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');
				$('.success, .warning, .attention, information, .error').remove();			
				if (json['error']) {
					$('body').removeClass('razmiv2');
					$('#pagefader2').fadeOut(70);
					$('#top3.absolutpo').css('right', 'initial');
					if ($(window).width() < 768) {
						$('#top #cart_mobi').css('margin-right', 'initial');
					}
					if (json['error']['option']) {
						if ($(window).width() < 768) {
							if (json['redirect']) {
								location = json['redirect'];
							}
						} else {
							for (i in json['error']['option']) {
								$('#input-option' + i).before('<span class="error bg-danger">' + json['error']['option'][i] + '</span>');
							}
						}
					}
				}
				if ( json['success'] ) {
					

						if (document.body.scrollHeight > document.body.offsetHeight) {
							$('#top3.absolutpo').css('right', '8.5px');
							if ($(window).width() < 768) {
								$('#top #cart_mobi').css('margin-right', '17px');
							}
						}
						$.magnificPopup.open({
							removalDelay: 170,
							callbacks: {
								beforeOpen: function() {
								   this.st.mainClass = 'mfp-zoom-in';
								},
								close: function() {
									$('body').removeClass('razmiv2');
									$('#pagefader2').fadeOut(70);
									$('#top3.absolutpo').css('right', 'initial');
									if ($(window).width() < 768) {
										$('#top #cart_mobi').css('margin-right', 'initial');
									}
								}
							},
							tLoading: '',
							items: {
								src: 'index.php?route=revolution/revpopupcart',
								type: 'ajax'
							}
						});
					
	
					$('#top #cart-total_mobi').html(json['total']);
					$('#top3 #cart-total').html(json['total']);
					$('#top2 #cart-total').html(json['total']);
					$('#top3 #cart-total-popup').html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					

						setTimeout(function() {
							$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
							$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
						}, 300);
					

				}
			}
		});
	}
	if ( action == "show_cart" ) {
		if (document.body.scrollHeight > document.body.offsetHeight) {
			$('#top3.absolutpo').css('right', '8.5px');
			if ($(window).width() < 768) {
				$('#top #cart_mobi').css('margin-right', '17px');
			}
		}
		$.magnificPopup.open({
			removalDelay: 170,
			callbacks: {
				beforeOpen: function() {
				   this.st.mainClass = 'mfp-zoom-in';
				},
				open: function() {
					$('body').addClass('razmiv2');
					$('#pagefader2').fadeIn(70);
				}, 
				close: function() {
					$('body').removeClass('razmiv2');
					$('#pagefader2').fadeOut(70);
					$('#top3.absolutpo').css('right', 'initial');
					if ($(window).width() < 768) {
						$('#top #cart_mobi').css('margin-right', 'initial');
					}
				}
			},
			tLoading: '',
			items: {
				src: 'index.php?route=revolution/revpopupcart',
				type: 'ajax'
			}
		});
	}
	if ( action == "redirect_cart" ) {
		  window.location.href = "/index.php?route=checkout/checkout"
	}
	if ( action == "module" ) {
		quantity = typeof(quantity) != 'undefined' ? quantity : 1;
		data = $('#'+block_id+' .product_'+product_id+' .options input[type=\"text\"], #'+block_id+' .product_'+product_id+' .options input[type=\"hidden\"], #'+block_id+' .product_'+product_id+' .options input[type=\"radio\"]:checked, #'+block_id+' .product_'+product_id+' .options input[type=\"checkbox\"]:checked, #'+block_id+' .product_'+product_id+' .options select');
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			success: function( json ) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');
				$('.success, .warning, .attention, information, .error').remove();
				

				if ( json['redirect'] ) {
					location = json['redirect'];
				}
				

				if ( json['success'] ) {
					

						if (document.body.scrollHeight > document.body.offsetHeight) {
							$('#top3.absolutpo').css('right', '8.5px');
							if ($(window).width() < 768) {
								$('#top #cart_mobi').css('margin-right', '17px');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = 'mfp-zoom-in';
							},
							close: function() {
								$('body').removeClass('razmiv2');
								$('#pagefader2').fadeOut(70);
								$('#top3.absolutpo').css('right', 'initial');
								if ($(window).width() < 768) {
									$('#top #cart_mobi').css('margin-right', 'initial');
								}
							}
						},
						tLoading: '',
						items: {
							src: 'index.php?route=revolution/revpopupcart',
							type: 'ajax'
						}
						});
					

					$('#top #cart-total_mobi').html(json['total']);
					$('#top3 #cart-total').html(json['total']);
					$('#top2 #cart-total').html(json['total']);
					$('#top3 #cart-total-popup').html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					

						setTimeout(function() {
							$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
							$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
						}, 300);
					

				}
			}
		});
	}
	if ( action == "module_in_product" ) {
		quantity = typeof(quantity) != 'undefined' ? quantity : 1;
		data = $('#'+block_id+' .product_'+product_id+' .options input[type=\"text\"], #'+block_id+' .product_'+product_id+' .options input[type=\"hidden\"], #'+block_id+' .product_'+product_id+' .options input[type=\"radio\"]:checked, #'+block_id+' .product_'+product_id+' .options input[type=\"checkbox\"]:checked, #'+block_id+' .product_'+product_id+' .options select');
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			

			beforeSend: function(){
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			},
			

			success: function( json ) {
				$('.alert, .text-danger').remove();
				$('.form-group').removeClass('has-error');
				$('.success, .warning, .attention, information, .error').remove();
				

				if ( json['redirect'] ) {
					location = json['redirect'];
				}
				

				if ( json['success'] ) {
					

						if (document.body.scrollHeight > document.body.offsetHeight) {
							$('#top3.absolutpo').css('right', '8.5px');
							if ($(window).width() < 768) {
								$('#top #cart_mobi').css('margin-right', '17px');
							}
						}
						$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = 'mfp-zoom-in';
							},
							close: function() {
								$('body').removeClass('razmiv2');
								$('#pagefader2').fadeOut(70);
								$('#top3.absolutpo').css('right', 'initial');
								if ($(window).width() < 768) {
									$('#top #cart_mobi').css('margin-right', 'initial');
								}
							}
						},
						tLoading: '',
						items: {
							src: 'index.php?route=revolution/revpopupcart',
							type: 'ajax'
						}
						});
					

					$('#top #cart-total_mobi').html(json['total']);
					$('#top3 #cart-total').html(json['total']);
					$('#top2 #cart-total').html(json['total']);
					$('#top3 #cart-total-popup').html(json['total']);
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					

						setTimeout(function() {
							$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
							$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
						}, 300);
					

				}
			}
		});
	}
}
function get_revpopup_cart_option (opt_id, option, quantity, product_id) {
	$('.tooltip').hide();
	$('.options_buy .pro_'+option+' input[name=\"option['+opt_id+']\"]').val(option);
	data = $('.product-info .options_buy .pro_'+option+' input[type=\"text\"], .product-info .options_buy .pro_'+option+' input[type=\"hidden\"], .product-info .options_buy .pro_'+option+' input[type=\"radio\"]:checked, .product-info .options_buy .pro_'+option+' input[type=\"checkbox\"]:checked, .product-info .options_buy .pro_'+option+' select, .product-info .options_buy .pro_'+option+' textarea');
    $.ajax({
        url: 'index.php?route=checkout/cart/add',
        type: 'post',
		data: data.serialize() + '&product_id=' + product_id + '&quantity=' + quantity,
        dataType: 'json',
		

			beforeSend: function(){
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			},
		

        success: function( json ) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			$('.success, .warning, .attention, information, .error').remove();
			if (json['error']) {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
			}
			if ( json['success'] ) {
				

					if (document.body.scrollHeight > document.body.offsetHeight) {
						$('#top3.absolutpo').css('right', '8.5px');
						if ($(window).width() < 768) {
							$('#top #cart_mobi').css('margin-right', '17px');
						}
					}
					$.magnificPopup.open({
						removalDelay: 170,
						callbacks: {
							beforeOpen: function() {
							   this.st.mainClass = 'mfp-zoom-in';
							},
							close: function() {
								$('body').removeClass('razmiv2');
								$('#pagefader2').fadeOut(70);
								$('#top3.absolutpo').css('right', 'initial');
								if ($(window).width() < 768) {
									$('#top #cart_mobi').css('margin-right', 'initial');
								}
							}
						},
						tLoading: '',
						items: {
							src: 'index.php?route=revolution/revpopupcart',
							type: 'ajax'
						}
					});
				

				$('#top #cart-total_mobi').html(json['total']);
				$('#top3 #cart-total').html(json['total']);
				$('#top2 #cart-total').html(json['total']);
				$('#top3 #cart-total-popup').html(json['total']);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
				

					setTimeout(function() {
						$('.product-thumb.product_'+ product_id +' .image .pr_in_cart_i').remove();
						$('.product-thumb.product_'+ product_id +' .image').append('<div class="pr_in_cart_i"><i class="fa fa-check"></i></div>');
					}, 300);
				

			}
		}
    });
}
function get_revpopup_login() {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$('.tooltip').hide();
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = 'mfp-zoom-in';
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			}, 
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
			}
		},
		tLoading: '',
		items: {
			src: 'index.php?route=revolution/revpopuplogin',
			type: 'ajax'
		}
	});
}
$(document).on('click', '.tel .dropdown-menu', function (e) {
	$(this).hasClass('dropdown-menu-right') && e.stopPropagation();
});





function get_revpopup_predzakaz(product_id) {
	if (document.body.scrollHeight > document.body.offsetHeight) {
		$('#top3.absolutpo').css('right', '8.5px');
		if ($(window).width() < 768) {
			$('#top #cart_mobi').css('margin-right', '17px');
		}
	}
	$.magnificPopup.open({
		removalDelay: 170,
		callbacks: {
			beforeOpen: function() {
			   this.st.mainClass = 'mfp-zoom-in';
			},
			open: function() {
				$('body').addClass('razmiv2');
				$('#pagefader2').fadeIn(70);
			}, 
			close: function() {
				$('body').removeClass('razmiv2');
				$('#pagefader2').fadeOut(70);
				$('#top3.absolutpo').css('right', 'initial');
				if ($(window).width() < 768) {
					$('#top #cart_mobi').css('margin-right', 'initial');
				}
			}
		},
		tLoading: '',
		items: {
			src: 'index.php?route=revolution/revpopuppredzakaz&revproduct_id='+product_id,
			type: 'ajax'
		}
	});
}


