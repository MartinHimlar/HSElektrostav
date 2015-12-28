<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Controls\CustomersGridFactory;
use App\Forms\AddCustomerFormFactory;
use Nette;
use App\Model;


class CustomersPresenter extends SecuredPresenter
{
	/** @var CustomersGridFactory @inject */
	public $customersGridFactory;

	/** @var AddCustomerFormFactory @inject */
	public $factory;

	public function actionEdit($id = NULL)
	{
		if ($id) {
			$this->factory->setCustomer($id);
		}
		$this->template->id = $id;
	}

	public function actionDetail($id)
	{}

	protected function createComponentCustomersGrid()
	{
		return $this->customersGridFactory->create();
	}

	protected function createComponentAddCustomerForm()
	{
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Users:');
		};
		return $form;
	}
}
