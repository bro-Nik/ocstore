var kyr2en = {
  fromChars : 'абвгдезиклмнопрстуфыэйхё',
  toChars : 'abvgdeziklmnoprstufyejxe',
  biChars : {'ж':'zh','ц':'ts','ч':'ch','ш':'sh','щ':'sch','ю':'ju','я':'ja','&':'-and-'},
  vowelChars : 'аеёиоуыэюяєї',
  translit : function(str) {
    str = str.replace(/[_\s\.,?!\[\](){}\\\/"':;]+/g, '-')
             .toLowerCase()
             .replace(new RegExp('(ь|ъ)(['+this.vowelChars+'])', 'g'), 'j$2')
             .replace(/(ь|ъ)/g, '');

    var _str = '';
    for (var x=0; x<str.length; x++)
      if ((index = this.fromChars.indexOf(str.charAt(x))) > -1)
        _str += this.toChars.charAt(index);
      else
        _str += str.charAt(x);
    str = _str;

    var _str = '';
    for (var x=0; x<str.length; x++)
      if (this.biChars[str.charAt(x)])
        _str += this.biChars[str.charAt(x)];
      else
        _str += str.charAt(x);
    str = _str;

    str = str.replace(/j{2,}/g, 'j')
             .replace(/[^-0-9a-z]+/g, '')
             .replace(/-{2,}/g, '-')
             .replace(/^-+|-+$/g, '');

    return str;
  }
}

function setTranslit(elem, entity){
	var stores = [];
	$('[name*=store]').each(function(){
		stores.push($(this).val());
	});
	var language_id = elem.attr('name').replace(entity + '_description[','').replace('][name]','');
	var keyword_name;
	$.each(stores,function(index,store_id){
		keyword_name = $('[name="' + entity + '_seo_url[' + store_id + '][' + language_id + ']' + '"]');
		if (keyword_name.attr('data-orginal')) return;
		keyword_name.val(kyr2en.translit(elem.val()));
	});
}

$(document).ready(function() {
	var stores = [];
	$('[name*=store]').each(function() {
		stores.push($(this).val());
	});
	var languages = [];
	$('#language li a').each(function() {
		languages.push($(this).attr('href').replace('#language',''));
	});
	var entities =['product','category','information'];
	$.each(stores,function(store_index,store_id) {
		$.each(languages,function(language_index,language_id) {
			$.each(entities,function(entity_index,entity) {
				keyword_name = $('input[name="' + entity + '_seo_url[' + store_id + '][' + language_id + ']'  + '"]');
				keyword_name.attr('data-orginal',keyword_name.val());
				keyword_name.on('keydown', function() {
					$(this).attr('data-orginal',$(this).val());
				});
			});
		});
	});

	$.each(languages,function(language_index,language_id) {
		$.each(entities,function(entity_index,entity) {
			keyword_name = $('input[name="' + entity + '_description[' + language_id + '][name]'  + '"]');			
			if (keyword_name.length) {
				keyword_name.on('keyup', function(){
					setTranslit($(this), entity); 
				});
			}
		});
	});
});