<?php
$head = array( 
	'Tamanho Inicial (Mb)',
	'Tamanho Atual (Mb)',
	'Mйdia Mb/Mкs',
	'Mйdia % Cresc./Mкs'
);

$body[] = array(
	number_format($row['nr_tamanho_inicio'],2, ',', '.'),
	number_format($row['nr_tamanho_atual'],2, ',', '.'),
	number_format($row['nr_tamanho_media'],2, ',', '.'),
	number_format($row['nr_tamanho_cresc'],2, ',', '.')
);


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;

$body = array();
$head = array( 
	'Data',
	'Tamanho (Mb)',
	'% de Crescimento'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item['dt_mes'],
		number_format($item['nr_tamanho'],2, ',', '.'),
		number_format($item['pr_crescimento'],2, ',', '.')
	);
}

$this->load->helper('grid');
$grid2 = new grid();
$grid2->head = $head;
$grid2->body = $body;
$grid2->view_count = false;


$body = array();
$head = array( 
	'Projeзгo',
	'Tamanho (Mb)',
	'Tamanho (Gb)'
);

$body[] = array(
	'5 Anos',
	number_format($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 5)),2, ',', '.'),
	number_format(($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 5)))/1024,2, ',', '.')
);

$body[] = array(
	'10 Anos',
	number_format($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 10)),2, ',', '.'),
	number_format(($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 10)))/1024,2, ',', '.')
);

$body[] = array(
	'15 Anos',
	number_format($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 15)),2, ',', '.'),
	number_format(($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 15)))/1024,2, ',', '.')
);

$body[] = array(
	'20 Anos',
	number_format($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 20)),2, ',', '.'),
	number_format(($row['nr_tamanho_inicio'] + ((($row['nr_tamanho_cresc'] * $row['nr_tamanho_inicio'])/100) * (12 * 20)))/1024,2, ',', '.')
);

$this->load->helper('grid');
$grid3 = new grid();
$grid3->head = $head;
$grid3->body = $body;
$grid3->view_count = false;

echo form_start_box("default_total_box", "Total");
	echo $grid->render();
echo form_end_box("default_total_box");

echo form_start_box("default_mensal_box", "Mensal");
	echo $grid2->render();
echo form_end_box("default_mensal_box");
echo form_start_box("default_projecao_box", "Projeзгo");
	echo $grid3->render();
echo form_end_box("default_projecao_box");

?>