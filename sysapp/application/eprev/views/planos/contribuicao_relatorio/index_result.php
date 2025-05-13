<?php
	$head = array( 
		'<input type="checkbox" id="check_all" onclick="check_all();" title="Clique para Marcar ou Desmarcar Todos">',
		'Ano/Mês',
		'Origem',
		'RE',
		'Nome',
		'Plano',
		'Telefone',
		'',
		'Dt. Inclusão',
		'Usuário',
		'Dt. Envio SMS',
		'Usuário',
		'Dt. Geração',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
		$campo_check = array(
			'name'  => 'item_'.$item['cd_contribuicao_relatorio_sms'],
			'id'    => 'item_'.$item['cd_contribuicao_relatorio_sms'],
			'value' => $item['cd_contribuicao_relatorio_sms']
		);	

		$body[] = array(
			((trim($item['fl_status_telefone']) == 'S' AND (intval($item['cd_sms']) == 0))? form_checkbox($campo_check) : $item['cd_sms']),
			$item['dt_referencia'],
			$item['ds_contribuicao_relatorio_origem'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['ds_nome'], 'text-align:left'),
			$item['ds_plano'],
			$item['ds_telefone'],
			'<span class="label '.$item['ds_class_status_telefone'].'">'.$item['ds_status_telefone'].'</span>',
			$item['dt_inclusao'],
			$item['ds_usuario'],
			$item['dt_envio_sms'],
			$item['ds_usuario_envio_sms'],
			$item['dt_geracao'],
			$item['ds_usuario_geracao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->id_tabela = 'tabela_contribuicao_relatorio';

	if(count($collection) > 0 AND gerencia_in(array('GFC')))
	{
		echo br();
		echo '<input type="button" value="Enviar SMS" onclick="enviarSMS();" class="btn btn-danger">  ';
		echo '<input type="button" value="Enviar E-mail Cadastro" onclick="enviar_email();" class="btn btn-success">';
		echo br();
	}
	
	echo $grid->render();
?>