<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;

		$router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);

		$router[] = $adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('prihlaseni', 'Sign:in');
		$adminRouter[] = new Route('admin[/<presenter>[/<action>[/<id>]]]', 'Homepage:default');

		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('kontakty', 'Homepage:contact');
		$frontRouter[] = new Route('fotogalerie', 'Homepage:foto');
		$frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
