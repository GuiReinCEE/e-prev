<img src="<?= base_url().$grafico?>" border="0">
<BR>
<?php

$ar_win_apresenta = array(
              'width'      => '700',
              'height'     => '500',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
              'screenx'    => '0',
              'screeny'    => '0'
            );

$body = array();
$head = array( 
	"Indicador",
	"Categoria",
	"Peso",
	"JAN<BR>Res<BR>(%)",	"JAN<BR>IGP<BR>(%)",
	"FEV<BR>Res<BR>(%)",	"FEV<BR>IGP<BR>(%)",
	"MAR<BR>Res<BR>(%)",	"MAR<BR>IGP<BR>(%)",
	"ABR<BR>Res<BR>(%)",	"ABR<BR>IGP<BR>(%)",
	"MAI<BR>Res<BR>(%)",	"MAI<BR>IGP<BR>(%)",
	"JUN<BR>Res<BR>(%)",	"JUN<BR>IGP<BR>(%)",
	"JUL<BR>Res<BR>(%)",	"JUL<BR>IGP<BR>(%)",
	"AGO<BR>Res<BR>(%)",	"AGO<BR>IGP<BR>(%)",
	"SET<BR>Res<BR>(%)",	"SET<BR>IGP<BR>(%)",
	"OUT<BR>Res<BR>(%)",	"OUT<BR>IGP<BR>(%)",
	"NOV<BR>Res<BR>(%)",	"NOV<BR>IGP<BR>(%)",
	"DEZ<BR>Res<BR>(%)",	"DEZ<BR>IGP<BR>(%)"
);

$pr_peso_tot = 0;
foreach($collection as $item)
{
	$body[] = array(
		array(anchor_popup("indicador/apresentacao/detalhe/".$item["cd_indicador_tabela"], $item["ds_indicador"]." (".$item["tp_analise"]." melhor)", $ar_win_apresenta), "text-align:left;"),
		array($item["ds_igp_tipo"], "text-align:left;"), 
		array(number_format(floatval($item["pr_peso"]),2,",","."), "text-align:right;"),
		array((trim($item["01_vl_mes_resultado"]) != "" ? number_format(floatval($item["01_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["01_vl_mes_igp"]) != "" ? number_format(floatval($item["01_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["02_vl_mes_resultado"]) != "" ? number_format(floatval($item["02_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["02_vl_mes_igp"]) != "" ? number_format(floatval($item["02_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["03_vl_mes_resultado"]) != "" ? number_format(floatval($item["03_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["03_vl_mes_igp"]) != "" ? number_format(floatval($item["03_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["04_vl_mes_resultado"]) != "" ? number_format(floatval($item["04_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["04_vl_mes_igp"]) != "" ? number_format(floatval($item["04_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["05_vl_mes_resultado"]) != "" ? number_format(floatval($item["05_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["05_vl_mes_igp"]) != "" ? number_format(floatval($item["05_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["06_vl_mes_resultado"]) != "" ? number_format(floatval($item["06_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["06_vl_mes_igp"]) != "" ? number_format(floatval($item["06_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["07_vl_mes_resultado"]) != "" ? number_format(floatval($item["07_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["07_vl_mes_igp"]) != "" ? number_format(floatval($item["07_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["08_vl_mes_resultado"]) != "" ? number_format(floatval($item["08_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["08_vl_mes_igp"]) != "" ? number_format(floatval($item["08_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["09_vl_mes_resultado"]) != "" ? number_format(floatval($item["09_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["09_vl_mes_igp"]) != "" ? number_format(floatval($item["09_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["10_vl_mes_resultado"]) != "" ? number_format(floatval($item["10_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["10_vl_mes_igp"]) != "" ? number_format(floatval($item["10_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["11_vl_mes_resultado"]) != "" ? number_format(floatval($item["11_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["11_vl_mes_igp"]) != "" ? number_format(floatval($item["11_vl_mes_igp"]),2,",",".") : ""), "text-align:right;"),
		array((trim($item["12_vl_mes_resultado"]) != "" ? number_format(floatval($item["12_vl_mes_resultado"]),2,",",".") : ""), "text-align:right;"),     array((trim($item["12_vl_mes_igp"]) != "" ? number_format(floatval($item["12_vl_mes_igp"]),2,",",".") : ""), "text-align:right;")
	); 

	$pr_peso_tot+= floatval($item["pr_peso"]);
}

if(count($collection) > 0)
{
	$body[] = array(
		array("RESULTADO", "text-align:left;font-weight:bold;"),
		"",
		array(number_format(floatval($pr_peso_tot),2,",","."), "text-align:right;font-weight:bold;"),
		
		"",     array((trim($ar_tot["01_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["01_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["02_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["02_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["03_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["03_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["04_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["04_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["05_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["05_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["06_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["06_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["07_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["07_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["08_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["08_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["09_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["09_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["10_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["10_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["11_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["11_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;"),
		"",     array((trim($ar_tot["12_vl_mes_igp"]) != "" ? number_format(floatval($ar_tot["12_vl_mes_igp"]),2,",",".") : ""), "text-align:right;font-weight:bold;")
	);  	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_oculta = $ar_oculta;
echo $grid->render();
?>