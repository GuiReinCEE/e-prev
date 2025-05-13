<?php
$body = array();
$head = array( 
	'Nome Campo',
	'Tamanho Campo',
	'Característica',
	'Formato Campo',
	'Definição',
	''
);	 	 
 				
$i = 0;

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;

foreach( $collection as $item )
{
	$excluir = ($fl_analista ? '<a href="javascript:void(0);" onclick="excluir_tipo('.$item['cd_tarefas_layout'].');" >[excluir]</a>' : '');
			
			
	$body = array();
	$body[] = array(
		form_input(array('name' => 'campo_nome_'.$item['cd_tarefas_layout'], 'id' => 'campo_nome_'.$item['cd_tarefas_layout'])),
		form_input(array('name' => 'campo_tamanho_'.$item['cd_tarefas_layout'], 'id' => 'campo_tamanho_'.$item['cd_tarefas_layout'])),
		form_input(array('name' => 'campo_caracteristica_'.$item['cd_tarefas_layout'], 'id' => 'campo_caracteristica_'.$item['cd_tarefas_layout'])),
		form_input(array('name' => 'campo_formato_'.$item['cd_tarefas_layout'], 'id' => 'campo_formato_'.$item['cd_tarefas_layout'])),
		form_textarea(array('name' => 'campo_definicao_'.$item['cd_tarefas_layout'], 'id' => 'campo_definicao_'.$item['cd_tarefas_layout']), '', 'style="height:90px; width:400px;"'),
		($fl_analista ? '<a href="javascript:void(0);" onclick="adicionar_campo('.$item['cd_tarefas_layout'].');" >[salvar]</a>' : '')
	);
	
	foreach($item['campo'] as $item2)
	{
		$body[] = array(
			$item2['ds_nome'],
			$item2['ds_tamanho'],
			$item2['ds_caracteristica'],
			$item2['ds_formato'],
			$item2['ds_definicao'],
			($fl_analista ? '<a href="javascript:void(0);" onclick="excluir_campo('.$item2['cd_tarefas_layout_campo'].');" >[excluir]</a>' : '')
			
		);
	}
	$grid->body = $body;
	
	echo form_start_box( "default_".$item['ds_tipo']."_box_".$i, "Tipo ".$item['ds_tipo']." ".$excluir, false  );
		echo $grid->render();
	echo form_end_box("default_tipo_box");
	
	$i++;
}

?>