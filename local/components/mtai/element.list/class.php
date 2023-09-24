<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock\Elements\ElementOfficialNewsTable;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;

class ElementList extends CBitrixComponent
{

    public function __construct($component = null)
    {
        parent::__construct($component);

        Loader::includeModule("iblock");
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['PAGE_SIZE'] = $arParams['PAGE_SIZE'] > 0 ? (int)$arParams['PAGE_SIZE'] : 20;

        return $arParams;
    }

    public function executeComponent()
    {
        $pageNavigation = $this->getPageNavigation();

        $query = $this->getQuery($pageNavigation);

        $this->arResult['ITEMS'] = array_map(function ($item) {
            $item["DATE_CREATE"] = $item["DATE_CREATE"]->format("d.m.Y H:i");

            $item["VOTE_ID"] = "IBLOCK_ELEMENT_" . $item['ID'];

            return $item;
        }, $query->fetchAll());

        $totalCount = $query->queryCountTotal();
        $pageNavigation->setRecordCount($totalCount);
        $this->arResult['NAV_OBJECT'] = $pageNavigation;

        $this->getItemsRating();

        $this->includeComponentTemplate();
    }

    protected function getQuery(PageNavigation $pageNavigation)
    {
        $order = ['ID' => 'desc'];
        $query = ElementOfficialNewsTable::query()
            ->setSelect([
                'ID',
                'NAME',
                'DATE_CREATE',
                'PREVIEW_TEXT',
                'CREATED_BY',
            ])
            ->where('ACTIVE', 'Y')
            ->setOrder($order)
            ->setLimit($pageNavigation->getLimit())
            ->setOffset($pageNavigation->getOffset());

        return $query;
    }

    protected function getPageNavigation(): PageNavigation
    {
        $pageNavigation = new PageNavigation('nav');
        $pageNavigation->setPageSize($this->arParams['PAGE_SIZE'])->initFromUri();

        return $pageNavigation;
    }

    protected function getItemsRating(): void
    {
        $ids = array_column($this->arResult['ITEMS'], "ID");
        $this->arResult["RATING_RESULTS"] = CRatings::GetRatingVoteResult(
            "IBLOCK_ELEMENT",
            $ids
        );

        $this->arResult['TOP_RATING_DATA'] = \CRatings::getEntityRatingData(array(
            'entityTypeId' => "IBLOCK_ELEMENT",
            'entityId' => $ids,
        ));
    }
}
