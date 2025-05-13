<?php
$body = array();
$head = array(
  'Área',
  'Nome',
  'Usuário',
  'Tipo',
  'UL - BLOQUEIO',
  'VPN',
  'UL - FERIAS',
  
  'UL - MFA',
  'UL - SABADO'
);

foreach ($collection as $ar_reg)
{
	$label_bloqueio = "label-important";
	if((in_array($ar_reg['tipo'],array("D","G"))) OR ($ar_reg['usuario'] == "rpfeuffer"))
	{
		#### CHECAR FL_ULBLOQUEIO DIRETORES/GERENTE/ANALISTA INFRA TI ####
		$label_bloqueio = "";
	}
	
	$body[] = array(
		$ar_reg['divisao'],
		array($ar_reg['nome'],'text-align:left;'),
		array($ar_reg['usuario'],'text-align:left;'),
		array($ar_reg['papel'],'text-align:left;'),
		($ar_reg['FL_ULBLOQUEIO'] ? '<span class="label label-success">SIM</span>' : '<span class="label '.$label_bloqueio.'">NAO</span>'),
		($ar_reg['FL_VPN']        ? '<span class="label label-info">SIM</span>' : '<span class="label">NAO</span>'),
		($ar_reg['FL_ULFERIAS']   ? '<span class="label label-warning">SIM</span>' : '<span class="label">NAO</span>'),
		
		($ar_reg['FL_ULMFA']      ? '<span class="label label-inverse">SIM</span>' : '<span class="label">NAO</span>'),
		($ar_reg['FL_ULSABADO']   ? '<span class="label label-inverse">SIM</span>' : '<span class="label">NAO</span>')
		
		
	);

}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
	 	 	 	 	 	 	 	 	
?>