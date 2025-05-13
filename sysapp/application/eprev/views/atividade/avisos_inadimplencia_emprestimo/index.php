<?php
set_title('Aviso Inadimplência Empréstimo');
$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if(($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/avisos_inadimplencia_emprestimo/listar'); ?>',
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$('#result_div').html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o Mês/Ano");
			$("#nr_mes").focus();
		}
    }
	
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_avisos_inadimplencia_emprestimo"),
        [
            'CaseInsensitiveString', 
			'RE', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString',
            'DateBR', 
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
        ob_resul.sort(2, false);
    }	
	
	function enviar()
    {
		getCheck();
		
		if($("#part_selecionado").val() != "")
		{
			var confirmacao = 'Confirma o envio do Aviso Inadimplência Empréstimo?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?php echo loader_html(); ?>");
				
				$.post('<?php echo site_url('atividade/avisos_inadimplencia_emprestimo/enviar'); ?>',
				$("#filter_bar_form").serialize(),
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar o Aviso Inadimplência Empréstimo");
		}
    }	
	
	
	function ir_emails()
	{
		location.href='<?php echo site_url("atividade/avisos_inadimplencia_emprestimo/emails/"); ?>';
	}
	
    function checkAll()
    {
        var ipts = $("#tabela_avisos_inadimplencia_emprestimo>tbody").find("input:checkbox");
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
        var ipts = $("#tabela_avisos_inadimplencia_emprestimo>tbody").find("input:checkbox:checked");
		
        $("#part_selecionado").val("");
		
        jQuery.each(ipts, function(){
            //alert(this.name + " => " + this.value);
            if(jQuery.trim($("#part_selecionado").val()) == "")
            {
                $("#part_selecionado").val("'" + this.value + "'");
            }
            else
            {
                $("#part_selecionado").val($("#part_selecionado").val() + ",'" + this.value + "'");
            }
        })		
    }	
	
	$(function(){
		if(($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			filtrar();
		}
	});
		
</script>
<?php
$abas[] = array('aba_lista', 'Enviar', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Emails', FALSE, 'ir_emails();');


echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo form_default_mes_ano('nr_mes', 'nr_ano', 'Mês/Ano:*',$dt_referencia_aviso);
		echo form_default_hidden('part_selecionado', "Selecionados:");
	echo form_end_box_filter();
	echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Informe o Mês/Ano</b></span></div>';
	echo br(5);
echo aba_end(); 
	 
$this->load->view('footer_interna');
?>