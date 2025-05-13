<?php
$body = array();

$head = array( 
	'Empresa',
	'Atividade',
	'Contato',
	'Data',
	'Usúario'
);

foreach( $collection as $item )
{
	$body[] = array( 
		array($item["ds_empresa"],"text-align:left;"), 
		array($item["ds_empresa_contato_atividade"],"text-align:left;"), 
		array(nl2br($item["ds_contato"]),"text-align:justify;"), 
		$item['dt_contato'],
		array($item["ds_usuario"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>