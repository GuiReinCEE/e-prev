<?php
	$head = array(
		'Código',
		'Origem',
		'Especificar',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('atividade/solic_fiscalizacao_audit_origem/cadastro/'.$item['cd_solic_fiscalizacao_audit_origem'], $item['cd_solic_fiscalizacao_audit_origem']),
		    array(anchor('atividade/solic_fiscalizacao_audit_origem/cadastro/'.$item['cd_solic_fiscalizacao_audit_origem'],$item['ds_solic_fiscalizacao_audit_origem']), 'text-align:left;'),
		    '<span class="label label-'.trim($item['ds_class_especificar']).'">'.$item['ds_especificar'].'</span>',
			'<a href="javascript:void(0)" onclick="excluir('.intval($item['cd_solic_fiscalizacao_audit_origem']).')">[excluir]</a>'
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>