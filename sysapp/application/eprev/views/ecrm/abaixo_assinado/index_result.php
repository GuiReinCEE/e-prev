<?php
	$head = array( 
        'Ano/NА',
        'Dt. Protocolo',
        'Dt. Limite Retorno',
        'RE',
        'Nome',
        'Descriчуo',
        'Email',
        'Telefone 1',
        'Telefone 2',
        'Aчуo',
        'Dt. Retorno',
        'Retorno'
	);

    $body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('ecrm/abaixo_assinado/cadastro/'.$item['cd_abaixo_assinado'], $item['nr_numero_ano']),
			anchor('ecrm/abaixo_assinado/cadastro/'.$item['cd_abaixo_assinado'], $item['dt_protocolo']),
			$item['dt_limite_retorno'],
			$item['nr_re'],
			array($item['ds_nome'], 'text-align:left;'),
			array(nl2br($item['ds_descricao']), 'text-align:justify;'),
			array($item['ds_email'], 'text-align:left;'),
			$item['ds_telefone_1'],
			$item['ds_telefone_2'],
			array($item['ds_acao'], 'text-align:justify;'),
			$item['dt_retorno'],
			array($item['ds_retorno'], 'text-align:justify;'),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>