<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6,$label_7, $label_8, $label_9, $label_10, ''
);

$ar_janela = array(
	'width'      => '700',
	'height'     => '500',
	'scrollbars' => 'yes',
	'status'     => 'yes',
	'resizable'  => 'yes',
	'screenx'    => '0',
	'screeny'    => '0'
);

echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela)."</div>";

$contador             = count($collection);
$media_ano            = array();
$a_data               = array(0, 0);
$contador_ano_atual   = 0;
$nr_total_inicial     = 0;
$nr_total_improcede   = 0;
$nr_total_parcial     = 0;
$nr_total_procede     = 0;
$nr_total_total       = 0;
$nr_total_geral_total = 0;

foreach($collection as $item)
{
	$a_data = explode( "/", $item['mes_referencia'] );

	if($item['fl_media'] != 'S')
	{
		$link = anchor("indicador_plugin/juridico_sucesso_acoes_juchem_civel/detalhe/" . $item["cd_juridico_sucesso_acoes_juchem_civel"], "editar");

		$referencia = substr($item['mes_referencia'], 0, 2);

		switch (intval($referencia))
		{
			case 1: $referencia = 'Fase Inicial'; break;
			case 2: $referencia = '1º Instância'; break;
			case 3: $referencia = '2º Instância'; break;
			case 4: $referencia = '3º Instância'; break;
		}

		$nr_total_inicial     += $item["nr_inicial"];
		$nr_total_improcede   += $item["nr_improcede"];
		$nr_total_parcial     += $item["nr_parcial"];
		$nr_total_procede     += $item["nr_procede"];
		$nr_total_total       += $item["nr_total"];
		$nr_total_geral_total += $item["nr_total_geral"];		
		
		$body[] = array(
			$contador--,
			$referencia,
			(trim($item["nr_inicial"])     != "" ? number_format($item["nr_inicial"],0,',','.') : ''),
			(trim($item["nr_improcede"])   != "" ? number_format($item["nr_improcede"],0,',','.') : ''),
			(trim($item["pr_improcede"])   != "" ? number_format($item["pr_improcede"],2,',','.')."%" : ''),
			(trim($item["nr_parcial"])     != "" ? number_format($item["nr_parcial"],0,',','.') : ''),
			(trim($item["pr_parcial"])     != "" ? number_format($item["pr_parcial"],2,',','.')."%" : ''),
			(trim($item["nr_procede"])     != "" ? number_format($item["nr_procede"],0,',','.') : ''),
			(trim($item["pr_procede"])     != "" ? number_format($item["pr_procede"],2,',','.')."%" : ''),			
			(trim($item["nr_total"])       != "" ? number_format($item["nr_total"],0,',','.') : ''),
			(trim($item["nr_total_geral"]) != "" ? number_format($item["nr_total_geral"],0,',','.') : ''),
			array($item["observacao"], 'text-align:left'), 
			$link 
		);		
		$contador_ano_atual++;
	}
}

$body[] = array(
	-1,
	"Total",
    '<b>'.(trim($nr_total_inicial)     != "" ? number_format($nr_total_inicial,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_improcede)   != "" ? number_format($nr_total_improcede,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_improcede)   != "" ? number_format((($nr_total_improcede / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_total_parcial)     != "" ? number_format($nr_total_parcial,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_parcial)     != "" ? number_format((($nr_total_parcial / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_total_procede)     != "" ? number_format($nr_total_procede,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_procede)     != "" ? number_format((($nr_total_procede / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100),2,',','.')."%" : '').'</b>',	
	'<b>'.(trim($nr_total_total)       != "" ? number_format($nr_total_total,0,',','.') : '').'</b>',
	'<b>'.(trim($nr_total_geral_total) != "" ? number_format($nr_total_geral_total,0,',','.') : '').'</b>',
	"", 
	""
);	


echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_oculta = Array(0);
echo $grid->render();
?>