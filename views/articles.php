<header style="display: block; width: 90%">
	<h1>Artigos</h1><hr>
</header>

<main style="margin-bottom: 5em;">
	<form method="POST" action="http://localhost/artigos">
		<div>
			<label>
				Título<br>
				<input type="text" name="title">
			</label>
		</div>
		<div>
			<label>
				<span>Conteúdo</span><br>
				<textarea type="text" name="text" rows="5" cols="50"></textarea>
			</label>
		</div>
		<button>Cadastrar</button>
	</form>
	<section class="articles">
		<h1 style="text-align: center; border-bottom: 1px dashed rgba(0,0,0, 0.5);">Mais Recentes</h1>
		<?php
			$limitWords = function(string $text, int $limit = 30, string $pointer = '...'): string
			{
				$text = trim($text);
				$words = explode(' ', $text);
				$total = count($words);
				if ($total <= $limit) {
					return $text;
				}
				return implode(' ', array_slice($words, 0, $limit)) . $pointer;
			};
			if (isset($articles)):
				foreach ($articles as $article):
					echo <<<ART
						<article class="article-item">
							<h3 style="color: red;">{$article->title}</h3>
							<div style="color: #323232; ">
								{$limitWords($article->content, 40, ' <a href="">Leia mais...</a>')}
							</div>
					ART;
					echo PHP_EOL . "<div id=\"comments-{$article->id}\">";
					if (isset($comments)):
						foreach ($comments as $comment):
							// logica para exibir os comentários
							echo "<output style=\"display: block;\" id=\"{$comment->id}\">
									<strong>{$comment->name}: </strong><span>{$comment->text}</span>
								</output>";
						endforeach;
					endif;
					echo "</div>";
					$title = implode('_', explode(' ', $article->title)) . "_{$article->id}";
					echo <<<FORM
							<form method="POST" action="http://localhost/comentario-atigos" style="display: flex; margin-top: 15px;" class="form-comment">
								<input type="text" name="comment" value="Digite um comentário"> <!-- placeholder -->
								<input type="hidden" name="article_id" value="{$article->id}">
								<input type="hidden" name="title" value="{$title}">
								<button>enviar</button>
							</form>
						</article>
					FORM;
				endforeach;
			endif;
		?>
	</section>
</main>
<script src="http://localhost/views/assets/vendor/js/autobahn.js"></script>
<script src="http://localhost/views/assets/js/articles.js"></script>
<script>
	let conn;
	let notificationsDiv;
	let notificationsContainer;

	const publish = function () {
	<?php foreach ($allArticles as $article):
		$title = implode('_', explode(' ', $article->title)) . "_{$article->id}"; ?>
		console.log('<?= $title; ?>');
		conn.subscribe('<?= $title; ?>', (topic, data) => {
			data = JSON.parse(data);
			fetch('http://localhost/views/assets/views-components/notify.php')
				.then(res => res.text())
				.then(dataRes => {
					notificationsDiv = document.querySelector('.notifications div');
					notificationsContainer = notifications.parentElement;
					dataRes = dataRes.replace('{{username}}', data.username);
					dataRes = dataRes.replace('{{comment}}', data.text);
					notificationsDiv.innerHTML = dataRes + notificationsDiv.innerHTML;
					
					let notificationsCount = document.querySelector('.notify-count');
					totNotifies = notificationsCount.innerText.length;
					totNotifies = (totNotifies > 0 ? notificationsCount.innerText : 0);
					totNotifies = parseInt(totNotifies);
					notificationsCount.innerText = totNotifies + 1;
				})
				.catch(err => { console.error(err); });
		});
	<?php endforeach; ?>
	};
	const close = function() {
		console.warn('Conexão ao WebSocket encerrada.');
	};

	conn = new ab.Session('ws://127.0.0.1:8080',
		publish,
		close,
		{'skipSubprotocolCheck': true}
	);
</script>
