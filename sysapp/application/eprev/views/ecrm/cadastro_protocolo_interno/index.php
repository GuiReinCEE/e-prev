<?php
set_title('Protocolo Interno');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        load();
    }

    function load()
    {
        if((($("#dt_cadastro_ini").val() != "") && ($("#dt_cadastro_fim").val() != "")) || (($("#nr_ano").val() != "") && ($("#nr_contador").val() != "")))
        {
            $("#result_div").html("<?php echo loader_html(); ?>");
			
            $.post('<?php echo site_url('/ecrm/cadastro_protocolo_interno/listar');?>', $('#filter_bar_form').serialize(), 
			function(data) 
			{
				$("#result_div").html(data);
				configure_result_table();
			});
        }
        else
        {
            alert("Informe o PROTOCOLO ou o PERÍODO DE CADASTRO antes de filtrar");
        }
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
			'Number',
			null,
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
			'DateTimeBR',
            'DateTimeBR',
			'DateTimeBR',
            'CaseInsensitiveString',
            null
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

    function novo()
    {
        location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno/detalhe/0"); ?>';
    }

    function ir_relatorio()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/relatorio'); ?>";
    }
	
	function ir_resumo()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/resumo'); ?>";
    }

    function excluir(cd_documento_recebido)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno/excluir"); ?>/' + cd_documento_recebido;
        }
    }
    
    $(function(){
        //filtrar();
    });
    
</script>

<?php
$abas[] = array('aba_lista', 'Lista', true, 'location.reload();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_resumo', 'Resumo', false, 'ir_resumo();');

$config['button'][] = array('Novo Protocolo Interno', 'novo()');

$ar_status = Array(
				Array('text' => 'Aguardando', 'value' => 'AG'), 
				Array('text' => 'Aguardando Envio', 'value' => 'AE'), 
				Array('text' => 'Aguardando Recebimento', 'value' => 'AR'), 
				Array('text' => 'Encerrado', 'value' => 'EN')
			);

$arr_sim_nao[] = array('value' => 'S', 'text' => 'Sim');
$arr_sim_nao[] = array('value' => 'N', 'text' => 'Não');

echo aba_start($abas);
    echo form_list_command_bar($config);
    echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_integer_ano('nr_ano', 'nr_contador', 'Ano/Protocolo: ');
        echo filter_date_interval('dt_cadastro_ini', 'dt_cadastro_fim', 'Dt Cadastro: ', calcular_data('', '2 month'), date('d/m/Y'));
        echo filter_dropdown('cd_status', 'Situação: ', $ar_status, array('AG'));
        echo filter_dropdown('cd_gerencia_remetente', 'Gerência Remetente: ', $arr_gerencia);
		echo filter_usuario_ajax('cd_usuario_destino', '', '', "Usuário Destino:", "Gerência Destino:");
		echo filter_dropdown('fl_mostrar_documentos', 'Mostrar documentos (471 e 51):', $arr_sim_nao, 'N');
        echo filter_dropdown('tipo_solicitacao',"Tipo de Solicitação GCM:",$tipo_solicitacao);
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