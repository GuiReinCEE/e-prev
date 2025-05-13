<?php
$body=array();
$head = array( 
	'#',
	'Informativo',
	'Tipo',
	'Enviado',
	'Atrasado',
	'Dt Limite',
	'Dt Envio',
	'Exemplares',
	'Público',
	'Retrabalho',
	'Reclamações',
	'Dt Alteração',
	'Usuário'
);


foreach($ar_reg as $ar_item )
{
	$body[] = array(
		anchor('ecrm/ri_controle_informativo/detalhe/'.$ar_item['cd_controle_informativo'], $ar_item['cd_controle_informativo']),
		array(anchor('ecrm/ri_controle_informativo/detalhe/'.$ar_item['cd_controle_informativo'], $ar_item['ds_informativo']),"text-align:left;"),
		
		$ar_item['ds_controle_informativo_tipo'],
		
		'<span class="label '.(trim($ar_item['fl_envio']) == "N" ? 'label-important' : 'label-default').'">'.(trim($ar_item['fl_envio']) == "N" ? 'Não' : 'Sim').'</span>',
		'<span class="label '.(trim($ar_item['fl_atrasado']) == "N" ? 'label-default' : 'label-important').'">'.(trim($ar_item['fl_atrasado']) == "N" ? 'Não' : 'Sim').'</span>',
		#$ar_item['fl_envio'],
		#$ar_item['fl_atrasado'],
		
		$ar_item['dt_envio_limite'],
		$ar_item['dt_envio'],

		array(number_format(intval($ar_item["nr_exemplar"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_publico"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),

		array(number_format(intval($ar_item["nr_retrabalho"]),0,",","."),'text-align:right; color: OrangeRed; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_reclamacao"]),0,",","."),'text-align:right; color: red; font-weight: bold;','int'),
		
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
