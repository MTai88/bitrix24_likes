<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\CurrentUser;

\Bitrix\Main\UI\Extension::load("ui.bootstrap4");

?>
<div class="container">
    <h2>Elements</h2>

    <?php foreach ($arResult['ITEMS'] as $item) { ?>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= $item["NAME"] ?></h5>
                <p class="card-text"><?= $item["PREVIEW_TEXT"] ?></p>
                <p class="card-text"><small class="text-muted"><?= $item["DATE_CREATE"] ?></small></p>

                <?php
                $userHasVoted = isset($arResult["RATING_RESULTS"][$item["ID"]]["USER_HAS_VOTED"])
                    && $arResult["RATING_RESULTS"][$item["ID"]]["USER_HAS_VOTED"] == 'Y';
                $emotion = (
                !empty($arResult["RATING_RESULTS"][$item["ID"]]["USER_REACTION"])
                    ? mb_strtoupper($arResult["RATING_RESULTS"][$item["ID"]]["USER_REACTION"])
                    : 'LIKE'
                );
                $buttonText = \CRatingsComponentsMain::getRatingLikeMessage($emotion);
                ?>
                <span id="bx-ilike-button-<?= htmlspecialcharsbx($item['VOTE_ID']) ?>"
                      class="feed-inform-ilike feed-new-like">
                    <span class="bx-ilike-left-wrap<?= ($userHasVoted ? ' bx-you-like-button' : '') ?>"><a
                                href="#like" class="bx-ilike-text"><?= $buttonText ?></a></span>
                </span>
                <div class="feed-post-emoji-top-panel-outer">
                    <div id="feed-post-emoji-top-panel-container-<?=htmlspecialcharsbx($item['VOTE_ID'])?>"
                         class="feed-post-emoji-top-panel-box <?=((int)($arResult["RATING_RESULTS"][$item["ID"]] ?? null) > 0 ? 'feed-post-emoji-top-panel-container-active' : '')?>">
                    <?php $APPLICATION->IncludeComponent("bitrix:rating.vote", "like_react",
                        array(
                            "ENTITY_TYPE_ID" => "IBLOCK_ELEMENT",
                            "ENTITY_ID" => $item['ID'],
                            "OWNER_ID" => $item['CREATED_BY'],
                            "PATH_TO_USER_PROFILE" => "/company/personal/user/#USER_ID#/",
                            "CURRENT_USER_ID" => CurrentUser::get()->getId(),
                            "VOTE_ID" => $item['VOTE_ID'],
                            "USER_VOTE" => $arResult["RATING_RESULTS"][$item["ID"]]["USER_VOTE"],
                            "USER_HAS_VOTED" => $arResult["RATING_RESULTS"][$item["ID"]]["USER_HAS_VOTED"],
                            "TOTAL_VOTES" => $arResult["RATING_RESULTS"][$item["ID"]]["TOTAL_VOTES"],
                            "TOTAL_POSITIVE_VOTES" => $arResult["RATING_RESULTS"][$item["ID"]]["TOTAL_POSITIVE_VOTES"],
                            "TOTAL_NEGATIVE_VOTES" => $arResult["RATING_RESULTS"][$item["ID"]]["TOTAL_NEGATIVE_VOTES"],
                            "TOTAL_VALUE" => $arResult["RATING_RESULTS"][$item["ID"]]["TOTAL_VALUE"],
                            "USER_REACTION" => $arResult["RATING_RESULTS"][$item["ID"]]["USER_REACTION"],
                            "REACTIONS_LIST" => $arResult["RATING_RESULTS"][$item["ID"]]["REACTIONS_LIST"],
                            "TOP_DATA" => (!empty($arResult['TOP_RATING_DATA'][$item["ID"]]) ? $arResult['TOP_RATING_DATA'][$item["ID"]] : false),
                        ),
                        null,
                        array("HIDE_ICONS" => "Y")
                    ); ?>
                    </div>
                </div>

            </div>
        </div>

    <?php } ?>

    <?php if (empty($arResult['ITEMS'])): ?>
        <div>No items added</div>
    <?php endif ?>

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