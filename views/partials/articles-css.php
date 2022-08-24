<style>
	header {
		margin: 0 auto;
	}
	main {
		height: calc(100vh - 75px);
;
	}
	main div:last-child {
		margin-left: 10px;
	}
	form div {
		margin-bottom: 12px;
	}
	form label {
		width: 100%;
	}
	section.articles {
		max-width: 40%;
		margin: 0 10px;
	}
	.article-item {
		background: rgba(25, 90, 255, 0.3);
		padding: 5px;
		border-radius: 5px;
		margin-bottom: 10px;
	}
	.article-item input {
		border: 1px solid gray;
		border-right: none;
		border-top-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}
	.article-item button {
		border: 1px solid gray;
		border-left: none;
		border-top-left-radius: 0;
		border-bottom-left-radius: 0;
	}
	@media (max-width: 750px) {
		main form {
			max-width: 90%;
			margin: auto;
		}
		main form button {
			display: block;
			margin-left: auto;
		}
		section.articles {
			max-width: 90%;
			margin: auto;
		}
	}
</style>
