<?php

namespace App\Forms;

use App\Customers\CustomerRepository;
use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;


class AddCustomerFormFactory extends Nette\Object
{

	/**
	 * @var CustomerRepository
	 */
	private $customers;

	/**
	 * @var ActiveRow
	 */
	private $customer;

	public function __construct(CustomerRepository $customerRepository)
	{
		$this->customers = $customerRepository;
	}

	public function setCustomer($id)
	{
		$this->customer = $this->customers->get($id);
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->getElementPrototype()->class[] = 'form-horizontal';

		$form->addText(CustomerRepository::COLUMN_NAME, 'Název')
				->setRequired('Název musí být vyplněn')
				->getControlPrototype()->class[] = 'form-control';

		$form->addText(CustomerRepository::COLUMN_STREET, 'Ulice')
				->setRequired('Ulice musí být vyplněna')
				->getControlPrototype()->class[] = 'form-control';

		$form->addText(CustomerRepository::COLUMN_CITY, 'Město')
				->setRequired('Město musí být vyplněno')
				->getControlPrototype()->class[] = 'form-control';

		$form->addText(CustomerRepository::COLUMN_ZIPCODE, 'PSČ')
				->setRequired('PSČ musí být vyplněno')
				->addRule(Form::INTEGER, 'PSČ musí být číslo')
				->getControlPrototype()->class[] = 'form-control';

		$form->addText(CustomerRepository::COLUMN_IC, 'IČ')
				->setRequired('IČ musí být vyplněno')
				->getControlPrototype()->class[] = 'form-control';

		$form->addText(CustomerRepository::COLUMN_DIC, 'DIČ')
				->getControlPrototype()->class[] = 'form-control';

		$form->addSubmit('send', 'Ulož')
				->getControlPrototype()->class[] = 'btn btn-primary';

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
		/*try {
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
		}*/
	}

}
