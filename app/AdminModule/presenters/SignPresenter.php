<?php

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use Nette;
use App\Forms\SignFormFactory;


class SignPresenter extends BasePresenter
{
	/** @var SignFormFactory @inject */
	public $factory;


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Homepage:');
		};
		return $form;
	}


	public function actionOut()
	{
		$this->getUser()->logout(TRUE);
		$this->flashMessage('Byli jste úspěšně odhlášeni.', 'success');
		$this->redirect('Sign:In');
	}

}
