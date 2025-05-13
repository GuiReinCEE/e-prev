<?php

$body = array();
$head = array(
	'Ano/Mês',
	'Diretoria',
	'Dt Assinado',	
	'Usuário'
);

foreach ($collection as $item)
{
    $body[] = array(
		anchor("gestao/plano_fiscal_parecer/assinar/".$item["cd_plano_fiscal_parecer"].'/'.$item['cd_diretoria'], $item["nr_ano_mes"]),
		$item['cd_diretoria'],
		$item["dt_inclusao"],
        array($item["nome"],"text-align:left;")
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>