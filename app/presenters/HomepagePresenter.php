<?php

namespace App\Presenters;

use App\Model\ImagesManager;
use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{
	/**
	 * @inject
	 * @var ImagesManager
	 */
	public $images;

	public function startup()
	{
		parent::startup();
		$this->images->setPath(IMAGES_DIR);
	}

	public function actionFoto()
	{
		$this->template->images = $this->images->findImages();
	}
}
