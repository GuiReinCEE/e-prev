<?php
	set_title('Relatório Protocolo Digitalização');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html(); ?>");

		$.post("<?= site_url('ecrm/protocolo_digitalizacao/relatorio_lista') ?>",
		{
			nr_ano                : $('#nr_ano').val(),
			nr_contador           : $('#nr_contador').val(),
			tipo_protocolo        : $('#tipo_protocolo').val(),
			cd_empresa            : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia       : $('#seq_dependencia').val(),
			nome_participante     : $('#nome_participante').val(),
			cd_tipo_doc           : $('#cd_tipo_doc').val(),
			cd_doc_juridico       : $('#cd_doc_juridico').val(),
			dt_envio_inicio       : $('#dt_envio_inicio').val(),
			dt_envio_fim          : $('#dt_envio_fim').val(),
			dt_recebimento_inicio : $('#dt_recebimento_inicio').val(),
			dt_recebimento_fim    : $('#dt_recebimento_fim').val(),
			cd_usuario_envio      : $('#cd_usuario_envio').val(),
			ds_processo           : $('#ds_processo').val(),
			ds_mes_ano_indicador  : $('#ds_mes_ano_indicador').val(),

			qt_pagina             : $('#qt_pagina').val(),
			nr_pagina             : $('#nr_pagina').val()
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
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTimeBR',
			'DateTimeBR',
			'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString',
			'DateBR',
			'DateTimeBR',
			'CaseInsensitiveString',
			'RE',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'CaseInsensitiveString',
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number', 
			'CaseInsensitiveString'
		]);
		ob_resul.onsort = function()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(7, true);
	}


	function ir_lista()
	{
		location.href = '<?= site_url('ecrm/protocolo_digitalizacao'); ?>';
	}

	$(function(){
		if($("#qt_pagina").val() == "")
		{
			$("#qt_pagina").val(100);
		}
		
		if($("#nr_pagina").val() == "")
		{
			$("#nr_pagina").val(1);
		}

		if($("#nr_ano").val() == "" || $("#nr_contador").val() == "")
		{
			$("#dt_recebimento_inicio_dt_recebimento_fim_shortcut").val("last7days");
			$("#dt_recebimento_inicio_dt_recebimento_fim_shortcut").change();
		}
		else
		{
			$("#dt_recebimento_inicio_dt_recebimento_fim_shortcut").val("reset");
			$("#dt_recebimento_inicio_dt_recebimento_fim_shortcut").change();
		}

		filtrar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', true, 'location.reload();');

$ar_tipo = array(
	array('text' => 'Papel', 'value' => 'P'),
	array('text' => 'Digital', 'value' => 'D')
);

/*
$config['id_codigo'] = 'cd_doc_juridico';
$config['id_nome']   = 'nome_documento_juridico';
$config['caption']='Documento Jurídico: ';	
*/

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter();
		echo filter_integer('nr_ano', 'Ano: ', $ano_filtro);
		echo filter_integer('nr_contador', 'Sequência: ', $seq_filtro);
		echo filter_dropdown('tipo_protocolo', 'Tipo: ', $ar_tipo);					
		echo form_default_participante(
			array(
				'cd_empresa',
				'cd_registro_empregado',
				'seq_dependencia', 
				'nome_participante'
			), 'Participante: ', false, true, false);
		echo filter_text('nome_participante', 'Nome do participante: ',"",'style="width: 500px;"');
		echo form_default_tipo_documento();
		#echo form_default_tipo_documento_juridico($config);
		echo form_default_text('ds_processo', 'Processo: ',"",'style="width: 500px;"' );
		echo filter_date_interval('dt_envio_inicio','dt_envio_fim','Data de envio:');
		echo filter_date_interval('dt_recebimento_inicio','dt_recebimento_fim','Data de recebimento: ');
		echo filter_dropdown('cd_usuario_envio', 'Remetente: ', $usuario_envio_dd);
		echo form_default_row('', '', '');
		echo filter_dropdown('ds_mes_ano_indicador', 'Mês/Ano Indicador: ', $mes_ano_indicador);		
		echo form_default_row('', '', '');
		echo filter_integer('qt_pagina', 'Qt por Página:');
		echo filter_integer('nr_pagina', 'Página:');
	echo form_end_box_filter();
	echo '
		<div id="result_div">
			<br/><br/>
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br(2);	
	echo aba_end(); 

$this->load->view('footer'); 
?>