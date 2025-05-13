<?php


$body = array();
$head = array(
  'Área',
  'Nome',
  'Usuário',
  'Dt Ini',
  'Dt Fim',
  'VPN',
  'UL - FERIAS',
  'Bloquear',
  'Liberar'
);

foreach ($collection as $ar_reg)
{
	$FL_ULFERIAS = $ar_reg['FL_ULFERIAS'];
	$FL_VPN      = $ar_reg['FL_VPN'];
	$FL_BLOQUEAR = $ar_reg['FL_BLOQUEAR'];
	$FL_LIBERAR  = $ar_reg['FL_LIBERAR'];
	$FL_FERIAS   = $ar_reg['FL_FERIAS'];

	$estilo = ($FL_BLOQUEAR ? 'color:red; font-weight:bold; font-size: 120%;' : ($FL_LIBERAR ? 'color:blue; font-weight:bold; font-size: 120%;' : ($FL_FERIAS ? 'background-color: coral; color:black; font-weight:bold; font-size: 100%;' : "" )));

    $body[] = array(
		array($ar_reg['divisao'],'text-align:left;'.$estilo),
		array($ar_reg['nome'],'text-align:left;'.$estilo),
		array($ar_reg['usuario'],'text-align:left;'.$estilo),
		array($ar_reg['dt_ferias_ini'],'text-align:center;'.$estilo),
		array($ar_reg['dt_ferias_fim'],'text-align:center;'.$estilo),
		array(($FL_VPN ? "SIM" : "NAO" ),'text-align:center;'.$estilo),
		array(($FL_ULFERIAS ? 'SIM' : "NAO").(($FL_ULFERIAS == FALSE AND $FL_VPN == FALSE) ? ' <span class="label label-success">**VERIFICAR**</span>' : ""),'text-align:center;'.$estilo),
		array(($FL_BLOQUEAR ? 'SIM' : "NAO" ),'text-align:center;'.$estilo),
		array(($FL_LIBERAR ? 'SIM' : "NAO" ),'text-align:center;'.$estilo)
	);

}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
	 	 	 	 	 	 	 	 	
?>