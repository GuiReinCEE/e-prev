<?php
$body=array();
$head = array( 
	'Dt Jogo',
	'EMP/RE/SEQ',
	'Nome',
	'Idade',
	'Sexo',
	'Resultado',
	'Tempo'
);

foreach( $collection as $item )
{
	$body[] = array(
	$item["dt_jogo"],
	$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
	array($item["nome_jogador"],"text-align:left;"),
	$item["idade"],
	$item["sexo"],
	$item["qt_acerto"],
	$item["hr_tempo"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
