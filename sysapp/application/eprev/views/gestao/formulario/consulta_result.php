<?php
$body=array();
$head = array( 
	'Cуdigo',
	'Descriзгo'
);

foreach( $collection as $item )
{
	
	$body[] = array(
		(trim($item['arquivo_nome']) != '' ? anchor(base_url().'up/cadastro_formulario/'.$item['arquivo'], $item["nr_formulario"] , array('target' => "_blank")) : $item["nr_formulario"]),
		 array( (trim($item['arquivo_nome']) != '' ? anchor(base_url().'up/cadastro_formulario/'.$item['arquivo'], nl2br($item["ds_formulario"]) , array('target' => "_blank")) : nl2br($item["ds_formulario"])), "text-align:left;")
		 
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>