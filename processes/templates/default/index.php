<?php

use config\Registry;

$this->module('head');
?>

<body>
<div><?php $this->module('header')?></div>
<div class="BOX" id="content"><?=Registry::get('content')?></div>
<?php $this->module('footer')?>
<?php $this->module('debug')?>
</body>
	</html>