const form = document.querySelector('form'),
	  table = document.querySelector('table'),
	  realForamter = (num) => {
		let formater = Intl.NumberFormat('pt-BR', {
			style: 'currency',
			currency: 'BRL'
		});
		return formater.format(num.replace(',', '.'));
	};

try {
	const ws = new WebSocket('ws://localhost:3000/')	
	ws.addEventListener('message', (e) => {
		data = JSON.parse(e.data);
		const price = realForamter(data.price);
		table.innerHTML += `<tr class="pdt-list">
								<td></td><td>${data.name}</td><td>${price}</td>
							</tr>`;
	});
	//ws.addEventListener('open', (e) => {});
} catch (error) {
	console.error('Nenhuma conexão websocket disponível na porta 3000.');
}

form.addEventListener('submit', (e) => {
	e.preventDefault();
	const formData = new FormData(form);
	fetch(`${form.getAttribute('action')}`, {
		method: form.getAttribute('method'),
		body: formData
	})
		.then(data => data.text() )
		.then(res => {
			if (ws) {
				ws.send(res);
			}
			const price = realForamter(form.price.value);
			table.innerHTML += `<tr class="pdt-list">
							<td></td><td>${form.name.value}</td><td>${price}</td>
						</tr>`;
		})
		.catch(error => { console.error(error); });
});

