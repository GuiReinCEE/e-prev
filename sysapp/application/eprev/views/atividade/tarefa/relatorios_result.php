<?php
$body = array();
$head = array( 
	'Banco',
	'Tabela',
	'Campo',
	'Label',
	''
);	 	 

foreach( $collection as $item )
{
	$input_campo = array(
					"name"=>"ds_campo_".$item['cd_tarefas_tabelas'], 
					"id"=>"ds_campo_".$item['cd_tarefas_tabelas']
					);
					
	$input_label = array(
					"name"=>"ds_label_".$item['cd_tarefas_tabelas'], 
					"id"=>"ds_label_".$item['cd_tarefas_tabelas']
					);

	$salvar  = '<a href="javascript: void(0)"  onclick="atualiza_relatorio('.$item['cd_tarefas_tabelas'].')" title="Salvar Relatório">[salvar]</a>';				
    $excluir = '<a href="javascript:void(0);" onclick="excluir_relatorios('.$item['cd_tarefas_tabelas'].');" >[excluir]</a>';
	
	$body[] = array(
		$item['ds_banco'],
		$item['ds_tabela'],
		form_input($input_campo,$item['ds_campo']),
		form_input($input_label,$item['ds_label']),
		($fl_analista ? $salvar.' '.$excluir  : '')
		
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();