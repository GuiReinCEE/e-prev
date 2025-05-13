<?php
$body = array();
$head = array(
	'Código',
	'Descrição',
	'Dt Referência',
	'Acompanhamento',
	'Qt Anexo'
);

foreach ($collection as $item)
{	

	$body[] = array(
		anchor("ecrm/registro_acao_marketing/cadastro/".$item["cd_registro_acao_marketing"], $item["cd_registro_acao_marketing"]),
		array(anchor("ecrm/registro_acao_marketing/cadastro/".$item["cd_registro_acao_marketing"], nl2br($item["ds_registro_acao_marketing"])), "text-align:left;"),
		$item["dt_referencia"],
		(trim($item["acompanhamento"]) != '' ? array(anchor("ecrm/registro_acao_marketing/acompanhamento/".$item["cd_registro_acao_marketing"], nl2br($item["acompanhamento"])), "text-align:justify") : ''),
		anchor("ecrm/registro_acao_marketing/anexo/".$item["cd_registro_acao_marketing"], $item["tl_anexo"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();