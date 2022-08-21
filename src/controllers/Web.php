<?php

namespace src\controllers;

class Web extends Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function home(): void
	{
		$post = filter_input_array(INPUT_POST);
		if ($post) {
			$file = __DIR__ . '/data/var/products.csv';
			$open = fopen($file, 'a+');
			if ($post) {
				wsClient($post); //json_encode($post);
				fwrite($open, "{$post['name']};{$post['price']}" . PHP_EOL);
			}
			fclose($open);
			return;
		}
		$file = dirname(__DIR__, 2) . '/data/var/products.csv';
		$open = fopen($file, 'a+');
		parent::render('home', [
			'title' => 'Página Home :)',
			'file' => $file,
			'open' => $open,
			'styles' => file_get_contents(parent::getDir() . "partials/home-css.php")
		]);
		fclose($open);
	}

	public function signIn(): void
	{
		if (isset($_SESSION['user'])) {
			header('Location: http://localhost/artigos');
			exit();
		}
		$post = filter_input_array(INPUT_POST);
		if ($post) {
			$stmt = \src\core\Connect::getConn()->prepare("SELECT * FROM users WHERE email = :e");
			$stmt->bindValue('e', $post['email']);
			$stmt->execute();
			$user = $stmt->fetch();
			if ($user && $post['password'] === $user->password) {
				session_regenerate_id(true);
				$_SESSION['user'] = (object)[
					'id' => $user->id,
					'email' => $user->email,
					'name' => $user->name
				];
				header('HTTP/1.1 303 See Other');
				header('Location: http://localhost:80/artigos');
				exit;
			} else {
				$_SESSION['message'] = "Nenhum usuário encontrado! <a href=\"http://localhost:80/login\">Atualizar</a>";
				header('Location: http://localhost:80/login');
				exit;
			}
			return;
		}
		$message = null;
		if (isset($_SESSION['message'])) {
			$message = $_SESSION['message'];
		}
		parent::render('login', [
			'message' => $message,
			'title' => 'login',
			'styles' => file_get_contents(dirname(__DIR__, 2) . '/views/partials/login-css.php')
		]);
	}

	public function signUp(): void
	{
		
	}
	
	public function logout(): void
	{
		session_unset();
		session_destroy();
		header('HTTP/1.1 303 See Other');
		header('Location: http://localhost');
	}
}
