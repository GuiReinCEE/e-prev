<?php
set_title('Contribuição instituidor - Contribuição Atrasada');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
        {
            $("#result_div").html("<?php echo loader_html(); ?>");

            $.post('<?php echo site_url('/planos/contribuicao_instituidor_atrasada/atrasada');?>',
            {
                cd_empresa : $("#cd_plano_empresa").val(),
                cd_plano   : $("#cd_plano").val(),
                nr_mes     : $("#nr_mes").val(),
                nr_ano     : $("#nr_ano").val()
            },
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
	
    function gerar()
    {
        if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
        {
			if($("#dt_emissao_eletro").val() != "")
			{
				var dt_hoje = new Date();
				dt_hoje.zeroTime();	
				
				var dt_emissao_eletro = Date.fromString($('#dt_emissao_eletro').val());
				dt_emissao_eletro.zeroTime();
				

				if(dt_hoje > dt_emissao_eletro)
				{            
					var confirmacao = 'Confirma a geração de contribuição para a competência ' + $("#nr_mes").val() + '/'+ $("#nr_ano").val() + '?\n\n'+
						'Clique [Ok] para Sim\n\n'+
						'Clique [Cancelar] para Não\n\n';
									  
					if(confirm(confirmacao))
					{
						$("#result_div").html("<?php echo loader_html(); ?>");

						$.post('<?php echo site_url('/planos/contribuicao_instituidor_atrasada/gerar');?>',
						{
							cd_empresa : $("#cd_plano_empresa").val(),
							cd_plano   : $("#cd_plano").val(),
							nr_mes     : $("#nr_mes").val(),
							nr_ano     : $("#nr_ano").val()
						},
						function(data)
						{
							filtrar();
						});
					}
			
				}
				else
				{
					alert("ATENÇÃO\n\nOs e-mails só podem ser enviados 1 DIA APÓS A DATA DE EMISSÃO NO ELETRO")
				}
			}
			else
			{
				alert("Informe a Dt Emissão Eletro");
				$("#dt_emissao_eletro").focus();
			}				
        }
        else
        {
            alert("Informe os campos com (*) e clique em filtrar");
            $("#cd_plano_empresa").focus();
        }
    }
	
    function enviarEmail()
    {
        if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
        {
            var confirmacao = 'ATENÇÃO esta ação é irreversível.\n\n' +
                'Confira a lista gerada antes de enviar os emails.\n\n' +
                'Confirma o envio de emails de contribuição para a competência ' + $("#nr_mes").val() + '/'+ $("#nr_ano").val() + '?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
							  
            if(confirm(confirmacao))
            {
                $("#result_div").html("<?php echo loader_html(); ?>");

                $.post('<?php echo site_url('/planos/contribuicao_instituidor_atrasada/enviarEmail');?>',
                {
                    cd_empresa : $("#cd_plano_empresa").val(),
                    cd_plano   : $("#cd_plano").val(),
                    nr_mes     : $("#nr_mes").val(),
                    nr_ano     : $("#nr_ano").val()
                },
                function(data)
                {
                    filtrar();
                });
            }
        }
        else
        {
            alert("Informe os campos com (*) e clique em filtrar");
            $("#cd_plano_empresa").focus();
        }
    }	

    function configure_result_table()
    {
        if(document.getElementById("table-1"))
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),[
                "RE",
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "DateTimeBR"
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
    }	
	
    function semEmail()
    {
        if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
        {
            $("#result_div").html("<?php echo loader_html(); ?>");

            $.post('<?php echo site_url('/planos/contribuicao_instituidor_atrasada/sem_email');?>',
            {
                cd_empresa : $("#cd_plano_empresa").val(),
                cd_plano   : $("#cd_plano").val(),
                nr_mes     : $("#nr_mes").val(),
                nr_ano     : $("#nr_ano").val()
            },
            function(data)
            {
                $("#result_div").html(data);
                configure_result_table_sem_email();
            });
        }
        else
        {
            alert("Informe os campos com (*) e clique em filtrar");
            $("#cd_plano_empresa").focus();
        }
    }	
	
    function enviarEmailCadastro()
    {
        if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
        {
            var confirmacao = 'Confirma o envio da lista para o Cadastro (GAP).\n\n' +
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
            if(confirm(confirmacao))
            {
                $.post('<?php echo site_url('/planos/contribuicao_instituidor_atrasada/enviarEmailCadastro');?>',
                {
                    cd_empresa : $("#cd_plano_empresa").val(),
                    cd_plano   : $("#cd_plano").val(),
                    nr_mes     : $("#nr_mes").val(),
                    nr_ano     : $("#nr_ano").val()
                },
                function(data)
                {
                    alert("Envio para o Cadastro (GAP) realizado");
                });
            }
        }
        else
        {
            alert("Informe os campos com (*) e clique em filtrar");
            $("#cd_plano_empresa").focus();
        }
    }
	
    function configure_result_table_sem_email()
    {
        if(document.getElementById("table-1"))
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),[
                "RE",
                "CaseInsensitiveString",
                "CaseInsensitiveString"
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
    }	
    
    function gerar_protocolo()
    {
        if(confirm("Deseja GERAR Protocolo de Digitalização?"))
        {
            document.getElementById('filter_bar_form').action = "<?php echo site_url('/planos/contribuicao_instituidor_atrasada/gerar_protocolo/');?>";
            document.getElementById('filter_bar_form').method = "post";
            document.getElementById('filter_bar_form').target = "_self";
            $("#filter_bar_form").submit();
        }
    }
	
    function ir_relatorio()
    {
        location.href='<?php echo site_url("planos/contribuicao_instituidor_atrasada/relatorio"); ?>';
    }
	
	function excluir(cd_empresa, cd_registro_empregado, seq_dependencia, nr_ano_competencia, nr_mes_competencia, cd_contribuicao_controle_tipo)
    {
		var confirmacao = 'Confirma a exclusão?\n\n' +
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
        {
			$.post('<?php echo site_url('/planos/contribuicao_instituidor_atrasada/excluir');?>',
			{
				cd_empresa                    : cd_empresa,
				cd_registro_empregado         : cd_registro_empregado,
				seq_dependencia               : seq_dependencia,
				nr_ano_competencia            : nr_ano_competencia,
				nr_mes_competencia            : nr_mes_competencia,
				cd_contribuicao_controle_tipo : cd_contribuicao_controle_tipo
			},
			function(data)
			{
				filtrar();
			});
		}
	}
	
	$(function(){
		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Contribuição Atrasada', TRUE, 'location.reload();');
$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:(*)', 'Plano:(*)', 'I');
		echo filter_integer('nr_mes', "Mês:(*)", (intval($nr_mes) > 0 ? intval($nr_mes) : date('m')));
		echo filter_integer('nr_ano', "Ano:(*)", (intval($nr_ano) > 0 ? intval($nr_ano) : date('Y')));
	echo form_end_box_filter();
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Clique no botão [Filtrar] para exibir as informações</b></span></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');

?>