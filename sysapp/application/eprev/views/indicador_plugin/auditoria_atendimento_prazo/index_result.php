<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3,$label_4,$label_5, ""
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

$a_data                = array(0, 0);
$contador_ano_atual    = 0;
$contador              = sizeof($collection);
$tl_solicitacoes       = 0;
$tl_respondidos_prazo  = 0;
$ultimo_mes            = 0;
$media_ano             = array();
$nr_meta               = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);

	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_plugin/auditoria_atendimento_prazo/cadastro/'.$item['cd_auditoria_atendimento_prazo'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$tl_solicitacoes += $item['nr_solicitacoes'];

		$tl_respondidos_prazo += $item['nr_respondidos_prazo'];

		$media_ano[] = $item['nr_respondidos'];

		$nr_meta = $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		$item['nr_solicitacoes'],
		$item['nr_respondidos_prazo'],
		number_format($item['nr_respondidos'], 2, ',', '.' ).'%',
		number_format($item['nr_meta'], 2, ',', '.' ).'%', 
		array(nl2br($item['observacao']), "text-align:left;"),
		$link 
	);
}

if(sizeof($media_ano) >0)
{
		
	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'<b>'.$tl_solicitacoes.'</b>', 
		'<b>'.$tl_respondidos_prazo.'</b>',
		'<b>'.(($tl_respondidos_prazo > 0 && $tl_solicitacoes > 0) ? number_format((($tl_respondidos_prazo/$tl_solicitacoes)*100), 2, ',', '.' ) : '').'%'.'</b>', 
		'<b>'.number_format($nr_meta, 2, ',', '.' ).'%'.'</b>', 
		'' ,
		''
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";
?>
<?= $grid->render() ?>