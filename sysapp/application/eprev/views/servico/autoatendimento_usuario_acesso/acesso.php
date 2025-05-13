<?php
	set_title('Autoatendimento Usuário Acesso - Acessos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('servico/autoatendimento_usuario_acesso/acesso_listar/') ?>",
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
		    "Number",
		    "Number",
			"RE",
		    "CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
		    "CaseInsensitiveString"
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
		ob_resul.sort(4, false);
	}

	function ir_lista()
	{
		location.href="<?= site_url('servico/autoatendimento_usuario_acesso') ?>";
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_acesso', 'Acessos', TRUE, 'location.reload();');
	
	$conf = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 	
	        echo form_default_hidden('cd_usuario', '', $ds_usuario['codigo']);
			echo filter_date_interval('dt_acesso_ini', 'dt_acesso_fim', 'Dt. Acesso:'); 
			echo filter_date_interval('dt_login_ini', 'dt_login_fim', 'Dt. Login:'); 
			echo filter_participante($conf, 'Participante:', array(), FALSE, TRUE, TRUE); 	
		echo form_end_box_filter();
		
		echo form_start_box('default_box', 'Usuário');
	            echo form_default_row('', 'Usuário:', $ds_usuario['nome']);
		echo form_end_box('default_box');
		
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>