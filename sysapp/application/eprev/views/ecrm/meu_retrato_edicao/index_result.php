<?php
	if(gerencia_in(array('GS')))
	{
		echo form_start_box("botoes_box", "Opções",true);
			echo form_default_row('', '', '
				<input type="button" onclick="setGerar();" value="Gerar" class="btn btn-mini btn-info" style="width: 200px;"> 
			');
		echo form_end_box("botoes_box");
	}

	$head = array(
		'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Edição',
		'Empresa',
		'Plano',
		'Tipo',
		'Nº Extrato',
		'Dt. Base',
		'Gerar',
		'Gerado',
		'Qt. Participantes',
		'Dt. Ger. Extrato',
		'Dt. Informática',
        'Usuário',
        'Dt. Atuarial/Benefício',
        'Usuário',
        'Dt. Comunicação',
        'Usuário'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$dt_liberacao_informatica = '';
		$ds_usuario_informatica   = '';

		if(trim($item['dt_liberacao_informatica']) != '')
		{
			$dt_liberacao_informatica = '<span class="label label-warning">'.$item['dt_liberacao_informatica'].'</span>';
			$ds_usuario_informatica   = '<span class="label label-warning">'.$item['ds_usuario_informatica'].'</span>';
		}
		else if(trim($item['dt_cancelamento_envio']) != '')
		{
			$dt_liberacao_informatica = '<span class="label label-important">'.$item['dt_cancelamento_envio'].'</span>';
			$ds_usuario_informatica   = '<span class="label label-important">'.$item['ds_usuario_cancelamento_envio'].'</span>';
		}

		$dt_liberacao_atuarial = '';
		$ds_usuario_atuarial   = '';

		if(trim($item['dt_liberacao_atuarial']) != '')
		{
			$dt_liberacao_atuarial = '<span class="label label-info">'.$item['dt_liberacao_atuarial'].'</span>';
			$ds_usuario_atuarial   = '<span class="label label-info">'.$item['ds_usuario_atuarial'].'</span>';
		}
		else if(trim($item['dt_cancelamento_envio']) != '')
		{
			$dt_liberacao_atuarial = '<span class="label label-important">'.$item['dt_cancelamento_envio'].'</span>';
			$ds_usuario_atuarial   = '<span class="label label-important">'.$item['ds_usuario_cancelamento_envio'].'</span>';
		}

		$dt_liberacao_comunicacao = '';
		$ds_usuario_comunicacao   = '';

		if(trim($item['dt_liberacao_comunicacao']) != '')
		{
			$dt_liberacao_comunicacao = '<span class="label label-success">'.$item['dt_liberacao_comunicacao'].'</span>';
			$ds_usuario_comunicacao   = '<span class="label label-success">'.$item['ds_usuario_comunicacao'].'</span>';
		}
		else if(trim($item['dt_cancelamento_envio']) != '')
		{
			$dt_liberacao_comunicacao = '<span class="label label-important">'.$item['dt_cancelamento_envio'].'</span>';
			$ds_usuario_comunicacao   = '<span class="label label-important">'.$item['ds_usuario_cancelamento_envio'].'</span>';
		}

		$campo_check = array(
			'name'        => $item['cd_edicao'].'chk',
			'id'          => $item['cd_edicao'].'chk',
			'value'       => $item['cd_edicao'],
			'checked'     => FALSE
		);

		$body[] = array(
			($item['fl_gerar'] == "N" ? form_checkbox($campo_check) : ""),
			array(anchor('ecrm/meu_retrato_edicao/cadastro/'.$item['cd_edicao'], $item['cd_edicao']), 'text-align:left;'),
			array(anchor('ecrm/meu_retrato_edicao/cadastro/'.$item['cd_edicao'], $item['sigla']), 'text-align:left;'),
			array($item['plano'], 'text-align:left'),
			'<span class="label '.trim($item['class_tipo_participante']).'">'.$item['tipo_participante'].'</span>',
			$item['nr_extrato'],
			$item['dt_base_extrato'],
			($item['fl_gerar'] == "S" ? '<span class="label">SIM</span>' :  '<span class="label label-important">NÃO</span>'),
			($item['tl_participante'] - $item['qt_participante'] == 0 ? '<span class="label">SIM</span>' :  '<span class="label label-important">NÃO</span>'),
			$item['tl_participante'].' / '.$item['qt_participante'],
			'<span class="label">'.$item['dt_geracao_extrato'].'</span>',
			$dt_liberacao_informatica,
			$ds_usuario_informatica,
			$dt_liberacao_atuarial,
			$ds_usuario_atuarial,
			$dt_liberacao_comunicacao,
			$ds_usuario_comunicacao
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'tabela_mr_lista';
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>