<?php
set_title('Sites Institucionais - Páginas');
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

		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/conteudo_site/listar'
			,{
				cd_versao   : '<?php echo intval($cd_versao); ?>',
				cd_site     : '<?php echo intval($cd_site); ?>',
				cd_secao    : $('#cd_secao').val(),
				fl_excluido : $('#fl_excluido').val()
				
			}
			,
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
		);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'DateTimeBR',
			'DateTimeBR',
			'DateTimeBR'
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

	function novo()
	{
		location.href='<?php echo site_url("ecrm/conteudo_site/detalhe/".intval($cd_site)."/".intval($cd_versao)."/0"); ?>';
	}
	

	function ir_sites()
	{
		location.href='<?php echo site_url("ecrm/site"); ?>';
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Sites', FALSE, 'ir_sites();');
	$abas[] = array('aba_pagina', 'Páginas', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Nova Página', 'novo()');
	echo form_list_command_bar($config);
		
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('cd_secao', 'Seção:', $ar_secao);
		echo filter_dropdown('fl_excluido', 'Excluido:', array(array('value'=>'S', 'text'=>'Sim'), array('value'=>'N', 'text'=>'Não')));
	echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
	echo aba_end(''); 
?>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>