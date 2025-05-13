<?php
set_title('Ação Preventiva');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        load();
    }

    function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>")
		
        $.post('<?php echo site_url("/gestao/acao_preventiva/listar") ?>',
		$('#filter_bar_form').serialize(),
        function(data)
        {
			$('#result_div').html(data)
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'Number',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateBR',
            'DateBR',
            'DateBR',
            'DateBR',
            'DateBR',
            'DateTimeBR',
            'DateTBR'
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
        ob_resul.sort(0, true);
    }

    function novo()
    {
        location.href='<?php echo site_url("gestao/acao_preventiva/cadastro/0/0"); ?>';
    }
	
	function filtro_poder()
	{
		if($('#poder').val() == 'P')
		{
			$('#implementado').val('S');
			$('#validado').val('N');
			$('#cancelamento').val('N');
			$('#dt_prazo_validacao_ini').val('01/01/<?php echo (intval(date('m')) == 1 ? (intval(date('Y')) - 1): date('Y')); ?>');
			$('#dt_prazo_validacao_fim').val('31/12/<?php echo (intval(date('m')) == 1 ? (intval(date('Y')) - 1): date('Y')); ?>');
			$('#cancelamento').val('N');
			
			$('#processo').val('');
			$('#usuario_gerencia').val('');
			$('#usuario').val('');
			$('#cd_usuario_titular').val('');
			$('#cd_usuario_substituto').val('');
			$('#dt_inclussao_ini').val('');
			$('#dt_inclussao_fim').val('');
			$('#dt_proposta_ini').val('');
			$('#dt_proposta_fim').val('');
			$('#dt_implementacao_ini').val('');
			$('#dt_implementacao_fim').val('');
			$('#dt_prorrogacao_ini').val('');
			$('#dt_prorrogacao_fim').val('');
			$('#dt_validacao_ini').val('');
			$('#dt_validacao_fim').val('');
		}
		else if($('#poder').val() == 'V')
		{	
			$('#implementado').val('S');
			$('#validado').val('S');
			$('#cancelamento').val('N');
			$('#dt_validacao_ini').val('01/01/<?php echo (intval(date('m')) == 1 ? (intval(date('Y')) - 1): date('Y')); ?>');
			$('#dt_validacao_fim').val('31/12/<?php echo (intval(date('m')) == 1 ? (intval(date('Y')) - 1): date('Y')); ?>');
			
			$('#processo').val('');
			$('#usuario_gerencia').val('');
			$('#usuario').val('');
			$('#cd_usuario_titular').val('');
			$('#cd_usuario_substituto').val('');
			$('#dt_inclussao_ini').val('');
			$('#dt_inclussao_fim').val('');
			$('#dt_proposta_ini').val('');
			$('#dt_proposta_fim').val('');
			$('#dt_implementacao_ini').val('');
			$('#dt_implementacao_fim').val('');
			$('#dt_prorrogacao_ini').val('');
			$('#dt_prorrogacao_fim').val('');
			$('#dt_prazo_validacao_ini').val('');
			$('#dt_prazo_validacao_fim').val('');
		}
		else
		{
			$('#processo').val('');
			$('#usuario_gerencia').val('');
			$('#usuario').val('');
			$('#cd_usuario_titular').val('');
			$('#cd_usuario_substituto').val('');
			$('#dt_inclussao_ini').val('');
			$('#dt_inclussao_fim').val('');
			$('#dt_proposta_ini').val('');
			$('#dt_proposta_fim').val('');
			$('#dt_implementacao_ini').val('');
			$('#dt_implementacao_fim').val('');
			$('#dt_prorrogacao_ini').val('');
			$('#dt_prorrogacao_fim').val('');
			$('#dt_prazo_validacao_ini').val('');
			$('#dt_prazo_validacao_fim').val('');
			$('#implementado').val('');
			$('#validado').val('');
			$('#cancelamento').val('');
			$('#dt_validacao_ini').val('');
			$('#dt_validacao_fim').val('');
		}
	}
	
	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$drop_ar[] = array('text' => 'Sim', 'value' => 'S');
$drop_ar[] = array('text' => 'Não', 'value' => 'N');

$arr_poder[] = array('text' => 'Pendentes', 'value' => 'P');
$arr_poder[] = array('text' => 'Verificadas', 'value' => 'V');

$config['button'][] = array('Novo', 'novo()');

echo aba_start($abas);

	echo form_list_command_bar($config);
	echo form_start_box_filter();
		//echo filter_dropdown('processo', 'Processo:', $processo_dd);
		echo filter_processo('processo', 'Processo :');
		echo filter_usuario_ajax('usuario', '', '', 'Responsável:');
		echo filter_dropdown('cd_usuario_titular', 'Auditor 1º Opção:', $arr_auditores);
		echo filter_dropdown('cd_usuario_substituto', 'Auditor 2º Opção:', $arr_auditores);
		echo filter_date_interval('dt_inclussao_ini', 'dt_inclussao_fim', 'Dt Cadastro:');
		echo filter_date_interval('dt_proposta_ini', 'dt_proposta_fim', 'Dt Proposta:');
		echo filter_date_interval('dt_implementacao_ini', 'dt_implementacao_fim', 'Dt Implementação:');
		echo filter_date_interval('dt_prazo_validacao_ini', 'dt_prazo_validacao_fim', 'Dt Validação Eficácia:');
		echo filter_date_interval('dt_prorrogacao_ini', 'dt_prorrogacao_fim', 'Dt Prorrogação:');
		echo filter_date_interval('dt_validacao_ini', 'dt_validacao_fim', 'Dt Verificação da Eficácia:');
		echo filter_dropdown('implementado', 'Implementado:', $drop_ar);
		echo filter_dropdown('validado', 'Verificado:', $drop_ar);
		echo filter_dropdown('cancelamento', 'Cancelado:', $drop_ar);
		echo filter_dropdown('poder', 'PODER:', $arr_poder, array(), 'onchange="filtro_poder();"');
	echo form_end_box_filter();
	
	echo '<div id="result_div"></div>';
	echo br();

echo aba_end();
$this->load->view('footer'); 
?>