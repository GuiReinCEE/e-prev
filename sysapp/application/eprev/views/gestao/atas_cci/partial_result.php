<?php
$body = array();
$head = array(
	'Ano/Mês',
	'Status',
	'Nº Reunião',
	'Dt Reunião',
	'Ata CCI',
	'Súmula CCI',
	'Anexos CCI',
	'Dt Homologado DE',
	'Ata DE',
	'Dt Homologado CD',
	'Ata CD',
	'Pub. Liquid',
	'Pub. E-prev',
	'Dt Atualização',
	'Atualização',
	'Acompanhamento GC',
	'Acompanhamento GIN',
	'Etapa'
);

foreach ($collection as $item)
{
	$total = 0;
	
	$total += (trim($item['fl_ata_cci']) == 'S' ? 1 : 0);
	$total += (trim($item['fl_sumula_cci']) == 'S' ? 1 : 0);
	$total += (trim($item['fl_anexo_cci']) == 'S' ? 1 : 0);
	$total += (trim($item['fl_homologado_diretoria']) == 'S' ? 1 : 0);
	$total += (trim($item['fl_publicado_alchemy']) == 'S' ? 1 : 0);
	$total += (trim($item['fl_publicado_eprev']) == 'S' ? 1 : 0);
	$total += (trim($item['nr_ata_diretoria']) != '' ? 1 : 0);
	
	$percent = (intval($total) * 100) /7;

	$body[] = array(		
		anchor(site_url('gestao/atas_cci/cadastro/'.intval($item['cd_atas_cci'])),$item['ano_mes']),
		progressbar(intval($percent)),
		$item['nr_reuniao'],
		$item['dt_reuniao'],
		(trim($item['fl_ata_cci']) == 'S' ? '<span class="label label-success">Sim</span>'.br(2).$item['dt_ata_cci'] : '<span class="label label-warning">Não</span>'.br(2).$item['dt_ata_cci']),
		(trim($item['fl_sumula_cci']) == 'S' ? '<span class="label label-success">Sim</span>'.br(2).$item['dt_sumula_cci'] : '<span class="label label-warning">Não</span>'.br(2).$item['dt_sumula_cci']),
		(trim($item['fl_anexo_cci']) == 'S' ? '<span class="label label-success">Sim</span>'.br(2).$item['dt_anexo_cci'] : '<span class="label label-warning">Não</span>'.br(2).$item['dt_anexo_cci']),
		$item['dt_homologado_diretoria'],
		'<span class="label label-success">'.$item['nr_ata_diretoria'].'</span>',
		$item['dt_homologado_conselho_fiscal'],
		'<span class="label label-success">'.$item['nr_ata_conselho_fiscal'].'</span>',
		(trim($item['fl_publicado_alchemy']) == 'S' ? '<span class="label label-success">Sim</span>' : '<span class="label label-warning">Não</span>'),
		(trim($item['fl_publicado_eprev']) == 'S' ? '<span class="label label-success">Sim</span>' : '<span class="label label-warning">Não</span>'),
		$item['dt_alteracao'],
		array($item['usuario_alteracao'], 'text-align:left'),
		array(nl2br($item['acompanhamento_gc']), 'text-align:left'),
		array(nl2br($item['acompanhamento_gin']), 'text-align:left'),
		array(nl2br($item['etapa']), 'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
#$grid->col_window = array(15);
echo $grid->render();

?>