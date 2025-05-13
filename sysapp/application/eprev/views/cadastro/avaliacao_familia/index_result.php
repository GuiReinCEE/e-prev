<?php
$body=array();
$head = array( 
	'Código',
	'Nome'
);

foreach( $collection as $item )
{
	$body[] = array(
	    anchor("cadastro/avaliacao_familia/cadastro/" . $item["cd_familia"], $item["cd_familia"]),
		(trim($item['nome_familia']) != '' ? array(anchor("cadastro/avaliacao_familia/cadastro/" . $item["cd_familia"], $item["nome_familia"]), 'text-align:left') : '' )
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>