<?php

namespace common;

use config\Registry;
use common\Html;

class Validate
{
	private static $flag = true;
	private static $data = [];
	private static $error = [];

    public static function isValid ($rules, $data)
    {
		foreach ($rules as $key => $val) {
			
			isset ($data[$val[0]]) ? $var = $data[$val[0]] : $var = '';
				
			if ($val[1] == 'series') $fl = self::seriesValid($var, $val[0], $val[2], $val[3], $val[4]);
			if ($val[1] == 'number') $fl = self::numberValid($var, $val[0], $val[2], $val[3], $val[4]);
			if ($val[1] == 'entry') $fl = self::entryValid($var, $val[0], $val[2], $val[3]);
			if ($val[1] == 'date') $fl = self::dateValid($var, $val[0], $val[2]);
		}

		Registry::set('post', self::$data);
		Registry::set('formError', self::$error);
		
		return self::$flag;
    }
	
	private static function dateValid($data, $name, $required)
	{
		self::$data[$name] = $data;

		if (empty($data)) {
			if($required == 'required') {
				self::$flag = false;
				if(isset($_POST[$name])) self::$error[$name] = 'Поле обязательное для заполнения';
			}
		} else {
			$d = explode('-', $data);
			if(!checkdate($d[1], $d[2], $d[0])) {
				self::$flag = false;
				self::$error[$name] = 'Поле дата заполнено некорректно';
			}
		}
	}

	private static function entryValid($data, $name, $str, $required)
	{
		self::$data[$name] = $data;
		
		if (empty($data)) {
			if($required == 'required') {
				self::$flag = false;
				if(isset($_POST[$name])) self::$error[$name] = 'Поле обязательное для заполнения';
			}
		} else {
			if (is_array($data)){
				foreach ($data as $k => $v){
					if(!strPos(':|' . $str . '|', '|' . $v . '|')) {
						self::$flag = false;
						self::$error[$name] = 'Поле должно содержать только ' . $str;
					}
				}
			} else {
				if(!strPos(':|' . $str . '|', '|' . $data . '|')) {
					self::$flag = false;
					self::$error[$name] = 'Поле должно содержать только ' . $str;
				}
			}
		}
	}

	private static function seriesValid($data, $name, $min, $max, $required)
	{
		self::$data[$name] = $data;
		
		if (empty($data)) {
			if($required == 'required' ) {
				self::$flag = false;
				if(isset($_POST[$name])) self::$error[$name] = 'Поле обязательное для заполнения';
			}
		} else {
			if(!preg_match('/^[A-Z]*$/u', $data) OR (mb_strlen($data) < $min) OR (mb_strlen($data) > $max)) {
				self::$flag = false;
				self::$error[$name] = 'Поле должно содержать ' . $min . ' - ' . $max . ' символов A - Z';
			}
		}
	}
	
	private static function numberValid($data, $name, $min, $max, $required)
	{
		self::$data[$name] = $data;
		
		if (empty($data)) {
			if($required == 'required' ) {
				self::$flag = false;
				if(isset($_POST[$name])) self::$error[$name] = 'Поле обязательное для заполнения';
			}
		} else {
			if(!preg_match('/^[0-9]*$/u', $data) OR ($data < $min) OR ($data > $max)) {
				self::$flag = false;
				self::$error[$name] = 'Поле должно содержать число больше ' . $min . ' и меньше ' . $max;
			}
		}
	}
}
