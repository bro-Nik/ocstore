
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
