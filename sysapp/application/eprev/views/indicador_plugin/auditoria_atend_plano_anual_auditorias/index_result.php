<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
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

echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresentação]', $ar_janela)."</div>";

$contador_ano_atual = 0;
$contador           = sizeof($collection);
$a_data             = array(0, 0);

$nr_auditoria_prevista  = 0;
$nr_auditoria_realizada = 0;
$nr_atendimento         = 100;
$nr_meta                = 0;
$ano_referencia         = '';

foreach ($collection as $key => $item)
{
	$a_data = explode('/', $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = 'Resultado de '.$item['ano_referencia'];
	}
	else
	{
		$link = anchor('indicador_plugin/auditoria_atend_plano_anual_auditorias/cadastro/'.$item['cd_auditoria_atend_plano_anual_auditorias'], 'editar');

		$referencia = $item['mes_referencia'];
	}
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$nr_auditoria_prevista  += $item['nr_auditoria_prevista'];
		$nr_auditoria_realizada += $item['nr_auditoria_realizada'];
		$nr_meta                = $item['nr_meta'];
		$ano_referencia         = $item['ano_referencia'];

		$contador_ano_atual++;
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		$item['nr_auditoria_prevista'],
		$item['nr_auditoria_realizada'],
		number_format($item['nr_atendimento'], 2, ',', '.').' %',
		number_format($item['nr_meta'], 2, ',', '.').' %',
		array($item['ds_observacao'], 'text-align:"justify"'), 
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	if($nr_auditoria_prevista > 0)
	{
		$nr_atendimento = ($nr_auditoria_realizada / $nr_auditoria_prevista) * 100;
	}

	$body[] = array(
		0, 
		'<b>Resultado de '.$ano_referencia.'</b>', 
		$nr_auditoria_prevista,
		$nr_auditoria_realizada, 
		'<big><b>'.number_format($nr_atendimento, 2, ',', '.').' %</b></big>', 
		'<big><b>'.number_format($nr_meta, 2, ',', '.').' %</b></big>', 
		'', 
		''
	);
}

echo '<input type="hidden" id="mes_input" name="mes_input" value="'.$a_data[0].'" />';
echo '<input type="hidden" id="contador_input" name="contador_input" value="'.$contador_ano_atual.'" />';

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

?>