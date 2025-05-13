<?php
$body = array();
$head = array(
	'#',
	'Cуd.',
	'Operaзгo',
	'Data',
	'Descriзгo',
	'Status',
	'Prioridade',
	'Posiзгo',
	'Total',
	'Ativ. Origem',
	'Status Origem'
);

$num=0;
foreach($collection as $item)
{
	$num++;
	$body[] = array(
				$num,
				$item['r_cd_atividade_prioridade'], 
				$item['r_tp_operacao'], 
				$item['r_dt_inclusao'], 
				array($item['r_tp_prioridade'],"text-align:left;"), 
				$item['ds_status_atual'], 
				$item['r_nr_prioridade'], 
				$item['r_nr_posicao'], 
				$item['r_nr_total'], 
				$item['r_cd_atividade_origem'],
				$item['ds_status_atual_origem']
			);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = "prioridade_historico";
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>