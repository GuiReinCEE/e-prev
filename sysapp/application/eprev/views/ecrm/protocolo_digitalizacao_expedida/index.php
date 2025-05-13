<?php
set_title('Protocolo Digitalização Expedida');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/protocolo_digitalizacao_expedida/listar') ?>",
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
			null,
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "RE",
		    "CaseInsensitiveString",
			"Number",
		    "CaseInsensitiveString",
		    "DateTimeBR"
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
		ob_resul.sort(4, true);
	}
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_protocolo') ?>";
	}
	
	function checkAll()
    {
        var ipts = $("#table-1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }
	
	function gerar_protocolo()
	{
		var ipts = $("#table-1>tbody").find("input:checkbox:checked");
		
		var documento = [];
	
		ipts.each(function(i, e) {
			documento.push($(this).val());
		});
		
		if(documento.length > 0)
		{			
			$("#documento").val(documento);
			
			filter_bar_form.method = "post";
			filter_bar_form.onsubmit = "";
			filter_bar_form.action = "<?= site_url('ecrm/protocolo_digitalizacao_expedida/salvar') ?>";
			filter_bar_form.target = "_self";
			filter_bar_form.submit();
		}
		else
		{
			alert('Selecione no mínimo uma correspondência');
		}
		
	}
	
	$(function(){
		$("#dt_ini_dt_fim_shortcut").val("last7days");
		$("#dt_ini_dt_fim_shortcut").change();		
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Correspondência Expedida', FALSE, 'ir_lista();');
	$abas[] = array('aba_protocolo_digitalizacao', 'Protocolo Digitalização', TRUE, 'location.reload();');
	
	$config['button'][] = array('Gerar Protocolo', 'gerar_protocolo()');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter();
			echo form_default_hidden('documento');
			echo filter_integer('ano', 'Ano:');
			echo filter_integer('numero', 'Número:');
			echo filter_dropdown('tipo', 'Tipo:', $tipo);
			echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Envio:');
			echo filter_dropdown('fl_gerado', 'Gerado:', $fl_gerado, 'N');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>