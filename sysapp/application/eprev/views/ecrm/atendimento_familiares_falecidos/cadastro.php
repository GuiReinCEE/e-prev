<?php
	set_title('Contato Familiares Ex-autárquicos Falecidos - Cadastro');
	$this->load->view('header');
?>

<script>
	<?= form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia')) ?>  

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos') ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/retorno/'.$row['cd_atendimento_familiares_falecidos']) ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/acompanhamento/'.$row['cd_atendimento_familiares_falecidos']) ?>";
	}

	function excluir()
	{
		var confirmacao = "Deseja EXCLUIR o Contato?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para Não\n\n";	

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/excluir/'.intval($row['cd_atendimento_familiares_falecidos'])) ?>";
		}
	}

	function encerrar()
	{
		var confirmacao = "Deseja ENCERRAR o Contato?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para Não\n\n";	

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/encerrar/'.intval($row['cd_atendimento_familiares_falecidos'])) ?>";
		}
	}

	function configure_result_table()
	{
	    var ob_resul = new SortableTable(document.getElementById("table-1"),
	    [
	    	'Number',
	    	'DateTimeBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString'
	    ]);
	    ob_resul.onsort = function ()
	    {
	        var rows = ob_resul.tBody.rows;
	        var l = rows.length;
	        for (var i = 0; i < l; i++)
	        {
	            removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
	            addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
	        }
	    };
	    ob_resul.sort(1, true);
	}

	$(function(){
		<? if(intval($row['cd_registro_empregado']) > 0): ?>
		consultar_participante_focus__cd_empresa();
		<? endif; ?>

		<? if(intval($row['cd_atendimento_familiares_falecidos']) > 0): ?>
		configure_result_table();
		<? endif; ?>
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_atendimento_familiares_falecidos']) > 0)
	{
		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

		$head = array(
			'Código',
		    'Dt. Retorno',
		    'Observação',
		    'Usuário'
		);

		$body = array();

		foreach ($retorno as $key => $item)
		{
			$body[] = array(
				anchor('ecrm/atendimento_familiares_falecidos/retorno/'.$item['cd_atendimento_familiares_falecidos'].'/'.$item['cd_atendimento_familiares_falecidos_retorno'], $item['cd_atendimento_familiares_falecidos_retorno']),
				anchor('ecrm/atendimento_familiares_falecidos/retorno/'.$item['cd_atendimento_familiares_falecidos'].'/'.$item['cd_atendimento_familiares_falecidos_retorno'], $item['dt_inclusao']),
				array(nl2br($item['ds_atendimento_familiares_falecidos_retorno']), 'text-align:justify;'),
				array($item['nome'], 'text-align:left;')
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
	}

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_familiares_falecidos/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_atendimento_familiares_falecidos', '', $row['cd_atendimento_familiares_falecidos']);
				echo form_default_participante(array('cd_empresa', 'cd_registro_empregado','seq_dependencia', 'nome'), 'Participante:*', $row, FALSE, TRUE);	
				echo form_default_text('contato', 'Contato:', $row, 'style="width:400px;"');
				echo form_default_textarea('observacao', 'Observação:', $row);
				echo form_default_integer('cd_atendimento', 'Atendimento:', $row); 
				if(trim($row['dt_encerramento']) != '')
				{
					echo form_default_row('dt_encerramento', 'Data Encerramento:', $row['dt_encerramento']);
				}
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();    
				if(trim($row['dt_encerramento']) == '')
				{
					echo button_save("Salvar");
				}

				if((intval($row['cd_atendimento_familiares_falecidos']) > 0) AND (trim($row['dt_encerramento']) == ''))
				{
					echo button_save('Registrar Retorno', 'ir_retorno();', 'botao_disabled');
					echo button_save('Encerrar', 'encerrar();', 'botao_verde');
					echo button_save('Excluir', 'excluir();', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		if(intval($row['cd_atendimento_familiares_falecidos']) > 0)
		{
			echo $grid->render();
		}
		echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');
?>