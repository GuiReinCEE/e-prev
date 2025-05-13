<?php
	set_title('Sistema de Avaliação - Abertura');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/cadastro/'.$cd_avaliacao) ?>";
	}

	function ir_avaliacao()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/avaliacao/'.$cd_avaliacao) ?>";
	}

	function ir_relatorio()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/relatorio/'.$cd_avaliacao) ?>";
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('cadastro/rh_avaliacao_abertura/listar_relatorio_pdi') ?>",
		{
			cd_avaliacao 								: '<?= $cd_avaliacao ?>',
			cd_gerencia     							: $("#cd_gerencia").val(),
			cd_usuario     								: $("#cd_usuario").val(),
			ds_cargo     								: $("#ds_cargo").val(),
			ds_avaliacao_usuario_plando_desenvolvimento : $("#ds_avaliacao_usuario_plando_desenvolvimento").val(),

		},
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(0, false);
	}

    $(function(){
    	filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_avaliacao', 'Avaliações', FALSE, 'ir_avaliacao();');
	$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');
	$abas[] = array('aba_relatorio_pdi', 'PDI', TRUE, 'location.reload();');

	echo aba_start($abas); 
		echo form_list_command_bar(array());
		echo form_start_box_filter();
			echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
			echo filter_dropdown('cd_usuario', 'Colaborador:', $usuarios);
			echo filter_dropdown('ds_cargo', 'Cargo:', $cargo);
			echo filter_dropdown('ds_avaliacao_usuario_plando_desenvolvimento', 'Competência:', $competencia);
	    echo form_end_box_filter();
	echo br();
	echo '<div id="result_div"></div>';
	echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>