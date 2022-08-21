<main style="">
	<form method="POST" action="http://localhost/login" enctype="multipart/form-data">
		<div>
			<input type="email" name="email" value="<?= $post['email'] ?? $_GET['email'] ?? ''; ?>" placeholder="Email">
		</div>
		<div>
			<input type="password" name="password" placeholder="Senha">
		</div>
		<button>Entrar</button>
	</form>
</main>
