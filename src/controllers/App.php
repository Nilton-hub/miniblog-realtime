<?php

namespace src\controllers;

use PDOException;

class App extends Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!isset($_SESSION['user']) && $_SERVER['REQUEST_URI'] !== '/login') {
			$_SESSION['message'] = 'É necessário fazer login para acessar esta página.';
			header('HTTP/1.1 303 See Other');
			header('Location: http://localhost:80/login');
			exit;
		}
	}

	/**
	 * @return void
	 */
	public function articles(): void
	{
		try {
			// all articles
			$stmt = \src\core\Connect::getConn()->query('SELECT a.id, a.user_id, a.title, a.content FROM articles AS a');
			$articles = $stmt->fetchAll();
			// comments
			$stmt = \src\core\Connect::getConn()->query('
				SELECT c.id, c.user_id, c.article_id, c.text, u.name FROM comments AS c JOIN users AS u ON u.id = c.user_id
			');
			$comments = $stmt->fetchAll();
			// user articles
			$allArticles = \src\core\Connect::getConn()->query('SELECT id, title FROM articles')->fetchAll();
			// var_dump($allArticles);
			parent::render('articles', [
				'title' => 'Artigos',
				'articles' => $articles,
				'comments' => $comments,
				'allArticles' => $allArticles,
				'styles' => file_get_contents($this->getDir() . "partials/articles-css.php")
			]);
		} catch (\PDOException $e) {
			var_dump($e);
		}
	}

	/**
	 * @return void
	 */
	public function articlesPost(): void
	{
		$post = filter_input_array(INPUT_POST);
		if (in_array('', $post)) {
			$_SESSION['message'] = 'Você precisa preencher todos os campos para publicar um artigo.';
			header('HTTP/1.1 303 See Other');
			header('Location: http://localhost:80/login');
			return;
		}
		if ($post) {
			try {
				$stmt = \src\core\Connect::getConn()->prepare('INSERT INTO articles (title, content) VALUES(:t, :c)');
				$stmt->bindValue('t', $post['title']);
				$stmt->bindValue('c', $post['text']);
				$stmt->execute();
				echo "<h2>Cadastro realizado com sucesso!</h2>";
			} catch (PDOException $e) {
				var_dump($e);
			}
		}
	}

	/**
	 * @return void
	 */
	public function articleComment(): void
	{
		$comment = filter_input(INPUT_POST, 'comment');
		$articleId = filter_input(INPUT_POST, 'article_id');
		if (!$comment || !$articleId) {
			$_SESSION['message'] = 'Digite um comentário antes de enviar.';
			header('HTTP/1.1 303 See Other');
			header('Location: http://localhost:80/login');
		}
		if ($comment) {
			/*
			$stmt = \src\core\Connect::getConn()
				->prepare('INSERT INTO comments (user_id, article_id, text) VALUES (:uid, :aid, :t)');
			$stmt->bindValue('uid', $_SESSION['user']->id);
			$stmt->bindValue('aid', $articleId);
			$stmt->bindValue('t', $comment);
			$stmt->execute();
			*/
			echo json_encode([
				'text' => $comment,
				'username' => $_SESSION['user']->name
			]);
			// logica para notificar os usuários
		}
	}

	/**
	 * @return void
	 */
	public function userPosts()
	{
		echo "<h1>Olá! :)</h1>
			<p>Esta paginá apresentará a lista com as suas postagens. Ela ainda não está implementada.</p>
			<p>Por enquanto, a unica coisa que você pode fazer é sair dela.</p>
			<a href=\"http://localhost\">&lt;- Sair</a>";
	}
}
