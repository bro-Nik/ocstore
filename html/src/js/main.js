// swiper
import { initProductSwipers, initHomeSwipers, initCarouselSwipers } from './swiper';
initProductSwipers();
initHomeSwipers();
initCarouselSwipers();

// mobile-menu
import { initMobilMenu } from './mmenu-light';
initMobilMenu();

import compare from './compare';
import wishlist from './wishlist';
import cart from './cart';
import events from './events/events';
import modals from './modals/init';
import prductServices from './product'
import review from './review';


document.addEventListener('DOMContentLoaded', () => {

  // Показываем первую вкладку
  const firstTab = document.querySelector('.nav.nav-tabs li:first-child a');
  if (firstTab) {
    firstTab.click();
  }

	// Записываем url
	const site_url = document.querySelector('input[name="site_url"]');
  if (site_url) {
		site_url.value = window.location.href;
  }

	showContent();
});
	
	
	
function showContent() {
  document.documentElement.classList.add('visible');
  
  // Удаляем стиль после анимации (опционально)
  setTimeout(() => {
    const styleEl = document.getElementById('preload-css');
    if (styleEl) styleEl.remove();
  }, 300);
}
// Fallback на случай проблем
setTimeout(showContent, 3000);

var h_top3 = $('#top3').outerHeight();
// $('.main-content').css('padding-top', h_top3+25);

if (!localStorage.getItem('display')) {
	localStorage.setItem('display', 'grid');
}

// Скрол вверх
document.addEventListener('scroll', function() {
  const scrollTopWrapper = document.querySelector('.btn-scroll-top');
  scrollTopWrapper.classList.toggle('show', window.scrollY > 100);
});

const scrollTopButton = document.querySelector('.btn-scroll-top');
if (scrollTopButton) {
  scrollTopButton.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}

// Функция форматирования числа
export function numberFormat(n) {
  return parseInt(Math.abs(n).toFixed(0)) + ''; 
}

// Функция форматирования цены
// function price_format(n) {
//   const c = {{ currency['decimals'] is empty ? "0" : currency['decimals'] }};
//   const d = '{{ currency['decimal_point'] }}';
//   const t = '{{ currency['thousand_point'] }}';
//   const s_left = '{{ currency['symbol_left'] }}';
//   const s_right = '{{ currency['symbol_right'] }}';
//   n = n * {{ currency['value'] }};
//   
//   let i = parseInt(n = Math.abs(n).toFixed(c)) + ''; 
//   let j = (i.length > 3) ? i.length % 3 : 0; 
//   
//   return s_left + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '') + s_right; 
// }

export function priceFormat(n) {
  const c = "0";
  const d = ' ';
  const t = '';
  const s = '₽';
  
  let i = parseInt(n = Math.abs(n).toFixed(c)) + ''; 
  let j = (i.length > 3) ? i.length % 3 : 0; 
  
  return (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '') + s; 
}


// Перенос описания категории
const footer = document.querySelector('.footer-category');
const desc = document.querySelector('.category_description');
footer?.appendChild(desc);

// $('.category_description').removeClass('dnone');

// $('.owl-carousel.owlproduct').remove();

// $(document).ready(function(){
// 	var triggered = false;
// 	$(".triggerbtn").click(function(){
// 		if(triggered == false){
// 			$(this).toggleClass('animbuttonsh');
// 			$(this).html('<i class=\"fa fa-close\" aria-hidden=\"true\"></i>');
// 			var id = 6;
// 			
//
// 			triggered = true;
// 		} else {
// 			$(this).toggleClass('animbuttonsh');
// 			$(this).html('<i class=\"fa fa-comments-o fa-lg\" aria-hidden=\"true\"></i>');
// 			$('.share_icon_callbtn').animate({bottom: '2em'}, 150);
// 			triggered = false;
// 		}
// 	})
// })

	// var win_shopcart = $(window).height();
	// var win_shopcart2 = $('#top').outerHeight()+$('#top2').outerHeight()+$('#top3').outerHeight();
	// //$('#cart .dropdown-menu > li').css('max-height', win_shopcart-win_shopcart2).css('overflow-y', 'auto');
	// $('#top3 #menu2 .child-box').css('max-height', win_shopcart-win_shopcart2).css('overflow-y', 'auto');

// function toggle_ellipses() {
//   var ellipses1 = $('.br_ellipses');
//   var howlong = $('.breadcrumb li:hidden').length;
//   if ($('.breadcrumb li:hidden').length > 1) {
//     ellipses1.show().css('display', 'inline');
//   } else {
//     ellipses1.hide();
//   }
// }



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







