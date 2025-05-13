<?php
	$head = array(
		'Ano/Nº',
		'Origem',
		'Dt. Recebimento',
		'Tipo',
		'Área Consolidadora',
		'Gestão',
		'Documento',
		'Dt. Prazo',
		'Dt. Envio',
		'Dt. Atendimento',
		'Anexo',
		'Qt. Sol. Doc.'
	);

	$body = array();

	foreach ($collection as $item)
	{
		$link_documento = base_url().'up/solic_fiscalizacao_audit/'.$item['arquivo'];

		if(intval($item['cd_liquid']) > 0)
		{
			$ext = pathinfo($item['arquivo'], PATHINFO_EXTENSION);

			if(in_array($ext, array('tif', 'pdf', 'png', 'jpg', 'jpeg', 'bmp', 'svg')))
			{
				$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento_liquid/'.$item['cd_liquid'];
			}
			else
			{
				$link_documento = 'atividade/solic_fiscalizacao_audit/abrir_documento/'.$item['cd_liquid'].'/'.$ext;
			}
		}

	  	$body[] = array(
		    anchor('atividade/solic_fiscalizacao_audit/cadastro/'.$item['cd_solic_fiscalizacao_audit'], $item['ds_ano_numero']),
		    array(anchor('atividade/solic_fiscalizacao_audit/cadastro/'.$item['cd_solic_fiscalizacao_audit'], $item['ds_solic_fiscalizacao_audit_origem']), 'text-align:left;'),
			anchor('atividade/solic_fiscalizacao_audit/cadastro/'.$item['cd_solic_fiscalizacao_audit'], $item['dt_recebimento']), 
	  		array(anchor('atividade/solic_fiscalizacao_audit/cadastro/'.$item['cd_solic_fiscalizacao_audit'], $item['ds_solic_fiscalizacao_audit_tipo']), 'text-align:left'),
	  		anchor('atividade/solic_fiscalizacao_audit/cadastro/'.$item['cd_solic_fiscalizacao_audit'], $item['cd_gerencia']),			
			implode(', ', $item['gerencia']),
			array(anchor('atividade/solic_fiscalizacao_audit/cadastro/'.$item['cd_solic_fiscalizacao_audit'], $item['ds_documento']), 'text-align:left'),
		   	'<span class="label label-'.$item['ds_class_prazo'].'">'.$item['dt_prazo'].'</span>',
		   	anchor('atividade/solic_fiscalizacao_audit/cadastro/'. $item['cd_solic_fiscalizacao_audit'], $item['dt_envio']),
		   	anchor('atividade/solic_fiscalizacao_audit/cadastro/'. $item['cd_solic_fiscalizacao_audit'], $item['dt_envio_atendimento']),
		   	anchor($link_documento, '[arquivo]', array('target' => '_blank')),
		   	'<span class="badge badge-success">'.$item['qt_solicitacoes'].'</span>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	