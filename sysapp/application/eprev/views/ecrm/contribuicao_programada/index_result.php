<?php
$body = array();
$head = array( 
	"Cód.",
	"RE",
	"Nome",
	"Valor Atual (R$)",
	"Valor Solicitado (R$)",
	"Dt Solicitação",
	"Dt Início",
	"Dt Cancelado",
	"Dt Confirmação",

);

foreach($collection as $item)
{
	$body[] = array(
		$item["cd_auto_atendimento_contrib_programada"],
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"], "text-align:left;"),
		number_format($item["vl_anterior"],2,",","."),
		number_format($item["vl_valor"],2,",","."),
		$item["dt_inclusao"],
		$item["dt_inicio"],
		'<span class="label label-important">'.$item['dt_cancelado'].'</span>',
		(trim($item["dt_confirmacao"]) == "" ? "[confirmar]" : $item["dt_confirmacao"])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>