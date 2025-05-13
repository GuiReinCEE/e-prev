<?php
	set_title('Protocolo Secretária');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('cadastro/protocolo_sg/listar') ?>",
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateBR",
		    "DateTimeBR",
		    "DateTimeBR",
			null,
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
					
	function novo()
	{
		location.href = "<?= site_url('cadastro/protocolo_sg/cadastro') ?>";
	}

	function enviar(cd_protocolo_sg)
	{
		var confirmacao = 'Deseja enviar?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/protocolo_sg/enviar') ?>/"+cd_protocolo_sg;
		}
	}

	function excluir(cd_protocolo_sg)
	{
		var confirmacao = 'Deseja excluir?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/protocolo_sg/excluir') ?>/"+cd_protocolo_sg;
		}
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Protocolo', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_integer_ano('nr_ano', 'nr_numero', 'Ano/Número:');
			//echo form_default_usuario_ajax('cd_usuario_responsavel', '', '', 'Usuário Responsável: ', 'Gerência Responsável: ');
			//echo form_default_usuario_ajax('cd_usuario_substituto', '', '', 'Usuário Substituto: ', 'Gerência Substituto: ');
			echo filter_date_interval('dt_prazo_ini', 'dt_prazo_fim', 'Dt. Prazo:');
			echo filter_dropdown('fl_respondido', 'Respondido:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), 'N');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end();
	$this->load->view('footer');
?>