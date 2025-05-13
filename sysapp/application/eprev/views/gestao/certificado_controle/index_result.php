<?php
	$head = array( 
		'Dt. Inclusão',
		'Nome',
		'Dt Nascimento',
		'Cargo',
		'Em Posse',
		'Certificado',
		'Recertificado',
		'Tipo Certificação',
		'Dt. Certificacão',
		'Dt. Expira Certificacão',
		'Anexo',
		'Pontuação',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$table = '';

		if(trim($item['fl_pontuacao']) == 'S')
		{
			$style = 'color:white; font-height:bold;';

			$table = '
				<table style="border:1px solid #000; width:100%;">
					<tr style="'.$style.' background:'.(intval($item['nr_pontuacao_1']) >= 24 ? 'green;' : 'red;' ).'">
						<td>1º ano</td>
						<td>'.intval($item['nr_pontuacao_1']).'</td>
					</tr>
					<tr style="'.$style.' background:'.(intval($item['nr_pontuacao_2']) >= 24 ? 'green;' : 'red;' ).'">
						<td>2º ano</td>
						<td>'.intval($item['nr_pontuacao_2']).'</td>
					</tr>
					<tr style="'.$style.' background:'.(intval($item['nr_pontuacao_3']) >= 24 ? 'green;' : 'red;' ).'">
						<td>3º ano</td>
						<td>'.intval($item['nr_pontuacao_3']).'</td>
					</tr>
					<tr style="'.$style.' background:'.(intval($item['nr_pontuacao_total']) >= 72 ? 'green;' : 'red;' ).'">
						<td>Total</td>
						<td>'.intval($item['nr_pontuacao_total']).'</td>
					</tr>
				</table>
			';
		}

		$fl_editar = TRUE;	

		if((trim($item['ds_certificado_controle_tipo']) != '') AND (trim($item['dt_expira_certificado']) != ''))
		{
			$fl_editar = FALSE;	
		}

		$body[] = array(
			anchor('gestao/certificado_controle/cadastro/'.$item['cd_certificado_controle'], $item['dt_inclusao']),
			array(anchor('gestao/certificado_controle/cadastro/'.$item['cd_certificado_controle'], $item['nome']), 'text-align:left'),
			$item['dt_nascimento'],
			array($item['ds_certificado_controle_cargo'], 'text-align:left'),
			(trim($item['dt_posse_fim']) == '' ? '<span class="label label-success">Sim</span>' : '<span class="label label-important">Não</span>'),
			'<span class="'.$item['class_certificado'].'">'.$item['certificado'].'</span>',
			(trim($item['fl_recertificacao']) == 'S' ? '<span class="label label-success">Sim</span>' : ''),
			array($item['ds_certificado_controle_tipo'], 'text-align:left'),
			$item['dt_certificao'],
			'<span class="'.$item['class_termino'].'">'.$item['dt_expira_certificado'].'</span>',
			(trim($item['arquivo_nome']) != '' ? array(anchor(base_url().'up/certificado_controle/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;") : ''),
			array($table, 'text-align:center;'),
			($fl_editar ? '<a href="javascript:void(0);" onclick="excluir('.$item["cd_certificado_controle"].')">[excluir]</a>' : "")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>