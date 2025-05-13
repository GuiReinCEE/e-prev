<?php
$head = array( 
	'Número da Ata',
	'Local',
	'Dt. Reunião',
	'Dt. Reunião Encerramento',
	'Dt. Encerramento',
	'Usuário',
	'Total de Itens',
	'Total de Itens c/ Recomendações',
	'Total de Itens Removidos',
	''
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		anchor('gestao/pauta_cci/assunto/'.$item['cd_pauta_cci'], $item['nr_pauta_cci']),
		array($item['ds_local'], "text-align:left;"),
		anchor('gestao/pauta_cci/assunto/'.$item['cd_pauta_cci'], $item['dt_pauta_cci']),
		$item['dt_pauta_cci_fim'],
		$item['dt_aprovacao'],
		$item['nome'],
		'<label class="badge badge-info">'.$item['qt_item'].'</label>',
		'<label class="badge badge-success">'.$item['qt_item_recomendado'].'</label>',
		'<label class="badge badge-warning">'.$item['qt_item_removido'].'</label>',
		(($item['qt_item']) != 0 ? anchor('gestao/pauta_cci/pauta/'.$item['cd_pauta_cci'], "[Pauta]") : '')

	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>