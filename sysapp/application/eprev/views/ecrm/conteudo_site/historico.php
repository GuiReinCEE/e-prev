<?php
set_title('Sites Institucionais - Histórico Página');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url("ecrm/conteudo_site/historicoListar"); ?>', 
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});	
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					null,
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
		ob_resul.sort(2, true);
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/site"); ?>';
	}

	function ir_paginas()
	{
		location.href='<?php echo site_url("ecrm/conteudo_site/index/".intval($cd_site)."/".intval($cd_versao)); ?>';
	}	
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/conteudo_site/detalhe/".intval($cd_site)."/".intval($cd_versao)."/".intval($cd_materia)); ?>';
	}	

	$(function(){
		filtrar();
	});	
</script>

<?php
	$abas[] = array('aba_lista', 'Sites', FALSE, "ir_lista()");
	$abas[] = array('aba_pagina', 'Páginas', FALSE, "ir_paginas()");
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "ir_cadastro()");
	$abas[] = array('aba_historico', 'Histórico', TRUE, "location.reload()");

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
		    echo form_default_hidden('cd_site','cd_site',intval($cd_site));
		    echo form_default_hidden('cd_versao','cd_versao',intval($cd_versao));
		    echo form_default_hidden('cd_materia','cd_materia',intval($cd_materia));
			echo filter_date_interval('dt_ini', 'dt_fim', 'Dt Atualização:', calcular_data('','1 month'), date('d/m/Y'));
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(5);
	echo aba_end();
	$this->load->view('footer');
?>