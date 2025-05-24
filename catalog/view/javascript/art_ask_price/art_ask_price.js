$(document).on('click','#art_ask_price_submit',function (argument) {
	$.ajax({
		url: 'index.php?route=extension/module/art_ask_price',
		type: 'post',
		dataType: 'json',
		data:$('#art_ask_price_form').serialize(),
		success: function(data) {	
			if (data['error']) {
				if (data['error']['name']) {
					$('.art_ask_price_error_name').html('<span class="text-danger">'+data['error']['name']+'</span>').show();
				} else {
               		$('.art_ask_price_error_name').hide().empty();
                }
				if (data['error']['email']) {
					$('.art_ask_price_error_email').html('<span class="text-danger">'+data['error']['email']+'</span>').show();
				} else {
               		$('.art_ask_price_error_email').hide().empty();
                }
				if (data['error']['comment']) {
					$('.art_ask_price_error_comment').html('<span class="text-danger">'+data['error']['comment']+'</span>').show();
				} else {
               		$('.art_ask_price_error_comment').hide().empty();
                }
				if (data['error']['phone']) {
					$('.art_ask_price_error_phone').html('<span class="text-danger">'+data['error']['phone']+'</span>').show();
				} else {
               		$('.art_ask_price_error_phone').hide().empty();
                }
            }
            if (data['success']) {
				$.ajax({
					url: 'index.php?route=extension/module/art_ask_price/success',
					type: 'post',
					success: function(data) {
						$('#art_ask_price').html(data);
					}
				});
			}
		}
	});
})

$(document).on('click','.ask_price',function (argument) {	
	$.ajax({
		url: 'index.php?route=extension/module/art_ask_price/getForm&product_id='+$(this).attr('data-id'),
		type: 'post',
		success: function(data) {
			$('#art_ask_price').remove();
			$('body').prepend(data);
			$('#art_ask_price').modal('show');
		}
	});
});



