<?php
$body=array();
$head = array(
    'Sъmula/Item',
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
    $resposta = '';
    
    if($item['cd_resposta'] == 'AP')
    {
        $resposta = 'Aзгo Preventiva';
    }
    else if($item['cd_resposta'] == 'NC')
    {
        $resposta = 'Nгo Conforminadade';
    }
    else if($item['cd_resposta'] == 'SR')
    {
        $resposta = 'Sem Reflexo';
    }

    if($item['complemento'] != '')
    {
        $resposta .= ': '.$item['complemento'];
    }

    $body[] = array(
		$item["nr_sumula_interventor_item"],
		(
		(trim($item['dt_resposta']) == "")
		? anchor('gestao/sumula_interventor/resposta/'.$item['cd_sumula_interventor_item'], '[responder]')
		: anchor('gestao/sumula_interventor/resposta/'.$item['cd_sumula_interventor_item'], '[ver]')
		),
		array(nl2br($item["descricao"]), "text-align:justify;"),
		array($item["responsavel"], "text-align:left;"),
		array($item["substituto"], "text-align:left;"),
		$item['dt_envio'],
		array($item['dt_limite'],"text-align:center; color:red; font-weight:bold;"),
		array($item['dt_resposta'],"text-align:center; color:blue; font-weight:bold;"),
		array(nl2br($resposta), "text-align:justify;"),
		array($item["nome"], "text-align:left;")
    );
}

$ar_window = Array(16,17);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;
echo $grid->render();

?>