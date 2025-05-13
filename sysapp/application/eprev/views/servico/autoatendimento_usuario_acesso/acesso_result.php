<?php
	$head = array(
		'Cód. Login',
		'Dt Login',
		'IP',
		'RE',
		'Nome',
		'Dt Acesso',
		'URI'
	);
	
	$cd_login_anterior = 0;
	$ip_anterior = 0;
	$re_anterior = 0;
	$nome_anterior = '';
	
	$body = array();
	
	foreach($collection as $item)
	{
		$body[] = array(
				($item['cd_login'] != $cd_login_anterior ? $item['cd_login'] : ''),
				$item['dt_login'],
				($item['nr_ip'] != $ip_anterior ? $item['nr_ip'] : ''),
				($item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'] != $re_anterior ? $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'] : ''),
				array(($item['nome'] != $nome_anterior ? $item['nome'] : ''), 'text-align:left'),
				$item['dt_acesso'],
				array($item['ds_uri'], 'text-align:left')
		);
		$cd_login_anterior = $item['cd_login'];
		$ip_anterior 	   = $item['nr_ip'];
		$nome_anterior 	   = $item['nome'];
		$re_anterior	   = $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'];
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>