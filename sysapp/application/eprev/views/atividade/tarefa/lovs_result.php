<?php
$body = array();
$head = array( 
	'Seq',
	'Tabela',
	'Campo Origem',
	'Campo Destino',
	''
);	 	 

foreach( $collection as $item )
{
	$body[] = array(
		$item['ds_seq'],
		$item['ds_tabela'],
		$item['ds_campo_ori'],
		$item['ds_campo_des'],
		($fl_analista ? '<a href="javascript:void(0);" onclick="excluir_lovs('.$item['cd_tarefas_lovs'].');" >[excluir]</a>' : '')
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();