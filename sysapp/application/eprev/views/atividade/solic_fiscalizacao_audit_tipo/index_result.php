<?php
	$head = array(
		'Cód',
		'Agrupamento',
		'Tipo',
	    'Gestão',
		'Área Consolidadora',
		'Especificar'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		$item['cd_solic_fiscalizacao_audit_tipo'],
	  		array(anchor('atividade/solic_fiscalizacao_audit_tipo/cadastro/'.$item['cd_solic_fiscalizacao_audit_tipo'], $item['ds_solic_fiscalizacao_audit_tipo_agrupamento']), 'text-align:left'),
	  		array(anchor('atividade/solic_fiscalizacao_audit_tipo/cadastro/'.$item['cd_solic_fiscalizacao_audit_tipo'], $item['ds_solic_fiscalizacao_audit_tipo']), 'text-align:left'),
			implode(', ', $item['gerencia']),
	  		anchor('atividade/solic_fiscalizacao_audit_tipo/cadastro/'.$item['cd_solic_fiscalizacao_audit_tipo'], $item['cd_gerencia']),			
	  		'<span class="label label-'.trim($item['ds_class_especificar']).'">'.$item['ds_especificar'].'</span>',
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	