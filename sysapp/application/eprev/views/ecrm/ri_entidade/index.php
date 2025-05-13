<?php
	set_title('Entidade');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if($("#cd_entidade").val() != "")
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_entidade/listar',
			{
				cd_entidade : $("#cd_entidade").val()
			}
			,
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
			);
		}
		else
		{
			alert("Escolha a Entidade e clique no botão [Filtrar]");
			$("#cd_entidade").focus();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',  
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
		ob_resul.sort(1, false);
	}
	
	function incluiItemUsuario(cd_entidade_item)
	{
		if(cd_entidade_item != "")
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_entidade/incluiItemUsuario',
			{
				cd_entidade_item : cd_entidade_item
			}
			,
			function(data)
			{
				alert("Sua doação foi registrada, agora entregue no Serviço Social.\n\nMuito obrigada!");
				filtrar();
			}
			);
		}
	}	
	
	function excluirItemUsuario(cd_entidade_item_usuario)
	{
		if(cd_entidade_item_usuario != "")
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_entidade/excluirItemUsuario',
			{
				cd_entidade_item_usuario : cd_entidade_item_usuario
			}
			,
			function(data)
			{
				filtrar();
			}
			);
		}
	}		
	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		echo filter_dropdown('cd_entidade', 'Entidade:', $ar_entidade);	
	echo form_end_box_filter();	
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<?php
	echo aba_end(''); 
?>
<script type="text/javascript">
	filtrar();
</script>
<?php
	$this->load->view('footer');
?>