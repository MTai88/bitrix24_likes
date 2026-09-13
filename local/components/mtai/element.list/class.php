<?php

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class ElementList extends CBitrixComponent
{
	public const DEFAULT_IBLOCK_API_CODE = 'official_news';
	public const DEFAULT_PAGE_SIZE = 20;

	public function onPrepareComponentParams($arParams)
	{
		$arParams['IBLOCK_API_CODE'] = trim((string)($arParams['IBLOCK_API_CODE'] ?? self::DEFAULT_IBLOCK_API_CODE));
		if ($arParams['IBLOCK_API_CODE'] === '')
		{
			$arParams['IBLOCK_API_CODE'] = self::DEFAULT_IBLOCK_API_CODE;
		}

		$pageSize = (int)($arParams['PAGE_SIZE'] ?? 0);
		$arParams['PAGE_SIZE'] = $pageSize > 0 ? $pageSize : self::DEFAULT_PAGE_SIZE;

		return $arParams;
	}

	public function executeComponent()
	{
		if (!Loader::includeModule('iblock'))
		{
			ShowError(Loc::getMessage('MTAI_ELEMENT_LIST_IBLOCK_MODULE_MISSING'));
			return;
		}

		$elementEntity = IblockTable::compileEntity($this->arParams['IBLOCK_API_CODE']);
		if ($elementEntity === false)
		{
			ShowError(
				Loc::getMessage(
					'MTAI_ELEMENT_LIST_IBLOCK_NOT_FOUND',
					['#IBLOCK_API_CODE#' => $this->arParams['IBLOCK_API_CODE']]
				)
			);
			return;
		}
		$elementClass = $elementEntity->getDataClass();

		$pageNavigation = $this->getPageNavigation();

		$this->arResult['ITEMS'] = $this->getItems($elementClass, $pageNavigation);
		$pageNavigation->setRecordCount($elementClass::getCount(['=ACTIVE' => 'Y']));
		$this->arResult['NAV_OBJECT'] = $pageNavigation;

		$this->prepareItemsRating();

		$this->includeComponentTemplate();
	}

	/**
	 * @param string $elementClass класс ORM-сущности элементов инфоблока (наследник DataManager)
	 */
	protected function getItems(string $elementClass, PageNavigation $pageNavigation): array
	{
		$rows = $elementClass::query()
			->setSelect([
				'ID',
				'NAME',
				'DATE_CREATE',
				'PREVIEW_TEXT',
				'CREATED_BY',
			])
			->where('ACTIVE', 'Y')
			->setOrder(['ID' => 'desc'])
			->setLimit($pageNavigation->getLimit())
			->setOffset($pageNavigation->getOffset())
			->fetchAll();

		foreach ($rows as &$row)
		{
			$row['DATE_CREATE'] = $row['DATE_CREATE']->format('d.m.Y H:i');
		}
		unset($row);

		return $rows;
	}

	protected function getPageNavigation(): PageNavigation
	{
		$pageNavigation = new PageNavigation('nav');
		$pageNavigation->setPageSize($this->arParams['PAGE_SIZE'])->initFromUri();

		return $pageNavigation;
	}

	/**
	 * Готовит для каждого элемента данные рейтинга и параметры подключения
	 * bitrix:rating.vote, чтобы шаблон занимался только выводом.
	 */
	protected function prepareItemsRating(): void
	{
		$ids = array_column($this->arResult['ITEMS'], 'ID');

		$ratingResults = CRatings::GetRatingVoteResult('IBLOCK_ELEMENT', $ids);
		$topRatingData = CRatings::getEntityRatingData([
			'entityTypeId' => 'IBLOCK_ELEMENT',
			'entityId' => $ids,
		]);

		$currentUserId = (int)CurrentUser::get()->getId();

		foreach ($this->arResult['ITEMS'] as &$item)
		{
			$id = (int)$item['ID'];
			$rating = $ratingResults[$id] ?? [];

			$item['VOTE_ID'] = 'IBLOCK_ELEMENT_' . $id;

			$reaction = mb_strtoupper((string)($rating['USER_REACTION'] ?? ''));
			if ($reaction === '')
			{
				$reaction = 'LIKE';
			}

			$item['RATING'] = [
				'USER_HAS_VOTED' => ($rating['USER_HAS_VOTED'] ?? 'N') === 'Y',
				'USER_REACTION' => $reaction,
				'BUTTON_TEXT' => CRatingsComponentsMain::getRatingLikeMessage($reaction),
				'HAS_REACTIONS' => (int)($rating['TOTAL_POSITIVE_VOTES'] ?? 0) > 0,
			];

			$item['VOTE_PARAMS'] = [
				'ENTITY_TYPE_ID' => 'IBLOCK_ELEMENT',
				'ENTITY_ID' => $id,
				'OWNER_ID' => (int)$item['CREATED_BY'],
				'PATH_TO_USER_PROFILE' => '/company/personal/user/#USER_ID#/',
				'CURRENT_USER_ID' => $currentUserId,
				'VOTE_ID' => $item['VOTE_ID'],
				'USER_VOTE' => (int)($rating['USER_VOTE'] ?? 0),
				'USER_HAS_VOTED' => $rating['USER_HAS_VOTED'] ?? 'N',
				'TOTAL_VOTES' => (int)($rating['TOTAL_VOTES'] ?? 0),
				'TOTAL_POSITIVE_VOTES' => (int)($rating['TOTAL_POSITIVE_VOTES'] ?? 0),
				'TOTAL_NEGATIVE_VOTES' => (int)($rating['TOTAL_NEGATIVE_VOTES'] ?? 0),
				'TOTAL_VALUE' => (int)($rating['TOTAL_VALUE'] ?? 0),
				'USER_REACTION' => $rating['USER_REACTION'] ?? '',
				'REACTIONS_LIST' => $rating['REACTIONS_LIST'] ?? [],
				'TOP_DATA' => $topRatingData[$id] ?? false,
			];
		}
		unset($item);
	}
}
