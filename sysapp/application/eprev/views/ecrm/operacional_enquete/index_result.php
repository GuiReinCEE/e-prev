<?php
$body = Array();
$head = array( 
	'Cód',
	'Pesquisa',
	'Responsável',
	'',
	'Dt Inclusão',
	'Dt Início',
	'Dt Final' 
);

foreach($collection as $item )
{
	$body[] = array( 
		anchor('ecrm/operacional_enquete/cadastro/'.$item["cd_enquete"], $item["cd_enquete"]),
		array(anchor('ecrm/operacional_enquete/cadastro/'.$item["cd_enquete"], $item["titulo"]), 'text-align:left'),
		array($item["nome"], 'text-align:left'),
		(($item['cd_responsavel'] == usuario_id()) ? "<a href='javascript: duplicar(".$item['cd_enquete'].");'>[duplicar]</a>" : ""),
		$item["dt_inclusao"], 
		$item["dt_inicio"], 
		$item["dt_fim"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
