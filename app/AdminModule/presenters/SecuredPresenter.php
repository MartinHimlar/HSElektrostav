<?php

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use Nette;
use App\Model;


class SecuredPresenter extends BasePresenter
{
	public function startup()
	{
		parent::startup();

		if (!$this->user->loggedIn) {
			$this->redirect('Sign:In');
		}
	}
}
