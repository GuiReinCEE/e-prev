<?php
	$head = array(
		'Dt. Acesso',
		'Dt. Login',
		'RE',
		'Nome',
		'URI',
		'Usuário e-prev'
	);
	
	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['dt_acesso'],
			$item['dt_login'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome_participante'], 'text-align:left'),
			array($item['ds_uri'], 'text-align:left'),
			array($item['nome'], 'text-align:left')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>