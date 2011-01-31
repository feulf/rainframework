<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->var['title'];?></title>
	<link rel="stylesheet" href="http://localhost/buongiornonewyork/rainphp/rainframework_package/rainframework2/application/templates/default/css/style.css" type="text/css" />
</head>
<body>
	
	<div id="header" class="doc">
		<div id="menu"><div><img src="http://localhost/buongiornonewyork/rainphp/rainframework_package/rainframework2/application/templates/default/img/logo_mini.png"><?php $counter1=-1; if( isset($this->var['menu']) && is_array($this->var['menu']) && sizeof($this->var['menu']) ) foreach( $this->var['menu'] as $key1 => $value1 ){ $counter1++; ?><a href="<?php echo $value1["link"];?>" <?php echo $value1["selected"]?'class="selected"':null;?>><?php echo $value1["name"];?></a><?php } ?></div>
	</div>

	<div id="section" class="doc">
		<div id="section_inside">
			<div id="section_inside_inside">
				<?php echo $this->var['center'];?>
			</div>
		</div>
	</div>

	<div id="footer">
		<div id="inner_footer">
			<div class="left"><?php $counter1=-1; if( isset($this->var['menu']) && is_array($this->var['menu']) && sizeof($this->var['menu']) ) foreach( $this->var['menu'] as $key1 => $value1 ){ $counter1++; ?><?php echo $counter1?' | ':'';?><a href="<?php echo $value1["link"];?>" <?php echo $value1["selected"]?'class="selected"':null;?>><?php echo $value1["name"];?></a><?php } ?></div>
			<div class="center">execution time: <?php echo $this->var['execution_time'];?><br/>
								executed query: <?php echo $this->var['n_query'];?><br/>
			</div>
			<div class="right">Copyright rain team</div>
		</div>
	</div>

</body>
</html>
