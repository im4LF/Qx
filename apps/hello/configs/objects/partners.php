<?php
return array(
	// object fields
	'fields' => array(
		'name' => array(
			'title'	=> '�������� ��������',	// title of field
			'type'	=> 'String',			// type
			'rules'	=> array('required'),	// rules
			'from'	=> 'post'				// get value from $_POST
		),
		'description' => array(
			'title'	=> '��������',
			'type'	=> 'Text',
			'rules'	=> array('xss_filter'),
			'from'	=> 'post'
		),
		'icon' => array(
			'title'	=> '�������',
			'type'	=> 'Image',
			'rules'	=> array('required', 'jpeg_gif'),
			'from'	=> 'files'
		),
		'www' => array(
			'title'	=> '����� �����',
			'type'	=> 'Link',
			'rules'	=> array('valid_http'),
			'from'	=> 'post'
		),
	),
	
	'groups' => array(
		'������������ ����' => array('name', 'icon'),
		'�������������' => array('description', 'www')
	)
);
?>