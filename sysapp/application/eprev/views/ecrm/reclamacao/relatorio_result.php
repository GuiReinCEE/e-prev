<?php
	$head = array( 
		'Nъmero',
	   	'Nome',
		'RE',
		'Assunto',
		'Descriзгo',
		'Dt. Prazo Aзгo',	
		'Dt. Prazo Classificaзгo',
		'Dt. Classificaзгo',
		'Ger. Responsбvel',
		'Aзгo',
		'Dt. Retorno'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('ecrm/reclamacao/cadastro/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], $item['cd_reclamacao']),
	      	array($item['nome'], 'text-align:left;'),
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			$item['ds_reclamacao_assunto'],
			array(anchor('ecrm/reclamacao/cadastro/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], nl2br($item['descricao'])), 'text-align:justify;'),
			$item['dt_prazo_acao'],
			$item['dt_prazo'],
			$item['dt_classificacao'],
			$item['cd_divisao'],
			array(nl2br($item['ds_acao']), 'text-align:justify;'),
			$item['dt_retorno']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	//$grid->col_oculta = array(8);
	echo $grid->render();
?>