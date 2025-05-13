<?php
$body = array();
$head = array( 
	'Código',
	'Nome'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_comp_inst"],
		array(anchor("cadastro/avaliacao_competencia_institucional/cadastro/" . $item["cd_comp_inst"], $item["nome_comp_inst"]),'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>