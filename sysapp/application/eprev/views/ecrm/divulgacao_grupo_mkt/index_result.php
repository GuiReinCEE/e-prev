<?php
	$head = array(
		'Descrição',
		'Qt Registro',
		'Tipo',
		'Alteração',
		'Dt Alteração',
		'Inclusão',
		'Dt Inclusão',
		''
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		$body[] = array(
			array(anchor('ecrm/divulgacao_grupo_mkt/cadastro/'.$item['cd_divulgacao_grupo'], $item['ds_divulgacao_grupo']), 'text-align:left;'),
			'<span class="label '.(intval($item['qt_registro']) > 0 ? "label-success" : "label-important").'">'.$item['qt_registro'].'</span>',
			$item['ds_grupo'],
			$item['cd_usuario_alteracao'],
			$item['dt_alteracao'],
			$item['cd_usuario_inclusao'],
			$item['dt_inclusao'],
			'<a href="javascript:void(0);" onclick="excluir('.$item['cd_divulgacao_grupo'].');">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>