<?php
	$head = array(
		'Tipo Documento',
		'Dt. Refer�ncia',
		'Caminho',
		'Dt. Altera��o',
		'Usu�rio',
		'Dt. Envio',
		'Usu�rio'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array(anchor('gestao/documento_integra/minhas_cadastro/'.$item['cd_documento_integra_doc_tipo'].'/'.$item['cd_documento_integra'], $item['ds_documento_integra_doc_tipo']), 'text-align:left;'),
			anchor('gestao/documento_integra/minhas_cadastro/'.$item['cd_documento_integra_doc_tipo'].'/'.$item['cd_documento_integra'], $item['dt_referencia_adicionado']),
			$item['ds_caminho_completo'],
			$item['dt_alteracao'],
			array($item['ds_usuario_inclusao'], 'text-align:left;'),
			$item['dt_envio'],
			array($item['ds_usuario_envio'], 'text-align:left;')
			
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>