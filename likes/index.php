<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
?>
<?php $APPLICATION->IncludeComponent("mtai:element.list","", Array(
		'PAGE_SIZE' => 5,
	)
);?>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
