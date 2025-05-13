<?php
set_title('Protocolo de Digitalização');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        load();
    }

    function load()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");
		
        $.post('<?= site_url("ecrm/protocolo_digitalizacao/listar") ?>', 
        $("#filter_bar_form").serialize(),
        function(data)
        { 
            $("#result_div").html(data);
            configure_result_table(); 
        });
    }

    function configure_result_table()
    {
        var nr_ordem = <?= ((gerencia_in(array("GAD"))) ? "3" : "0") ?>;
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'DateTimeBR',
            'CaseInsensitiveString',
            'DateTimeBR',
            'CaseInsensitiveString',
            'DateTimeBR',
            'CaseInsensitiveString',
            null, null,null,null,null,null, null
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
        ob_resul.sort(nr_ordem, true);
    }

    function novo()
    {
        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe/0"); ?>';
    }

    function editar_protocolo(id)
    {
        location.href='<?php echo site_url('ecrm/protocolo_digitalizacao/detalhe/'); ?>'+'/'+id;
    }

    function receber_protocolo(id,tipo)
    {
        location.href='<?php echo site_url('ecrm/protocolo_digitalizacao/receber/'); ?>'+'/'+id;
    }

    function indexar_protocolo(id,tipo)
    {
        location.href='<?php echo site_url('ecrm/protocolo_digitalizacao/indexar/'); ?>'+'/'+id;
    }

    function enviar_protocolo(id)
    {
        if( confirm('Enviar o protocolo?') )
        {
            $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/enviar_protocolo'); ?>", 
            {
                cd_documento_protocolo : id
            }, 
            function(data)
            { 
                if(data=='true')
                {
                    filtrar();
                }
                else
                {
                    alert(data);
                } 
            });
        }
    }

    function ir_relatorio(cd_documento_protocolo)
    {
        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/relatorio"); ?>';
    }	
	
	function excluir_protocolo(cd_documento_protocolo)
    {
		if( confirm('Excluir protocolo?') )
		{
			location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/excluir_protocolo"); ?>/'+cd_documento_protocolo;
		}
    }

    $(function(){
        if(<?= intval($nr_ano) ?> > 0)
        {
            $("#ano").val("<?= $nr_ano ?>");
        }
        else if($("#ano").val() == "")
        {
            $("#ano").val("<?= date('Y') ?>");
        }

        if(<?= intval($nr_ano) ?> > 0 && <?= intval($nr_sequencia) ?> > 0)
        {
            $("#dt_inclusao_ini").val("");
            $("#dt_inclusao_fim").val("");
        }
        else
        {
            $("#dt_inclusao_ini").val("<?= calcular_data('', '1 month') ?>");
            $("#dt_inclusao_fim").val("<?= date('d/m/Y') ?>");
        }
    });
        
</script>

<?php
$abas[] = array('aba_lista', 'Lista', true, 'location.reload();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');

$config['button'][] = array('Novo Protocolo', 'novo()');

$ar_tipo = Array(Array('text' => 'Papel', 'value' => 'P'), Array('text' => 'Digital', 'value' => 'D'));
$ar_envio = Array(Array('text' => 'Sim', 'value' => 'S'), Array('text' => 'Não', 'value' => 'N'));
$ar_recebido = Array(Array('text' => 'Sim', 'value' => 'S'), Array('text' => 'Não', 'value' => 'N'));

echo aba_start($abas);

    echo form_list_command_bar($config);
    echo form_start_box_filter('filter_bar', 'Filtros');
        echo filter_integer('ano', 'Ano: ');
        echo filter_integer('contador', 'Sequência: ', $nr_sequencia);
        echo filter_dropdown('tipo_protocolo', 'Tipo: ', $ar_tipo);
        echo filter_dropdown('fl_envio', 'Enviado: ', $ar_envio);
        echo filter_dropdown('fl_recebido', 'Recebido: ', $ar_recebido);
        echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Cadastro: ');
        echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt Envio: ');
        echo filter_date_interval('dt_recebido_ini', 'dt_recebido_fim', 'Dt Recebido: ');
        echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
        echo filter_dropdown('cd_usuario_envio', 'Usuário Envio: ', $usuario_envio);
    echo form_end_box_filter();

    echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';

    echo br(2);

echo aba_end();

$this->load->view('footer');
?>