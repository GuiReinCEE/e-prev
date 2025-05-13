<?php
	$head = array( 
		'Tipo de Reunião'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(nl2br(anchor('gestao/pendencia_gestao/tipo_cadastro/'.$item['cd_reuniao_sistema_gestao_tipo'],($item['ds_reuniao_sistema_gestao_tipo']))),'text-align:left;')
		);
	}
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>