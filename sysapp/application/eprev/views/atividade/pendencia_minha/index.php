<?php
set_title('Minhas Pendências');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('atividade/pendencia_minha/listar');?>',
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
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR",
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString'
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

    $(function(){
		var ipts = $("#cd_pendencia_row").find("input:checkbox");
        var fl_marca_todos = true;
		
        jQuery.each(ipts, function(){
            if ((this.checked == true) && (this.id != "cd_pendencia_checkall"))
			{
				fl_marca_todos = false;
			}
		});		

		if (fl_marca_todos == true)
		{		
			jQuery.each(ipts, function(){
				this.checked = true;
			});
		}
		
        filtrar();
    });
</script>
<?php
	/*
	$ar_pendencia[] = array('value' => 'NC',     'text' => 'Não Conformidade');
	$ar_pendencia[] = array('value' => 'SAP',    'text' => 'Ação Preventiva');
	$ar_pendencia[] = array('value' => 'SDE',    'text' => 'Súmula Diretoria');
	$ar_pendencia[] = array('value' => 'SCD',    'text' => 'Súmula Conselho Deliberativo');
	$ar_pendencia[] = array('value' => 'SCF',    'text' => 'Súmula Conselho Fiscal');
	$ar_pendencia[] = array('value' => 'RECSEG', 'text' => 'Reclamação Seguro');
	$ar_pendencia[] = array('value' => 'REC',    'text' => 'Reclamação');
	$ar_pendencia[] = array('value' => 'SUG',    'text' => 'Sugestão');
	$ar_pendencia[] = array('value' => 'PFP',    'text' => 'Plano Fiscal - Parecer');
	$ar_pendencia[] = array('value' => 'PFI',    'text' => 'Plano Fiscal - Indicadores PGA');
	$ar_pendencia[] = array('value' => 'CLVP',   'text' => 'Cenário Legal - Verificação de Procedência');
	$ar_pendencia[] = array('value' => 'SAPNV',  'text' => 'Comitê Qualidade: Ação Preventiva Não Validada');
	$ar_pendencia[] = array('value' => 'NCNV',   'text' => 'Comitê Qualidade: Não Conformidade Não Validada');
	$ar_pendencia[] = array('value' => 'ATVGI',  'text' => 'Atividade GI');
	$ar_pendencia[] = array('value' => 'TARGI',  'text' => 'Tarefa GI');
	*/
	
	$ar_atrasada[] = array('value' => '',  'text' => 'Todos');
	$ar_atrasada[] = array('value' => 'S', 'text' => 'Sim');
	$ar_atrasada[] = array('value' => 'N', 'text' => 'Não');	
	
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_checkbox_group('cd_pendencia', 'Tipo:', $ar_pendencia);
			echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Dt. Limite:');
			echo filter_dropdown('fl_atrasada', 'Atrasada:', $ar_atrasada);
			#echo filter_dropdown('cd_responsavel', 'Responsável 1:', $ar_resp1);
			#echo filter_dropdown('cd_substituto', 'Responsável 2:', $ar_resp2);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(5);
	echo aba_end();
$this->load->view('footer');
?>