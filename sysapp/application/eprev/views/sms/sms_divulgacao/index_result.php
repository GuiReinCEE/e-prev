<?php
$body = array();
$head = array( 
	'Cód.',
	'Assunto',
	'Texto',
	'Dt inclusão',
	'Usuário'
);

foreach($collection as $item)
{
	$body[] = array( 
		anchor(site_url("sms/sms_divulgacao/cadastro/")."/".$item['cd_sms_divulgacao'], $item["cd_sms_divulgacao"] ),
		array(anchor( site_url("sms/sms_divulgacao/cadastro/")."/".$item['cd_sms_divulgacao'], $item["ds_assunto"] ),'text-align:left;'),
		array(nl2br($item["ds_conteudo"]),'text-align:left;'),
		$item["dt_inclusao"],
		array($item["usuario_inclusao"],'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>