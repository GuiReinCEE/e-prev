<?php

$body=array();
$head = array( 
	'Atividade',
	'Dt. Atividade',
	'Dt. Conclusão',
	'Solic/Atend',
	'Gerência',
	'Descrição',
	'Status',
	'Complexidade',
	'Projeto'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item['numero'],
		$item['dt_atividade'],
		$item['dt_conclusao'],
		$item["solicitante"]."<BR><i>".$item["atendente"]."</i>",
		$item["divisao"],
		array("<div style='width:500px;'>" . $item["descricao"]. "</div>",'text-align:justify'),	
		array($item["status_atividade"], 'font-weight:bold; color:'.$item["status_cor"].';'),
		array($item["ds_complexidade"],"text-align:left"),
		array($item["projeto_nome"],"text-align:left")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>