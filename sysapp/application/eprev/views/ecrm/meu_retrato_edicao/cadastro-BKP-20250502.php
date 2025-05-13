<?php
	set_title('Meu Retrato Edição - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_edicao', 'cd_plano', 'cd_plano_empresa', 'dt_base_extrato'), 'valida_plano(form)') ?>

    function valida_plano(form)
    {
        var cd_plano = parseInt($("#cd_plano").val());
        var fl_submit = true;

        if($("#cd_plano").val() !== 1)
        {
            if($("#tp_participante").val() == '')
            {
                alert("Informe o Tipo de Participante.")
                fl_submit = false;
            }
        }

        if(fl_submit)
        {
            form.submit();
        }
    }

	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao') ?>";
    }

    function ir_participante()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante/'.$row['cd_edicao']) ?>";
    }
	
    function ir_verificar()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/verificar/'.$row['cd_edicao']) ?>";
    }	

    function libera_atuarial()
    {
    	var confirmacao = "Deseja Liberar o Meu Reatrato para a GC-Atuarial?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/meu_retrato_edicao/libera_informatica/'.$row['cd_edicao']) ?>";
        }
    }

    function libera_beneficio()
    {
        var confirmacao = "Deseja Liberar o Meu Reatrato para a GP-Benefício?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/meu_retrato_edicao/libera_informatica_beneficio/'.$row['cd_edicao']) ?>";
        }
    }

    function gerar()
    {
        var confirmacao = "Deseja gerar o meu retrato?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/meu_retrato_edicao/gerar/'.$row['cd_edicao']) ?>";
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

    if(intval($row['cd_edicao']) > 0)
    {
        $abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante();');
		if(gerencia_in(array('GTI')))
		{
			$abas[] = array('aba_verificar', 'Verificar', FALSE, 'ir_verificar();');
		}		
    }   

    echo aba_start($abas);
    	echo form_open('ecrm/meu_retrato_edicao/salvar');
            echo form_start_box('default_box', 'Cadastro');	
            	if(intval($row['cd_edicao']) > 0)
            	{
            		echo form_default_row('cd_edicao', 'Edição:', '<span class="label label-inverse">'.$row['cd_edicao'].'</span>');
            	}	

                echo form_default_hidden('cd_edicao', '', $row['cd_edicao']);
                echo form_default_plano_empresa_ajax('cd_plano', $row['cd_plano'], $row['cd_empresa'], 'Plano: (*)', 'Empresa: (*)');
                echo form_default_dropdown('tp_participante', 'Tipo Participante: (*)', $tipo_participante, $row['tp_participante']);
                echo form_default_date('dt_base_extrato', 'Dt. Base:', $row);
            	
            	if(intval($row['cd_edicao']) > 0)
            	{
                    if(intval($row['cd_plano']) != 1)
                    {
                        echo form_default_integer('nr_extrato', 'Nº Extrato:', $row);
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
	            	echo form_default_upload_iframe('arquivo_comparativo', 'meu_retrato', 'Arquivo Comparativo :', array($row['arquivo_comparativo'], $row['arquivo_comparativo_nome']), 'meu_retrato');

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

					echo form_default_row('', '', '');

	            	echo form_default_row('dt_inclusao', 'Dt. Inclusão:', '<span class="label">'.$row['dt_inclusao'].'</span>');
                    echo form_default_row('usuario_inclusao', 'Usuário Inclusão:', '<span class="label">'.$row['usuario_inclusao'].'</span>');
                    echo form_default_row('dt_alteracao', 'Dt. Alteração:', '<span class="label label-inverse">'.$row['dt_alteracao'].'</span>');
                    echo form_default_row('usuario_alteracao', 'Usuário Alteração:', '<span class="label label-inverse">'.$row['usuario_alteracao'].'</span>');
                    
                    if(trim($row['dt_liberacao_informatica']) != '')
                    {
                        echo form_default_row('dt_liberacao_informatica', 'Dt. Informática:', '<span class="label label-warning">'.$row['dt_liberacao_informatica'].'</span>');
                        echo form_default_row('usuario_informatica', 'Usuário:', '<span class="label label-warning">'.$row['usuario_informatica'].'</span>');
                    }

                    if(trim($row['dt_liberacao_atuarial']) != '')
                    {
                        echo form_default_row('dt_liberacao_atuarial', 'Dt. Atuarial/Benefício:',  '<span class="label label-info">'.$row['dt_liberacao_atuarial'].'</span>');
                        echo form_default_row('usuario_atuarial', 'Usuário:',  '<span class="label label-info">'.$row['usuario_atuarial'].'</span>');
                    }

                    if(trim($row['dt_liberacao_comunicacao']) != '')
                    {
                        echo form_default_row('dt_liberacao_comunicacao', 'Dt. Comunicação:', '<span class="label label-success">'.$row['dt_liberacao_comunicacao'].'</span>');
                        echo form_default_row('usuario_comunicacao', 'Usuário:', '<span class="label label-success">'.$row['usuario_comunicacao'].'</span>');
                    }
            	}

            echo form_end_box('default_box');
            echo form_command_bar_detail_start();

                echo button_save('Salvar');

            	if(trim($row['dt_liberacao_informatica']) == '')
            	{
            		if(intval($row['cd_edicao']) > 0)
	            	{
                        if(trim($row['fl_gerar']) == 'S')
                        {
                            if(intval($row['cd_plano']) == 1 OR (intval($row['cd_plano']) == 2 AND trim($row['tp_participante']) == 'APOSM'))
                            {
                                echo button_save('Liberar', 'libera_beneficio();', 'botao_verde');
                            }
                            else
                            {
                                echo button_save('Liberar', 'libera_atuarial();', 'botao_verde');
                            }
                        }
                        else
                        {
                            echo button_save('Gerar', 'gerar();', 'botao_verde');
                        }
	            	}
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

		
        echo br(10);
	echo aba_end();

    $this->load->view('footer_interna');
?>