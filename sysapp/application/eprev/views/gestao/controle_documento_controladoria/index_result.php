<?php
	$head = array( 
		'Tipo Documento',
		'Arquivo',
		'Dt. Atualizaчуo',
		'Dt. Referъncia',
		'Descriчуo',
		'Dt. Envio'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor('gestao/controle_documento_controladoria/cadastro/'.$item['cd_controle_documento_controladoria_tipo'],$item['ds_controle_documento_controladoria_tipo']), 'text-align:left;'),
			array(anchor(base_url().'up/controle_documento_controladoria/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			$item['dt_inclusao'],
			$item['dt_referencia'],
			array(nl2br($item['ds_controle_documento_controladoria']), 'text-align:left;'),
			$item['dt_envio']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>