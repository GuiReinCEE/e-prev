<?php
$body=array();
$head = array( 
	'Categoria',
	'Sexo',
	'Idade',
	'Renda',
	'Cidade'
);

/*
tipo_participante, 
sexo,
idade_faixa, 
renda_faixa, 
cidade_faixa
*/

foreach($ar_reg as $ar_item )
{
	$body[] = array(
		$ar_item["tipo_participante"],
		$ar_item["sexo"],
		$ar_item["idade_faixa"],
		$ar_item["renda_faixa"],
		$ar_item["cidade_faixa"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
