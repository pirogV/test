<?php

use config\Registry;

	echo '<!--<br><hr><h3>Module Debug</h3><hr>';
		
		Registry::set('content', strlen(Registry::get('content')) . ' символов');
		Registry::view();
	
	echo '<br><hr>-->';
