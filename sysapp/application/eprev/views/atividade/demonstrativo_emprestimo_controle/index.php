<?php
set_title('Demonstrativo de Empréstimo');
$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if(($("#dt_inicio").val() != "") && ($("#dt_final").val() != ""))
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/demonstrativo_emprestimo_controle/listar'); ?>', $('#filter_bar_form').serialize(),
			function(data)
			{
				$('#result_div').html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o Período do Demonstrativo de Empréstimo.");
			$("#dt_inicio").focus();
		}
    }
	
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_demonstrativo"),
        [
			null,
            'CaseInsensitiveString', 
            'RE', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateBR', 
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
	
	function ir_emails()
	{
		location.href='<?php echo site_url("atividade/demonstrativo_emprestimo_controle/emails/"); ?>';
	}
	
	function checkAll()
    {
        var ipts = $("#tabela_demonstrativo > tbody").find("input:checkbox");
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
        var ipts = $("#tabela_demonstrativo > tbody").find("input:checkbox:checked");
		
        $("#part_selecionado").val("");
		
        jQuery.each(ipts, function(){

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
	
	function enviar()
    {
		getCheck();
		
		if($("#part_selecionado").val() != "")
		{
			var confirmacao = 'Confirma o envio do Demonstrativo de Empréstimo?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?php echo loader_html(); ?>");
				
				$.post('<?php echo site_url('atividade/demonstrativo_emprestimo_controle/enviar'); ?>',
				$('#filter_bar_form').serialize(),
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar o Demonstrativo de Empréstimo.");
		}
    }	
	
	$(function(){
		if(($("#dt_inicio").val() != "") && ($("#dt_final").val() != ""))
		{
			filtrar();
		}
		
		$('#dt_inicio_dt_final_shortcut').val('currentMonth');
		$('#dt_inicio_dt_final_shortcut').change();
	});
		
</script>
<?php
$abas[] = array('aba_lista', 'Enviar', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Emails', FALSE, 'ir_emails();');

$arr[] = array('value' => '', 'text' => 'Todos');
$arr[] = array('value' => 'N', 'text' => 'Não');
$arr[] = array('value' => 'S', 'text' => 'Sim');

$ar_eletronico[] = Array('text' => 'Sim', 'value' => 'I');
$ar_eletronico[] = Array('text' => 'Não', 'value' => 'C');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_date_interval('dt_inicio', 'dt_final', 'Período do Demonstrativo :*');
		echo filter_dropdown('fl_enviado', 'Enviado :', $arr);
		echo filter_dropdown('fl_email', 'Com email :', $arr);
		echo filter_dropdown('fl_eletronico', 'Eletrônico :', $ar_eletronico);
		echo form_default_hidden('part_selecionado', "Selecionados:");
	echo form_end_box_filter();
	echo '<div id="result_div">'.br(2).'<span class="label label-success">Informe o Período do Demonstrativo de Empréstimo</span></div>';
	echo br(5);
echo aba_end(); 
	 
$this->load->view('footer_interna');
?>