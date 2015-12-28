<?php

namespace App\Customers;

use Nette;

class CustomerRepository extends Nette\Object
{
	const
		TABLE_NAME = 'customers',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'name',
		COLUMN_IC = 'ic',
		COLUMN_DIC = 'dic',
		COLUMN_CITY = 'city',
		COLUMN_STREET = 'street',
		COLUMN_ZIPCODE = 'zipcode';


	/** @var Nette\Database\Context */
	private $database;

	/**
	 * CustomerRepository constructor.
	 * @param Nette\Database\Context $database
	 */
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Returns all customers
	 * @return Nette\Database\Table\Selection
	 */
	public function find()
	{
		return $this->database->table(self::TABLE_NAME);
	}

	/**
	 * @param array $data
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function add($data)
	{
		return $this->find()->insert($data);
	}

	/**
	 * Return customer by id
	 * @param int $id
	 * @return Nette\Database\Table\IRow
	 */
	public function get($id)
	{
		return $this->find()->where(self::COLUMN_ID, $id)->fetch();
	}

	/**
	 * Delete customer by id
	 * @param int $id
	 */
	public function delete($id)
	{
		$selection = $this->find()->where(self::COLUMN_ID, $id);

		if (Count($selection) < 1) {
			throw new \CustomerNotFoundException('Zákazníka nelze smazat, neexistuje.');
		}
		$selection->delete();
	}

	/**
	 *  Update customer data by id
	 * @param array $data
	 * @return int
	 */
	public function update($data)
	{
		$id = $data['id'];
		unset($data['id']);

		return $this->find()->where(self::COLUMN_ID, $id)->update($data);
	}

}
