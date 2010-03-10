<?php
return array(
	'#^/category/.*#'	=> 'Category_Controller',
	'#^/forum/.*#'		=> 'Forum_Controller',
	'#^/thread/.*#'		=> 'Thread_Controller',
	'#^/$#'				=> 'Index_Controller'
);
?>