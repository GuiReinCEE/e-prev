<?php
	$head = array(
		'Dt. Login',
		'RE',
		'Nome',
		'IP',
		'Navegador',
		'SO',
		'Usuário e-prev',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['dt_login'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome_participante'], 'text-align:left'),
			$item['nr_ip'],
			array($item['ds_agent'], 'text-align:left'),
			array($item['ds_platform'], 'text-align:left'),
			array($item['nome'], 'text-align:left'),
			anchor('servico/autoatendimento_login/acesso/'.$item['cd_login'], '[acesso]')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>