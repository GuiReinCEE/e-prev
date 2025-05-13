<?php
	$body = array();
	$head = array(
		'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAllProtocolo();" title="Clique para Marcar ou Desmarcar Todos">',
		'RE',
		'Nome',
		'Idade',
		'Status',
		'E-mail',
		'Dt Rescisão',
		'Dt Digita Rescisão',
		'',
		'Dt Envio',
		'Usuário',
		''
	);

	foreach ($collection as $item)
	{			
		$campo_check = array(
			'name'        => 'check',
			'id'          => 'check',
			'value'       => $item['cd_empresa'].'_'.$item['cd_registro_empregado'].'_'.$item['seq_dependencia'],
			'checked'     => FALSE
		);
	
		$body[] = array(
		    (((trim($item['dt_rescisao']) == "") and (trim($item['fl_email']) == "s")) ?form_checkbox($campo_check) : ''),
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'],'text-align:left;'),
			$item['idade'],
			'<span class="'.trim($item['class_status']).'">'.$item['status'].'</span>',
			'<span class="'.(trim($item['fl_email']) == 's' ? 'label label-success' : 'label label-important').'">'.(trim($item['fl_email']) == 's' ? 'Sim' : 'Não').'</span>',
			$item['dt_demissao'],
			$item['dt_digita_demissao'],
			(((trim($item['dt_rescisao']) != "") and (trim($item['dt_envio_email']) == "")) ? '<a href="javascript:void(0);" onclick="remover(\''.$item['cd_empresa'].'_'.$item['cd_registro_empregado'].'_'.$item['seq_dependencia'].'\')">[remover]</a>' : ""),
			$item['dt_envio_email'],
			$item['nome_usuario'],
			anchor("atividade/rescisao_controle/acompanhamento/".$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'], '[acompanhamento]')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'tabela_lista';
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>