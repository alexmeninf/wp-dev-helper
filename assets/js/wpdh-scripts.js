jQuery(function ($) {
	/*----------  Fill in new type post entries  ----------*/
	$('#acf-field_5889dd80bc33c').on('blur', function () {
		var singularName = $("#acf-field_5889dd80bc33c").val(); //Get singular name
		var pluralName = singularName; // Set initial plural name

		// Is Language pt-BR
		if ($('html').attr('lang') == 'pt-BR') {
			if (singularName[singularName.length - 1] != 's') {
				if (singularName.toLowerCase() == 'pão') {
					pluralName = 'pães';
				} else if (singularName.toLowerCase() == 'mãe') {
					pluralName = 'mães';
				} else if (singularName.toLowerCase() == 'cão') {
					pluralName = 'cães';
				} else if (singularName.toLowerCase() == 'guardião') {
					pluralName = 'guardiães';
				} else if (singularName.toLowerCase() == 'banner') {
					pluralName = 'Banners';
				} else if (singularName[singularName.length - 1] == 'm') {
					pluralName = singularName.slice(0, -1) + 'ns';
				} else if (singularName.substr(-2) == 'ão') {
					pluralName = singularName.slice(0, -2) + 'ões';
				} else if (singularName.substr(-2) == 'ã') {
					pluralName = singularName.slice(0, -1) + 'ães';
				} else if ((singularName[singularName.length - 1] == 'r' || singularName[singularName.length - 1] == 'z')) {
					pluralName = singularName + 'es';
				} else {
					pluralName = singularName + 's';
				}
			}
			// Set Plural Name
			$("#acf-field_5889de236a588").val(pluralName);
			// Set Labels
			if ($('#acf-field_5889df8ba111b').val() == '') { $('#acf-field_5889df8ba111b').val(pluralName); } else { $('#acf-field_5889df8ba111b').val(pluralName); } // Menu name
			if ($('#acf-field_5889e03aa111c').val() == '') { $('#acf-field_5889e03aa111c').val(pluralName); } else { $('#acf-field_5889e03aa111c').val(pluralName); } // Menu name
			if ($('#acf-field_5889e0d1a1120').val() == '') { $('#acf-field_5889e0d1a1120').val('Todos os ' + pluralName); } else { $('#acf-field_5889e0d1a1120').val('Todos os ' + pluralName); } // All Items
			if ($('#acf-field_5889e106a1121').val() == '') { $('#acf-field_5889e106a1121').val('Adicionar Novo ' + singularName); } else { $('#acf-field_5889e106a1121').val('Adicionar Novo ' + singularName); } // Add New Item
			if ($('#acf-field_5889e119a1122').val() == '') { $('#acf-field_5889e119a1122').val('Adicionar Novo'); } else { $('#acf-field_5889e119a1122').val('Adicionar Novo'); } // Add New
			if ($('#acf-field_5889e26ebd359').val() == '') { $('#acf-field_5889e26ebd359').val('Novo ' + singularName); } else { $('#acf-field_5889e26ebd359').val('Novo ' + singularName); } // New Item
			if ($('#acf-field_5889e532bd35a').val() == '') { $('#acf-field_5889e532bd35a').val('Editar ' + singularName); } else { $('#acf-field_5889e532bd35a').val('Editar ' + singularName); } // Edit Item
			if ($('#acf-field_5889e555bd35b').val() == '') { $('#acf-field_5889e555bd35b').val('Atualizar ' + singularName); } else { $('#acf-field_5889e555bd35b').val('Atualizar ' + singularName); } // Update Item
			if ($('#acf-field_5889e585bd35c').val() == '') { $('#acf-field_5889e585bd35c').val('Ver ' + singularName); } else { $('#acf-field_5889e585bd35c').val('Ver ' + singularName); } // View Item
			if ($('#acf-field_5889e5a3bd35d').val() == '') { $('#acf-field_5889e5a3bd35d').val('Ver ' + pluralName); } else { $('#acf-field_5889e5a3bd35d').val('Ver ' + pluralName); } // View Items
			if ($('#acf-field_5889e5c7bd35e').val() == '') { $('#acf-field_5889e5c7bd35e').val('Pesquisar ' + pluralName); } else { $('#acf-field_5889e5c7bd35e').val('Pesquisar ' + pluralName); } // Search Item
			if ($('#acf-field_5889e5e4bd35f').val() == '') { $('#acf-field_5889e5e4bd35f').val('Nada Encontrado'); } else { $('#acf-field_5889e5e4bd35f').val('Nada Encontrado'); } // Not Found
			if ($('#acf-field_5889e5fbbd360').val() == '') { $('#acf-field_5889e5fbbd360').val('Nada Encontrado na Lixeira'); } else { $('#acf-field_5889e5fbbd360').val('Nada Encontrado na Lixeira'); } // Not Found in Trash
			if ($('#acf-field_5889e690bd365').val() == '') { $('#acf-field_5889e690bd365').val('Inserir no ' + singularName); } else { $('#acf-field_5889e690bd365').val('Inserir no ' + singularName); } // Insert into item
			if ($('#acf-field_5889e6c9bd367').val() == '') { $('#acf-field_5889e6c9bd367').val('Lista de ' + pluralName); } else { $('#acf-field_5889e6c9bd367').val('Lista de ' + pluralName); } // Items list

			// Is Language en-US
		} else {
			if (singularName.substr(-2) == 'ss' || singularName.substr(-2) == 'ch' || singularName.substr(-2) == 'sh') {
				pluralName = singularName + 'es';
			} else if (singularName[singularName.length - 1] == 's' || singularName[singularName.length - 1] == 'x' || singularName[singularName.length - 1] == 'z' || singularName[singularName.length - 1] == 'o') {
				pluralName = singularName + 'es';
			} else if (singularName[singularName.length - 1] == 'y') {
				pluralName = singularName.slice(0, -1) + 'ies';
			} else if (singularName.substr(-2) == 'fe') {
				pluralName = singularName.slice(0, -2) + 'ves';
			} else if (singularName[singularName.length - 1] == 'f') {
				pluralName = singularName.slice(0, -2) + 'ves';
			}
			// Set Plural Name
			$("#acf-field_5889de236a588").val(pluralName);
			// Set Labels
			if ($('#acf-field_5889df8ba111b').val() == '') { $('#acf-field_5889df8ba111b').val(pluralName); } else { $('#acf-field_5889df8ba111b').val(pluralName); } // Menu name
			if ($('#acf-field_5889e0d1a1120').val() == '') { $('#acf-field_5889e0d1a1120').val('All ' + pluralName); } else { $('#acf-field_5889e0d1a1120').val('All ' + pluralName); } // All Items
			if ($('#acf-field_5889e106a1121').val() == '') { $('#acf-field_5889e106a1121').val('Add New ' + singularName); } else { $('#acf-field_5889e106a1121').val('Add New ' + singularName); } // Add New Item
			if ($('#acf-field_5889e119a1122').val() == '') { $('#acf-field_5889e119a1122').val('Add New'); } else { $('#acf-field_5889e119a1122').val('Add New'); } // Add New
			if ($('#acf-field_5889e26ebd359').val() == '') { $('#acf-field_5889e26ebd359').val('New ' + singularName); } else { $('#acf-field_5889e26ebd359').val('New ' + singularName); } // New Item
			if ($('#acf-field_5889e532bd35a').val() == '') { $('#acf-field_5889e532bd35a').val('Edit ' + singularName); } else { $('#acf-field_5889e532bd35a').val('Edit ' + singularName); } // Edit Item
			if ($('#acf-field_5889e555bd35b').val() == '') { $('#acf-field_5889e555bd35b').val('Update ' + singularName); } else { $('#acf-field_5889e555bd35b').val('Update ' + singularName); } // Update Item
			if ($('#acf-field_5889e585bd35c').val() == '') { $('#acf-field_5889e585bd35c').val('View ' + singularName); } else { $('#acf-field_5889e585bd35c').val('View ' + singularName); } // View Item
			if ($('#acf-field_5889e5a3bd35d').val() == '') { $('#acf-field_5889e5a3bd35d').val('View ' + pluralName); } else { $('#acf-field_5889e5a3bd35d').val('View ' + pluralName); } // View Items
			if ($('#acf-field_5889e5c7bd35e').val() == '') { $('#acf-field_5889e5c7bd35e').val('Search ' + pluralName); } else { $('#acf-field_5889e5c7bd35e').val('Search ' + pluralName); } // Search Item
			if ($('#acf-field_5889e5e4bd35f').val() == '') { $('#acf-field_5889e5e4bd35f').val('Not Found'); } else { $('#acf-field_5889e5e4bd35f').val('Not Found'); } // Not Found
			if ($('#acf-field_5889e5fbbd360').val() == '') { $('#acf-field_5889e5fbbd360').val('Not Found in Trash'); } else { $('#acf-field_5889e5fbbd360').val('Not Found in Trash'); } // Not Found in Trash
			if ($('#acf-field_5889e690bd365').val() == '') { $('#acf-field_5889e690bd365').val('Insert into ' + singularName); } else { $('#acf-field_5889e690bd365').val('Insert into ' + singularName); } // Insert into item
			if ($('#acf-field_5889e6c9bd367').val() == '') { $('#acf-field_5889e6c9bd367').val(pluralName + ' list'); } else { $('#acf-field_5889e6c9bd367').val(pluralName + ' list'); } // Items list
		}
	});

	// Enable swtich button
	$('.acf-input input[type=checkbox], .form-table input[type=checkbox]').addClass('switch');

	/**
	 * Substitui o campo texto para o botão no layout.
	 * Atualmente não existe um tipo 'button' para exibir, então foi feita esse gatilho.
	 */
	$('div[data-name="wpdevhelperAdvanced-delete_revisions"] .acf-input-wrap').html(`
		 <button type="button" class="button" data-wpdh-delete-revisions><span class="dashicons dashicons-trash" style="vertical-align: text-bottom;"></span> Iniciar otimização.</button>
		 `);

	$('div[data-name="pwa-optimize_files"] .acf-input-wrap').html(`
		 <button type="button" class="button" data-wpdh-pwa-optimize_files>Iniciar otimização.</button>
		 `);

	// Delete revisions in posts
	$('button[data-wpdh-delete-revisions]').on('click', function (e) {
		e.preventDefault();

		if (window.confirm("Tem certeza disso? Isso irá excluir todas as revisões dos seus posts e páginas. Essa ação não pode ser desfeita.")) {

			let btn = $(this);

			$.ajax({
				url: wpdh_ajax_obj.url,
				postType: 'POST',
				data: {
					action: 'delete_revision_posts',
					nonce: wpdh_ajax_obj.nonce_revision
				},
				beforeSend: function () {
					btn.text('Removendo...');
				}
			}).done(function (data) {console.log(data)
				let res = JSON.parse(data);
				alert(res.message);
			})
				.fail(function (data) {console.log(data)
					let res = JSON.parse(data);
					alert(res.message);
					btn.text('Algo deu errado. Tente novamente mais tarde.');
				})
				.then(function () {
					btn.text('Otimização finalizada!');
				});
		}
	});

	// Gerar arquivos de cache
	$('button[data-wpdh-pwa-optimize_files]').on('click', function (e) {
		e.preventDefault();

		let btn = $(this);

		$.ajax({
			url: wpdh_ajax_obj.url,
			postType: 'POST',
			data: {
				action: 'regenerate_cache_files',
				nonce: wpdh_ajax_obj.nonce_pwa
			},
			beforeSend: function () {
				btn.text('Gerando lista de arquivos...');
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
	});
});
