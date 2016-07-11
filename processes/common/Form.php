<?php
namespace common;

use config\Registry;
/**
 * Конструктор форм
 */
final class Form
{

	private static	$js = '';
	private static	$rules 	= [];
	private static	$name 	= [];

    /**
	 * public function begin()
     * генерирует тег <form> И обвертку для формы
	 * @return $this Возвращаем экземпляр класса
     */
	public static function begin($rules, $options)
    {
		self::$js = '';
		self::$rules = $rules;
		if (isset($options['name'])) self::$name = $options['name'];
        Registry::append('addHead', '<script language="JavaScript" type="text/javascript" src="/js/jquery.validate.js"></script>');

		$str = '';

		foreach ($options as $key => $val) {
			if ($key == 'head') continue;
			$str .= ' ' . $key . '="' . $val . '"';
		}

		$html = '<form' . $str . '>';
		
		if (isset ($options['head'])) $html .= '<h3>' . $options['head'] . '</h3><div class="LN"></div>';
		return $html;
    }

    /**
	 * public function end()
     * закрывает тег <form> И обвертку для формы
	 * @return $html Возвращаем html формы
     */
	public static function end()
    {
		//Закоментируйте для отправки формы без javascript
		Registry::append('addHead', '<script language="JavaScript" type="text/javascript">$(document).ready( function (){' . self::$js . '} )</script>');
		return '</form>';
    }

    /**
	 * public function files($name)
     * закрывает тег <input type="file">
	 * $param $name првязка к rules
	 * @return $this Возвращаем  экземпляр класса
     */
	public static function text50($name, $label)
	{
		$error = Registry::get('formError');
		$data = Registry::get('post');
		isset($error[$name]) ? $strError = $error[$name] : $strError = '';
		isset($data[$name]) ? $value = $data[$name] : $value = '';
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span class="red" id="form-' . $name . '-error">' . $strError . '</span></div>';
		$html .= '<input style="width:50%" name="' . $name . '" value="' . $value . '">';
		$html .= '</div>';

  		return $html;
	}

	public static function datepicker($name, $label)
	{
		$error = Registry::get('formError');
		$data = Registry::get('post');
		isset($error[$name]) ? $strError = $error[$name] : $strError = '';
		isset($data[$name]) ? $value = $data[$name] : $value = '';
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span class="red" id="form-' . $name . '-error">' . $strError . '</span></div>';
		$html .= '<input class="datepic" name="' . $name . '" value="' . $value . '">';
		$html .= '</div>';

		Registry::append('addHead', '<script language="JavaScript" type="text/javascript" src="/js/jquery-ui/jquery-ui.min.js"></script>');
		Registry::append('addHead', '<link href="/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">');
		Registry::append('addHead', '<script language="JavaScript" type="text/javascript">$(document).ready
										(
											function ()
											{ 
$("body").on("focus",".datepic", function(){

    if( $(this).hasClass("hasDatepicker") === false )  {
        $(this).datepicker({dateFormat: "yy-mm-dd"});
    }

});
											}
										)</script>');

		return $html;
	}

	public static function checkboxArray($name, $arr, $label)
	{
		$error = Registry::get('formError');
		$data = Registry::get('post');
		
		isset($error[$name]) ? $strError = $error[$name] : $strError = '';
		isset($data[$name]) ? $value = $data[$name] : $value = '';
		
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span class="red" id="form-' . $name . '-error">' . $strError . '</span></div>';
		
		foreach ($arr as $k => $v) {
			
			if (is_array($value)) {
				in_array($k, $value) ? $checked = ' checked' : $checked = '';
			} else {
				$k === $value ? $checked = ' checked' : $checked = '';
			}
			
			$html .= '<div><label><input type="checkbox" name="' . $name . '[]" value="' . $k . '"' . $checked . '>' . $v . '</label></div>';
		}
		
		$html .= '</div>';

  		return $html;
	}

	public static function radio($name, $arr, $label)
	{
		$error = Registry::get('formError');
		$data = Registry::get('post');
		
		isset($error[$name]) ? $strError = $error[$name] : $strError = '';
		isset($data[$name]) ? $value = $data[$name] : $value = '';
		
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span class="red" id="form-' . $name . '-error">' . $strError . '</span></div>';
		$i = 0;
		foreach ($arr as $k => $v) {
			if ($value === '') {
				$i == 0 ? $checked = ' checked' : $checked = '';
				$i++;
			} else {
				$k == $value ? $checked = ' checked' : $checked = '';
			}
			
			$html .= '<div><label><input type="radio" name="' . $name . '" value="' . $k . '"' . $checked . '>' . $v . '</label></div>';
		}

  		return $html . '</div>';
	}

	public static function submitAjax($href, $box, $link, $append = '')
	{
		$html = '<div class="form-wrap-input">';
		$html .= '<a class="ajax pagination" box="' . $box . '" form="' . self::$name . '" href="' . $href . '">' . $link . '</a> &nbsp ' . $append;
		$html .= '</div>';

  		return $html;
	}

	public static function submit()
	{
		$html = '<div class="form-wrap-input">';
		$html .= '<input type="submit" value="Сохранить">';
		$html .= '</div>';

  		return $html;
	}

	private function js($name) 
	{
		self::$js .= '$(\'input[name="' . self::$rules[$name]['options']['name'] . '"]\').
			on("change", function(){
				$(\'input[name="' . self::$rules[$name]['options']['name'] . '"]\').
					validate(
						"' . self::$rules['begin']['options']['name'] . '", 
						"' . self::$rules[$name]['options']['name'] . '", 
						"' . self::$rules[$name]['rules']['valid'] . '", 
						' . self::$rules[$name]['rules']['required'] . ', 
						' . self::$rules[$name]['rules']['min'] . ', 
						' . self::$rules[$name]['rules']['max'] . ', 
						"' . self::$rules[$name]['error'] . '"
					);
			});';
	}
}