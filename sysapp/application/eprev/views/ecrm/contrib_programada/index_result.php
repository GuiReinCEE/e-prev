<?php
	$head = array(
		'<input type="checkbox" id="checkboxCheckAll" onclick="check_all();" title="Clique para Marcar ou Desmarcar Todos">',
		'RE',
		'Nome',
		'Valor Atual R$',
		'Valor Solicitado R$',
		'Dt. Solicitação',
		'Dt. Ínicio',
		'Dt. Cancelado',
		'Dt. Confirmação',
		'Usuário'
	);

	$body = array();

	foreach ($collection as $item)
	{
		$checkbox = array(
			'name'  => 'part_'.$item['cd_empresa'].'_'.$item['cd_registro_empregado'].'_'.$item['seq_dependencia'],
			'id'    => 'part_'.$item['cd_empresa'].'_'.$item['cd_registro_empregado'].'_'.$item['seq_dependencia'],
			'value' => $item['cd_contribuicao_programada']
		);	

		$body[] = array(
			(trim($item['dt_confirmacao']) == '' ? form_checkbox($checkbox) : ''),
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left;'),
			number_format($item['vl_anterior'],2,',','.'),
			number_format($item['vl_valor'],2,',','.'),
			$item['dt_inclusao'],
			$item['dt_inicio'],
			'<span class="label label-important">'.$item['dt_cancelado'].'</span>',
			'<span class="label label-success">'.$item['dt_confirmacao'].'</span>',
			$item['ds_usuario_confirmacao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	if(count($collection) > 0 AND $fl_permissao_receber)
	{
		echo '
			<table border="0" align="center" cellspacing="20">
				<tr style="height: 30px;">
					<td>
						<input type="button" value="Confirmar" onclick="confirmar();" class="btn btn-danger btn-small" style="width: 120px;">
						<input type="button" value="Cancelar" onclick="cancelar();" class="btn btn-secondary btn-small" style="width: 120px;">
					</td>
				</tr>
			</table>';
	}

	echo $grid->render();
?>