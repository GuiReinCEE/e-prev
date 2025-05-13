<?php
$body=array();
$head = array( 
	'#',
	'Evento',
	'Dt Evento',
	'Tipo',
	'Local',
	'Convidado',
	'Estimado',
	'Presente',
	'Respondente',
	'Satisfeito',
	'Dt Alteração',
	'Usuário'
);

foreach($ar_reg as $ar_item )
{
	$body[] = array(
		anchor('ecrm/ri_controle_evento/detalhe/'.$ar_item['cd_controle_evento'], $ar_item['cd_controle_evento']),
		array(anchor('ecrm/ri_controle_evento/detalhe/'.$ar_item['cd_controle_evento'], $ar_item['ds_evento']),"text-align:left;"),
		
		$ar_item["dt_evento"],
		
		$ar_item['ds_controle_evento_tipo'],
		array($ar_item['ds_controle_evento_local'],"text-align:left;"),

		array(number_format(intval($ar_item["nr_convidado"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_estimado"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_presente"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),

		array(number_format(intval($ar_item["nr_respondente"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_satisfeito"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		
		$ar_item["dt_alteracao"],
		array($ar_item['usuario_alteracao'],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
