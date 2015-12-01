<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends SecuredPresenter
{
	public function startup()
	{
		parent::startup();

		if (!$this->user->loggedIn) {
			$this->redirect('Sign:In');
		}
	}
}
