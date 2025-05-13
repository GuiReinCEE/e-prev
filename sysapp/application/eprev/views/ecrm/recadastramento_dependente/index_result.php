<?php
	$head = array(
		'RE',
		'Participante',
		'Situação Assinatura',
		'Dt. Envio Part.',
		'Dt. Limite Ass.',
		'Dt. Confirmação',
		'Usuário',
		'Dt. Cancelamento',
		'Usuário'
	);  

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('ecrm/recadastramento_dependente/cadastro/'.$item['cd_recadastramento_dependente'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			array(anchor('ecrm/recadastramento_dependente/cadastro/'.$item['cd_recadastramento_dependente'], $item['ds_nome']), 'text-align:left;'),
			'<span class="'.$item["situacao_label"].'">'.$item["situacao"].'</span>',
			$item['dt_envio_participante'],
			'<span class="'.$item["cor_limite"].'">'.$item["dt_limite"].'</span>',
			$item['dt_confirmacao'],
			$item['ds_usuario_confirmacao'],
			$item['dt_cancelamento'],
			$item['ds_usuario_cancelamento']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>