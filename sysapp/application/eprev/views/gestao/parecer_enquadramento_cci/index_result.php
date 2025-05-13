<?php
$body=array();
$head = array( 
	'Ano/Número',
	'Descrição',
	'Situação',
	'Dt Cadastro',
	'Dt Envio',
	'Dt Limite',
	'Dt Encerrado',
	'Dt. Cancelamento'
);

foreach( $collection as $item )
{
	
	$body[] = array(
		anchor("gestao/parecer_enquadramento_cci/cadastro/".$item["cd_parecer_enquadramento_cci"], $item["nr_ano_numero"]),
		array(anchor("gestao/parecer_enquadramento_cci/cadastro/".$item["cd_parecer_enquadramento_cci"], nl2br($item["descricao"])) ,'text-align:left'),
		'<span class="'.trim($item['cor_situacao']).'">'.$item["situacao"].'</span>',
		$item['dt_inclusao'],
		'<span class="label label-success">'.$item['dt_envio'].'</span>',
		'<span class="label label-important">'.$item['dt_limite'].'</span>',
		'<span class="label">'.$item['dt_encerrado'].'</span>',
		$item['dt_cancelamento']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>