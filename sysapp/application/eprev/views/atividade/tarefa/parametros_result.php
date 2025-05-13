<?php
$body = array();
$head = array( 
	'Nome Campo',
	'Tipo Campo',
	'Ordem',
	''
);	 	 

foreach( $collection as $item )
{
	$body[] = array(
		$item['ds_campo'],
		$item['ds_tipo'],
		$item['nr_ordem'],
		($fl_analista ? '<a href="javascript:void(0);" onclick="excluir_parametros('.$item['cd_tarefas_parametros'].');" >[excluir]</a>' : '')
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();