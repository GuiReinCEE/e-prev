<?php
$body=array();
$head = array(
    'Cod.'
    , 'Item'
	, 'Auditoria'
	, 'Data'
    , 'Constat.'
	, 'Impacto'
	, 'Processo'
    , 'Resp.'
	, 'Gerъncia'
	, 'Situaчуo'
	, 'Acompanhamento'
);

foreach($collection as $item)
{    
    $text = '';
    foreach($item['acompanhamento'] as $item2)
    {
        $text .= nl2br($item2['text']).br(2);
    }
    
	$body[] = array(
		anchor("gestao/iso/cadastro/".$item["cd_pendencia_auditoria_iso"], $item["cd_pendencia_auditoria_iso"]),
        array(anchor("gestao/iso/cadastro/".$item["cd_pendencia_auditoria_iso"], $item["ds_item"]),'style="text-align:justify;"'),

        $item['ds_pendencia_auditoria_iso_tipo'],
		$item["dt_inclusao"],
        $item["nr_contatacao"],
        ($item["fl_impacto"] == 'S' ? 'Sim' : 'Nуo'),
        array($item['procedimento'],'style="text-align:left;"'),
        array($item['nome_usuario'],'style="text-align:left;"'),
        $item['cd_gerencia'],
        ($item['dt_encerrada'] == '' ? array('Aberto', 'color:red; font-weight: bold;') : array('Encerrado', 'color:green; font-weight: bold;')),
        array($text,'text-align:justify;')
        
	);
}

$ar_window = Array(10);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;

echo $grid->render();
?>