<?php
	$head = array(
		'Tipo Documento',
		'Caminho',
		'Ger�ncia',
		'Respons�veis',
		'Periodicidade',
		'Dt. Refer�ncia'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array(anchor('gestao/documento_integra/cadastro/'.$item['cd_documento_integra_doc_tipo'], $item['ds_documento_integra_doc_tipo']), 'text-align:left;'),
			$item['ds_caminho'],
			$item['cd_gerencia'],
			$item['ds_responsavel'].br().$item['ds_responsavel_2'],
			$item['ds_periodicidade'],
			$item['dt_referencia']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>