<?php
$body = array();
$head = array( 
	'Código',
	'Nome'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_responsabilidade"],
		array(anchor("cadastro/avaliacao_responsabilidade/cadastro/" . $item["cd_responsabilidade"], $item["nome_responsabilidade"]),'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>