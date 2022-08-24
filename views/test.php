<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		output {
			display: block;
			max-height: 250px;
			padding: 5px;
		}
	</style>
	<title>WebSocket - teste</title>
</head>
<body>
	<main>
		<div>
			<output></output>

			<select name="room" id="room">
				<optgroup label="Selecionar sala">
					<option disabled="" selected>-- </option>
					<option value="room_1">Sala 1</option>
					<option value="room_2">Sala 2</option>
				</optgroup>
			</select><br>

			<form action="" method="get">
				<input type="text" name="name" id="name" placeholder="Seu nome"><br>
				<textarea name="msg" id="msg" cols="30" rows="10" placeholder="Mensagem..."></textarea><br>
				<button>Enviar</button>
			</form>
		</div>
	</main>
	
	<script src="http://localhost/views/assets/vendor/js/autobahn.js"></script>
	<!-- <script src="http://localhost/views/assets/js/ws-pusher.js"></script> -->
	<script>
		const form = document.querySelector('form'),
			output = document.querySelector('output'),
			select = document.querySelector('select');
		let conn;

		select.addEventListener('change', (e) => {
			room = select.value
			console.log(room);

			const publish = function() {
				conn.subscribe(room, function(topic, data) {
					while (typeof data === 'string') {
						JSON.parse(data);
					}
					console.log(topic);
					console.log(data);
					// console.log('New article published to category "' + topic + '" : ' + data.title);
				});
			};
			const close = function() {
				console.warn('WebSocket connection closed');
			};

			conn = new ab.Session('ws://localhost:8080',
				publish,
				close,
				{'skipSubprotocolCheck': true} //pular VerificaÃ§Ã£o de Subprotocolo
			);
		});

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			
		});
	</script>
</body>
</html>
