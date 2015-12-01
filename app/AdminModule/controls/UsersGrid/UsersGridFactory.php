<?php

namespace App\StockModule\Controls;

use App\Users\UserManager;
use Nette;
use Nextras\Datagrid\Datagrid;

class UsersGridFactory extends Nette\Object
{

	/**
	 * @var UsersDataSource
	 */
	private $dataSource;

	public function __construct(UsersDataSource $dataSource)
	{
		$this->dataSource = $dataSource;
	}

	public function create($parent = NULL, $name = NULL)
	{
		$grid = new Datagrid($parent, $name);
		$grid->addColumn(UserManager::COLUMN_ID, 'Id')
			->enableSort(Datagrid::ORDER_ASC);
		$grid->addColumn(UserManager::COLUMN_NAME, 'uživatelské jméno')
			->enableSort();
		$grid->addColumn(UserManager::COLUMN_EMAIL, 'email')
			->enableSort();
		$grid->addColumn(UserManager::COLUMN_ROLE, 'role');

		$grid->setDataSourceCallback(array($this->dataSource, 'getDatasource'));
		$grid->setPagination(20, array($this->dataSource, 'getDatasourceSum'));

		$grid->setFilterFormFactory(function() {
			$form = new Nette\Forms\Container;
			$form->addText(UserManager::COLUMN_ID)
					->getControlPrototype()
					->class = 'btn btn-primary';
			$form->addText(UserManager::COLUMN_NAME)
					->getControlPrototype()
					->class = 'btn btn-primary';
			$form->addText(UserManager::COLUMN_EMAIL)
					->getControlPrototype()
					->class = 'btn btn-primary';
			return $form;
		});

		$grid->addCellsTemplate(__DIR__ . '/../../../templates/datagrid/@bootstrap3.datagrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/../../../templates/datagrid/@bootstrap3.extended-pagination.datagrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/@UsersGrid.latte');
		return $grid;
	}

}
