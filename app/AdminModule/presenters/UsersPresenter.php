<?php

namespace App\AdminModule\Presenters;

use App\Forms\AddUserFormFactory;
use App\StockModule\Controls\UsersGridFactory;
use App\Users\UserManager;
use Nette;
use App\Model;
use Tracy\Debugger;


class UsersPresenter extends SecuredPresenter
{
	/** @var AddUserFormFactory @inject */
	public $factory;

	/** @var UsersGridFactory @inject */
	public $usersGridFactory;

	/** @var UserManager @inject  */
	public $userManager;

	/**
	 * Editing user
	 * @param int $id
	 */
	public function actionEdit($id)
	{
		try {
			$this->factory->setUser($id);
			$this->template->setFile(__DIR__ . '/templates/users/add.latte');
		} catch (\UserNotFoundException $e) {
			Debugger::log($e->getMessage());
			$this->flashMessage($e->getMessage(), 'danger');
			$this->redirect('default');
		}
	}

	public function actionDelete($id)
	{
		try {
			$this->userManager->delete($id);
			$this->flashMessage('Uživatel byl smazán', 'success');
			$this->redirect('default');
		} catch(\UserNotFoundException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
			$this->redirect('default');
		}
	}

	/**
	 * Add user form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentAddUserForm()
	{
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Users:');
		};
		return $form;
	}

	/**
	 * Create grid of Users
	 * @return \Nextras\Datagrid\Datagrid
	 */
	protected function createComponentUsersGrid()
	{
		return $this->usersGridFactory->create();
	}
}
