<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Новости с реакциями');
?>
<?php $APPLICATION->IncludeComponent('mtai:element.list', '', [
	'IBLOCK_API_CODE' => 'official_news',
	'PAGE_SIZE' => 5,
]);?>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
