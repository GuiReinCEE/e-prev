<?php
	set_title('Recursos Humanos - Aviso');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('cadastro/rh_aviso/listar') ?>",
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
		    "CaseInsensitiveString",
			"CaseInsensitiveString",
			"Date",
			"CaseInsensitiveString",
			"DateTimeBR",
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
	
	function novo()
	{
		location.href = "<?= site_url('cadastro/rh_aviso/cadastro') ?>";
	}
	
	function excluir_item(cd_rh_aviso)
	{
		var confirmacao = 'Deseja EXCLUIR?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('cadastro/rh_aviso/excluir') ?>/"+cd_rh_aviso;
		}		
	}	

	function confirmar(cd_rh_aviso)
	{
		var confirmacao = 'Deseja CONFIRMAR as informações do aviso?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('cadastro/rh_aviso/confirmar') ?>/"+cd_rh_aviso;
		}		
	}	
	
	$(function(){
		filtrar();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo aviso', 'novo()');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_dropdown('cd_periodicidade', 'Periodicidade:', $periodicidade);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

	$this->load->view('footer');
?>