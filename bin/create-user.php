<?php

if (!isset($_SERVER['argv'][2])) {
	echo '
Add new user to database.

Usage: create-user.php <name> <password>
';
	exit(1);
}

list(, $name, $password, $role) = $_SERVER['argv'];

$container = require __DIR__ . '/../app/bootstrap.php';
$manager = $container->getByType('App\Users\UserManager'); /** @var $manager App\Users\UserManager */

try {
	$manager->add($name, $password, NULL, $role);
	echo "User $name was added.\n";

} catch (DuplicateNameException $e) {
	echo "Error: duplicate name.\n";
	exit(1);
}
