<?php
$head = array( 
	'Mês/Ano',
	'Número de Dias',
	'Dt. Encerramento'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		anchor('servico/indisp_sistemas/cadastro/'.$item['cd_indisp_sistemas'], $item['ds_indisp_sistemas']),
		$item['nr_dias'],
		$item['dt_encerramento']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>