<?php
$body=array();
$head = array( 
	'#',
	'Data',
	'Descri��o',
	'Local',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_video"],
		$item["dt_evento"],
		'<div style="width: 100%; text-align:left;" onclick="visualizarVideo('.$item["cd_video"].');"><a href="javascript:visualizarVideo('.$item["cd_video"].');" title="Clique para assistir o v�deo">'.$item["titulo"].'</a></div>',
		array($item["ds_local"],"text-align:left;"),
		'<a href="javascript: visualizarVideo('.$item["cd_video"].');" title="Clique para assistir o v�deo">Ver V�deo</a>',
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
