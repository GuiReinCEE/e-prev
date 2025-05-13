<?php
echo form_start_box( "default_box", "Ações Corretivas Implementadas fora do prazo", true, false, "style='text-align:center;'" );
$head = array(
	'Núm. da NC'
    , 'Dt Abertura NC'
    , 'Dt Limite Apresentar AC'
    , 'Dt Apresentada AC '
    , 'Dt Implementada AC '
    , 'Ger. Resp. Processo'
    , 'Responsável pela NC '
);

$body=array();
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();

foreach( $collection as $item )
{
	$body[] = array(
        $link=anchor("gestao/nc/cadastro/" . $item['cd_nao_conformidade'], $item['cd_nao_conformidade'])
        ,$item['dt_abertura']
        ,$item["dt_proposta"]
        ,$item["dt_prorrogada"]
        ,$item['dt_implementacao']
        ,$item['area_responsavel']
        ,array($item['ds_responsavel'],'style="text-align:left;"')
	);
}

$ar_titulo[] = 'No Prazo : '. $qt_implementada_prazo;
$ar_dado[] = $qt_implementada_prazo;

$ar_titulo[] = 'Fora do Prazo: '. $qt_implementada_fora;
$ar_dado[] = $qt_implementada_fora;

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_corr_impl_fora';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

if(count($ar_dado) != 0)
{
    $ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Ações Corretivas Implementadas');
    echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';
}

echo form_end_box("default_box");
?>