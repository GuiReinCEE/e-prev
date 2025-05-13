<?php
set_title('Carta de Concess�o - Enviar');
$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if(($("#dt_inicio").val() != "") && ($("#dt_final").val() != ""))
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/carta_concessao/listar'); ?>', $('#filter_bar_form').serialize(),
			function(data)
			{
				$('#result_div').html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o Per�odo da Carta de Concess�o");
			$("#dt_inicio").focus();
		}
    }
	
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_carta"),
        [
            null,
            'CaseInsensitiveString', 
            'RE', 
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
	
	function enviar()
    {
		getCheck();
		
		if($("#part_selecionado").val() != "")
		{
			var confirmacao = 'Confirma o envio da Carta de Concess�o?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para N�o\n\n';			

			if(confirm(confirmacao))
			{
				$('#result_div').html("<?php echo loader_html(); ?>");
				
				$.post('<?php echo site_url('atividade/carta_concessao/enviar'); ?>',
				{
					part_selecionado : $("#part_selecionado").val()
				},
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar a Carta de Concess�o");
		}
    }	
	
	
	function ir_emails()
	{
		location.href='<?php echo site_url("atividade/carta_concessao/emails/"); ?>';
	}
	
    function checkAll()
    {
        var ipts = $("#tabela_carta>tbody").find("input:checkbox");
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
        var ipts = $("#tabela_carta>tbody").find("input:checkbox:checked");
		
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
		if(($("#dt_inicio").val() != "") && ($("#dt_final").val() != ""))
		{
			filtrar();
		}
	});
		
</script>
<?php
$abas[] = array('aba_lista', 'Enviar', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Emails', FALSE, 'ir_emails();');

$arr[] = array('value' => '', 'text' => 'Todos');
$arr[] = array('value' => 'N', 'text' => 'N�o');
$arr[] = array('value' => 'S', 'text' => 'Sim');


echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_date_interval('dt_inicio', 'dt_final', 'Per�odo da Carta de Concess�o:*');
		echo filter_dropdown('fl_enviado', 'Enviado:', $arr);
		echo filter_dropdown('fl_email', 'Com email:', $arr);
		
		echo form_default_hidden('part_selecionado', "Selecionados:");
	echo form_end_box_filter();
	echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Informe o Per�odo da Carta de Concess�o</b></span></div>';
	echo br(5);
echo aba_end(); 
	 
$this->load->view('footer_interna');
?>