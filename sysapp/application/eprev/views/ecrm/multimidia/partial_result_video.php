<?php
$body=array();
$head = array( 
	'#',
	'Data',
	'Descrição',
	'Local',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("ecrm/multimidia/video_cadastro/".$item["cd_video"], $item["cd_video"]),
		$item["dt_evento"],
		array(anchor("ecrm/multimidia/video_cadastro/".$item["cd_video"], $item["titulo"]), 'text-align:left;'),
		array($item["ds_local"],"text-align:left;"),
		'<a href="javascript: visualizarVideo('.$item["cd_video"].');" title="Clique para assistir o vídeo">Ver Vídeo</a>',
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>