<?php
namespace config;

class Registry
{

	private static 	$arr = 	[];

	const CONTROLLERS = ':|discount|card|';
	const MODULES = 	':|footer|debug|head|header|';
	const TEMPLATES = 	':|404|default|';

	public static function setArr($arr)
	{
		self::$arr = self::$arr + $arr;
	}

	public static function set($a, $b)
	{
		self::$arr[$a] = $b;
	}
	
	public static function append($a, $b)
	{
		self::$arr[$a] .= $b;
	}

	public static function get($a, $b = null)
	{
		if (isset(self::$arr[$a])) {
			if ($b !== null) {
				if(isset(self::$arr[$a][$b])) {
					return self::$arr[$a][$b];
				} else {
					return null;
				}
			} else {
				return self::$arr[$a];				
			}
		}

		return null;
	}

	public static function view()
	{
		 echo '<h3>Config</h3><pre><div>';
		 print_r (self::$arr);
		 echo '</div></pre>';
	}
}
