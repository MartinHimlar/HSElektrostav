<?php

namespace App\Forms;

use App\Users\UserManager;
use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Tracy\Debugger;


class AddUserFormFactory extends Nette\Object
{
	/** @var UserManager $userManager */
	private $userManager;

	/** @var ActiveRow  */
	private $user;


	/**
	 * AddUserFormFactory constructor.
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}

	public function setUser($id)
	{
		$this->user = $this->userManager->get($id);
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form;
		$form->getElementPrototype()->class[] = 'form-horizontal';
		$form->addText('username', 'Jméno:')
			->setRequired('Zadej uživatelské jméno.')
				->getControlPrototype()->class[] = 'form-control';

		$form->addText('email', 'email:')
				->setRequired('Zadej email.')
				->addRule(Form::EMAIL, 'Zadej správný email.')
				->getControlPrototype()->class[] = 'form-control';

		//$form->addCheckbox('remember', 'Keep me signed in');

		if ($this->user) {
			$form->setDefaults(array(
				'username' => $this->user->username,
				'email' => $this->user->email,
			));
		} else {
			$form->addPassword('password', 'Heslo:')
					->setRequired('Zadej heslo.')
					->getControlPrototype()->class[] = 'form-control';

			$form->addPassword('confirmPassword', 'Heslo pro potvrzení:')
					->setRequired('Zadej heslo pro potvrzení.')
					->getControlPrototype()->class[] = 'form-control';
		}

		$form->addSubmit('send', 'Ulož')
				->getControlPrototype()->class[] = 'btn btn-primary';

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
		try {
			if ($this->user) {
				$this->user->update(array(
					UserManager::COLUMN_NAME => $values->username,
					UserManager::COLUMN_EMAIL => $values->email,
				));
			} else {
				if ($values->password != $values->confirmPassword) {
					throw new \PasswordsNotCorectedException('hesla se neshodují!');
				}
				$this->userManager->add($values->username, $values->password, $values->email);
			}
		} catch (\DuplicateNameException $e) {
			$form->addError($e->getMessage());
		} catch (\PasswordsNotCorectedException $e) {
			$form->addError($e->getMessage());
		} catch (\PDOException $e) {
			Debugger::log($e->getMessage());
			$form->addError('Uživatele nelze změnit');
		}
	}

}
