<?php
$body = array();
$head = array( 
	'C�d.',
	'EMP/RE/SEQ',
	'Nome',
	'Dt E-mail',
	'Dt Agenda',
	'Dt Envio',
	'Assunto',
	'Situa��o',
	'Visualizado',
	'Para',
	'Cc',
	'Cco'	
);

foreach($collection as $item)
{
	$body[] = array(
		anchor("ecrm/reenvio_email/index/".$item["cd_email"], $item["cd_email"], "target='_blank'"),
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		$item["dt_email"],
		$item["dt_schedule_email"],
		$item["dt_envio"],
		array($item["assunto"],"text-align:left;"),
		($item["fl_retorno"] == "S" ? '<span class="label label-important">Retornou</span>' : ($item["dt_envio"] != "" ? '<span class="label label-success">Normal</span>' : '<span class="label label-info">Aguardando envio</span>')),
		($item["fl_visualizado"] == "S" ? '<span class="label label-warning">Sim</span>' : '<span class="label">N�o</span>'),
		$item["para"],
		$item["cc"],
		$item["cco"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>