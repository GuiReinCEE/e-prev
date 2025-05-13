<?php
	set_title('Contato Familiares Ex-autárquicos Falecidos - Acompanhamento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_atendimento_familiares_falecidos_acompanhamento')) ?>  

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/cadastro/'.$row['cd_atendimento_familiares_falecidos']) ?>";
	}

	function encerrar()
	{
		var confirmacao = "Deseja ENCERRAR o Contato?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para Não\n\n";	

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/atendimento_familiares_falecidos/encerrar/'.intval($contato['cd_atendimento_familiares_falecidos'])) ?>";
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
		configure_result_table();
	});

</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

	$head = array(
		'Código',
	    'Dt. Acompanhamento',
	    'Observação',
	    'Usuário'
	);

	$body = array();

	foreach ($collection as $key => $item)
	{
		$body[] = array(
			$item['cd_atendimento_familiares_falecidos_acompanhamento'],
			$item['dt_inclusao'],
			array(nl2br($item['ds_atendimento_familiares_falecidos_acompanhamento']), 'text-align:justify;'),
			array($item['nome'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_familiares_falecidos/acompanhamento_salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_row('re', 'Participante:', $contato['cd_empresa'].'/'.$contato['cd_registro_empregado'].'/'.$contato['seq_dependencia']);
				echo form_default_row('nome', 'Nome:', $contato['nome']);
				if(trim($contato['cd_atendimento']) != '')
				{
					echo form_default_row('cd_atendimento', 'Atendimento:', $contato['cd_atendimento']);
				}
				if(trim($contato['dt_encerramento']) != '')
				{
					echo form_default_row('dt_encerramento', 'Data Encerramento:', $contato['dt_encerramento']);
				}
			echo form_end_box('default_box');

			echo form_start_box('default_acompanhamento_box', 'Acompanhamento');
				echo form_default_hidden('cd_atendimento_familiares_falecidos', '', $row['cd_atendimento_familiares_falecidos']);
				echo form_default_hidden('cd_atendimento_familiares_falecidos_acompanhamento', '', $row['cd_atendimento_familiares_falecidos_acompanhamento']);
				echo form_default_textarea('ds_atendimento_familiares_falecidos_acompanhamento', 'Observação:*', $row);
			echo form_end_box('default_acompanhamento_box');
			echo form_command_bar_detail_start();    
				echo button_save('Salvar');

				if(trim($contato['dt_encerramento']) == '')
				{
					echo button_save('Encerrar', 'encerrar();', 'botao_verde');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
		echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');
?>