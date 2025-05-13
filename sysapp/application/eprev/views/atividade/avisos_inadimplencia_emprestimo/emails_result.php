<?php

$body=array();
$head = array( 
	'Código',
	'Emp/Re/Seq',
	'Nome',
	'Dt Email',
	'Dt Envio',
	'Assunto',
	'Situação',
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
		$item["dt_envio"],
		array($item["assunto"],"text-align:left;"),
		($item["fl_retorno"] == "S" ? "<b style='color:red;'>Retornou</b>" : ($item["dt_envio"] != "" ? "<b style='color:green;'>Normal</b>" : "<b style='color:blue;'>Aguardando envio</b>")),
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