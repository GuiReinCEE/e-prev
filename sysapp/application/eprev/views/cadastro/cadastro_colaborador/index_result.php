<?php
	$head = array( 
		'Dt. Solicitação',
		'Nome',
		'Tipo',
		'Gerência',
		'Cargo',
		'Dt. Admissão',
		'Status',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{	
		$body[] = array(
			anchor('cadastro/cadastro_colaborador/cadastro/'.$item['cd_cadastro_colaborador'], $item['dt_enviado']),
			array(anchor('cadastro/cadastro_colaborador/cadastro/'.$item['cd_cadastro_colaborador'], $item['ds_nome']), 'text-align:left'),
			'<span class="'.$item['ds_class_tipo'].'">'.$item['ds_tipo'].'</span>',
			$item['cd_gerencia'],
			array($item['nome_cargo'], 'text-align:left'),
			$item['dt_admissao'],
			'<span class="'.$item['ds_status_label'].'">'.$item['ds_status'].'</span>',
			array($item['ds_usuario'], 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo $grid->render();
?>