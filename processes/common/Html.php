<?php
namespace common;

final class Html
{

    public static function p($t)
    {
		 echo '<pre><div class="HELP">';
		 print_r ($t);
		 if ($t === null) echo 'NULL';
		 if ($t === true) echo 'TRUE';
		 echo '</div></pre>';
    }
	
	public static function error($t)
    {
		 echo '<div class="ERROR" style="border: 1px solid #CCCCCC; padding: 12px;">'. $t . '</div>';
    }	

}