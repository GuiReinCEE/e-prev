<?php
set_title('Escritório Jurídico - Escritório');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?=loader_html()?>");
		
		$.post('<?=site_url('atividade/escritorio_juridico/listar')?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"Number",
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
		ob_resul.sort(1, false);
	}
	
	function ativar(cd_escritorio_oracle)
	{
		if(confirm("Deseja ativar esse escritório?"))
		{
			$.post('<?=site_url('atividade/escritorio_juridico/ativar')?>',
			{
				cd_escritorio_oracle : cd_escritorio_oracle
			},
			function(data)
			{
				filtrar();
			});
		}
	}
	
	function desativar(cd_escritorio)
	{
		if(confirm("Deseja desativar esse escritório?"))
		{
			$.post('<?=site_url('atividade/escritorio_juridico/desativar')?>',
			{
				cd_escritorio : cd_escritorio
			},
			function(data)
			{
				filtrar();
			});
		}
	}
	
	function reativar(cd_escritorio)
	{
		if(confirm("Deseja reativar esse escritório?"))
		{
			$.post('<?=site_url('atividade/escritorio_juridico/reativar')?>',
			{
				cd_escritorio : cd_escritorio
			},
			function(data)
			{
				filtrar();
			});
		}
	}
	
	function ir_usuarios()
	{
		location.href='<?=site_url("atividade/escritorio_juridico_usuario")?>';
	}

	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Usuários', false, 'ir_usuarios();');

$arr_ativo[] = array('value' => 'S', 'text' => 'Sim');
$arr_ativo[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter(); 
		echo filter_text('nome_fantasia', 'Nome:', '', 'style="width:300px;"');
		echo filter_text('representante', 'Representante:', '', 'style="width:300px;"');
		echo filter_dropdown('fl_ativo', 'Ativo:', $arr_ativo);  
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>