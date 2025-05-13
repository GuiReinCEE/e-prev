<?php
$this->load->helper('grid');
$grid = new grid();

$body = array();
$head = array( 
	'Banco.Tabela',
	'Campo',
	'Tipo Campo',
	'Val. de Domínio',
	'Ds/En',
	'Prompt',
	'Visível',
	''
);	 

$arr_campo['']  =  'Selecione';
$arr_campo['T'] = 'Text Item';
$arr_campo['L'] = 'List Item';
$arr_campo['C'] = 'Check Box';
$arr_campo['R'] = 'Radio Group';
$arr_campo['P'] = 'Push Bottom';
$arr_campo['D'] = 'Display Item';

$arr_ds_en['']  =  'Selecione';
$arr_ds_en['E'] = 'Enable';
$arr_ds_en['D'] = 'Disable';

$arr_visivel['']  =  'Selecione';
$arr_visivel['S'] = 'Sim';
$arr_visivel['N'] = 'Não';


foreach( $collection_tabelas as $item )
{
	$input_campo = array(
					"name"=>"ds_campo_".$item['cd_tarefas_tabelas'], 
					"id"=>"ds_campo_".$item['cd_tarefas_tabelas']
					);
					
	$input_dominio = array(
					"name"=>"ds_vl_dominio_".$item['cd_tarefas_tabelas'], 
					"id"=>"ds_vl_dominio_".$item['cd_tarefas_tabelas']
					);
	
	$input_prompt = array(
					"name"=>"ds_label_".$item['cd_tarefas_tabelas'], 
					"id"=>"ds_label_".$item['cd_tarefas_tabelas']
					);
					
	$salvar = ($fl_analista ? '<a href="javascript: void(0)"  onclick="atualiza_tabela('.$item['cd_tarefas_tabelas'].')" title="Salvar Tabela">[salvar]</a>' : '');
	$excluir = ($fl_analista ?'<a href="javascript: void(0)"  onclick="excluir_tabela('.$item['cd_tarefas_tabelas'].')" title="Excluir Tabela">[excluir]</a>': '');

	$body[] = array(
		$item['ds_banco'].'.'.$item['ds_tabela'],
		form_input($input_campo,$item['ds_campo']),
		form_dropdown("fl_campo_".$item['cd_tarefas_tabelas'], $arr_campo, array($item['fl_campo_id'])),
		form_input($input_dominio,$item['ds_vl_dominio']),
		form_dropdown("fl_campo_de_".$item['cd_tarefas_tabelas'], $arr_ds_en, array($item['fl_campo_de_id'])),
		form_input($input_prompt,$item['ds_label']),
		form_dropdown("fl_visivel_".$item['cd_tarefas_tabelas'], $arr_visivel, array($item['fl_visivel'])),
		$salvar.' '.$excluir
	);
}	 
 	 	 	 	 	 	
$grid->head = $head;	
$grid->body = $body;
echo $grid->render();	

$body = array();
$head = array( 
	'Banco',
	'Tabela',
	'Campo',
	'Ordem',
	''
);	 

foreach( $collection_ordenacao as $item )
{
	$input_nr_ordem = array(
					"name"=>"nr_ordem_".$item['cd_tarefas_tabelas'], 
					"id"=>"nr_ordem_".$item['cd_tarefas_tabelas']
					);
					
	$salvar = ($fl_analista ? '<a href="javascript: void(0)"  onclick="atualiza_ordenacao('.$item['cd_tarefas_tabelas'].')" title="Salvar Tabela">[salvar]</a>' : '');
	$excluir = ($fl_analista ? '<a href="javascript: void(0)"  onclick="excluir_tabela('.$item['cd_tarefas_tabelas'].')" title="Excluir Tabela">[excluir]</a>' : '');

	$body[] = array(
		$item['ds_banco'],
		$item['ds_tabela'],
		$item['ds_campo'],
		form_input($input_nr_ordem,$item['nr_ordem']),
		$salvar.' '.$excluir
	);
}

$grid->head = $head;	
$grid->body = $body;
echo $grid->render();	


?>