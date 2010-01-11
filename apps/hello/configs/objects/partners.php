<?php
return array(
	// object fields
	'fields' => array(
		'name' => array(
			'title'	=> 'Название партнера',	// title of field
			'type'	=> 'String',			// type
			'rules'	=> array('required'),	// rules
			'from'	=> 'post'				// get value from $_POST
		),
		'description' => array(
			'title'	=> 'Описание',
			'type'	=> 'Text',
			'rules'	=> array('xss_filter'),
			'from'	=> 'post'
		),
		'icon' => array(
			'title'	=> 'Логотип',
			'type'	=> 'Image',
			'rules'	=> array('required', 'jpeg_gif'),
			'from'	=> 'files'
		),
		'www' => array(
			'title'	=> 'Адрес сайта',
			'type'	=> 'Link',
			'rules'	=> array('valid_http'),
			'from'	=> 'post'
		),
	),
	
	'groups' => array(
		'Обязательные поля' => array('name', 'icon'),
		'Дополнительно' => array('description', 'www')
	)
);
?>