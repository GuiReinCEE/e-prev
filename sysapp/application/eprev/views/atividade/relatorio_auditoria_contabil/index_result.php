<?php
$body = array();
$head = array( 
    'Ano/Número',
    'Descrição',
    'Arquivo',
    'Dt. Cadastro',
    'Dt. Envio GC',
    'Dt enviado SG',
    'Dt Alchemy',
    'Total Não Resp.',
    'Total Resp.',
    'Resp Fora Prazo',
    'Total'
);

foreach( $collection as $item )
{
    $nao_respondidos = intval($item['qt_itens']) - intval($item['qt_respondidos']);
    
    $body[] = array(
        anchor("atividade/relatorio_auditoria_contabil/cadastro/".$item["cd_relatorio_auditoria_contabil"], $item["ano_numero"]),
        array(nl2br($item['ds_relatorio_auditoria_contabil']),'text-align:justify'),
        array(anchor(base_url().'up/relatorio_auditoria_contabil/'.$item['arquivo'], $item["arquivo_nome"], array('target' => "_blank")), "text-align:left;"),
        $item['dt_inclusao'],
        $item['dt_envio_gc'],
        $item['dt_envio_sg'],
        $item['dt_alchemy'],
        array('<span class="label '.(intval($nao_respondidos) > 0 ? 'label-important' : '').'">'.$nao_respondidos.'</span>','tex-align:center;' ,'int'),
        array('<span class="label label-info">'.$item['qt_respondidos'].'</span>','tex-align:center;' ,'int'),
        array('<span class="label '.(intval($item['qt_respondidos_limite']) > 0 ? 'label-important' : '').'">'.$item['qt_respondidos_limite'].'</span>','tex-align:center;' ,'int'),
        array('<span class="label label-success">'.$item['qt_itens'].'</span>','tex-align:center;' ,'int')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>