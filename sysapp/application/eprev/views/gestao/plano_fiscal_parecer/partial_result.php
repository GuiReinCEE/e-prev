<?php

$body = array();
$head = array(
	'Ano/Mês ',
	'',	
	'Qt Itens',
	'Qt Respondido',
	'Qt Assinado',
	'Qt Não Respondido',
	'Qt Não Assinado',
	'Usuário ',
	'Dt. Inclusão',
    'Dt. Encerrado',
    'Usuário Encerrado'
);

foreach ($collection as $item)
{
    $body[] = array(
		anchor("gestao/plano_fiscal_parecer/cadastro/".$item["cd_plano_fiscal_parecer"], $item["nr_ano_mes"]),
		array(progressbar(((intval($item['qt_assinado']) * 100) / intval($item['qt_itens']))),"text-align:left;"),
		$item['qt_itens'],
		$item['qt_respondidos'],
		$item['qt_assinado'],
		intval($item['qt_itens']) - intval($item['qt_respondidos']),
		intval($item['qt_itens']) - intval($item['qt_assinado']),
		array($item["nome"],"text-align:left;"),
		$item["dt_inclusao"],
        $item["dt_encerra"],
        array($item["usuario_encerrado"],"text-align:left;"),
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>