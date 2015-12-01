<?php

namespace App\Users;

use DuplicateNameException;
use Nette;
use Nette\Security\Passwords;

class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'users',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'username',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_ROLE = 'role',
		COLUMN_LAST_LOGIN = 'last_login',
		COLUMN_EMAIL = 'email',
		ROLE_USER = 1,
		ROLE_OWNER = 2,
		ROLE_ADMIN = 3;


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @param array $credentials
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$toUpdate[self::COLUMN_LAST_LOGIN] = new Nette\Utils\DateTime();

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

		if (!$row || !Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('Uživatelské jméno nebo heslo není správné.');

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$toUpdate[self::COLUMN_PASSWORD_HASH] = Passwords::hash($password);
		}

		$row->update($toUpdate);

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user.
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param int $role
	 * @throws DuplicateNameException
	 */
	public function add($username, $password, $email = NULL, $role = self::ROLE_USER)
	{
		try {
			$this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
				self::COLUMN_ROLE => $role,
				self::COLUMN_EMAIL => $email,
			));
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException('Uživatelské jméno ' . $username . ' již existuje, zvol jiné.');
		}
	}


	/**
	 * Find all users
	 * @return Nette\Database\Table\Selection
	 */
	public function find()
	{
		return $this->database->table(self::TABLE_NAME);
	}

	/**
	 * @param int $id
	 * @return Nette\Database\Table\IRow
	 */
	public function get($id)
	{
		$user = $this->find()->where(self::COLUMN_ID, $id)->fetch();

		if (!$user) {
			throw new \UserNotFoundException('Neexistující uživatel');
		}

		return $user;
	}

	public function delete($id)
	{
		$selection = $this->find()->where(self::COLUMN_ID, $id);

		if (Count($selection) < 1) {
			throw new \UserNotFoundException('Uživatele nelze smazat, již neexistuje.');
		} else {
			$selection->delete();
		}
	}

}
