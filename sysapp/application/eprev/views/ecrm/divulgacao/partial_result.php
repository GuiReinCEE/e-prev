<?php
$body = array();
$head = array( 
	'Cód.',
	'Divulgação',
	'Gerência',
	'Dt divulgação',
	'Dt últ. envio',
	#'Envio',
	'E-mails aguardando envio',
	'E-mails enviados',
	#'E-mails retornados',
	'Total de emails',
	'Qt Visualizações',
	'Qt E-mails Visualizados',
	'Qt Part. Visualizaram'
);

foreach( $collection as $item )
{
	$body[] = array( 
		anchor(site_url("ecrm/divulgacao/cadastro/")."/".$item['cd_divulgacao'], $item["cd_divulgacao"] ),
		array(anchor( site_url("ecrm/divulgacao/cadastro/")."/".$item['cd_divulgacao'], $item["assunto"] ),'text-align:left;'),
		$item["divisao"],
		$item["data_div"],
		
		array('<span id="'.$item["cd_divulgacao"].'_dt_ultimo_email_enviado">'.$item["dt_ultimo_email_enviado"].'</span>',"text-align:center;"),
		
		#array('<span id="'.$item["cd_divulgacao"].'_percentual_envio"></span><script>$(function(){ listar_estatistica('.$item["cd_divulgacao"].'); });</script>',"text-align:center;"),
		
		array('<span id="'.$item["cd_divulgacao"].'_qt_email_aguarda_env" class="label label-success">'.$item["qt_email_aguarda_env"].'</span>',"text-align:center;","int"),
		array('<span id="'.$item["cd_divulgacao"].'_qt_email_env" class="label label-info">'.$item["qt_email_env"].'</span>',"text-align:center;","int"),
		#array('<span id="'.$item["cd_divulgacao"].'_qt_email_nao_env" class="label label-important">'.$item["qt_email_nao_env"].'</span>',"text-align:center;","int"),
		array('<span id="'.$item["cd_divulgacao"].'_qt_email" class="label">'.$item["qt_email"].'</span>',"text-align:center;","int"),
		array('<span id="'.$item["cd_divulgacao"].'_qt_visualizacao" class="label label-warning">'.$item["qt_visualizacao"].'</span>',"text-align:center;","int"),
		array('<span id="'.$item["cd_divulgacao"].'_qt_visualizacao_unica" class="label label-success">'.$item["qt_visualizacao_unica"].'</span>',"text-align:center;","int"),
		array('<span id="'.$item["cd_divulgacao"].'_qt_participante" class="label label-inverse">'.$item["qt_participante"].'</span>',"text-align:center;","int")
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>