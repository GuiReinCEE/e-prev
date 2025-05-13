<?php
$body=array();
$head = array( 
	$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, ''
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

$contador_ano_atual = 0;
$a_data = array(0, 0);
$ar_anterior = Array();

foreach($collection as $item)
{
	$a_data = explode("/", $item['mes_referencia']);
	
	if(trim($item['fl_media']) != 'S')
	{
		$link = anchor("indicador_plugin/juridico_par_acoes_jud/detalhe/".$item["cd_juridico_par_acoes_jud"], "editar");
		
		$body[] = array(
			$item['mes_referencia'],
			(trim($item['qt_sem']) != '' ? number_format($item['qt_sem'], 0, ',', '.') : ''),
			(trim($item['qt_novos']) != '' ? number_format($item['qt_novos'], 0, ',', '.') : ''),
			(trim($item['qt_reincidentes']) != '' ? number_format($item['qt_reincidentes'], 0, ',', '.') : ''),
			(trim($item['qt_acoes']) != '' ? number_format($item['qt_acoes'], 0, ',', '.') : ''),
			(trim($item['nr_percentual_reincidentes']) != '' ? number_format($item['nr_percentual_reincidentes'], 2, ',', '.')."%" : ''),
			(trim($item['qt_assistidos']) != '' ? number_format($item['qt_assistidos'], 0, ',', '.') : ''),
			(trim($item['nr_percentual_assistidos_com']) != '' ? number_format($item['nr_percentual_assistidos_com'], 2, ',', '.')."%" : ''),
			(trim($item['nr_meta']) != '' ? number_format($item['nr_meta'], 2, ',', '.')."%" : ''),
			array(nl2br($item['observacao']), 'text-align:left'), 
			$link 
		);		
		
		$contador_ano_atual++;
	}
	else
	{
		$ar_anterior[] = array(
			array("12/".$item['ano_referencia'], 'font-weight:bold;'),
			(trim($item['qt_sem']) != '' ? number_format($item['qt_sem'], 0, ',', '.') : ''),
			(trim($item['qt_novos']) != '' ? number_format($item['qt_novos'], 0, ',', '.') : ''),
			(trim($item['qt_reincidentes']) != '' ? number_format($item['qt_reincidentes'], 0, ',', '.') : ''),
			(trim($item['qt_acoes']) != '' ? number_format($item['qt_acoes'], 0, ',', '.') : ''),
			(trim($item['nr_percentual_reincidentes']) != '' ? number_format($item['nr_percentual_reincidentes'], 2, ',', '.')."%" : ''),
			(trim($item['qt_assistidos']) != '' ? number_format($item['qt_assistidos'], 0, ',', '.') : ''),
			(trim($item['nr_percentual_assistidos_com']) != '' ? number_format($item['nr_percentual_assistidos_com'], 2, ',', '.')."%" : ''),
			(trim($item['nr_meta']) != '' ? number_format($item['nr_meta'], 2, ',', '.')."%" : ''),
			array(nl2br($item['observacao']), 'text-align:left'), 
			"" 
		);		
	}
		

}

foreach($ar_anterior as $item)
{
	$body[] = $item;
}

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>