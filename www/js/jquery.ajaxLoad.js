(function( $ ) {
	$.fn.ajaxLoad = function(options)
	{
		settings = $.extend(
		{
			effect		: 'insert',
			form		: '',
			href		: '/',
			type		: 'POST',
			cache		: false,
			datatype	: 'html'
		}, options);

		box = this;

		$.ajax({
			url			: settings.href,
			dataType	: settings.datatype,
			data		: getDataForm(settings.form),
			cache		: settings.cache,
			type		: settings.type,
			beforeSend	: function()
			{

			},
			success		: function(data)
			{
				if(settings.effect == 'insert') insert (data);
			}
		});

		function insert (data)
		{
			$(box).html(data);
		}

		function getDataForm(data)
		{
			if(data != '') {
				if(data.indexOf('=') == -1) {
					return $('form[name="'+options.form+'"]').serialize();
				} else {
					return data;
				}
			}
			return '';
		}

		return this;
	}
})(jQuery);