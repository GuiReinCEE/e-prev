<?php
$body = array();
$head = array( 
	'Grau',
	'Descrição',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_escala"],
		array(anchor("cadastro/avaliacao_escolaridade/cadastro_escala/".$item["cd_escala"], $item["descricao"]),'text-align:left'),
		'<a href="javascript:void(0);" onclick="excluir(\''.trim($item["cd_escala"]).'\')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>