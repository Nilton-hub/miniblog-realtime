
	<?= ($scripts ?? ''); ?>
	<script>
		// MENU
		const toggleMenu = document.querySelector('#toggle-menu');
		if (toggleMenu){
			toggleMenu.addEventListener('click', () => {
				document.querySelector('.main-nav ul').classList.toggle('active');
			});
		}
		const btnMessage = document.querySelector('div.message button');
		if (btnMessage) {
			btnMessage.addEventListener('click', () => {
				document.querySelector('div.message').remove();
			});
		}

		// NOTIFICATIONS
		/*
		const notifications = document.querySelector('.notifications div');
		const notifyContainer = notifications.parentElement;
		let arrSort = [0, 1, 2, 0, 1, 2, 0, 1, 2, 1];
		let rand = parseInt(Math.random() * 10);
		rand = (arrSort[rand] !== 'undefined' ? rand * arrSort[rand] : rand);

		for (let i = 0; i < rand; i++) {
			notifyContainer.style = '';
			fetch('http://localhost/views/assets/views-components/notify.php')
				.then(res => res.text())
				.then(data => {
					notifications.innerHTML += data;
					let b = notifications.children[i];
					if (b) {
						b.childNodes[1].innerHTML = `Usuário ${i + 1}`;
					}
				})
				.catch(e => { console.log(e); });
		}
		if (rand !== 0 && !isNaN(rand)) {
			document.querySelector('.notify-count').innerText = rand;
		} else {
			notifyContainer.style.display = 'flex';
			notifyContainer.style.alignItems = 'center';
			notifyContainer.style.justifyContent = 'center';
			notifications.innerHTML = '<h3 style="color: gray;">Você não tem novas notificações</h3>';
		}
		*/
		document.querySelector('.notify-btn').addEventListener('click', e => {
			notifyContainer.classList.toggle('active');
			document.querySelector('.notify-count').innerText = '';
		});
	</script>
	</body>
</html>
