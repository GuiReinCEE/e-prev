<?php
$body = array();
$head = array( 
	'#', $label_0, '', $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7,  ''
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

$contador_ano_atual = 0;
$contador           = sizeof($collection);
$contador_mes       = 0;
$ultimo_mes         = 0;
$a_data = array();
$referencia = '';

foreach($collection as $key => $item)
{
	$a_data = explode("/", $item['mes_referencia']);

	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_plugin/investimento_rentabilidade_planos_cd_pga/cadastro/'.$item['cd_investimento_rentabilidade_planos_cd_pga'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;
	}

	$contador_mes = $contador--;

	$body[] = array(
		$contador_mes,
		$referencia,
		'CEEE PREV',
		number_format($item['nr_realizado_ceee_mes'], 4, ',','.'),
		number_format($item['nr_projetado_ceee_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_ceee_mes'], 4, ',','.'),
		number_format($item['nr_realizado_ceee_ano'], 4, ',','.'),
		number_format($item['nr_projetado_ceee_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_ceee_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);

	$body[] = array(
		$contador_mes,
		$referencia,
		'CRM PREV',
		number_format($item['nr_realizado_crm_mes'], 4, ',','.'),
		number_format($item['nr_projetado_crm_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_crm_mes'], 4, ',','.'),
		number_format($item['nr_realizado_crm_ano'], 4, ',','.'),
		number_format($item['nr_projetado_crm_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_crm_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);

	$body[] = array(
		$contador_mes,
		$referencia,
		'SENGE PREV',
		number_format($item['nr_realizado_senge_mes'], 4, ',','.'),
		number_format($item['nr_projetado_senge_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_senge_mes'], 4, ',','.'),
		number_format($item['nr_realizado_senge_ano'], 4, ',','.'),
		number_format($item['nr_projetado_senge_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_senge_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);

	$body[] = array(
		$contador_mes,
		$referencia,
		'SINPRO PREV',
		number_format($item['nr_realizado_sinpro_mes'], 4, ',','.'),
		number_format($item['nr_projetado_sinpro_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_sinpro_mes'], 4, ',','.'),
		number_format($item['nr_realizado_sinpro_ano'], 4, ',','.'),
		number_format($item['nr_projetado_sinpro_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_sinpro_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);

	$body[] = array(
		$contador_mes,
		$referencia,
		'FAMÍLIA',
		number_format($item['nr_realizado_familia_mes'], 4, ',','.'),
		number_format($item['nr_projetado_familia_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_familia_mes'], 4, ',','.'),
		number_format($item['nr_realizado_familia_ano'], 4, ',','.'),
		number_format($item['nr_projetado_familia_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_familia_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);

	$body[] = array(
		$contador_mes,
		$referencia,
		'INPELPREV',
		number_format($item['nr_realizado_inpel_mes'], 4, ',','.'),
		number_format($item['nr_projetado_inpel_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_inpel_mes'], 4, ',','.'),
		number_format($item['nr_realizado_inpel_ano'], 4, ',','.'),
		number_format($item['nr_projetado_inpel_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_inpel_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);

	$body[] = array(
		$contador_mes,
		$referencia,
		'PGA',
		number_format($item['nr_realizado_pga_mes'], 4, ',','.'),
		number_format($item['nr_projetado_pga_mes'], 4, ',','.'),
		number_format($item['nr_bechmark_pga_mes'], 4, ',','.'),
		number_format($item['nr_realizado_pga_ano'], 4, ',','.'),
		number_format($item['nr_projetado_pga_ano'], 4, ',','.'),
		number_format($item['nr_bechmark_pga_ano'], 4, ',','.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
?>

<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $a_data[0] ?>"/>
<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>