<?php
$body = array();
$head = array(
	'Dt Inclusão',
	'Usuário',
	'Arquivo',
	''
);

foreach ($collection as $item)
{	

	$body[] = array(
		$item["dt_inclusao"],
		$item["nome"],
		array(anchor(base_url().'up/registro_acao_marketing/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_registro_acao_marketing_anexo'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();