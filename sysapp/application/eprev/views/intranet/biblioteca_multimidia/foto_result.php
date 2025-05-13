<?php
$body=array();
$head = array( 
	'#',
	'Descrição',
	'Data',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_fotos"],
		array('<a href="javascript: visualizarFotos('.$item["cd_fotos"].');" title="Clique para ver as fotos">'.$item["ds_titulo"].'</a>',"text-align:left;"),
		$item["dt_data"],
		'<a href="javascript: visualizarFotos('.$item["cd_fotos"].');" title="Clique para ver as fotos">Ver Fotos</a>',
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
