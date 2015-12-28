<?php

namespace App\AdminModule\Controls;

use App\Customers\CustomerRepository;
use Nette;
use Nextras\Datagrid\Datagrid;

class CustomersGridFactory extends Nette\Object
{

	/**
	 * @var CustomersDataSource
	 */
	private $dataSource;

	public function __construct(CustomersDataSource $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	public function create($parent = NULL, $name = NULL)
	{
		$grid = new Datagrid($parent, $name);
		$grid->addColumn(CustomerRepository::COLUMN_ID, 'id')
			->enableSort(Datagrid::ORDER_ASC);
		$grid->addColumn(CustomerRepository::COLUMN_NAME, 'název')
			->enableSort();
		$grid->addColumn(CustomerRepository::COLUMN_CITY, 'město')
			->enableSort();
		$grid->addColumn(CustomerRepository::COLUMN_IC, 'IČ');

		$grid->setDataSourceCallback(array($this->dataSource, 'getDatasource'));
		$grid->setPagination(20, array($this->dataSource, 'getDatasourceSum'));

		$grid->setFilterFormFactory(function() {
			$form = new Nette\Forms\Container;
			$form->addText(CustomerRepository::COLUMN_ID);
			$form->addText(CustomerRepository::COLUMN_NAME);
			$form->addText(CustomerRepository::COLUMN_CITY);
			$form->addText(CustomerRepository::COLUMN_IC);
			return $form;
		});

		$grid->addCellsTemplate(__DIR__ . '/../../../templates/datagrid/@bootstrap3.datagrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/../../../templates/datagrid/@bootstrap3.extended-pagination.datagrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/@CustomersGrid.latte');
		return $grid;
	}

}
