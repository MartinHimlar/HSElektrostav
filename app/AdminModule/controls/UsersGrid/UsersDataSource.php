<?php

namespace App\StockModule\Controls;

use App\Stock\Movement\Place;
use App\Users\UserManager;
use Nette\Object;
use Nette\Utils\Paginator;
use Nette\Utils\Strings;

class UsersDataSource extends Object
{
	/**
	 * @var UserManager
	 */
	protected $users;

	/**
	 * @var int
	 */
	protected $placeId;

	public function __construct(UserManager $users)
	{
		$this->users = $users;
	}

	public function getDatasource($filter, $order, Paginator $paginator = NULL)
	{
		$selection = $this->prepareDataSource($filter, $order);
		if ($paginator) {
			$selection->limit($paginator->getItemsPerPage(), $paginator->getOffset());
		}
		return $selection;
	}


	public function getDatasourceSum($filter, $order)
	{
		return $this->prepareDataSource($filter, $order)->count('*');
	}


	private function prepareDataSource($filter, $order)
	{
		$filters = array();
		foreach ($filter as $k => $v) {
			if (is_array($v)) {
				$filters[$k] = $v;
			} else {
				$filters[$k . ' LIKE ?'] = "%$v%";
			}
		}
		$selection = $this->users->find()->where($filters);

		if ($order) {
			$selection->order(implode(' ', $order));
		}

		return $selection;
	}
}
