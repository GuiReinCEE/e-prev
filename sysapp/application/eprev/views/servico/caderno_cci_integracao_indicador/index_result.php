<?php
	$head = array(
		'Indicador',
		'Referência'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			array(anchor('servico/caderno_cci_integracao_indicador/cadastro/'.$item['cd_caderno_cci_integracao_indicador'], $item['ds_indicador']), 'text-align: left'),
			array(anchor('servico/caderno_cci_integracao_indicador/cadastro/'.$item['cd_caderno_cci_integracao_indicador'], $item['ds_caderno_cci_integracao_indicador']), 'text-align: left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>