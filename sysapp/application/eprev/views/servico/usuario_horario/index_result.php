<?php
$body = array();
$head = array(
  'Área',
  'Nome',
  'Usuário',
  'Dt Liberar',
  'Hr Ini',
  'Hr Fim'
);

foreach ($collection as $ar_reg)
{
    $body[] = array(
		array($ar_reg['divisao'],'text-align:left;'),
		
		(
			$ar_reg['fl_editar'] == "S"
			?
			array(anchor('servico/usuario_horario/cadastro/'.$ar_reg['cd_usuario_horario'],$ar_reg['nome']),'text-align:left;')
			:
			array($ar_reg['nome'],'text-align:left;')
		),
		
		array($ar_reg['usuario'],'text-align:left;'),
		
		(
			$ar_reg['fl_editar'] == "S"
			?
			'<span class="label label-success">'.$ar_reg['dt_liberar'].'</span>'
			:
			$ar_reg['dt_liberar']
		),	
		
		$ar_reg['hr_ini'],
		$ar_reg['hr_fim']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
	 	 	 	 	 	 	 	 	
?>