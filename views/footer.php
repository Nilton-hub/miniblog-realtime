
	<?= ($scripts ?? ''); ?>
	<script>
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
	</script>
	</body>
</html>
