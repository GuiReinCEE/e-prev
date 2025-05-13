<?php
echo form_start_box( "default_box", "Ações Corretivas não apresentadas com prazo vencido", true, false, "style='text-align:center;'" );
$head = array(
	'Núm. da NC'
    , 'Dt Abertura NC'
    , 'Dt Limite Apresentar AC'
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
        ,$item["dt_apresenta_limite"]
        ,$item['area_responsavel']
        ,array($item['ds_responsavel'],'style="text-align:left;"')
	);
}

$ar_titulo[] = 'Não Apresentada com Prazo Vencido; '.$qt_ac_nao_apresentada_fora;
$ar_dado[] = $qt_ac_nao_apresentada_fora;

$ar_titulo[] = 'Apresentadas: '. $total;
$ar_dado[] = $total;

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_corr_ven';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

if(count($ar_dado) != 0)
{
    $ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Ações Corretivas não apresentadas com prazo vencido');
    echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';
}

echo form_end_box("default_box");
?>