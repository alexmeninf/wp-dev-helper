jQuery(function ($) {

	/**
	 * Substitui o campo texto para o botão no layout.
	 * Atualmente não existe um tipo 'button' para exibir, então foi feita esse gatilho.
	 */
	$('div[data-name="wpdevhelperAdvanced-delete_revisions"] .acf-input-wrap').html(`
	<button type="button" class="button" data-wpdh-delete-revisions><span class="dashicons dashicons-trash" style="vertical-align: text-bottom;"></span> Iniciar otimização.</button>
	`);

	// Delete revisions in posts
	$('button[data-wpdh-delete-revisions]').on('click', function (e) {
		e.preventDefault();

		if (window.confirm("Tem certeza disso? Isso irá excluir todas as revisões dos seus posts e páginas. Essa ação não pode ser desfeita.")) {

			let btn = $(this);

			$.ajax({
				url: revision_var.url,
				postType: 'POST',
				data: {
					action: 'delete_revision_posts',
					nonce: revision_var.nonce
				},
				beforeSend: function () {
					btn.text('Removendo...');
				}
			}).done(function (data) {
				let res = JSON.parse(data);
				alert(res.message);
			})
				.fail(function (data) {
					let res = JSON.parse(data);
					alert(res.message);
					btn.text('Algo deu errado. Tente novamente mais tarde.');
				})
				.then(function () {
					btn.text('Otimização finalizada!');
				});
		}
	});
});
