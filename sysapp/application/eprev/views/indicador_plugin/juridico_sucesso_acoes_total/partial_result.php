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

$contador_ano_atual=0;
$contador = count($collection);
$media_ano=array();
$a_data=array(0, 0);
$nr_soma = 0;
$cent_valor1 = 0;
$cent_valor2 = 0;
$cent_valor3 = 0;
$soma = 0;

$nr_total_inicial     = 0;
$nr_total_improcede   = 0;
$nr_total_parcial     = 0;
$nr_total_procede     = 0;
$nr_total_total       = 0;
$nr_total_geral_total = 0;

$nr_tot_valor_1 = 0;
$nr_tot_valor_2 = 0;
$nr_tot_valor_3 = 0;
$nr_tot_valor_4 = 0;

foreach($collection as $item)
{
	$a_data = explode( "/", $item['mes_referencia'] );

	$nr_meta = $item["nr_meta"];
	$nr_percentual_f = "";
	$observacao = $item["observacao"];

	if($item['fl_media'] != 'S')
	{
		$link = anchor("indicador_plugin/juridico_sucesso_acoes_total/detalhe/" . $item["cd_juridico_sucesso_acoes_total"], "editar");

		$referencia = substr($item['mes_referencia'], 0, 2);

		switch ($referencia)
		{
			case '01':
				$referencia = 'Fase Inicial';
				break;
			case '02':
				$referencia = '1º Instância';
				break;
			case '03':
				$referencia = '2º Instância';
				break;
			case '04':
				$referencia = '3º Instância';
				break;
		}

		$nr_total_inicial     += $item["nr_valor_1"];
		$nr_total_improcede   += $item["nr_valor_2"];
		$nr_total_parcial     += $item["nr_valor_3"];
		$nr_total_procede     += $item["nr_valor_4"];		
		
		$nr_valor_1 = $item["nr_valor_1"];
		$nr_valor_2 = $item["nr_valor_2"];
		$nr_valor_3 = $item["nr_valor_3"];
		$nr_valor_4 = $item["nr_valor_4"];

		$nr_soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3 + $nr_valor_4;
		
		$nr_total_total = $nr_soma;
		$nr_total_geral_total += $nr_soma;		

		$soma += $nr_soma;

		if($nr_soma > 0){
			$cent_valor1 = (floatval($nr_valor_1)/floatval($nr_soma) * 100);
			$cent_valor2 = (floatval($nr_valor_2)/floatval($nr_soma) * 100);
			$cent_valor3 = (floatval($nr_valor_3)/floatval($nr_soma) * 100);
			$cent_valor4 = (floatval($nr_valor_4)/floatval($nr_soma) * 100);
		} 
		
		$nr_tot_valor_1 += floatval($cent_valor1);
		$nr_tot_valor_2 += floatval($cent_valor2);
		$nr_tot_valor_3 += floatval($cent_valor3);
		$nr_tot_valor_4 += floatval($cent_valor4);			
		
		$body[] = array(
			 $contador--
			, $referencia
			, ($nr_valor_1!='')?$nr_valor_1:''
			, ($cent_valor1!='')? number_format($cent_valor1,2,',','.').' %':''
			, ($nr_valor_2!='')?$nr_valor_2:''
			, ($cent_valor2!='')? number_format($cent_valor2,2,',','.').' %':''
			, ($nr_valor_3!='')?$nr_valor_3:''
			, ($cent_valor3!='')? number_format($cent_valor3,2,',','.').' %':''
			, ($nr_valor_4!='')?$nr_valor_4:''
			, ($cent_valor4!='')? number_format($cent_valor4,2,',','.').' %':''
			, ($nr_soma!='')?$nr_soma:$nr_percentual_f
			, array(nl2br($observacao), 'text-align:left')
			, $link
		);	

		$contador_ano_atual++;
	}
}

$body[] = array(
	-1,
	"Meta","","","",
    '<b>'.$nr_meta_improcede.'</b>',
    "",
    '<b>'.$nr_meta_parcial.'</b>',
    "",
    '<b>'.$nr_meta_procede.'</b>',	
	"","",""
);

$body[] = array(
	-2,
	"Total",
    '<b>'.(trim($nr_total_inicial)     != "" ? number_format($nr_total_inicial,0,',','.') : '').'</b>',
	"",
    '<b>'.(trim($nr_total_improcede)   != "" ? number_format($nr_total_improcede,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_improcede)   != "" ? number_format((floatval($nr_tot_valor_2)/3),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_total_parcial)     != "" ? number_format($nr_total_parcial,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_parcial)     != "" ? number_format((floatval($nr_tot_valor_3)/3),2,',','.')."%" : '').'</b>',
    '<b>'.(trim($nr_total_procede)     != "" ? number_format($nr_total_procede,0,',','.') : '').'</b>',
    '<b>'.(trim($nr_total_procede)     != "" ? number_format((floatval($nr_tot_valor_4)/3),2,',','.')."%" : '').'</b>',	
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