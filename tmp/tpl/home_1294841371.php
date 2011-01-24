<?php if(!class_exists('raintpl')){exit('Hacker attempt');}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	</head>
	<body>
		
		<div align="center">
			<h1><?php  echo $var["title"];?></h1>
			<div><?php  echo $var["content"];?><br></div>
			<div>Time: <?php  echo time_format( $var["time"] );?></div>
			<div>Money: $ {function="format_money($money)"}</div>
		</div>

	</body>
</html>