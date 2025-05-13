<?php
$body=array();
$head = array( 
	'Ordem',
	'',
	'Descrição',
	'Link',
	'Dt Cadastro',
	''
);

foreach( $collection as $item )
{

	$input_ordem = array(
		"name"=>"nr_ordem_".$item['cd_intranet_link'], 
		"id"=>"nr_ordem_".$item['cd_intranet_link'],
		"onblur" => "salvar_ordem(".$item['cd_intranet_link'].");",
		"style"=>"display:none;width:50px;"
	);

/*
	$editar_ordem = '<a href="javascript: editar_ordem('.$item['cd_intranet_link'].', $(this)); void(0);" id="nr_ordem_editar_'.$item['cd_intranet_link'].'" title="Editar">[editar]</a>';		
	
	$salvar_ordem = '<a href="javascript: salvar_ordem('.$item['cd_intranet_link'].', $(this)); void(0);" id="nr_ordem_salvar_'.$item['cd_intranet_link'].'" title="Salvar">[salvar]</a>';		
			*/		
	$body[] = array(
		'<span id="ajax_ordem_valor_'.$item['cd_intranet_link'].'"></span> '.'<span id="valor_ordem_'.$item['cd_intranet_link'].'">'.$item['nr_ordem'].'</span>'.
		form_input($input_ordem, $item['nr_ordem'])."<script> jQuery(function($){ $('#intranet_".$item['cd_intranet_link']."').numeric(); }); </script>".
		'<a id="editar_ordem_'.$item['cd_intranet_link'].'" href="javascript: editar_ordem('.$item['cd_intranet_link'].'); void(0);" title="Editar a ordem">[editar]</a>'.
		'<a id="salvar_ordem_'.$item['cd_intranet_link'].'" href="javascript: salvar_ordem('.$item['cd_intranet_link'].'); void(0);" style="display:none" title="Salvar a ordem">[salvar]</a>',

		array($item["texto_link"],'text-align:left'),
		array(anchor_file($item['link'],$item['link'], array('target' => '_black') ),'text-align:left'),
		$item["dt_inclusao"],
		'<a href="javascript: excluir_link('.intval($item['cd_intranet_link']).'); void(0);">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();	
?>