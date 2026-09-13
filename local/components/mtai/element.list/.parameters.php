<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
	'GROUPS' => [],
	'PARAMETERS' => [
		'IBLOCK_API_CODE' => [
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'official_news',
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ELEMENT_LIST_IBLOCK_API_CODE'),
		],
		'PAGE_SIZE' => [
			'TYPE' => 'INT',
			'MULTIPLE' => 'N',
			'DEFAULT' => '20',
			'PARENT' => 'BASE',
			'NAME' => GetMessage('ELEMENT_LIST_PAGE_SIZE'),
		],
	],
];
