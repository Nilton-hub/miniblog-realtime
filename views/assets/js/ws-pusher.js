var conn = new ab.Session('ws://localhost:8080',
	function() {
		conn.subscribe('kittensCategory', function(topic, data) { //Categoria gatinhos
			// Aqui é onde você adicionaria o novo artigo ao DOM (além do escopo deste tutorial)
			console.log('New article published to category "' + topic + '" : ' + data.title);
		});
	},
	function() {
		console.warn('WebSocket connection closed');
	},
	{'skipSubprotocolCheck': true} //pular Verificação de Subprotocolo
);
