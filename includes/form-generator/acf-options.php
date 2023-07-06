<?php

/**
 * @version 4.0 - This file version
 */

if (function_exists('acf_add_local_field_group')) :

	acf_add_local_field_group(array(
		'key' => 'group_6138f0a5158fb',
		'title' => 'Formulário',
		'fields' => array(
			array(
				'key' => 'field_6138f0cc212c8',
				'label' => 'Campos',
				'name' => 'campos',
				'type' => 'repeater',
				'instructions' => 'Adicione cada campo do seu formulário aqui.',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => 1,
				'max' => 0,
				'layout' => 'row',
				'button_label' => '',
				'sub_fields' => array(
					array(
						'key' => 'field_613a1644eff7f',
						'label' => 'Adicionar novo',
						'name' => 'display',
						'type' => 'radio',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'input' => 'Input',
							'title' => 'Título',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'input',
						'layout' => 'horizontal',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key' => 'field_613a1716ca8d2',
						'label' => 'Título do grupo',
						'name' => 'group_title',
						'type' => 'text',
						'instructions' => 'Adicione um título para um grupo de campos.',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'title',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_de98a6cc7c0b8',
						'label' => 'Exibir descrição?',
						'name' => 'enable_description_group',
						'type' => 'true_false',
						'instructions' => 'Adicione uma descrição abaixo do título do grupo de campos.',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'title',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_348a79dfb987a',
						'label' => 'Descrição do grupo',
						'name' => 'group_description',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_de98a6cc7c0b8',
									'operator' => '==',
									'value' => 1,
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_6138f10cd1613',
						'label' => 'Nome',
						'name' => 'input_name',
						'type' => 'text',
						'instructions' => 'Nome de exibição do campo.',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '!=',
									'value' => 'hidden',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_6138f181d1615',
						'label' => 'Tipo do campo',
						'name' => 'input_type',
						'type' => 'select',
						'instructions' => 'Escolha o modelo de campo que deseja para melhor experiência.',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'file' => 'Arquivo',
							'date' => 'Data',
							'email' => 'E-mail',
							'hidden' => 'Hidden',
							'number' => 'Número',
							'tel' => 'Telefone',
							'text' => 'Texto',
							'textarea' => 'Área de texto',
							'url' => 'URL',
							'radio' => 'Radio',
							'checkbox' => 'Checkbox',
							'select' => 'Seletor',
						),
						'default_value' => 'text',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_6138f33bd1616',
						'label' => 'Rotulo de identificação',
						'name' => 'input_id',
						'type' => 'text',
						'instructions' => 'Defina o valor do atributo <i>ID</i> da input.',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_61fd3f8299c62',
						'label' => 'Valor',
						'name' => 'input_value',
						'type' => 'textarea',
						'instructions' => 'Coloque o valor do campo.<br><br>
							Caso for um <i>select</i> ou <i>radio button</i>, digite cada opção em uma nova linha. <br><br>
							Para mais controle, você pode especificar tanto os valores quanto os rótulos e atributos, como nos exemplos:<br><br>
							vermelho : Vermelho : atributos',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_453bff098aa35',
						'label' => 'Permitir upload de multiplos arquivos?',
						'name' => 'upload_multiple_files',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '==',
									'value' => 'file',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_d47f0b88ac153c',
						'label' => 'Quantidade máxima de arquivos',
						'name' => 'upload_max_files',
						'type' => 'number',
						'instructions' => 'Defina um número máximo de arquivos que podem ser enviados no formulário. Valor padrão: 10.',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '==',
									'value' => 'file',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
								array(
									'field' => 'field_453bff098aa35',
									'operator' => '==',
									'value' => 1,
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 10,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_09f8b98ca3d6e',
						'label' => 'Habilitar UI Switch?',
						'name' => 'enable_ui_switch',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '==',
									'value' => 'checkbox',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '==',
									'value' => 'radio',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_4faa24d0b6f3da',
						'label' => 'Exibir opções em linha?',
						'name' => 'inline_options_ui',
						'type' => 'true_false',
						'instructions' => 'Exiba todas as opções nos espaços disponíveis entre a largura de cada opção, em vez de um embaixo do outro.',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '==',
									'value' => 'checkbox',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '==',
									'value' => 'radio',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_6138f36dd1617',
						'label' => 'Campo obrigatório?',
						'name' => 'is_required',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '!=',
									'value' => 'hidden',
								),
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_613907b251006',
						'label' => 'Classes',
						'name' => 'class_name',
						'type' => 'text',
						'instructions' => 'Acione outras classes CSS na input.',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
							),
						),
						'wrapper' => array(
							'width' => '16.66666666666667',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_613a003cb34ad',
						'label' => 'Atributos',
						'name' => 'attributes',
						'type' => 'text',
						'instructions' => 'Adicione atributos para este campo.',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_613a1644eff7f',
									'operator' => '==',
									'value' => 'input',
								),
								array(
									'field' => 'field_6138f181d1615',
									'operator' => '!=',
									'value' => 'select',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
				),
			),
			array(
				'key' => 'field_9f8bdda146300',
				'label' => 'Localização do formulário',
				'name' => 'form_location',
				'type' => 'text',
				'instructions' => 'Informe em qual página estará localizado este formulrário, para encontrar de forma fácil.',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'Ex: Página de contato',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_6138f81c96b0f',
				'label' => 'Estilo do formulário',
				'name' => 'form_style',
				'type' => 'select',
				'instructions' => 'Escolha o modelo de layout do formulário.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'material-form standard-basic' => 'Sublinhado',
					'material-form translucent-form standard-basic' => 'Sublinhado Translúcido',
					'material-form outlined-basic' => 'Com contorno',
					'material-form translucent-form outlined-basic' => 'Com contorno Translúcido',
					'no-style-input' => 'Estilo comum',
				),
				'default_value' => 'standard-basic',
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_a898bc79340dde5',
				'label' => 'Classes para o formulário',
				'name' => 'custom_form_class',
				'type' => 'text',
				'instructions' => 'Adicione ou removidas as classes principais do formulário.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'd-flex flex-wrap',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_6138f7c479584',
				'label' => 'Texto do botão',
				'name' => 'button_text',
				'type' => 'text',
				'instructions' => 'Texto no botão de envio.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_4d539abf470e',
				'label' => 'Classes para o botão',
				'name' => 'button_class',
				'type' => 'text',
				'instructions' => 'Estilize o botão com outras classes. Padrão <i>btn-theme btn-medium</i>',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'btn-theme btn-medium',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_613913e1db74a',
				'label' => 'AJAX data',
				'name' => 'ajax_data',
				'type' => 'code',
				'instructions' => 'Valores para envio. Por padrão ele envia <i>formData</i>, com os dados já recebidos do formulário.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'mode' => 'javascript',
				'theme' => 'tomorrow-night-bright',
			),
			array(
				'key' => 'field_61715f6f7edd9',
				'label' => 'API URL',
				'name' => 'api_url',
				'type' => 'url',
				'instructions' => 'Faça envio para outras API\'s. Caso esteja vázio, o formulário irá salvar os dados e exibir em Lista de e-mails no painel do admin aqui do site.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array(
				'key' => 'field_545308f9d9612',
				'label' => 'Permitir enviar os dados deste formulário por e-mail?',
				'name' => 'enable_send_email',
				'type' => 'true_false',
				'instructions' => 'Permite enviar um e-mail para o remetente do formulário. Caso esteja desativado, apenas salva a cópia no banco de dados.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_345d0eb89935',
				'label' => 'Assunto do e-mail',
				'name' => 'subject_email',
				'type' => 'text',
				'instructions' => 'Informe o título do assunto que será enviado no e-mail.',
				'required' => 1,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_545308f9d9612',
							'operator' => '==',
							'value' => 1,
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'Ex: Nova mensagem recebida',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_db84a71befcc05',
				'label' => 'Exibir tooltip UI',
				'name' => 'enable_tooltip',
				'type' => 'true_false',
				'instructions' => 'Certifique-se que os arquivos do Bootstrap.js e Pooper.js foram habilitados no código.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => 'Exibido apenas em input e textarea',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_06578aed0544c8bb5',
				'label' => 'Habilitar parâmetros na URL para o formulário.',
				'name' => 'enable_received_parameter',
				'type' => 'true_false',
				'instructions' => 'Use os Rotulo de identificação dos campos para receber valores pela URL pelo método GET, permitindo preencher o formulário. <i>Obs: use o prefixo "f" antes dos nomes dos rotulos nos parâmetros.</i>. Exemplo: phone -> fphone, email -> femail',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'generator_form',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => array(
			0 => 'permalink',
			1 => 'the_content',
			2 => 'excerpt',
			3 => 'discussion',
			4 => 'comments',
			5 => 'revisions',
			6 => 'slug',
			7 => 'author',
			8 => 'format',
			9 => 'page_attributes',
			10 => 'featured_image',
			11 => 'categories',
			12 => 'tags',
			13 => 'send-trackbacks',
		),
		'active' => true,
		'description' => '',
	));

endif;
