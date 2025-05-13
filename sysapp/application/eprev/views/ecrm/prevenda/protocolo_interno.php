<?php
set_title('Pré-venda - Pedido Inscrição');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		$.post('<?php echo site_url('ecrm/prevenda/protocolo_interno_listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("tb_protocolo_interno"),
			[
				null,
				"CaseInsensitiveString", 
				"DateTimeBR", 
				"RE",
				"CaseInsensitiveString", 
				"DateTimeBR", 
				"DateBR", 
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
		ob_resul.sort(5, true);
	}

	function checkAll()
    {
        var ipts = $("#tb_protocolo_interno > tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }	
	
	function getCheck()
    {
        var ipts = $("#tb_protocolo_interno > tbody").find("input:checkbox:checked");
		
        $("#prevendacontato_selecionado").val("");
		
        jQuery.each(ipts, function(){

            if(jQuery.trim($("#prevendacontato_selecionado").val()) == "")
            {
                $("#prevendacontato_selecionado").val(this.value);
            }
            else
            {
                $("#prevendacontato_selecionado").val($("#prevendacontato_selecionado").val() + "," + this.value);
            }
        });		
    }	
	
	function criar_protocolo_interno()
    {
		getCheck();
		
		if($("#prevendacontato_selecionado").val() != "")
		{
			var confirmacao = 'Deseja criar o Protocolo Interno?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$("#filter_bar_form").attr('action', '<?php echo site_url('ecrm/prevenda/protocolo_interno_criar');?>');
				$("#filter_bar_form").attr("onsubmit", "true");
				$("#filter_bar_form").attr("method", "post");
				$("#filter_bar_form").submit();
			}
		}
		else
		{
			alert("Selecione o(s) Contato(s) criar o Protocolo Interno.");
		}
    }	
	
	
	function ir_lista()
	{
		location.href='<?php echo site_url('/ecrm/prevenda'); ?>';
	}	
	
	function ir_relatorio()
	{
		location.href='<?php echo site_url('/ecrm/prevenda/relatorio'); ?>';
	}
	
	$(function(){
		if(($("#dt_contato_ini").val() == "") && ($("#dt_contato_fim").val() == ""))
		{
			$("#dt_contato_ini_dt_contato_fim_shortcut").val("last30days");
			$("#dt_contato_ini_dt_contato_fim_shortcut").change();
		}
		
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');
$abas[] = array('aba_protocolo', 'Encaminhar Ped. Inscrição', TRUE, 'location.reload();');

$arr[] = array('value' => '', 'text' => 'Todos');
$arr[] = array('value' => 'N', 'text' => 'Não');
$arr[] = array('value' => 'S', 'text' => 'Sim');

echo aba_start( $abas );
	echo form_list_command_bar();	
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo form_default_hidden('prevendacontato_selecionado', "Selecionados:");
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", Array(), TRUE, FALSE );
		echo filter_text('nome', 'Nome:','','style="width: 350px;"');
		echo filter_date_interval('dt_contato_ini', 'dt_contato_fim', 'Dt Contato:');
		echo filter_date_interval('dt_protocolo_envio_ini', 'dt_protocolo_envio_fim', 'Dt Envio Protocolo:');
		echo filter_dropdown('fl_protocolo', 'Procolo Interno Criado:', $arr);
		echo filter_dropdown('fl_protocolo_enviado', 'Procolo Interno Enviado:', $arr);
	echo form_end_box_filter();
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer');
?>