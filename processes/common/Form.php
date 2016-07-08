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
		
		if (isset ($options['head'])) $html .= '<h3>' . $options['head'] . '</h3>';
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
	 
	public static function text25($name, $label)
	{
		$error = '';
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span id="form-' . $name . '-error">' . $error . '</span></div>';
		$html .= '<input style="width:25%" name="' . $name . '" value="">';
		$html .= '</div>';

  		return $html;
	}

	public static function datepicker($name, $label)
	{
		$error = '';
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span id="form-' . $name . '-error">' . $error . '</span></div>';
		$html .= '<input class="datepic" name="' . $name . '" value="">';
		$html .= '</div>';

		Registry::append('addHead', '<script language="JavaScript" type="text/javascript" src="/js/jquery-ui/jquery-ui.min.js"></script>');
		Registry::append('addHead', '<link href="/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">');
		Registry::append('addHead', '<script language="JavaScript" type="text/javascript">$(document).ready( function (){ $( ".datepic" ).datepicker({ dateFormat: "yy-mm-dd" }); })</script>');

		return $html;
	}

	public static function checkboxArray($name, $value, $label)
	{
		$error = '';
		$html = '<div class="form-wrap-input">';
		$html .= '<div class="form-wrap-label" id="form-' . $name . '-label">' . $label . ' &nbsp; <span id="form-' . $name . '-error">' . $error . '</span></div>';
		foreach ($value as $k => $v) {
			$html .= '<div><label><input type="checkbox" name="' . $name . '[]" value="' . $k . '">' . $v . '</label></div>';
		}
		$html .= '</div>';

  		return $html;
	}
	
	public static function submitAjax($href, $box, $link)
	{
		$html = '<div class="form-wrap-input">';
		$html .= '<a class="ajax pagination" box="' . $box . '" form="' . self::$name . '" href="' . $href . '">' . $link . '</a>';
		$html .= '</div>';

  		return $html;
	}

	public function files($name)
	{
		$this->js($name);
		
		$str = '';
		if (isset (self::$rules[$name]['options'])) {
			foreach (self::$rules[$name]['options'] as $key => $val) $str .= ' ' . $key . '="' . $val . '"';
		}
		
		$error = '';
		$class = 'form-label';
		if (Config::get(self::$rules['begin']['options']['name'] . 'FormError', self::$rules[$name]['options']['name'])) {
			$error = self::$rules[$name]['error'];
			$class = 'form-error';
		}
		
		self::$html .= '<div class="form-wrap-input">';
		if (isset (self::$rules[$name]['label'])) {
			self::$html .= '<div class="' . $class . '" id="form-' . self::$rules['begin']['options']['name'] . '-label-' 
			. self::$rules[$name]['options']['name'] . '">' 
			. self::$rules[$name]['label'] . ' &nbsp; <span id="form-' . self::$rules['begin']['options']['name'] . '-error-' 
			. self::$rules[$name]['options']['name'] . '">' . $error . '</span></div>';
		}

		self::$html .= '<input' . $str . '>';
		self::$html .= '</div>';
		
		return $this;
	}

    /**
	 * public function save()
     * создаем кнопку отправки формы
	 * 
	 * @return $this Возвращаем  экземпляр класса
     */
	public function save()
	{
		$str = '';

		// Спорное решение. Без javascript отправить форму будет невозможно. Чтоб отправить без javascript закоментируйте foreach
		foreach (self::$rules as $key => $val) {
			if (isset (self::$rules[$key]['rules']['required']) and self::$rules[$key]['rules']['required'] == 1) $str = ' disabled="disabled"';
		}
		
		self::$html .= '<div class="form-wrap-input">';
			self::$html .= '<input name="form-' . self::$rules['begin']['options']['name'] . '-submit" type="submit" value="Сохранить"' . $str . '>';
		self::$html .= '</div>';
		
		return $this;
	}

    /**
	 * private function js($name)
     * генерируем javascript который вешает события на инпуты для проверки форм
     */
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
	
    /**
	 * public function text($name)
     * создает тег <input type="text">
	 * $param $name првязка к rules
	 * @return $this Возвращаем  экземпляр класса
     */
	public function text($name)
    {
		$this->js($name);

		$str = '';
		$hidden = '<input type="hidden" name="form-' . self::$rules['begin']['options']['name'] . '-disabled" value="1">';
		if (isset (self::$rules[$name]['options'])) {
			foreach (self::$rules[$name]['options'] as $key => $val) $str .= ' ' . $key . '="' . $val . '"';
		}
		if (Config::get('post', self::$rules[$name]['options']['name']) !== null) {
			if (self::$rules[$name]['rules']['valid'] == 'date'){
				$arr = explode('-', Config::get('post', self::$rules[$name]['options']['name']));
				$val = $arr[1] . '/' . $arr[2] . '/' . $arr[0];
			} else $val = Config::get('post', self::$rules[$name]['options']['name']);
			$str .= ' value="' . $val . '"';
			$hidden = '';
		}

		self::$html .= '<div class="form-wrap-input">';
		
		$error = '';
		$class = 'form-label';
		if (Config::get(self::$rules['begin']['options']['name'] . 'FormError', self::$rules[$name]['options']['name'])) {
			$error = self::$rules[$name]['error'];
			$class = 'form-error';
		}
		
		if (isset (self::$rules[$name]['label'])) {

			self::$html .= '<div class="' . $class . '" id="form-' . self::$rules['begin']['options']['name'] . '-label-' 
				. self::$rules[$name]['options']['name'] . '">' 
				. self::$rules[$name]['label'] . ' &nbsp; <span id="form-' . self::$rules['begin']['options']['name'] . '-error-' 
				. self::$rules[$name]['options']['name'] . '">' . $error . $hidden . '</span></div>';
		}

		self::$html .= '<input' . $str . '>';
		self::$html .= '</div>';

		return $this;
    }
}