<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, 
	//$label_3, $label_4, 
	$label_5, $label_6, 
	//$label_7, $label_8,
	$label_9, $label_10, '', $label_13, ''
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
$ultimo_mes         = 0;

$referencia = '';

$nr_valor_1            = 0;
$nr_meta               = 0;
$nr_atuarial_projetado = 0;
$nr_inpc               = 0;
$nr_atuarial           = 0;
$nr_rentabilidade_acum = 0;
$nr_meta_acum          = 0;
$nr_inpc_acum          = 0;
$nr_poder_referencia   = 0;
$nr_poder_resultado    = 0;

foreach($collection as $key => $item)
{
	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_plugin/investimento_rentabilidade_carteira/cadastro/'.$item['cd_investimento_rentabilidade_carteira'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		if($nr_valor_1 == 0)
		{
			$nr_valor_1 = ($item["nr_valor_1"]/100)+1;
		}
		else
		{
			$nr_valor_1 *= ($item["nr_valor_1"]/100)+1;
		}

		if($nr_meta == 0)
		{
			$nr_meta = ($item['nr_meta']/100)+1;
		}
		else
		{
			$nr_meta *= ($item['nr_meta']/100)+1;
		}

		if($nr_inpc == 0)
		{
			$nr_inpc = ($item['nr_inpc']/100)+1;
		}
		else
		{
			$nr_inpc *= ($item['nr_inpc']/100)+1;
		}

		if($nr_poder_referencia == 0)
		{
			$nr_poder_referencia = ($item['nr_poder_referencia']/100)+1;
		}
		else
		{
			$nr_poder_referencia *= ($item['nr_poder_referencia']/100)+1;
		}

		$nr_poder_resultado = $item['nr_poder_resultado'];

		$nr_atuarial_projetado = $item['nr_atuarial_projetado'];
		$nr_atuarial           = $item['nr_atuarial'];
		$nr_rentabilidade_acum = $item['nr_rentabilidade_acum'];
		$nr_meta_acum 		   = $item['nr_meta_acum'];
		$nr_inpc_acum          = $item['nr_inpc_acum'];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_valor_1'], 4, ',' ,'.').'%',
		number_format($item['nr_meta'], 4, ',' ,'.').'%',
		//number_format($item['nr_inpc'], 4, ',' ,'.').'%',
		//number_format($item['nr_atuarial'], 4, ',' ,'.').'%',
		number_format($item['nr_rentabilidade_acum'], 4, ',' ,'.').'%',
		number_format($item['nr_meta_acum'], 4, ',' ,'.').'%',
		//number_format($item['nr_inpc_acum'], 4, ',' ,'.').'%',
		//number_format($item['nr_atuarial_projetado'], 4, ',' ,'.').'%',
		number_format($item['nr_poder_referencia'], 4, ',' ,'.').'%',
		number_format($item['nr_poder_resultado'], 4, ',' ,'.').'%',
		$item['ds_tabela'],
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
	
}

if(intval($contador_ano_atual) > 0)
{
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format(($nr_valor_1-1)*100, 4, ',' ,'.').'%</b>',
		'<b>'.number_format(($nr_meta-1)*100, 4, ',', '.').'%</b>',
		//'<b>'.number_format(($nr_inpc-1)*100, 4, ',', '.').'%</b>',
		//'<b>'.number_format($nr_atuarial, 4, ',', '.').'%</b>',
		'<b>'.number_format($nr_rentabilidade_acum, 4, ',', '.').'%</b>',
		'<b>'.number_format($nr_meta_acum, 4, ',', '.').'%</b>',
		//'<b>'.number_format($nr_inpc_acum, 4, ',', '.').'%</b>',
		//'<b>'.number_format($nr_atuarial_projetado, 4, ',', '.').'%</b>',
		'<b>'.number_format(($nr_poder_referencia-1)*100, 4, ',', '.').'%</b>',
		'<b>'.number_format($nr_poder_resultado, 4, ',', '.').'%</b>',
		'',
		'',
		''
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
?>

<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>