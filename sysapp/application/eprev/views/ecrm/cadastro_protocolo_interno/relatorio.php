<?php
set_title('Relatório do Protocolo Interno');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('/ecrm/cadastro_protocolo_interno/relatorio_lista');?>', $('#filter_bar_form').serialize(),
		function(data) {
			$('#result_div').html(data);
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateTimeBR',
            'CaseInsensitiveString',
            'DateTimeBR',
            'RE',
            'CaseInsensitiveString',
            'Number',
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
        ob_resul.sort(0, true);
    }

    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno"); ?>';
    }
	
	function ir_resumo()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/resumo'); ?>";
    }

    $(function(){
        //filtrar();
		
		if($('#cd_tipo_doc').val() != '')
		{
			consultar_tipo_documentos__cd_tipo_doc();
		}
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', true, 'void(0);');
$abas[] = array('aba_resumo', 'Resumo', false, 'ir_resumo();');

$config['value'] = $cd_documento;

$arr_sim_nao[] = array('value' => 'S', 'text' => 'Sim');
$arr_sim_nao[] = array('value' => 'N', 'text' => 'Não');

echo aba_start($abas);
    echo form_list_command_bar();
    echo form_start_box_filter();
        echo filter_text('nr_ano', 'Ano:');
        echo filter_text('nr_contador', 'Sequencia:');
        echo form_default_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante'), 'Participante:', false, true, false);
        echo filter_text('nome_participante', 'Nome do participante:');
        echo form_default_tipo_documento($config);
        echo filter_date_interval('dt_envio_inicio', 'dt_envio_fim', 'Data de envio:', date('d/m/Y'), date('d/m/Y'));
        echo filter_date_interval('dt_recebimento_inicio', 'dt_recebimento_fim', 'Data de recebimento:');
        echo filter_dropdown('cd_gerencia_remetente', 'Gerência Remetente:', $arr_gerencia);
        echo filter_dropdown('cd_usuario_envio', 'Remetente:', $usuario_envio_dd);
		echo filter_dropdown('cd_gerencia_destino', 'Gerência Destino:', $arr_gerencia);
        echo filter_dropdown('cd_usuario_destino', 'Destino:', $usuario_destino_dd);
        echo filter_dropdown('cd_usuario_encerrado', 'Encerrado por:', $usuario_encerrado_dd);
		echo filter_dropdown('fl_encerrado', 'Encerrado:', $arr_sim_nao);
		echo filter_dropdown('fl_enviado', 'Enviado:', $arr_sim_nao);
        echo filter_dropdown('tipo_solicitacao',"Tipo de Solicitação GCM:",$tipo_solicitacao);
        echo filter_date_interval('dt_devolucao_ini', 'dt_devolucao_fim', 'Data de devolução:');
		echo filter_dropdown('fl_mostrar_documentos', 'Mostrar documentos (471 e 51):', $arr_sim_nao, 'N');
    echo form_end_box_filter();
	echo '
		<div id="result_div">
			<br><br>
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>