<?php
$body = array();
$head = array( 
	'Cod. Aval.',
	'Cod. Eletro',
	'Empresa',
	'Serviço',
	'Resultado (%)',
	'Gestor Contrato',
	'Qt Avaliador',
	'Dt Início',
	'Dt Fim',
	'Dt Limite',
	'Dt Oracle',
	
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor('cadastro/contrato_avaliacao/avaliacao/'.$item["cd_contrato_avaliacao"],$item["cd_contrato_avaliacao"]),
		$item["seq_contrato"],
		array(anchor('cadastro/contrato_avaliacao/avaliacao/'.$item["cd_contrato_avaliacao"],$item["ds_empresa"]), 'text-align:left'),
		array($item["ds_servico"], 'text-align:left'),
		$item["vl_resultado"],
		$item["gestor_contrato"],
		$item["qt_avaliador"],
		$item["dt_inicio_avaliacao"],
		$item["dt_fim_avaliacao"],
		$item["dt_limite_avaliacao"],
		$item["dt_integracao_oracle"]
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>