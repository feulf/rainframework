<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	</head>
	<body>
		
		<div align="center">
			<h1><?php echo $this->var['title'];?></h1>
			<div><?php echo $this->var['content'];?><br></div>
			<div>Time: <?php echo time_format( $this->var['time'] );?></div>
			<div>Money: <?php echo ( moneyFormat( $this->var['money'], true ) );?></div>
		</div>

	</body>
</html>