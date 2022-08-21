<header style="display: block; width: 90%">
	<h1>Artigos</h1><hr>
</header>
<main>
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
			$limitWords = function(string $text, int $limit = 30, $pointer = '...') {
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
							echo "<output style=\"display: block;\">
									<strong>{$comment->name}: </strong><span>{$comment->text}</span>
								</output>";
						endforeach;
					endif;
					echo "</div>";
					echo <<<FORM
							<form method="POST" action="http://localhost/comentario-atigos" style="display: flex; margin-top: 15px;" class="form-comment">
								<input type="text" name="comment" placeholder="Digite um comentário">
								<input type="hidden" name="article_id" value="{$article->id}">
								<button>enviar</button>
							</form>
						</article>
					FORM;
				endforeach;
			endif;
		?>
	</section>
</main>
<script>
	const commentComponnet = (id, data) => {
		let output = document.createElement('output');
		output.style.display = 'block';
		let user = document.createElement('strong');
		let comment = document.createElement('span');
		
		user.innerHTML = data.username + ": ";
		comment.innerHTML = data.text;
		output.append(user, comment);
		
		document.querySelector(`div#comments-${id}`).append(output);
		return output;
	};

	const formsComment = document.querySelectorAll('form.form-comment')
		postComment = (e) => {
			e.preventDefault();
			const form = e.target;
			const id = form.article_id.value;
			const formData = new FormData(form);
			fetch(`${form.action}`, {
				method: form.method,
				body: formData
			})
				.then(res => res.json())
				.then(data => {
					console.log(data);
					commentComponnet(id, data);
				});
		};
	
	formsComment.forEach((e) => {
		e.addEventListener('submit', postComment);
	});
</script>
