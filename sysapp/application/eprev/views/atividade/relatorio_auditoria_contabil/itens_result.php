<?php
$body = array();
$head = array( 
    'Número',
    'Descrição',
    'Responsável',
    'Substituto',
    'Dt. Envio',
    'Dt. Limite',
    'Dt. Resposta',
    'Respondido',
    'Arq. Resposta',
    'Resposta',
    ''
);

foreach( $collection as $item )
{
    $link_arquivo = '';
    
    if(trim($item["arquivo_nome"]) != '')
    {
        $link_arquivo = anchor(base_url().'up/relatorio_auditoria_contabil/'.$item['arquivo'], $item["arquivo_nome"], array('target' => "_blank"));
    }
    
    $body[] = array(
        $item["nr_numero_item"],
        array($item['ds_relatorio_auditoria_contabil_item'],'text-align:justify'),
        array($item['usuario_responsavel'],'text-align:left'),
        array($item['usuario_substituto'],'text-align:left'),
        $item["dt_envio"],
        $item["dt_limite"],
        $item["dt_resposta"],
        array($item['usuario_resposta'],'text-align:left'),
        array($link_arquivo, "text-align:left;"),
        array(nl2br($item['ds_resposta']),'text-align:justify'),
        (trim($item['dt_envio']) == '' ? '<a href="javascript:void(0);" onclick="excluir_item('.intval($item['cd_relatorio_auditoria_contabil_item']).')">[excluir]</a>' : '')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>