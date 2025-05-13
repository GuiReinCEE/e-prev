<?php
	set_title('Meu Retrato Edição');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ficaadica', 'comentario_rentabilidade')) ?>

	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao') ?>";
    }

    function ir_participante()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante/'.$row['cd_edicao']) ?>";
    }

    function replicar(fl_tipo)
    {
        var confirmacao = "Deseja REPLICAR as informações?\n\n"+
                              "Clique [Ok] para Sim\n\n"+
                              "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {  
            location.href = "<?= site_url('ecrm/meu_retrato_edicao/duplicar_comunicao/'.$row['cd_edicao']) ?>/"+fl_tipo;
        }
    }

    function libera()
    {
    	var confirmacao = "Deseja Liberar o Meu Reatrato?\n\n"+
    					  "O grupo e-mail marketing será criado automaticamente.\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/meu_retrato_edicao/libera/'.$row['cd_edicao']) ?>";
        }
    }

    function equilibrio_add()
    {
        if(($("#equilibrio_nr_ano").val() != "") && ($("#equilibrio_vl_provisao").val() != "") && ($("#equilibrio_vl_cobertura").val() != ""))
        {
            var confirmacao = "Deseja ADICIONAR?\n\n"+
                              "Clique [Ok] para Sim\n\n"+
                              "Clique [Cancelar] para Não\n\n"; 

            if(confirm(confirmacao))
            {           
                $.post("<?= site_url('ecrm/meu_retrato_edicao/equilibrio_add') ?>",
                {
                    cd_edicao    : $("#cd_edicao").val(),
                    nr_ano       : $("#equilibrio_nr_ano").val(),
                    vl_provisao  : $("#equilibrio_vl_provisao").val(),
                    vl_cobertura : $("#equilibrio_vl_cobertura").val()      
                },
                function(data)
                {
                    equilibrio();
                });
            }
        }
        else
        {
            alert("Preencha os campos");
        }
    }   

    function equilibrio()
    {
        $("#equilibrio_result_div").html("<?= loader_html() ?>");
                
        $.post("<?= site_url('ecrm/meu_retrato_edicao/equilibrio_listar') ?>",
        {
            cd_edicao : $("#cd_edicao").val()
        },
        function(data)
        {
            $("#equilibrio_result_div").html(data);
            //configure_result_table();
        }); 
    }

    function equilibrio_del(cd_edicao_equilibrio)
    {
        if (cd_edicao_equilibrio != "")
        {
            var confirmacao = "Deseja EXCLUIR?\n\n"+
                              "Clique [Ok] para Sim\n\n"+
                              "Clique [Cancelar] para Não\n\n"; 

            if(confirm(confirmacao))
            {           
                $.post("<?= site_url('ecrm/meu_retrato_edicao/equilibrio_del') ?>",
                {
                    cd_edicao_equilibrio : cd_edicao_equilibrio 
                },
                function(data)
                {
                    equilibrio();
                });
            }
        }
        else
        {
            alert("Código não informado");
        }
    }


    function getIndiceComparativo()
    {
        if( ($.trim($("#dt_inicial_comparativo").val()) != "") && ($.trim($("#dt_final_comparativo").val()) != "") )
        {       
            getIndice(3,"CDI",'comparativo_vl_cdi');
            getPoupanca("comparativo_vl_poupanca");
            getIndice(1,'INPC','comparativo_vl_inpc');
            getIndice(4,"IGPM",'comparativo_vl_igpm');
            getRentabilidade('comparativo_vl_plano');
        }
        else
        {
            alert("Informe as datas de início e fim para o comparativo.");
            $("#dt_inicial_comparativo").focus();
        }
    }

	function getIndice(cd_indice, ds_titulo, id_campo)
	{
		if( ($.trim($("#dt_inicial_comparativo").val()) != "") && ($.trim($("#dt_final_comparativo").val()) != "") )
        {
            $("#RESULT_"+ds_titulo).html("Buscando a "+ds_titulo+", aguarde...");
            
            $.post("<?= site_url('ecrm/meu_retrato_edicao/getIndice') ?>",
            {
                cd_indice : cd_indice,
                ds_titulo : ds_titulo,
                dt_ini    : $("#dt_inicial_comparativo").val(),
                dt_fim    : $("#dt_final_comparativo").val()
            },
            function(data)
            {
                $("#"+id_campo).val(data.pr_acumulado_formatado);
                $("#RESULT_"+ds_titulo).html("");

            },'json');	
        }
        else
        {
            alert("Informe as datas de início e fim para o comparativo.");
            $("#dt_inicial_comparativo").focus();
        }
	}    

	function getRentabilidade(id_campo)
	{
		if( ($.trim($("#dt_inicial_comparativo").val()) != "") && ($.trim($("#dt_final_comparativo").val()) != "") )
        {
            $("#RESULT_RENTABILIDADE").html("Buscando a RENTABILIDADE, aguarde...");

            $.post("<?= site_url('ecrm/meu_retrato_edicao/getRentabilidade') ?>",
            {
                cd_plano   : $("#cd_plano").val(),
                cd_empresa : $("#cd_plano_empresa").val(),
                dt_ini     : $("#dt_inicial_comparativo").val(),
                dt_fim     : $("#dt_final_comparativo").val()
            },
            function(data)
            {
                $("#"+id_campo).val(data.nr_cota_acumulada_formatado);
                $("#RESULT_RENTABILIDADE").html("");

            },'json');	
        }
        else
        {
            alert("Informe as datas de início e fim para o comparativo.");
            $("#dt_inicial_comparativo").focus();
        }
	} 

 	function getPoupanca(id_campo)
	{
		if( ($.trim($("#dt_inicial_comparativo").val()) != "") && ($.trim($("#dt_final_comparativo").val()) != "") )
        {
            $("#RESULT_POUPANCA").html("Buscando a POUPANÇA, aguarde...");

            $.post("<?= site_url('ecrm/meu_retrato_edicao/getPoupanca') ?>",
            {
                dt_ini     : $("#dt_inicial_comparativo").val(),
                dt_fim     : $("#dt_final_comparativo").val()
            },
            function(data)
            {
                $("#"+id_campo).val(data.pr_acumulado_formatado);
                $("#RESULT_POUPANCA").html("");

            },'json');	
        }
        else
        {
            alert("Informe as datas de início e fim para o comparativo.");
            $("#dt_inicial_comparativo").focus();
        }
	}         
    

    $(function(){
        if($("#cd_edicao").val() > 0)
        {
            equilibrio();
        }
    }); 
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    $abas[] = array('aba_participante', 'Meu Retrato Participante', FALSE, 'ir_participante();');

    echo aba_start($abas);
    	echo form_open('ecrm/meu_retrato_edicao/salvar_comunicacao');
            echo form_start_box('default_edicao_box', 'Edição');	
                echo form_default_row('cd_edicao', 'Edição:', '<span class="label label-inverse">'.$row['cd_edicao'].'</span>');
                
                echo form_default_hidden('cd_plano', '', $row['cd_plano']);
                echo form_default_hidden('cd_plano_empresa', '', $row['cd_empresa']);

                echo form_default_row('sigla', 'Empresa:', $row['sigla']);   
                echo form_default_row('plano', 'Plano:', $row['plano']);   
            	echo form_default_row('nr_extrato', 'Nº Extrato:', $row['nr_extrato']);   	
        		echo form_default_row('dt_base_extrato', 'Dt. Base:', $row['dt_base_extrato']);
            	echo form_default_row('dt_inclusao', 'Dt. Inclusão:', '<span class="label">'.$row['dt_inclusao'].'</span>');
                echo form_default_row('usuario_inclusao', 'Usuário Inclusão:', '<span class="label">'.$row['usuario_inclusao'].'</span>');
                echo form_default_row('dt_alteracao', 'Dt. Alteração:', '<span class="label label-info">'.$row['dt_alteracao'].'</span>');
                echo form_default_row('usuario_alteracao', 'Usuário Alteração:', '<span class="label label-info">'.$row['usuario_alteracao'].'</span>');
                echo form_default_row('dt_liberacao_informatica', 'Dt. Informática:', '<span class="label label-warning">'.$row['dt_liberacao_informatica'].'</span>');
                echo form_default_row('usuario_informatica', 'Usuário Informática:', '<span class="label label-warning">'.$row['usuario_informatica'].'</span>');
                echo form_default_row('dt_liberacao_atuarial', 'Dt. Atuarial:',  '<span class="label label-important">'.$row['dt_liberacao_atuarial'].'</span>');
                echo form_default_row('usuario_atuarial', 'Usuário Atuarial:',  '<span class="label label-important">'.$row['usuario_atuarial'].'</span>');

                if(trim($row['dt_liberacao_comunicacao']) != '')
                {
                    echo form_default_row('dt_liberacao_comunicacao', 'Dt. Liberação Comunicação:', '<span class="label label-success">'.$row['dt_liberacao_comunicacao'].'</span>');
                    echo form_default_row('usuario_comunicacao', 'Usuário Comunicação:', '<span class="label label-success">'.$row['usuario_comunicacao'].'</span>');
                }

            echo form_end_box('default_edicao_box');
            echo form_start_box('default_box', 'Cadastro');	
            	echo form_default_hidden('cd_edicao', '', $row['cd_edicao']);

                if(intval($row['cd_plano']) != 1)
                {
                   echo form_default_textarea('ficaadica', 'Fica a Dica:', $row);
                }

                if(intval($row['cd_plano']) == 1)
                {
                   echo form_default_date('dt_equilibrio', 'Dt. Referência Gráfico Equilíbrio:', $row); 
                   echo form_default_text('ds_equilibrio', 'Texto Gráfico Equilíbrio:', $row, 'style="width: 500px;"'); 
                }   

                if(intval($row['cd_plano']) == 1)
                {
                    $label_comentario = 'Comentários sobre o equilíbrio do plano:'; 
                }
                else
                {
                    $label_comentario = 'Comentários sobre a rentabilidade:'; 
                }

            	echo form_default_textarea('comentario_rentabilidade', $label_comentario, $row);
            	echo form_default_upload_iframe('arquivo_comparativo', 'meu_retrato', 'Arquivo Comparativo :', array($row['arquivo_comparativo'], $row['arquivo_comparativo_nome']), 'meu_retrato', true);

                if(intval($row['cd_plano']) == 1)
                {
                    echo form_default_upload_iframe('arquivo_premissas_atuariais', 'meu_retrato', 'Arquivo Premissas Atuarias :', array($row['arquivo_premissas_atuariais'], $row['arquivo_premissas_atuariais_nome']), 'meu_retrato');
                }
				
				#### DADOS GRAFICO COMPARATIVO RENTABILIDADE ####
				if (intval($row['cd_plano']) > 1)
				{
                    $dt_inicial_comparativo = $row['dt_inicial_comparativo'];
                        
                    if(trim($row['dt_inicial_comparativo']) == "")
                    { 
                        $dt = new DateTime($row['dt_base_comparativo']);
                        $dt->modify('-10 years');
                        $dt->modify('+1 month');
                        $dt_inicial_comparativo = date_format($dt, '01/m/Y');
                    }

                    echo form_default_row('', '', '');
                    echo form_default_hidden('cd_edicao_comparativo', 'cd_edicao_comparativo', $row['cd_edicao_comparativo']);
                    echo form_default_date('dt_inicial_comparativo', 'Dt. Inicial:', $dt_inicial_comparativo);
                    echo form_default_date('dt_final_comparativo', 'Dt. Final:', ( trim($row['dt_final_comparativo']) == "" ? $row['dt_base_extrato'] : $row['dt_final_comparativo']) );
                    
                    echo form_default_row('', '', '<input type="button" value="Buscar índices" onclick="getIndiceComparativo();">');
                    echo form_default_row('', '', '
                    <div id="RESULT_RENTABILIDADE"></div>
                    <div id="RESULT_CDI"></div>
                    <div id="RESULT_POUPANCA"></div>
                    <div id="RESULT_INPC"></div>
                    <div id="RESULT_IGPM"></div>
                    ');

					echo form_default_numeric('comparativo_vl_plano', 'Rentabilidade Plano:', number_format(floatval($row['vl_plano']),2,",","."));
					echo form_default_numeric('comparativo_vl_cdi', 'CDI:', number_format(floatval($row['vl_cdi']),2,",","."));
					echo form_default_numeric('comparativo_vl_poupanca', 'Poupança:', number_format(floatval($row['vl_poupanca']),2,",","."));
					echo form_default_numeric('comparativo_vl_inpc', 'INPC:', number_format(floatval($row['vl_inpc']),2,",","."));
					echo form_default_numeric('comparativo_vl_igpm', 'IGPM:', number_format(floatval($row['vl_igpm']),2,",","."));
					#echo form_default_numeric('comparativo_vl_ipca_ibge', 'IPCA-IBGE:', number_format(floatval($row['vl_ipca_ibge']),2,",","."));
				}

            
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
            	echo button_save('Salvar');

                echo button_save('Replicar Informações (MESMO PLANO e TIPO)', 'replicar("S");', 'botao_amarelo');

            	if((trim($row['dt_liberacao_atuarial']) != '') AND (trim($row['dt_liberacao_comunicacao']) == ''))
            	{
            		echo button_save('Liberar', 'libera();', 'botao_verde');
            	}
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
        if(intval($row['cd_plano']) == 1)
        {
            echo form_start_box('equilibrio_box', 'Dados para o gráfico do Equilíbrio Atuarial');   
                echo form_default_integer('equilibrio_nr_ano', 'Ano:');
                echo form_default_numeric('equilibrio_vl_provisao', 'Valor Provisões Matemáticas:','0,00');
                echo form_default_numeric('equilibrio_vl_cobertura', 'Valor Patrimônio de Cobertura:','0,00');
                
                
                echo form_default_row('', '',   button_save('Adicionar', 'equilibrio_add();', 'botao'));
                echo form_default_row('', '',  br(2).'<div id="equilibrio_result_div"></div>');
            
            echo form_end_box('equilibrio_box');
        }
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>