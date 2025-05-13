<?php
$body = array();
$head = array(
  'Anexo',
  'Dt Inclusão',
  'Tipo',
  ''
);

foreach ($collection as $item)
{            
    $body[] = array(
	    anchor('http://'.$_SERVER['SERVER_NAME'].'/eletroceee/app/up/entidade/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")),
		$item['dt_inclusao'],	
		array('<span class="'.trim($item['class_label']).'">'.$item['ds_movimento_retorno_tipo'].'</span>', 'text-align:center;'),
		(trim($item['dt_retorno']) == '' ? '<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_movimento_anexo'].')">[excluir]</a>' : '')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>