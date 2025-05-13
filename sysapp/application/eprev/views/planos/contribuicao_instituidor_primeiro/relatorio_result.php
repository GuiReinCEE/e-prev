<?php
$body=array();
$head = array( 
	'Cd. Email',
	'EMP/RE/SEQ',  
	'Nome',
	'Dt Cad',
	'Dt Envio',
	'Situação',
	'Para',
	'Com Cópia',
    'Assunto'
);

foreach($collection as $item)
{
	$body[] = array(
		anchor("ecrm/reenvio_email/index/".$item["cd_email"], $item["cd_email"], "target='_blank'"),
	    $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		$item['dt_envio'],
		$item['dt_email_enviado'],
		(trim($item['dt_email_enviado']) == "" ? '<span style="font-weight: bold; color: blue;">Aguardando Envio</span>' : ($item['fl_retornou'] == "S" ? '<span style="font-weight: bold; color: red;">Retornou</span>' : '<span style="font-weight: bold; color: green;">Normal</span>')),
		array($item["para"],"text-align:left;"),
		array($item["cc"],"text-align:left;"),
		array($item["assunto"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
</div>
<BR><BR><BR>