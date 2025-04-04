(function($){
	var $menu = $(),
		options = {
			popup: false,
			maximize: false
		},
		exitMenuDelay = 0,
		topDelayedChange = 0,
		itemMouseleaveDelay = 0,
		delayTime = 100,
		menuElDelayTime = 200,
		subMenuWidth = 190,
		maxMenuHeight = 0,
		scrollStep = 1000,
		scrollViewport = 0,
		scrollSpeed = 'fast',
		openCls = 'open',
		closeCls = 'closed',
		rowSelector = 'div.revlevel_1',
		childrenCls = '.childrenList';	
	function init (menu, params) {
		options = $.extend( {}, options, params );
		$menu = $(menu);
		menuNamespace = $menu.attr('id');
		var mm = $('#menu2').outerHeight();
		var ww = $('.box-content.am').outerWidth();
		$('#menu2 .podmenu2').css('height', mm);
		$('#menu2 .podmenu2').css('min-width', ww-0.5);	
		$menu.menuAim({
			rowSelector: rowSelector,
			activate: activateSubmenu,
			exitMenu: exitMenu
		});
	};
	function exitMenu() {
		$('#pagefader').fadeOut(70);
		$('body').removeClass('razmiv');
		clearTimeout(itemMouseleaveDelay);
		exitMenuDelay = setTimeout(function() {
			$('#' + menuNamespace + ' ' + childrenCls).hide();
			toggleClasses('#' + menuNamespace + ' .catalog_list .revlevel_1', openCls, closeCls);
			var mm = $('#menu2').outerHeight();
			$('#menu2 .podmenu2').css('height', mm);
			if (!options.popup) {
				hidePopupMenu();
			}
		}, delayTime);
		return true;
	}
	function enterMenu(row) {
			$('#pagefader').fadeIn(70);
			$('body').addClass('razmiv');
			var $row = $(row);
			if (exitMenuDelay > 0) {
				clearTimeout(exitMenuDelay);
			}
			clearTimeout(topDelayedChange);
			toggleClasses('#' + menuNamespace + ' .catalog_list .revlevel_1 div.open', openCls, closeCls);
			toggleClasses(row, closeCls, openCls);
			$row.find('> '+childrenCls).show();
			$('#' + menuNamespace + ' .catalog_list .revlevel_1.open').not($row).each(function(){	
				toggleClasses(this, openCls, closeCls);
			});
			var mm = $('#menu2').outerHeight();
			$('#menu2 .podmenu2').css('display', 'block');
			$('#menu2 .podmenu2').css('height', mm);
	}
	function activateSubmenu(row) {
		var $row = $(row),
		$submenu = $row.find(childrenCls + ':first');
		clearTimeout(exitMenuDelay);
		$submenu.show();
		enterMenu(row);
		toggleClasses(row, closeCls, openCls);
		var mm = $('#menu2').outerHeight();
		var ww = $('#menu2').outerWidth();
		$('#menu2 .child-box').css('min-height', mm);
		$('#menu2 .podmenu2').css('min-width', ww-0.5);
		var dd = $('#' + menuNamespace + ' .catalog_list .revlevel_1.open').children('.childrenList').outerHeight();			
		if (mm < dd) {
		$('#menu2 .podmenu2').css('display', 'block');
		$('#menu2 .podmenu2').css('height', dd);
		};
	}
	function deactivateSubmenu(row) {
		return true;
	}
	function toggleClasses(el, rmCls, addCls) {
		$(el).removeClass(rmCls).addClass(addCls);
	}
	$.fn.popupMenuWidget = function(options){
		init(this, options);
	};
})(jQuery);
$(document).ready(function(){
	var options = {};
			options = {
		popup: true, maximize: true
	};
	$("#menu2").popupMenuWidget(options);		   		
		
});