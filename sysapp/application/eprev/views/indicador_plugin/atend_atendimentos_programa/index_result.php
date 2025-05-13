<?php
$body = array();
$head = array( 
	'#', $label_0, 'Programa', $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, ''
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

$nr_pessoal_cad_total    = 0;
$nr_pessoal_emp_total    = 0;
$nr_pessoal_inv_total    = 0;
$nr_pessoal_pre_total    = 0;
$nr_pessoal_seg_total    = 0;
$nr_telefonico_cad_total = 0;
$nr_telefonico_emp_total = 0;
$nr_telefonico_inv_total = 0;
$nr_telefonico_pre_total = 0;
$nr_telefonico_seg_total = 0;
$nr_email_cad_total		 = 0;
$nr_email_emp_total		 = 0;
$nr_email_inv_total		 = 0;
$nr_email_pre_total		 = 0;
$nr_email_seg_total		 = 0;
$nr_whatsapp_cad_total		 = 0;
$nr_whatsapp_emp_total		 = 0;
$nr_whatsapp_inv_total		 = 0;
$nr_whatsapp_pre_total		 = 0;
$nr_whatsapp_seg_total		 = 0;

$nr_virtual_cad_total		 = 0;
$nr_virtual_emp_total		 = 0;
$nr_virtual_inv_total		 = 0;
$nr_virtual_pre_total		 = 0;
$nr_virtual_seg_total		 = 0;

$nr_consulta_cad_total		 = 0;
$nr_consulta_emp_total		 = 0;
$nr_consulta_inv_total		 = 0;
$nr_consulta_pre_total		 = 0;
$nr_consulta_seg_total		 = 0;

$nr_total_cad_total		 = 0;
$nr_total_emp_total		 = 0;
$nr_total_inv_total		 = 0;
$nr_total_pre_total		 = 0;
$nr_total_seg_total		 = 0;

$referencia = '';

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

		$link = anchor('indicador_plugin/atend_atendimentos_programa/cadastro/'.$item['cd_atend_atendimentos_programa'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) AND ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];
		
		$nr_pessoal_cad_total      += $item['nr_pessoal_cad'];
		$nr_pessoal_emp_total      += $item['nr_pessoal_emp'];
		$nr_pessoal_inv_total      += $item['nr_pessoal_inv'];
		$nr_pessoal_pre_total      += $item['nr_pessoal_pre'];
		$nr_pessoal_seg_total      += $item['nr_pessoal_seg'];
		$nr_telefonico_cad_total   += $item['nr_telefonico_cad'];
		$nr_telefonico_emp_total   += $item['nr_telefonico_emp'];
		$nr_telefonico_inv_total   += $item['nr_telefonico_inv'];
		$nr_telefonico_pre_total   += $item['nr_telefonico_pre'];
		$nr_telefonico_seg_total   += $item['nr_telefonico_seg'];
		$nr_email_cad_total		   += $item['nr_email_cad'];
		$nr_email_emp_total		   += $item['nr_email_emp'];
		$nr_email_inv_total		   += $item['nr_email_inv'];
		$nr_email_pre_total		   += $item['nr_email_pre'];
		$nr_email_seg_total		   += $item['nr_email_seg'];

		$nr_whatsapp_cad_total		   += $item['nr_whatsapp_cad'];
		$nr_whatsapp_emp_total		   += $item['nr_whatsapp_emp'];
		$nr_whatsapp_inv_total		   += $item['nr_whatsapp_inv'];
		$nr_whatsapp_pre_total		   += $item['nr_whatsapp_pre'];
		$nr_whatsapp_seg_total		   += $item['nr_whatsapp_seg'];

		$nr_virtual_cad_total		   += $item['nr_virtual_cad'];
		$nr_virtual_emp_total		   += $item['nr_virtual_emp'];
		$nr_virtual_inv_total		   += $item['nr_virtual_inv'];
		$nr_virtual_pre_total		   += $item['nr_virtual_pre'];
		$nr_virtual_seg_total		   += $item['nr_virtual_seg'];

		$nr_consulta_cad_total		   += $item['nr_consulta_cad'];
		$nr_consulta_emp_total		   += $item['nr_consulta_emp'];
		$nr_consulta_inv_total		   += $item['nr_consulta_inv'];
		$nr_consulta_pre_total		   += $item['nr_consulta_pre'];
		$nr_consulta_seg_total		   += $item['nr_consulta_seg'];

		$nr_total_cad_total		   += $item['nr_total_cad'];
		$nr_total_emp_total		   += $item['nr_total_emp'];
		$nr_total_inv_total		   += $item['nr_total_inv'];
		$nr_total_pre_total		   += $item['nr_total_pre'];
		$nr_total_seg_total		   += $item['nr_total_seg'];
	}

	$body[] = array(
		$contador,
		$referencia,
		array('Cadastro', 'text-align:left'),
		number_format($item['nr_pessoal_cad'], 0, ',' ,'.'),
		number_format($item['nr_telefonico_cad'], 0, ',' ,'.'),
		number_format($item['nr_email_cad'], 0, ',' ,'.'),
		number_format($item['nr_whatsapp_cad'], 0, ',' ,'.'),
		number_format($item['nr_virtual_cad'], 0, ',' ,'.'),
		number_format($item['nr_consulta_cad'], 0, ',' ,'.'),
		number_format($item['nr_total_cad'], 0, ',' ,'.'),		
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
	
	$body[] = array(
		$contador,
		$referencia,
		array('Empréstimo', 'text-align:left'),
		number_format($item['nr_pessoal_emp'], 0, ',' ,'.'),
		number_format($item['nr_telefonico_emp'], 0, ',' ,'.'),
		number_format($item['nr_email_emp'], 0, ',' ,'.'),
		number_format($item['nr_whatsapp_emp'], 0, ',' ,'.'),
		number_format($item['nr_virtual_emp'], 0, ',' ,'.'),
		number_format($item['nr_consulta_emp'], 0, ',' ,'.'),
		number_format($item['nr_total_emp'], 0, ',' ,'.'),		
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
	
	$body[] = array(
		$contador,
		$referencia,
		array('Investimento', 'text-align:left'),
		number_format($item['nr_pessoal_inv'], 0, ',' ,'.'),
		number_format($item['nr_telefonico_inv'], 0, ',' ,'.'),
		number_format($item['nr_email_inv'], 0, ',' ,'.'),
		number_format($item['nr_whatsapp_inv'], 0, ',' ,'.'),
		number_format($item['nr_virtual_inv'], 0, ',' ,'.'),
		number_format($item['nr_consulta_inv'], 0, ',' ,'.'),
		number_format($item['nr_total_inv'], 0, ',' ,'.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
	
	$body[] = array(
		$contador,
		$referencia,
		array('Previdenciário', 'text-align:left'),
		number_format($item['nr_pessoal_pre'], 0, ',' ,'.'),
		number_format($item['nr_telefonico_pre'], 0, ',' ,'.'),
		number_format($item['nr_email_pre'], 0, ',' ,'.'),
		number_format($item['nr_whatsapp_pre'], 0, ',' ,'.'),
		number_format($item['nr_virtual_pre'], 0, ',' ,'.'),
		number_format($item['nr_consulta_pre'], 0, ',' ,'.'),
		number_format($item['nr_total_pre'], 0, ',' ,'.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
	
	$body[] = array(
		$contador--,
		$referencia,
		array('Seguro', 'text-align:left'),
		number_format($item['nr_pessoal_seg'], 0, ',' ,'.'),
		number_format($item['nr_telefonico_seg'], 0, ',' ,'.'),
		number_format($item['nr_email_seg'], 0, ',' ,'.'),
		number_format($item['nr_whatsapp_seg'], 0, ',' ,'.'),
		number_format($item['nr_virtual_seg'], 0, ',' ,'.'),
		number_format($item['nr_consulta_seg'], 0, ',' ,'.'),
		number_format($item['nr_total_seg'], 0, ',' ,'.'),		
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		array('<b>Cadastro</b>', 'text-align:left'),
		'<b>'.number_format($nr_pessoal_cad_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_telefonico_cad_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_email_cad_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_whatsapp_cad_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_virtual_cad_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_consulta_cad_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_total_cad_total, 0, ',', '.').'</b>',
		'',
		''
	);
	
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		array('<b>Empréstimo</b>', 'text-align:left'),
		'<b>'.number_format($nr_pessoal_emp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_telefonico_emp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_email_emp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_whatsapp_emp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_virtual_emp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_consulta_emp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_total_emp_total, 0, ',', '.').'</b>',
		'',
		''
	);
	
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		array('<b>Investimento</b>', 'text-align:left'),
		'<b>'.number_format($nr_pessoal_inv_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_telefonico_inv_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_email_inv_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_whatsapp_inv_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_virtual_inv_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_consulta_inv_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_total_inv_total, 0, ',', '.').'</b>',
		'',
		''
	);
	
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		array('<b>Previdenciário</b>', 'text-align:left'),
		'<b>'.number_format($nr_pessoal_pre_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_telefonico_pre_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_email_pre_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_whatsapp_pre_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_virtual_pre_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_consulta_pre_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_total_pre_total, 0, ',', '.').'</b>',
		'',
		''
	);
	
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		array('<b>Seguro</b>', 'text-align:left'),
		'<b>'.number_format($nr_pessoal_seg_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_telefonico_seg_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_email_seg_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_whatsapp_seg_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_virtual_seg_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_consulta_seg_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_total_seg_total, 0, ',', '.').'</b>',
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