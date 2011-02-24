<?php

	global $form_layout;
	$form_layout['default']['openTable'] = 		'	<table class="{$class}" cellspacing="0" cellpadding="0">' . "\n" .
												'		<thead><tr><th>{$title}</th><th class="subtitle">{$subtitle}</th></tr></thead>' . "\n" . 
												'		<tbody>' . "\n";
	$form_layout['default']['closeTable'] = 	'		</tbody>' . "\n" . 
												'	</table>' . "\n";
	$form_layout['default']['layout'] = 		'		<tr class="r{$counter}">' . "\n" .
												'			<td>' . "\n" .
												'				<div class="name">{$title}</div>' . "\n" .
												'				<div class="description">{$description}</div>' . "\n" .
												'			</td>' . "\n" .
												'			<td>{$input}</td>' . "\n" .
												'		</tr>';
	$form_layout['default']['row'] = 			'		<tr class="r{$counter}"><td colspan="2">' . "\n" .
												'				<div class="name">{$title}<div class="description">{$description}</div></div>' . "\n" .
												'				<div>{$input}</div>' . "\n" .
												'		</td></tr>' . "\n";
	$form_layout['default']['buttons'] = 		'		<tr class="r{$counter}"><td colspan="2" align="right"><div class="button_div">{$button}</div></td></tr>' . "\n";
	$form_layout['default']['html'] = 			'		<tr class="r{$counter}"><td colspan="2">{$html}</td></tr>' . "\n";

?>