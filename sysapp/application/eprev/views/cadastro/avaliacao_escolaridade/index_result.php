<?php
$body = array();
$head = array( 
	'Código',
	'Nome',
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_escolaridade"],
		array(anchor("cadastro/avaliacao_escolaridade/cadastro/" . $item["cd_escolaridade"], $item["nome_escolaridade"]),'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>