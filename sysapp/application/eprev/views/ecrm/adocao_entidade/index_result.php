<?php
$body = array();
$head = array(
    'Entidade',
	'Tipo',
	'Período',
	'Acompanhamento'
);

foreach ($collection as $item)
{	
	$body[] = array(
		anchor("ecrm/adocao_entidade/acompanhamento/".$item["cd_adocao_entidade"], $item['ds_adocao_entidade']),
		$item['ds_adocao_entidade_tipo'],
		$item["ds_adocao_entidade_periodo"],
		array(nl2br($item['acompanhamento']), 'text-align:justify')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>

