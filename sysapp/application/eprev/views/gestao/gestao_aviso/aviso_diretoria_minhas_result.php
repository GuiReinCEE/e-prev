<?php
	$head = array(
		'Cуd',
		'Descriзгo', 
	   	'Dt Referкncia',
	   	'Dt Inclusгo',
	   	'Solicitante',
	   	'Dt. Prazo',
	   	'Dt. Verificado',
	   	'Usuбrio Verificado',
	   	'Acompanhamento',
	   	''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url("gestao/gestao_aviso/verificar/".$item["cd_gestao_aviso_verificacao"]), $item["cd_gestao_aviso"]),
			array(anchor(site_url("gestao/gestao_aviso/verificar/".$item["cd_gestao_aviso_verificacao"]), $item["ds_descricao"]), "text-align:left;"),
			$item["dt_referencia"],
			$item["dt_inclusao"],
			array($item["usuario_inclusao"], "text-align:left;"),
			$item['dt_referencia'],
			$item['dt_verificacao'],
			array($item['ds_usuario_verificado'], 'text-align:left;'),
			array(nl2br($item["ds_acompanhamento"]), "text-align:justify;"),
			//anchor(site_url("gestao/gestao_aviso/historico/".$item["cd_gestao_aviso"]), '[histуrico]').' '. 
			anchor(site_url("gestao/gestao_aviso/verificar/".$item["cd_gestao_aviso_verificacao"]), '[verificar]') 
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>