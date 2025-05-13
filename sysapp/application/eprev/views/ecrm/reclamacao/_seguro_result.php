<?php
$body=array();
$head = array( 
	'Número',
	'RE',
	'Nome',
	'Descrição',
	'Status',
	'Dt Cadastro',
	'Dt Prazo',
	'Dt Encerrado',
	'Acompanhamento',
	'Responsável',
	'Registrada por'
);

foreach( $collection as $item )
{
	$body[] = array(
	
		'<a href="'.site_url('atividade/atividade_solicitacao/index/'.intval($item["numero"])).'">'.$item["numero"].'</a>',
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		array('<a href="'.site_url('atividade/atividade_solicitacao/index/'.intval($item["numero"])).'">'.nl2br($item["descricao"]).'</a>',"text-align:justify;"),
		'<span class="label '.trim($item['status_label']).'">'.$item['ds_status'].'</span>',
		$item["dt_inclusao"],
		'<span class="label '.trim($item['cor_dt_limite']).'">'.$item['dt_limite'].'</span>',
		$item["dt_fim_real"],
		array(nl2br($item["ds_acompanhamento"]),"text-align:justify;"),
		array($item["ds_atendente"],"text-align:left;"),
		array($item["ds_solicitante"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>