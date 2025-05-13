<?php 
set_title( 'Protocolo Correspondência Expedida' );
$this->load->view('header'); 
?>
<script>
	function filtrar()
	{
		if($('#fl_gerar').val() == "P")
		{
			exportarPDF();
		}
		else if($('#fl_gerar').val() == "M")
		{
			malaDireta();
		}
		else
		{
			$("#result_div").html("<?=loader_html()?>");
			
			$.post('<?=site_url("ecrm/atendimento_protocolo/listar")?>', 
			$('#filter_bar_form').serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});
		}
	}

	function configure_result_table()
	{

		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    null,
			null,
			'RE',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTimeBR',
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
		ob_resul.sort(9, true);
	}

	function receber(cd_atendimento_protocolo)
	{
		var aviso = "Atenção\n\nConfirmar o recebimento da correspondência?\n\n";

		if(confirm(aviso))
		{
            location.href='<?=site_url("ecrm/atendimento_protocolo/receber")?>/'+ cd_atendimento_protocolo;
		}
	}

    function cancelar(cd_atendimento_protocolo)
	{
        var aviso = "Atenção\n\nConfirmar o cancelamento da correspondência?\n\n";

		if(confirm(aviso))
		{
            location.href='<?=site_url("ecrm/atendimento_protocolo/cancelar")?>/'+ cd_atendimento_protocolo;

		}
    }
    
    function devolver(cd_atendimento_protocolo)
	{
        location.href='<?=site_url("ecrm/atendimento_protocolo/detalhe")?>/'+ cd_atendimento_protocolo;
	}

	function ir_protocolo_digitalizacao()
	{
		location.href = "<?= site_url('ecrm/protocolo_digitalizacao_expedida') ?>";
	}

    function novo()
    {
        location.href='<?=site_url('ecrm/atendimento_protocolo/detalhe')?>';
    }

    function exportarPDF()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?=site_url('/ecrm/atendimento_protocolo/listarPDF')?>';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}

    function malaDireta()
	{
        var aviso = "Atenção\n\nA sua seleção do mala direta Eletro será limpa.\n\n";

		if(confirm(aviso))
		{
            filter_bar_form.method = "post";
            filter_bar_form.action = '<?=site_url('/ecrm/atendimento_protocolo/malaDireta')?>';
            filter_bar_form.target = "_self";
            filter_bar_form.submit();
        }
	}
	
	function integrar_mala_direta()
	{
		$('#fl_gerar').val('M');
		filtrar();
	}
	
	function checkAll()
    {
        var ipts = $("#tabela_1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }
	

	function receber_todos()
	{
		var ipts = $("#tabela_1>tbody").find("input:checkbox:checked");
		
		var check = [];
	
		ipts.each(function(i, e) {
			check.push($(this).val());
		});
		
		if(check.length > 0)
		{
			if(confirm('Atenção\n\nConfirmar o recebimento da correspondência?\n\n'))
			{
				$.post( '<?php echo site_url('ecrm/atendimento_protocolo/receber_todos'); ?>',
				{
					'check[]' : check
				},
				function(data)
				{
					filtrar();
				});
			}
		}
		else
		{
			alert('Selecione no mínimo uma correspondência');
		}
	}
	
	$(function (){
		filtrar();
	});
</script>
<?php

$abas[] = array( 'aba_lista', 'Lista', true, 'location.reload();' );
$abas[] = array( 'aba_lista', 'Protocolo Digitalização', false, 'ir_protocolo_digitalizacao();' );

$arr_flag[] = array('text' => 'Sim', 'value' => 'S');
$arr_flag[] = array('text' => 'Não', 'value' => 'N');

$arr_gerar[] = array('text' => 'Tela', 'value' => 'T');
$arr_gerar[] = array('text' => 'Exportar para PDF', 'value' => 'P');
$arr_gerar[] = array('text' => 'Integrar Mala Direta (Etiquetas)', 'value' => 'M');

$config['button'][] = array('Nova Correspondência', 'novo()');

if(gerencia_in(array('GP')))
{
	$config['button'][] = array('Integrar Mala Direta', 'integrar_mala_direta()');
}

if(gerencia_in(array('GFC')))
{
	$config['button'][] = array('Receber Selecionados', 'receber_todos()');
}

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter();
		echo form_default_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante :', false, true);
		echo form_default_text("nome", "Nome :");
		echo filter_dropdown('fl_recebido', 'Recebido :', $arr_flag);
		echo filter_dropdown('fl_cancelado', 'Cancelado :', $arr_flag);
        echo filter_date_interval('dt_inclusao_inicial', 'dt_inclusao_final', 'Dt Remessa :', calcular_data(date('d/m/Y'),'3 week', '-'), date('d/m/Y'));
		echo form_default_time("hr_inclusao_inicial", "Hr Início Remessa : ");
        echo form_default_time("hr_inclusao_final", "Hr Fim Remessa : ");
        echo filter_date_interval('dt_devolucao_inicial', 'dt_devolucao_final', 'Dt. Devolvido:');
        echo filter_dropdown('fl_devolvido', 'Devolvido:', $arr_flag);
		echo filter_dropdown('cd_gerencia_origem', 'Gerência Origem :', $arr_gerencia_origem);
		echo filter_dropdown('cd_usuario_inclusao', 'Remetente :', $arr_remetente);
		echo filter_date_interval('dt_recebimento_inicial', 'dt_recebimento_final', 'Dt Recebimento :');
		echo filter_dropdown('cd_atendimento_protocolo_tipo', 'Tipo :', $arr_tipo);
		echo filter_dropdown('cd_atendimento_protocolo_discriminacao', 'Discriminação :', $arr_discriminacao);
		echo form_default_text("identificacao", "Identificação : ");
		echo filter_text('cd_atendimento', 'Protocolo :');
		echo filter_text('cd_encaminhamento', 'Encaminhamento :');
		echo filter_dropdown('fl_gerar', 'Gerar :', $arr_gerar, array("T"));
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 

$this->load->view('footer'); 