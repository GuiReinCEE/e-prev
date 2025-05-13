<?php
$body = array();
$head = array( 
	'Código',
	'Nome',
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_comp_espec"],
		array(anchor("cadastro/avaliacao_competencia_especifica/cadastro/" . $item["cd_comp_espec"], $item["nome_comp_espec"]),'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>