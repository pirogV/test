<?php use config\Registry;?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="ru" />
<meta name="description" content="<?=Registry::get('description')?>" />
<title><?=Registry::get('title')?></title>
<script language="JavaScript" type="text/javascript" src="/js/jquery-3.0.0.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery.ajaxLoad.js"></script>
<script language="JavaScript" type="text/javascript">
$(document).ready( function (){
	$(document).on("click", ".ajax", function(e)
	{
		e.preventDefault();

		options = {
			effect		: $(this).attr('effect'),
			form		: $(this).attr('form'),
			href		: $(this).attr('href'),
			datatype	: $(this).attr('datatype')
		}

		$('#' + $(this).attr('box')).ajaxLoad(options);
	})

	$(document).on("click", ".ajax-confirm", function(e)
	{
		e.preventDefault();

		if (confirm("Необратимое действие. Выполнить?")) {
			options = {
				effect		: $(this).attr('effect'),
				form		: $(this).attr('form'),
				href		: $(this).attr('href'),
				datatype	: $(this).attr('datatype')
			}

			$('#' + $(this).attr('box')).ajaxLoad(options);
		}
	})
});
</script>
<link href="/css/general.css" rel="stylesheet" type="text/css" />
<?=Registry::get('addHead')?>
</head>