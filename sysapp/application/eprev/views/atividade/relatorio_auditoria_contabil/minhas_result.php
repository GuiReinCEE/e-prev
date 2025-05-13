<?php
$body = array();
$head = array( 
    'Ano/Número',
    'Descrição',
    'Nr Item',
    'Item',
    'Dt. Envio',
    'Dt. Limite',
    'Responsável',
    'Substituto',
    'Dt. Resposta',    
    'Respondido'
);

foreach( $collection as $item )
{
    $body[] = array(
        anchor("atividade/relatorio_auditoria_contabil/resposta/".$item["cd_relatorio_auditoria_contabil_item"], $item["ano_numero"]),
        array(nl2br($item['ds_relatorio_auditoria_contabil']),'text-align:justify'),
        anchor("atividade/relatorio_auditoria_contabil/resposta/".$item["cd_relatorio_auditoria_contabil_item"], $item["nr_numero_item"]),
        array(anchor("atividade/relatorio_auditoria_contabil/resposta/".$item["cd_relatorio_auditoria_contabil_item"], nl2br($item['ds_relatorio_auditoria_contabil_item'])),'text-align:justify'),
        $item["dt_envio"],
        '<span class="label '.(trim($item["dt_resposta"]) == '' ? 'label-important' : '').'">'.$item["dt_limite"].'</span>',
        array($item['usuario_responsavel'],'text-align:left'),
        array($item['usuario_substituto'],'text-align:left'),
        '<span class="label label-success">'.$item["dt_resposta"].'</span>',   
        array($item['usuario_resposta'],'text-align:left')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>