<?php
	$head = array(
		'Formulário',
		'Respondente',
		'Tipo Treinamento',
		'Dias Para Envio',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			array(anchor('cadastro/treinamento_colaborador_formulario/cadastro/'.$item['cd_treinamento_colaborador_formulario'], $item['ds_treinamento_colaborador_formulario']), 'text-align: left;'),
			'<span class="'.$item['class_enviar_para'].'">'.$item['enviar_para'].'</span>',
			array(nl2br(implode(br(), $item['tipo'])), 'text-align: left;'),
			$item['nr_dias_envio'],
			anchor('cadastro/treinamento_colaborador_formulario/visualizar/'.$item['cd_treinamento_colaborador_formulario'], '[visualizar]')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>