<main>
	<div class="content" style="text-align: center;">
		<h2>Cadastrar Produto</h2>
		<form method="POST" action="http://localhost/cadastrar">
			<label>
				Nome:
				<input type="text" name="name">
			</label>
			<label>
				Preço:
				<input type="text" name="price">
			</label>
			<input type="submit" value="Cadastrar">
		</form>
	</div>
	<div style="background: gray; width: 1px;"></div>
	<div style="text-align: center;" class="content">
		<h2>Listar Produto</h2>
		<table cellpadding="5">
			<tr style="background: #121212; color: #f1f1f1; letter-spacing: 0.5px;">
				<th>N°</th><th>Nome</th><th>Preço</th>
			</tr>
			<?php
			while (!feof($open)):
				$line = explode(';', fgets($open));
				if (!empty($line) && count($line) > 1):
					$price = (float)str_replace(',', '.', $line[1]);
					$price = 'R$ ' . number_format($price, 2, ',', '.');
					echo <<<TR
						<tr class="pdt-list">
							<td></td><td>{$line[0]}</td><td>{$price}</td>
						</tr>
					TR;
				endif;
			endwhile;
			?>
			<!--
			<tr class="pdt-list">
				<td></td><td>Produto A</td><td>0,00</td>
			</tr>
			-->
		</table>
	</div>
</main>
<?php
$scripts = "<script src=\"http://localhost/views/assets/js/script.js\"></script>";
?>
