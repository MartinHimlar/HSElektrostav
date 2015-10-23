<?php

namespace App\Model;


use Nette\Http\FileUpload;
use Nette\Object;
use Nette\Utils\Finder;
use NotImagesToShow;
use PathNotSetException;
use SplFileInfo;

class ImagesManager extends Object
{

	/**
	 * @var array of filename
	 */
	private $files;

	/**
	 * @var string
	 */
	private $path;

	public function setPath($dir)
	{
		$this->path = $dir;
	}

	/**
	 * @return array
	 * @throws NotImagesToShow
	 */
	public function findImages()
	{
		if (!$this->path) {
			throw new PathNotSetException();
		}

		/**
		 * @var SplFileInfo $file
		 */
		foreach (Finder::findFiles('*.jpg', '*.png')->in($this->path) as $key => $file) {
			$this->files[] = DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'foto' . DIRECTORY_SEPARATOR . $file->getFilename();
		}

		if (count($this->files) < 1) {
			throw new NotImagesToShow('Nebyli nalezeny žádné obrázky');
		}

		return $this->files;
	}

	/**
	 * @todo not implement
	 * @param FileUpload $images
	 * @return bool
	 */
	public function uploadImage(FileUpload $images)
	{
		return FALSE;
	}

	/**
	 * @param $key
	 */
	public function deleteImage($key)
	{
		if (!$this->files) {
			$this->findImages();
		}

		if (!array_key_exists($key, $this->files)) {
			throw new \ImageNotExistException('Nelze smazat obrázek, již neexistuje.');
		}

		@unlink($this->files[$key]);
		unset($this->files[$key]);
	}

}