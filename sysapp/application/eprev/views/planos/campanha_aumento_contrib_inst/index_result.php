<?php
$head = array(
	'Dt. Inclusão',
	'Instituidor',
	'Meu Retrato Edição',
	'Dt. Base Extrato',
	'Quantidade',
	'Dt. Envio',
	'Usuário Envio',
	'E-mails aguardando envio', 
	'E-mails enviados',
	'E-mails retornados', 
	'Total de emails'

);

$body = array();

foreach ($collection as $item)
{	
	$body[] = array(
		anchor('planos/campanha_aumento_contrib_inst/cadastro/'.$item['cd_campanha_aumento_contrib_inst'], $item['dt_inclusao']),
		array(anchor('planos/campanha_aumento_contrib_inst/cadastro/'.$item['cd_campanha_aumento_contrib_inst'], $item['ds_instituidor']), 'text-align:left;'),
		$item['cd_edicao'],
		'<span style="font-size:14pt; color:red; font-weight:bold;">'.$item['dt_base_extrato'].'</span>',
		'<label class="badge badge-info">'.$item['qt_participante'].'</label>',
		$item['dt_envio'],
		$item['usuario_envio'],
		'<label class="badge badge-success">'.$item['qt_email_aguarda_env'].'</label>',
		'<label class="badge badge-info">'.$item['qt_email_env'].'</label>',
		'<label class="badge badge-important">'.$item['qt_email_nao_env'].'</label>',
		'<label class="badge badge-default">'.$item['qt_email'].'</label>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>