<?php
set_title('Entidades');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('atividade/entidade/listar');?>',
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
			"Number",
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
	
	function desativar(cd_entidade)
	{
		if(confirm("Deseja desativar essa entidade?"))
		{
			$.post('<?php echo site_url('atividade/entidade/desativar');?>',
			{
				cd_entidade : cd_entidade
			},
			function(data)
			{
				filtrar();
			});
		}
	}
	
	function ativar(cd_entidade)
	{
		if(confirm("Deseja ativar essa entidade?"))
		{
			$.post('<?php echo site_url('atividade/entidade/ativar');?>',
			{
				cd_entidade : cd_entidade
			},
			function(data)
			{
				filtrar();
			});
		}
	}
		
	function ir_usuarios()
	{
		location.href='<?php echo site_url("atividade/entidade_usuario"); ?>';
	}
	
	function novo()
	{
		location.href='<?php echo site_url("atividade/entidade/cadastro"); ?>';
	}

	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Usuários', false, 'ir_usuarios();');

$config['button'][] = array('Nova Entidade', 'novo()');

echo aba_start($abas);
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo filter_text('ds_entidade', 'Entidade :', '', 'style="width:300px;"');
		echo filter_cnpj('cnpj', 'CNPJ :');
		echo filter_integer('cd_recolhimento', 'Código de Recolhimento :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>