<?php
	$head = array( 
		'Arquivo',
		'Dt. Geração',
		'Usuário',
		'Total de Registros',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor(base_url().'up/contribuicao_sms/'.$item['arquivo'], $item['arquivo']), 'text-align:left;'),
			$item['dt_inclusao'],
			$item['ds_usuario'],
			'<span class="badge badge-success">'.$item['tl_registro'].'</span>',
			'<a href="javascript:void(0);" onclick="ver_gerado('.$item['cd_contribuicao_relatorio_sms_geracao'].')">[ver registros]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>