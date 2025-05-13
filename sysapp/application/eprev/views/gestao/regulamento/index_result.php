<?php
	$head = array(
		'Regulamento',
		'CNPB',
		'Ger. Respons�vel',
		'Pub.no Site',
		'Arquivo',
		'Dt. Aprova��o CD',
		'Nr. Ata Aprova��o CD',
		'Dt. Envio PREVIC',
		'Dt. Aprova��o PREVIC',
		'Descri��o Doc. PREVIC',
		'Doc. Aprova��o',
		'Quadros Comparativos'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
		    array(anchor('gestao/regulamento/cadastro/'.$item['cd_regulamento'], $item['ds_regulamento_tipo']), 'text-align:left;'),
		    $item['ds_cnpb'],
		    $item['cd_gerencia_responsavel'],
		    $item['ds_publicado_site'],
		    anchor(base_url().'up/regulamento/'.$item['arquivo'], '[regulamento]', array('target' => '_blank')),
		    anchor('gestao/regulamento/cadastro/'.$item['cd_regulamento'], $item['dt_aprovacao_cd']),
		    anchor('gestao/regulamento/cadastro/'.$item['cd_regulamento'], $item['nr_ata_cd']),
		    anchor('gestao/regulamento/cadastro/'.$item['cd_regulamento'], $item['dt_envio_previc']),
		    anchor('gestao/regulamento/cadastro/'.$item['cd_regulamento'], $item['dt_aprovacao_previc']), 
		    array($item['ds_aprovacao_previc'], 'text-align:left;'),
			(trim($item['arquivo_aprovacao_previc']) != '' ? anchor(base_url().'up/regulamento/'.$item['arquivo_aprovacao_previc'], '[arquivo]', array('target' => '_blank')) : ''),
			(trim($item['arquivo_comparativo']) != '' ? anchor(base_url().'up/regulamento/'.$item['arquivo_comparativo'], '[arquivo]', array('target' => '_blank')) : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>