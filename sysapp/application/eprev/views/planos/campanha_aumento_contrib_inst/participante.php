<?php
set_title('Campanha Aumento de Contribuição - Participantes');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('planos/campanha_aumento_contrib_inst/participante_listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			null
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
		ob_resul.sort(0, true);
	}

	function remover(cd_campanha_aumento_contrib_inst_participante)
	{
		var confirmacao = 'Deseja remover participante?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('planos/campanha_aumento_contrib_inst/excluir_participante/'.$row['cd_campanha_aumento_contrib_inst']) ?>/' + cd_campanha_aumento_contrib_inst_participante;
		}
	}

	function adicionar(cd_campanha_aumento_contrib_inst_participante)
	{
		var confirmacao = 'Deseja adicionar participante?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('planos/campanha_aumento_contrib_inst/adicionar_participante/'.$row['cd_campanha_aumento_contrib_inst']) ?>/' + cd_campanha_aumento_contrib_inst_participante;
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('planos/campanha_aumento_contrib_inst')?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('planos/campanha_aumento_contrib_inst/cadastro/'.$row['cd_campanha_aumento_contrib_inst']) ?>";
	}

	function ver_email(cd_empresa, cd_registro_empregado, seq_dependencia)
	{
		window.open("<?= site_url('planos/campanha_aumento_contrib_inst/ver_email/'.$row['cd_campanha_aumento_contrib_inst']) ?>/"+ cd_empresa + "/" + cd_registro_empregado + "/" + seq_dependencia);
	}

	function enviar_email(cd_empresa, cd_registro_empregado, seq_dependencia)
	{
		var confirmacao = 'Deseja encaminhar um e-mail teste (previdencia@eletroceee.com.br)?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('planos/campanha_aumento_contrib_inst/enviar_email_teste/'.$row['cd_campanha_aumento_contrib_inst']) ?>/"+ cd_empresa + "/" + cd_registro_empregado + "/" + seq_dependencia;
		}
	}

	$(function(){
		filtrar();
	})
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_participante', 'Participantes', TRUE, 'location.reload();');

echo aba_start($abas);
	
	echo form_list_command_bar();
	echo form_start_box_filter();
		echo form_default_hidden('cd_campanha_aumento_contrib_inst', '', $row);	 
		echo filter_dropdown('fl_exclusao', 'Removido :', array(
				array('value' => 'N', 'text' => 'Não'), 
				array('value' => 'S', 'text' => 'Sim')
			), array('N')
		);
		echo filter_dropdown('fl_email', 'Com E-mail :', array(
				array('value' => 'N', 'text' => 'Não'), 
				array('value' => 'S', 'text' => 'Sim')
			)
		);
		echo filter_dropdown('fl_app', 'Com APP :', array(
				array('value' => 'N', 'text' => 'Não'), 
				array('value' => 'S', 'text' => 'Sim')
			)
		);
	echo form_end_box_filter();
	echo form_start_box('default_box', $row['ds_instituidor']);
		echo form_default_row('', 'Edição Meu Retrato :', '<span class="badge badge-inverse">'.$row['cd_edicao'].'</span>');
		echo form_default_row('', 'Qt. E-mail :', '<span class="badge badge-info">'.$row['qt_email'].'</span>');
		echo form_default_row('', 'Qt. APP :', '<span class="badge badge-info">'.$row['qt_app'].'</span>');
	echo form_end_box('default_box');
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>