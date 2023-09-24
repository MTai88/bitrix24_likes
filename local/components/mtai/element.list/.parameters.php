<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
	'GROUPS' => array(),
	'PARAMETERS' => array(
		'PAGE_SIZE' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '20',
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ELEMENT_LIST_PAGE_SIZE'),
		),
	),
);
