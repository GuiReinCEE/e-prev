<?php
$head = array(
	'OS',
	'Status',
	'Solicitante',
	'Descrição',
	'Projeto',
	'Dt. Limite'
);

$body = array();

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;

$grid->view_count = false;

$divisao_antiga      = $collection[0]['divisao'];
$divisao_antiga_nome = $collection[0]['nome'];

foreach($collection as $key => $item)
{
	if($item['divisao'] != $divisao_antiga)
	{		
		$grid->body = $body;

		echo form_start_box('default_box'.$divisao_antiga, $divisao_antiga_nome);
			echo $grid->render();
		echo form_end_box('default_box'.$divisao_antiga);

		$body = array();
	}
	
	$body[] = array(
		anchor(site_url('atividade/atividade_solicitacao/index/'.$item['area'].'/'.$item['numero']), $item['numero']),
		'<span class="'.$item['status_label'].'">'.$item['status'].'</span>',
		array($item['solicitante'], 'text-align: left'), 
		array(nl2br($item['descricao']), 'text-align: justify'),
		$item['projeto_nome'],
		$item['data_limite']
	);
	
	$divisao_antiga      = $item['divisao'];
	$divisao_antiga_nome = $item['nome'];
}

$grid->body = $body;

echo form_start_box('default_box'.$divisao_antiga,  $divisao_antiga_nome);
	echo $grid->render();
echo form_end_box('default_box'.$divisao_antiga);
?>