<?php
$body=array();
$head = array( 
	'Código',
	'EMP/RE/SEQ',
	'Dt Email',
	'Dt Envio Agendado',
	'Dt Envio',
	'Situação',
	'Assunto',
	'Para',
	'Cc',
	'Cco'	
);

foreach($collection as $item)
{
	$body[] = array(
		anchor("ecrm/reenvio_email/index/".$item["cd_email"], $item["cd_email"], "target='_blank'"),
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		$item["dt_email"],
		$item["dt_schedule_email"],
		$item["dt_envio"],
		($item["fl_retorno"] == "S" ? "<b style='color:red;'>Retornou</b>" : ($item["dt_envio"] != "" ? "<b style='color:green;'>Normal</b>" : "<b style='color:blue;'>Aguardando envio</b>")),
		array($item["assunto"],"text-align:left;"),
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