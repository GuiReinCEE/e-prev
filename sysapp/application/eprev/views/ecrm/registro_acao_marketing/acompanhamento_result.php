<?php
$body = array();
$head = array(
	'Dt Inclusão',
	'Usuário',
	'Descrição',
	'Anexo'
);

foreach ($collection as $item)
{	
	$anexo = '';

	foreach($item['anexo'] as $item2)
	{
		$anexo .= anchor(base_url().'up/registro_acao_marketing/'.$item2['arquivo'], $item2['arquivo_nome'] , array('target' => "_blank")).br();
	}

	$body[] = array(
		$item["dt_inclusao"],
		$item["nome"],
		array(nl2br($item["ds_registro_acao_marketing_acompanhamento"]), "text-align:justify"),
		array(nl2br($anexo), "text-align:left")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();