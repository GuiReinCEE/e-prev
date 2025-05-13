<?php
	$head = array(
		'Ano',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/demonstrativo_resultado/estrutura/'.$item['cd_demonstrativo_resultado'], $item['nr_ano']),
			anchor('gestao/demonstrativo_resultado/estrutura/'.$item['cd_demonstrativo_resultado'], '[estrutura]').' '.
			anchor('gestao/demonstrativo_resultado/meses/'.$item['cd_demonstrativo_resultado'], '[manutenчуo]').
			(trim($fl_usuario_tipo) == 'G' ? anchor('gestao/demonstrativo_resultado/excluir/'.$item['cd_demonstrativo_resultado'], ' [excluir]') : '')
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>