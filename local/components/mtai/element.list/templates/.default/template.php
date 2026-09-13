<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

\Bitrix\Main\UI\Extension::load('ui.bootstrap4');

?>
<div class="container">
	<h2><?= Loc::getMessage('MTAI_ELEMENT_LIST_TITLE') ?></h2>

	<?php foreach ($arResult['ITEMS'] as $item) { ?>

		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?= $item['NAME'] ?></h5>
				<p class="card-text"><?= $item['PREVIEW_TEXT'] ?></p>
				<p class="card-text"><small class="text-muted"><?= $item['DATE_CREATE'] ?></small></p>

				<span id="bx-ilike-button-<?= htmlspecialcharsbx($item['VOTE_ID']) ?>"
					  class="feed-inform-ilike feed-new-like">
					<span class="bx-ilike-left-wrap<?= ($item['RATING']['USER_HAS_VOTED'] ? ' bx-you-like-button' : '') ?>"><a
								href="#like" class="bx-ilike-text"><?= $item['RATING']['BUTTON_TEXT'] ?></a></span>
				</span>
				<div class="feed-post-emoji-top-panel-outer">
					<div id="feed-post-emoji-top-panel-container-<?= htmlspecialcharsbx($item['VOTE_ID']) ?>"
						 class="feed-post-emoji-top-panel-box <?= ($item['RATING']['HAS_REACTIONS'] ? 'feed-post-emoji-top-panel-container-active' : '') ?>">
						<?php
						$APPLICATION->IncludeComponent(
							'bitrix:rating.vote',
							'like_react',
							$item['VOTE_PARAMS'],
							null,
							['HIDE_ICONS' => 'Y']
						);
						?>
					</div>
				</div>

			</div>
		</div>

	<?php } ?>

	<?php if (empty($arResult['ITEMS'])) { ?>
		<div><?= Loc::getMessage('MTAI_ELEMENT_LIST_EMPTY') ?></div>
	<?php } ?>

	<?php
	$APPLICATION->IncludeComponent(
		'bitrix:main.pagenavigation',
		'',
		[
			'NAV_OBJECT' => $arResult['NAV_OBJECT'],
			'SEF_MODE' => 'N',
			'AJAX_PARAMS' => [],
		],
		false,
		['HIDE_ICONS' => 'Y']
	);
	?>
</div>
