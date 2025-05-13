<?php
set_title('Boas Vindas - Patrocinadora');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#qt_part_selecionado").val(0);
        $("#part_selecionado").val("");        
		
		if(($("#dt_ini_ingresso").val() != "") && ($("#dt_fim_ingresso").val() != ""))
        {
            $("#result_div").html("<?php echo loader_html(); ?>");

            $.post('<?php echo site_url('/planos/boas_vindas_patrocinadora/listar');?>', $("#filter_bar_form").serialize(),
            function(data)
            {
                $("#result_div").html(data);
				configure_result_table();
            });
        }
        else
        {
            alert("Informe os campos com (*) e clique em filtrar");
            $("#cd_plano_empresa").focus();
        }
    }
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_boas_vindas"),
        [
			null,
            'RE', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			'DateBR', 
			'DateBR', 
			'DateBR', 
            'CaseInsensitiveString', 
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
			var confirmacao = 'Total de Participantes selecionados: ' + $("#qt_part_selecionado").val() + '\n\n' +
			                  'Confirma o envio de Boas Vindas - Patrocinadora?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			

			if(confirm(confirmacao))
			{
				$.post('<?php echo site_url('planos/boas_vindas_patrocinadora/enviar'); ?>',
				{
					part_selecionado : $("#part_selecionado").val(),
					cd_empresa       : $("#cd_plano_empresa").val(),
					cd_plano         : $("#cd_plano").val()
				},
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert("Selecione o(s) Participante(s) para enviar Boas Vindas - Patrocinadora.");
		}	
		
    }	
	
	function checkAll()
    {
        var ipts = $("#tabela_boas_vindas > tbody").find("input:checkbox");
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
        var ipts = $("#tabela_boas_vindas > tbody").find("input:checkbox:checked");
		var qt_part = 0;
        $("#qt_part_selecionado").val(0);
        $("#part_selecionado").val("");
		
        jQuery.each(ipts, function(){
			qt_part = qt_part + 1;
			
            if(jQuery.trim($("#part_selecionado").val()) == "")
            {
                $("#part_selecionado").val("'" + this.value + "'");
            }
            else
            {
                $("#part_selecionado").val($("#part_selecionado").val() + ",'" + this.value + "'");
            }
        });

		$("#qt_part_selecionado").val(qt_part);
    }
</script>
<?php
$abas[] = array('aba_lista', 'Boas Vindas', TRUE, 'location.reload();');

$ar_opcao[] = Array('text' => 'Sim', 'value' => 'S');
$ar_opcao[] = Array('text' => 'Não', 'value' => 'N');

$ar_eletronico[] = Array('text' => 'Sim', 'value' => 'I');
$ar_eletronico[] = Array('text' => 'Não', 'value' => 'C');

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_date_interval('dt_ini_ingresso','dt_fim_ingresso', "Período de Ingresso:(*)");
		echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:', 'Plano:', 'P');
		echo filter_dropdown('fl_inscricao', 'Inscrição:', $ar_opcao);			
		echo filter_dropdown('fl_certificado', 'Certificado:', $ar_opcao);			
		echo filter_dropdown('fl_email', 'Email:', $ar_opcao);			
		echo filter_dropdown('fl_gerado', 'Gerado:', $ar_opcao);			
		echo filter_dropdown('fl_enviado', 'Enviado:', $ar_opcao);			
		echo filter_dropdown('fl_eletronico', 'Eletrônico:', $ar_eletronico);			
		echo form_default_hidden('part_selecionado', "Selecionados:");
		echo form_default_hidden('qt_part_selecionado', "Qt Selecionados:");
	echo form_end_box_filter();
	echo '
		<div id="result_div">'.br(2).'
			<span class="label label-success">
				Clique no botão [Filtrar] para exibir as informações
			</span>
		</div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');

?>