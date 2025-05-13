<?php
set_title('Reuniões SG - Lista');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        if(($('#dt_reuniao_ini').val() != '' && $('#dt_reuniao_fim').val() != '') || ($('#dt_ini_ini').val() != '' && $('#dt_ini_fim').val() != ''))  
        {
            load();
        }
        else
        {
            alert('Informe o filtro da data de reunião ou iniício reunião.');
        }
    }

    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

        $.post( '<?php echo base_url() . index_page(); ?>/atividade/reuniao_sg/listar_reuniao'
        ,{
            dt_reuniao_ini            : $('#dt_reuniao_ini').val(),
            dt_reuniao_fim            : $('#dt_reuniao_fim').val(),
			dt_ini_ini                : $('#dt_ini_ini').val(),
            dt_ini_fim                : $('#dt_ini_fim').val(),
            cd_reuniao_sg_instituicao : $('#cd_reuniao_sg_instituicao').val()
        }
        ,
        function(data)
        {
            document.getElementById("result_div").innerHTML = data;
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'DateTimeBR',  
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
        ob_resul.sort(0, true);
    }
    
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/index"); ?>';
    }
    
    function imprimir()
    {
        if(($('#dt_reuniao_ini').val() != '' && $('#dt_reuniao_fim').val() != '') || ($('#dt_ini_ini').val() != '' && $('#dt_ini_fim').val() != ''))  
        {
            filter_bar_form.method = "post";
            filter_bar_form.action = '<?php echo base_url() . index_page(); ?>/atividade/reuniao_sg/imprimir_relatorio';
            filter_bar_form.target = "_blank";
            filter_bar_form.submit();
        }
        else
        {
            alert('Informe o filtro da data de reunião.');
        }
            
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Relatório', TRUE, 'location.reload();');

$config['button'][] = array('Gerar Relatório', 'imprimir()');

echo aba_start($abas);

echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros');
    echo filter_date_interval('dt_reuniao_ini', 'dt_reuniao_fim', 'Dt da Reunião:', calcular_data('','1 month'), date('d/m/Y'));
	echo filter_date_interval('dt_ini_ini', 'dt_ini_fim', 'Dt Início Reunião:');
    echo filter_dropdown('cd_reuniao_sg_instituicao', 'Instituição:', $instituicoes);
echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize o filtro da data de reunião exibir a lista</b></span></div>
<br />
<?php
echo aba_end('');
?>
<script type="text/javascript">
    if($('#dt_reuniao_ini').val() != '' && $('#dt_reuniao_fim').val() != '')
    {
        filtrar();
    }
</script>
<?php
$this->load->view('footer');
?>