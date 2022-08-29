<?php

namespace src\controllers;

use PDOException;
use \src\core\Connect;
use DateTimeImmutable;
use DateTime;

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
		parent::addData('notifications', Connect::getConn()
			->query('SELECT nt.text, u.name, nt.created_at FROM notifications AS nt JOIN users AS u ON (nt.user_id = u.id) ORDER BY nt.created_at DESC')
			->fetchAll());
		parent::addData('totalNotifications', Connect::getConn()
			->query("SELECT COUNT(id) AS totalNotifications FROM notifications WHERE user_id = {$_SESSION['user']->id} AND opened = 0")->fetch());
	}

	/**
	 * @return void
	 */
	public function articles(): void
	{
		try {
			// all articles
			$stmt = Connect::getConn()->query('SELECT a.id, a.user_id, a.title, a.content FROM articles AS a');
			$articles = $stmt->fetchAll();
			// comments
			$stmt = Connect::getConn()->query('
				SELECT c.id, c.user_id, c.article_id, c.text, u.name FROM comments AS c JOIN users AS u ON u.id = c.user_id
			');
			$comments = $stmt->fetchAll();
			// user articles
			$allArticles = Connect::getConn()->query('SELECT id, title FROM articles WHERE user_id = ' . $_SESSION['user']->id)->fetchAll();
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
				$stmt = Connect::getConn()->prepare('INSERT INTO articles (title, content) VALUES(:t, :c)');
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
			// insert comments
			/*
			$connect = Connect::getConn();
			$connect->prepare('INSERT INTO comments (user_id, article_id, text) VALUES (:uid, :aid, :t)');
			$stmt->bindValue('uid', $_SESSION['user']->id);
			$stmt->bindValue('aid', $articleId);
			$stmt->bindValue('t', $comment);
			$stmt->execute();
			// insert notifications
			$stmt = $connect->prepare('INSERT INTO notifications (user_id, article_id, text, opened, created_at) VALUES (:uid, :aid, :t, :o, :create)');
			$stmt->bindValue('uid', $_SESSION['user']->id);
			$stmt->bindValue('aid', $articleId);
			$stmt->bindValue('t', $comment);
			$stmt->bindValue('o', 0);
			$stmt->bindValue('create', (new DateTimeImmutable())->format('Y-m-d H:i'));
			$stmt->execute();
			*/
			// select articles
			$stmt = Connect::getConn()->prepare('SELECT id, title FROM articles WHERE id = ?');
			$stmt->bindValue(1, $articleId);
			$stmt->execute();
			$title = implode('_', explode(' ', $stmt->fetch()->title)) . "_{$articleId}";
			echo json_encode([
				'text' => $comment,
				'username' => $_SESSION['user']->name,
				'topic' => $title,
				'article_id' => $articleId
			]);
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
