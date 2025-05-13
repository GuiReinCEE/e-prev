<?php
	$head = array(
		'Dt. Login Erro',
		'RE',
		'Nome',
		'CPF',
		'Erro',
		'Usuário e-prev'
	);
	
	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['dt_inclusao'],
			(intval($item['cd_registro_empregado']) > 0 ? $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'] : ''),
			array($item['nome_participante'], 'text-align:left'),
			$item['cpf'],
			array($item['ds_login_erro'], 'text-align:left'),
			array($item['nome'], 'text-align:left')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>