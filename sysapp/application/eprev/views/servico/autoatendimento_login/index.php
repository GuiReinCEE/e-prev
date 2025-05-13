<?php
	set_title('Login Autoatendimento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		var dt_login_ini = $("#dt_login_ini").val();
		var dt_login_fim = $("#dt_login_fim").val();

		if(dt_login_ini != '' || dt_login_fim != '')
		{
			$("#result_div").html("<?= loader_html() ?>");
					
			$.post("<?= site_url('servico/autoatendimento_login/listar') ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});	
		}
		else
		{
			alert("Informe a data de login.");
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
	
	$(function(){
		$("#dt_login_ini_dt_login_fim_shortcut").val("currentMonth");
		$("#dt_login_ini_dt_login_fim_shortcut").change();

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$conf = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_participante($conf, "Participante:", array(), FALSE, TRUE, TRUE); 	
			echo filter_date_interval('dt_login_ini', 'dt_login_fim', 'Dt. Login:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>