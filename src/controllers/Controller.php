<?php

namespace src\controllers;

abstract class Controller
{
	private string $dir;
	
	public function __construct(string $dir = __DIR__ . "/../../views/")
	{
		$this->dir = $dir;
	}

	/**
	 * @var string $view
	 * @var string $data
	 * @return void
	 */
	protected function render(string $view, array $data = []): void
	{
		extract($data);
		require($this->dir.'header.php');
		require($this->dir.$view.'.php');
		require($this->dir.'footer.php');
	}

	/**
	* @return string
	*/
	public function getDir(): string
	{
		return $this->dir;
	}
}
