<?php
set_title('Aviso de desligamento');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        if($('#cd_plano_empresa').val() == "")
        {
            alert("Informe a empresa");
        }
        else if($('#nr_mes').val() == "")
        {
            alert("Informe o mês");
        }
        else if($('#nr_ano').val() == "")
        {
            alert("Informe o ano");
        }	
        else
        {	
            load();
        }
    }

    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

        $.post( '<?php echo base_url() . index_page(); ?>/planos/aviso_desligamento/listar'
        ,{
            cd_empresa : $('#cd_plano_empresa').val(),
            cd_plano   : $("#cd_plano").val(),
            nr_mes     : $('#nr_mes').val(),
            nr_ano     : $('#nr_ano').val()
        }
        ,
        function(data)
        {
            document.getElementById("result_div").innerHTML = data;
            configure_result_table();
        }
    );
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'RE',
            'CaseInsensitiveString',
            'DateTimeBR',
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
        ob_resul.sort(1, false);
		
		
        if(($('#qt_registro').val() > 0) && ($('#fl_gerado_email').val() == 0) && ($('#fl_envia_email').val() == 0))
        {
            $("#btGerarEmail").show();
            $("#btEnviaEmail").hide();
            $("#btGerarProtocolo").hide();
            $("#status_envio").html("Ainda <b>NÃO</b> foi GERADO email para esta empresa e competência.");
        }
        else if(($('#qt_registro').val() > 0) && (($('#fl_gerado_email').val() > 0)) && ($('#fl_envia_email').val() == 0))
        {
            $("#btGerarEmail").hide();
            $("#btEnviaEmail").show();
            $("#btGerarProtocolo").hide();
            $("#status_envio").html("Ainda <b>NÃO</b> foi ENVIADO email para esta empresa e competência.");	
        }		
        else if($('#fl_envia_email').val() > 0)
        {
            $("#btGerarEmail").hide();
            $("#btEnviaEmail").hide();
            $("#btGerarProtocolo").show();
            $("#status_envio").html("Já foi enviado email para esta empresa e competência.");		
        }
        else
        {
            $("#btGerarEmail").hide();
            $("#btEnviaEmail").hide();
            $("#btGerarProtocolo").show();
            $("#status_envio").html("Não foi encontrado registro(s) para esta empresa e competência.");
        }
		
    }

	function gerarEmail()
    {
        if($('#cd_plano_empresa').val() == "")
        {
            alert("Informe a empresa");
        }
        else if($('#nr_mes').val() == "")
        {
            alert("Informe o mês");
        }
        else if($('#nr_ano').val() == "")
        {
            alert("Informe o ano");
        }	
        else
        {	
            var confirmacao = 'Deseja GERAR os emails de aviso de desligamento de ' + $("#nr_mes").val() + '/'+ $("#nr_ano").val() + '?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';		
			
            if(confirm(confirmacao))
            {
                $("#result_div").html("<?php echo loader_html(); ?>");
				
                $.post( '<?php echo base_url() . index_page(); ?>/planos/aviso_desligamento/gerar_email'
                ,{
                    cd_empresa : $('#cd_plano_empresa').val(),
                    cd_plano   : $("#cd_plano").val(),
                    nr_mes     : $('#nr_mes').val(),
                    nr_ano     : $('#nr_ano').val()
                }
                ,
                function(data)
                {
                    $("#result_div").html(data);
                    configure_result_table();
                }
            );
            }
        }	
    }
	
    function enviaEmail()
    {
        if($('#cd_plano_empresa').val() == "")
        {
            alert("Informe a empresa");
        }
        else if($('#nr_mes').val() == "")
        {
            alert("Informe o mês");
        }
        else if($('#nr_ano').val() == "")
        {
            alert("Informe o ano");
        }	
        else
        {	
            var confirmacao = 'ATENÇÃO esta ação é irreversível.\n\n' +
                'Confira a lista gerada antes de enviar os emails.\n\n' +
                'Confirma o envio de emails de aviso de desligamento de ' + $("#nr_mes").val() + '/'+ $("#nr_ano").val() + '?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';		
			
            if(confirm(confirmacao))
            {
                $("#result_div").html("<?php echo loader_html(); ?>");
				
                $.post( '<?php echo base_url() . index_page(); ?>/planos/aviso_desligamento/envia_email'
                ,{
                    cd_empresa : $('#cd_plano_empresa').val(),
                    cd_plano   : $("#cd_plano").val(),
                    nr_mes     : $('#nr_mes').val(),
                    nr_ano     : $('#nr_ano').val()
                }
                ,
                function(data)
                {
                    $("#result_div").html(data);
                    configure_result_table();
                }
            );
            }
        }	
    }
    
	function excluirAviso(cd_empresa,cd_registro_empregado,seq_dependencia,nr_ano_competencia,nr_mes_competencia)
	{
            var confirmacao = 'Confirma a exclusão?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';		
			
            if(confirm(confirmacao))
            {
                $("#result_div").html("<?php echo loader_html(); ?>");
				
                $.post('<?php echo base_url() . index_page(); ?>/planos/aviso_desligamento/excluir_aviso',
                {
                    cd_empresa            : cd_empresa,
                    cd_registro_empregado : cd_registro_empregado,
                    seq_dependencia       : seq_dependencia,
                    nr_mes                : nr_mes_competencia,
                    nr_ano                : nr_ano_competencia
                },
                function(data)
                {
                    filtrar();
                });
            }		
	}
	
    function gerar_protocolo()
    {
        if(confirm("Deseja GERAR Protocolo de Digitalização?"))
        {
            document.getElementById('filter_bar_form').action = "<?php echo base_url() . index_page(); ?>/planos/aviso_desligamento/gerar_protocolo/";
            document.getElementById('filter_bar_form').method = "post";
            document.getElementById('filter_bar_form').target = "_self";
            $("#filter_bar_form").submit();
        }
    }

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start($abas);

echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');
echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:(*)', 'Plano:(*)');
echo filter_integer('nr_mes', "Mês:(*)", (intval($nr_mes) > 0 ? intval($nr_mes) : date('m')));
echo filter_integer('nr_ano', "Ano:(*)", (intval($nr_ano) > 0 ? intval($nr_ano) : date('Y')));
echo form_end_box_filter();
?>

<div id="result_div"><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<script>
    filtrar();
</script>
<?php
echo br(5);
echo aba_end('');
$this->load->view('footer');
?>