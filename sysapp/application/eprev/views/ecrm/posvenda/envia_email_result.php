<?php
$body=array();
$head = array( 
	'',
	'Dt Cadastro',
	'Dt Envio',
	'Situação',
	'RE',
	'Nome',
	'Usuário'
);

foreach($collection as $item)
{
	$body[] = array(
		anchor("ecrm/reenvio_email/index/".$item["cd_email"], $item["cd_email"], "target='_blank'"),
		$item["dt_envio"],
		$item["dt_email_enviado"],
        '<span class="label label-'.trim($item['class_retornou']).'">'.trim($item['retornou']).'</span>',
		$item['cd_empresa']."/".$item['cd_registro_empregado']."/".$item['seq_dependencia'],
		array($item["nome"], "text-align:left;"),
		array($item["nome_usuario"], "text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
