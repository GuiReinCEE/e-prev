<?php
$body = array();
$head = array(
	'Dt Correspondência ',
	'Origem',
	'Tipo',
	'Identificador',
	'Dt Recebido',
	'Recebido por',
	''
);

$fl_item = FALSE;
	
foreach ($collection as $item)
{	
	$editar  = '<a href="javascript:void(0)" onclick="editar_item('.$item['cd_correspondencia_recebida_item'].')">[editar]</a>';
	$excluir = '<a href="javascript:void(0)" onclick="excluir_item('.$item['cd_correspondencia_recebida_item'].')" style="color:red;">[excluir]</a>';
	
	$body[] = array(
		$item['dt_correspondencia'],
		array($item['origem'],'text-align:center'),
		$item['ds_correspondencia_recebida_tipo'],
		array($item['identificador'],'text-align:center'),
		$item['dt_recebido'],
		array($item['nome_recebido'],'text-align:left'),
		(trim($dt_envio) == '' ? $editar.' '.$excluir : '')
	);
	
	$fl_item = TRUE;
}

echo '
<script>
	$(function(){
		$("#btn_enviar").'.($fl_item ?  'show' : 'hide').'();
	});
</script>';

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>