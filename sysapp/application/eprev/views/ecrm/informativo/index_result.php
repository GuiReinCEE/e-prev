<?php
$body = array();
$head = array( 
	'Data e Hora', 
	'Título', 
	'Editorial'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item['data'], 
		array(anchor("ecrm/informativo/cadastro/" . $item["codigo"], $item['titulo']), 'text-align:left'), 
		array($item['ds_noticia_editorial'], 'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>