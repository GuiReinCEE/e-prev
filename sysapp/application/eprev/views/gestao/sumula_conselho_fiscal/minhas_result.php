<?php
$body=array();
$head = array(
    'Sъmula do Conselho/Item',
    '',
    'Descriзгo',
    'Resp.',
    'Subst.',
    'Dt Envio',
	'Dt Limite',
    'Dt Reposta',
    'Resposta',
    'Resposta por'
);

foreach($collection as $item )
{    
    $body[] = array(
		$item["nr_sumula_conselho_fiscal_item"],
		(
		(trim($item['dt_resposta']) == "")
		? anchor('gestao/sumula_conselho_fiscal/resposta/'.$item['cd_sumula_conselho_fiscal_item'], '[responder]')
		: anchor('gestao/sumula_conselho_fiscal/resposta/'.$item['cd_sumula_conselho_fiscal_item'], '[ver]')
		),
		array(nl2br($item["descricao_sumula"]), "text-align:justify;"),
		array($item["responsavel"], "text-align:left;"),
		array($item["substituto"], "text-align:left;"),
		$item['dt_envio'],
		array($item['dt_limite'],"text-align:center; color:red; font-weight:bold;"),
		array($item['dt_resposta'],"text-align:center; color:blue; font-weight:bold;"),
		array(nl2br($item['descricao']), "text-align:justify;"),
		array($item["nome"], "text-align:left;")
    );
}

$ar_window = Array(2,8);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;
echo $grid->render();

?>