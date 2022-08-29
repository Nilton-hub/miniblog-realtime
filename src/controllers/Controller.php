<?php

namespace src\controllers;

abstract class Controller
{
	private string $dir;
	private array $data;
	
	public function __construct(string $dir = __DIR__ . "/../../views/", ?array $data = [])
	{
		$this->dir = $dir;
		$this->data = $data;
	}

	/**
	 * @var string $view
	 * @var string $data
	 * @return void
	 */
	protected function render(string $view, array $data = []): void
	{
		$data = array_merge($data, $this->data);
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

	/**
	 * array|string
	 * mixed
	 */
	public function addData($data, $value = null): void
	{
		if (gettype($data) === 'array') {
			$this->data = array_merge($this->data, $data);
			return;
		}
		$this->data[$data] = $value;
	}
}
