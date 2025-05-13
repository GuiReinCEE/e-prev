<?php
	set_title('Login Erro Autoatendimento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		var dt_login_ini = $("#dt_inclusao_ini").val();
		var dt_login_fim = $("#dt_inclusao_fim").val();

		if(dt_login_ini != '' || dt_login_fim != '')
		{
			$("#result_div").html("<?= loader_html() ?>");
					
			$.post("<?= site_url('servico/autoatendimento_login_erro/listar') ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});	
		}
		else
		{
			alert("Informe a data de erro do login.");
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimeBR",
			"RE",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}
	
	$(function(){
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val("currentMonth");
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").change();

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$conf = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_participante($conf, 'Participante:', array(), FALSE, TRUE, TRUE); 	
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Login Erro:'); 
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>