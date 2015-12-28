<?php

namespace App\AdminModule\Controls;

use App\Customers\CustomerRepository;
use Nette\Object;
use Nette\Utils\Paginator;

class CustomersDataSource extends Object
{
	/**
	 * @var CustomerRepository
	 */
	protected $customers;

	/**
	 * @var int
	 */
	protected $placeId;

	public function __construct(CustomerRepository $customers)
	{
		$this->customers = $customers;
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
		$selection = $this->customers->find()->where($filters);

		if ($order) {
			$selection->order(implode(' ', $order));
		}

		return $selection;
	}
}
