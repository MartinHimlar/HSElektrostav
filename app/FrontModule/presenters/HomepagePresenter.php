<?php

namespace App\FrontModule\Presenters;

use App\Model\ImagesManager;
use App\Presenters\BasePresenter;
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

	public function actionFoto($page = 1)
	{
		$images = $this->images->findImages();
		$paginator = new Nette\Utils\Paginator;
		$paginator->setItemCount(count($images));
		$paginator->setItemsPerPage(12);
		$paginator->setPage($page);
		$img = [];
		foreach ($images as $key => $image) {
			if ($key >= $paginator->getOffset() && $key < $paginator->getItemsPerPage() + $paginator->getOffset()) {
				$img[$key] = $image;
			}
		}
		$this->template->paginator = $paginator;
		$this->template->images = $img;
	}
}
