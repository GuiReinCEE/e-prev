<?php
    //echo "<pre>"; print_r($assunto); exit;

    set_title('Pauta SG - Assuntos');
	$this->load->view("header");
?>
<script>
	<?php
		$js_submit = array(
			'nr_item_sumula', 
			'ds_pauta_sg_assunto', 
			'cd_gerencia_responsavel',
			'cd_usuario_responsavel',
			'cd_gerencia_substituto',
			'cd_usuario_substituto',
            'fl_ordem_fornecimento',
            'fl_aplica_rds'
		);

		if(trim($row['fl_sumula']) == 'DE')
		{	
			$js_submit[] = 'cd_diretoria';
		}

		echo form_default_js_submit($js_submit, 'valida_pautar();');
	?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/pauta_sg/cadastro/'.$row['cd_pauta_sg']) ?>";
	}

	function ir_presentes()
	{
		location.href = "<?= site_url('gestao/pauta_sg/presentes/'.$row['cd_pauta_sg']) ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg']) ?>";
	}

	function sumula()
	{
		window.open("<?= site_url('gestao/pauta_sg/sumula/'.$row['cd_pauta_sg']) ?>");
	}

	function pauta()
	{
		window.open("<?= site_url('gestao/pauta_sg/pauta/'.$row['cd_pauta_sg']) ?>");
	}

	function remover(cd_pauta_sg_assunto)
	{
		var confirmacao = 'Deseja remover o assunto da pauta?\n\n'+
			'Removendo o assunto o mesma será incluído na próxima pauta\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/assunto_remover/'.$row['cd_pauta_sg']) ?>/"+cd_pauta_sg_assunto;
		}
	}

	function excluir(cd_pauta_sg_assunto)
	{
		var confirmacao = 'Deseja excluir o assunto da pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/assunto_excluir/'.$row['cd_pauta_sg']) ?>/"+cd_pauta_sg_assunto;
		}
	}

	function reabrir_assunto(cd_pauta_sg_assunto)
	{
		var confirmacao = 'Deseja reabrir o assunto da pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/reabrir_assunto/'.$row['cd_pauta_sg']) ?>/"+cd_pauta_sg_assunto;
		}
	}

	function enviar()
	{
		var confirmacao = 'Deseja enviar para os responsáveis?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/enviar/'.$row['cd_pauta_sg']) ?>";
		}
	}

	function encerrar()
	{
		var confirmacao = 'Deseja encerrar a pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/encerrar/'.$row['cd_pauta_sg']) ?>";
		}
	}

	function reabrir()
	{
		var confirmacao = 'Deseja reabrir a pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/reabrir/'.$row['cd_pauta_sg']) ?>";
		}
	}

	function enviar_colegiado()
	{
		var confirmacao = 'Deseja enviar e-mail para os membros do colegiado?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/enviar_colegiado/'.$row['cd_pauta_sg']) ?>";
		}
	}

	function configure_result_table()
	{
		<?php if(trim($row['fl_sumula']) == 'IN'): ?>
			var ob_resul = new SortableTable(document.getElementById("table-1"),
			[
				null,
				"Number",
				null,
			    "CaseInsensitiveString",
			    "Number",
			    "CaseInsensitiveString",
                "Number",
                "CaseInsensitiveString",
			    "CaseInsensitiveString",
			    "Number",
			    "CaseInsensitiveString",
			    "CaseInsensitiveString",
			    null,
			    "CaseInsensitiveString",
			    "Number",
			    "DateTimeBR",
				null
			]);
		<?php else : ?>
			var ob_resul = new SortableTable(document.getElementById("table-1"),
			[
				null,
				"Number",
				null,
			    "CaseInsensitiveString",
			    "Number",
			    "CaseInsensitiveString",
                "Number",
                "CaseInsensitiveString",
			    "Number",
			    "CaseInsensitiveString",
			    "CaseInsensitiveString",
			    null,
			    "CaseInsensitiveString",
			    "CaseInsensitiveString",
			    "Number",
			    "DateTimeBR",
				null
			]);
		<?php endif; ?>

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

	function set_ordem(cd_pauta_sg, cd_pauta_sg_assunto)
    {
        $("#ajax_ordem_valor_" + cd_pauta_sg_assunto).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('gestao/pauta_sg/set_ordem') ?>",
        {
        	cd_pauta_sg         : cd_pauta_sg,
            cd_pauta_sg_assunto : cd_pauta_sg_assunto,
            nr_item_sumula      : $("#nr_item_sumula_" + cd_pauta_sg_assunto).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_pauta_sg_assunto).empty();
			
			$("#nr_item_sumula_" + cd_pauta_sg_assunto).hide();
			$("#ordem_salvar_" + cd_pauta_sg_assunto).hide(); 
			
            $("#ordem_valor_" + cd_pauta_sg_assunto).html($("#nr_item_sumula_" + cd_pauta_sg_assunto).val()); 
			$("#ordem_valor_" + cd_pauta_sg_assunto).show(); 
			$("#ordem_editar_" + cd_pauta_sg_assunto).show(); 
			
        });
    }	
	
	function editar_ordem(cd_cronograma_item)
	{
		$("#ordem_valor_" + cd_cronograma_item).hide(); 
		$("#ordem_editar_" + cd_cronograma_item).hide(); 

		$("#ordem_salvar_" + cd_cronograma_item).show(); 
		$("#nr_item_sumula_" + cd_cronograma_item).show(); 
		$("#nr_item_sumula_" + cd_cronograma_item).focus();	
	}

	function set_resolucao_diretoria(cd_pauta_sg_assunto)
	{
	    $.post("<?= site_url('gestao/pauta_sg/set_resolucao_diretoria') ?>",
        {
            cd_pauta_sg_assunto    : cd_pauta_sg_assunto,
            fl_resolucao_diretoria : $("#fl_resolucao_diretoria_" + cd_pauta_sg_assunto).val()	
        },
        function(data){});
	}

	function get_usuarios(cd_gerencia, campo)
	{
		$.post("<?= site_url('gestao/pauta_sg/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var usuario = $("#"+campo); 
									
			if(usuario.prop) 
			{
				var usuario_opt = usuario.prop("options");
			}
			else
			{
				var usuario_opt = usuario.attr("options");
			}

			$("option", usuario).remove();

			usuario_opt[usuario_opt.length] = new Option("Selecione", "");

			$.each(data, function(val, text) {
				usuario_opt[usuario_opt.length] = new Option(text.text, text.value);
			});

		}, "json", true);
	}

	function set_gerencia_substituto(cd_gerencia)
	{
		if($("#cd_gerencia_substituto").val() == "") 
		{
			$("#cd_gerencia_substituto").val(cd_gerencia);
			$("#cd_gerencia_substituto").change();
		}
	}

    function valida_pautar()
    {
        if($("#cd_pauta_sg_assunto").val() > 0) // SE FOR UM ASSUNTO JÁ CADASTRO ELE ENTRA NESTE IF 
        {   
            if($("#fl_pendencia_gestao").val() == 'S') //SE FOR UMA PENDÊNCIA DE GESTÃO ELE ENTRA NESTE IF
            {
                if($("#cd_gerencia_pendencia").val() == '') //SE O CAMPO GERÊNCIA RESPONSÁVEL PELA PENDENCIA ESTIVER VAZIO EMITE UM ALERTA
                {
                    var confirmacao = "O campo Gerência Responsável pela pendência deve ser preenchido!";

                    alert(confirmacao);

                    $("#cd_gerencia_pendencia").focus();
                }
                else if($("#cd_usuario_pendencia").val() == '') //SE O CAMPO RESPONSÁVEL PELA PENDENCIA ESTIVER VAZIO EMITE UM ALERTA
                {
                    var confirmacao = "O campo Responsável pela pendência deve ser preenchido!";

                    alert(confirmacao);
                    
                    $("#cd_usuario_pendencia").focus();
                }
                else if($("#fl_pautar_reuniao").val() == '') //SE O CAMPO PAUTAR EM OUTRA REUNIÃO ESTIVER VAZIO EMITE UM ALERTA
                {
                    var confirmacao = "O campo Pautar em Outra Reunião deve ser preenchido!";

                    alert(confirmacao);

                    $("#fl_pautar_reuniao").focus();
                }
                else if($("#fl_pautar_reuniao").val() == 'N') //SE NÃO TIVER QUE PAUTAR EM OUTRA REUNIÃO DA SUBMIT NO FORMULÁRIO
                {
                    var confirmacao = "Salvar?\n\n"+
                                      "[OK] para Sim\n\n"+
                                      "[Cancelar] para Não";

                    if(confirm(confirmacao))
                    {
                        $("form").submit();
                    }
                } 
                else //SE TIVER QUE PAUTAR EM OUTRA REUNIÃO ENTRA NESTE ELSE
                {
                    if($("#tp_colegiado_pautar").val() == '') //SE O CAMPO COLEGIADO ESTIVER VAZIO EMITE UM ALERTA
                    {
                        var confirmacao = "O campo Colegiado deve ser preenchido!";

                        alert(confirmacao);

                        $("#tp_colegiado_pautar").focus();
                    }
                    else if($("#fl_proxima_reuniao").val() == '') //SE O CAMPO PRÓXIMA REUNIÃO ESTIVER VAZIO EMITE UM ALERTA
                    {
                        var confirmacao = "O campo Próxima Reunião deve ser preenchido!";

                        alert(confirmacao);

                        $("#fl_proxima_reuniao").focus();
                    }
                    else if($("#fl_proxima_reuniao").val() == 'S') //SE TIVER QUE PAUTAR NA PRÓXIMA REUNIÃO DA SUBMIT NO FORMULARIO
                    {
                        var confirmacao = "Salvar?\n\n"+
                                          "[OK] para Sim\n\n"+
                                          "[Cancelar] para Não";

                        if(confirm(confirmacao))
                        {
                            $("form").submit();
                        }
                    }
                    else //SE NÃO TIVER QUE PAUTAR NA PRÓXIMA REUNIÃO ENTRA NESTE ELSE
                    {
                        if($("#nr_mes_pautar").val() == '') //SE O CAMPO MES ESTIVER VAZIO EMITE UM ALERTA
                        {
                            var confirmacao = "O campo do Mês deve ser preenchido!";

                            alert(confirmacao);

                            $("#nr_mes_pautar").focus();
                        }
                        else if($("#nr_ano_pautar").val() == '') //SE O CAMPO ANO ESTIVER VAZIO EMITE UM ALERTA
                        {
                            var confirmacao = "O campo do Ano deve ser preenchido!";
                            
                            alert(confirmacao);
                            
                            $("#nr_ano_pautar").focus();
                        }
                        else //SE O CAMPO MES E ANO ESTIVER PREENCHIDO DA SUBMIT NO FORMUARIO
                        {
                            var confirmacao = "Salvar?\n\n"+
                                              "[OK] para Sim\n\n"+
                                              "[Cancelar] para Não";

                            if(confirm(confirmacao))
                            {
                                $("form").submit();
                            }
                        }
                    }
                }               
            }
            else if($("#fl_pendencia_gestao").val() == 'N') //SE NÃO FOR UMA PENDENCIA DE GESTÃO ELE DA SUBMIT NO FORMULÁRIO
            { 
                var confirmacao = "Salvar?\n\n"+
                                  "[OK] para Sim\n\n"+
                                  "[Cancelar] para Não";

                if(confirm(confirmacao))
                {
                    $("form").submit();
                }
            }
            else // SE O CAMPO PENDÊNCIA DE GESTÃO ESTIVER VAZIO EMITE UM ALERTA
            { 
                var confirmacao = "O campo Pendência de Gestão deve ser preenchido!";
                
                alert(confirmacao);
                
                $("#fl_pendencia_gestao").focus();
            }
        }
        else // SE NÃO FOR UM ASSUNTO JÁ CADASTRO ELE DA SUBMIT NO FORMULÁRIO 
        { 
            var confirmacao = "Salvar?\n\n"+
                              "[OK] para Sim\n\n"+
                              "[Cancelar] para Não";

            if(confirm(confirmacao))
            {
                $("form").submit();
            }
        }
    }

    function ocultar_dropdown()
    {
        $("#fl_pendencia_gestao").val("N");
        $("#fl_pautar_reuniao").val("")

        $("#cd_gerencia_pendencia_row").hide();
        $("#cd_usuario_pendencia_row").hide();

        $("#fl_pautar_reuniao_row").hide();
        $("#tp_colegiado_pautar_row").hide();
        $("#fl_proxima_reuniao_row").hide();
        $("#nr_mes_pautar_nr_ano_pautar_row").hide();
        $("#text_row").hide();
    }

    function set_pendencia_gestao(fl_campo)
    {       
        if(fl_campo == "S")
        {
            $("#fl_pautar_reuniao_row").show();
            ($("#fl_pautar_reuniao").val() == "" ? $("#fl_pautar_reuniao").val("S") : "");

            $("#cd_gerencia_pendencia_row").show();
            $("#cd_usuario_pendencia_row").show();
            
            if($("#cd_gerencia_pendencia").val() == '')
            {
                $("#cd_gerencia_pendencia").val($("#cd_gerencia_responsavel").val());
                $("#cd_usuario_pendencia").val($("#cd_usuario_responsavel").val());
            }

            set_pautar_reuniao("S");
        }
        else
        {
            $("#fl_pautar_reuniao_row").hide();

            $("#cd_gerencia_pendencia_row").hide();
            $("#cd_usuario_pendencia_row").hide();

            set_pautar_reuniao("N");
            set_proxima_reuniao("S");
        }
    }

    function set_pautar_reuniao(fl_campo)
    {
        if(fl_campo == "S")
        {        
            $("#tp_colegiado_pautar_row").show();
            ($("#tp_colegiado_pautar").val() == "" ? $("#tp_colegiado_pautar").val($("#tp_colegiado").val()) : "");

            $("#fl_proxima_reuniao_row").show();
            ($("#nr_mes_pautar").val() == "" ? $("#fl_proxima_reuniao").val("S") : "");

            set_proxima_reuniao($("#fl_proxima_reuniao").val());
        }
        else
        {
            $("#tp_colegiado_pautar_row").hide();

            $("#fl_proxima_reuniao_row").hide();

            set_proxima_reuniao("S");
        }
    }

    function set_proxima_reuniao(fl_campo)
    {
        if(fl_campo == "N")
        {
            $("#nr_mes_pautar_nr_ano_pautar_row").show();
            $("#text_row").show();
        }
        else
        {
            $("#nr_mes_pautar_nr_ano_pautar_row").hide();
            $("#text_row").hide();
        }
    }

    function mostrar_dropdown()
    {
        set_pendencia_gestao("S");

        if($("#fl_pautar_reuniao").val() == "S")
        {
            set_pautar_reuniao("S");
        }
        else
        {
            set_pautar_reuniao("N");
        }

        if($("#fl_pautar_reuniao").val() == "S" && $("#fl_proxima_reuniao").val() == "N")
        {
            set_proxima_reuniao("N");
        }
        else
        {
            set_proxima_reuniao("S");
        }
    }

    function check_item(t)
    {
        aprovar_assunto(t.val(), t.is(':checked') ? 'S' : 'N');
    }

    function aprovar_assunto(cd_pauta_sg_assunto, fl_checked)
    {
        $.post("<?= site_url('gestao/pauta_sg/aprovar_assunto') ?>",
        {
            cd_pauta_sg_assunto : cd_pauta_sg_assunto,
            fl_aprovado         : fl_checked
        },
        function(data){
            
        });
    }

	$(function(){
		configure_result_table();   

        if($("#cd_pauta_sg_assunto").val() != '' && $("#fl_pendencia_gestao").val() == "N")
        {
            ocultar_dropdown();
        }
        else
        {
            mostrar_dropdown();
        }
		
		$("#cd_usuario_responsavel_gerencia").change(function() {
			$("#cd_usuario_substituto_gerencia").val($("#cd_usuario_responsavel_gerencia").val());
			$("#cd_usuario_substituto_gerencia").change();
		});
	})
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_assunto', 'Assuntos', TRUE, 'location.reload();');
	$abas[] = array('aba_presentes', 'Presentes', FALSE, 'ir_presentes();');

	$body = array();

	$head[] = 'Aprovado';
	$head[] = 'Nº';
	$head[] = '';

	if(trim($row['fl_sumula']) == 'IN')
	{	
		$head[] = 'Instância de aprovação';
		$head[] = 'Área de Atuação';
	}
	else
	{
		$head[] = 'Diretoria';
	}

	$head[] = 'RDS';
    $head[] = 'Contr. Serviço.';
    $head[] = 'N° O.F.';
	$head[] = 'Assunto';
	$head[] = 'Tempo';
	$head[] = 'Responsável.';
	$head[] = 'Substituto.';

	/*if(trim($row['fl_sumula']) == 'DE')
	{	
		$head[] = 'Resolução de Diretoria.';
	}
	else if(trim($row['fl_sumula']) == 'IN')
	{	
		$head[] = 'Resolução do Interventor.';
	}*/

    $head[] = 'Decisão';
    $head[] = 'Pend. de Gestão';
    $head[] = 'Pautar Outra Reunião';
	$head[] = 'Qt. Arq.';
	$head[] = 'Dt. Encerramento';
	$head[] = '';

	$i = 0;

	foreach($collection as $item)
	{
        $ds_proxima_reuniao = 'Não';
        
        if($item['fl_proxima_reuniao'] == 'S')
        {
            $ds_proxima_reuniao = 'Próxima Reunião do '.$item['tp_colegiado_pautar'];
        }
        else if($item['fl_proxima_reuniao'] == 'N' AND $item['nr_mes_pautar'] != '')
        {
            $ds_proxima_reuniao = 'Reunião de '.mes_extenso($item['nr_mes_pautar']).'/'.$item['nr_ano_pautar'].' do '.$item['tp_colegiado_pautar'];
        }

		if((trim($item['fl_removido']) == 'N') AND (trim($row['dt_aprovacao']) == ''))
		{
			$config = array(
				'name'   => 'nr_item_sumula_'.$item['cd_pauta_sg_assunto'], 
				'id'     => 'nr_item_sumula_'.$item['cd_pauta_sg_assunto'],
				'onblur' => 'set_ordem('.$row['cd_pauta_sg'].', '.$item['cd_pauta_sg_assunto'].');',
				'style'  => 'display:none; width:50px;'
			);

			$campo_check = array(
	            'name'     => 'cd_pauta_sg_assunto_'.$item['cd_pauta_sg_assunto'],
	            'id'       => 'cd_pauta_sg_assunto_'.$item['cd_pauta_sg_assunto'],
	            'value'    => $item['cd_pauta_sg_assunto'],
	            'checked'  => ($item['fl_aprovado'] == 'S' ? TRUE : FALSE),
	            'onchange' => 'check_item($(this))'   
	        );

			$body[$i][] = form_checkbox($campo_check);

			$body[$i][] = '
				<span id="ajax_ordem_valor_'.$item['cd_pauta_sg_assunto'].'"></span> 
				<span id="ordem_valor_'.$item['cd_pauta_sg_assunto'].'">'.$item['nr_item_sumula'].'</span>'.
				form_input($config, $item['nr_item_sumula']).'
				<script> 
					jQuery(function($){ 
						$("#cd_pauta_sg_assunto_'.$item['cd_pauta_sg_assunto'].'").numeric(); 
					}); 
				</script>';
			
			$body[$i][] = '
				<a id="ordem_editar_'.$item['cd_pauta_sg_assunto'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_pauta_sg_assunto'].');" title="Editar a ordem">
					[editar]
				</a>
				<a id="ordem_salvar_'.$item['cd_pauta_sg_assunto'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">
					[salvar]
				</a>';

			$body[$i][] = array(anchor('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['ds_diretoria']), 'text-align:left;');

			if(trim($row['fl_sumula']) == 'IN')
			{
				$body[$i][] = array(anchor('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['instancia_aprovacao']), 'text-align:left;');
			}

            $body[$i][] = (trim($item['nr_ano_numero_rds']) != '' ? $item['nr_ano_numero_rds'] : (trim($item['fl_aplica_rds']) == 'S' ? 'SIM' : 'NÃO'));
            $body[$i][] = $item['fl_ordem_fornecimento']; 
            $body[$i][] = $item['nr_ordem_fornecimento'];


			$body[$i][] = array(nl2br(anchor('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['ds_pauta_sg_assunto'])), 'text-align:justify;');
			$body[$i][] = anchor('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['nr_tempo']);

			$body[$i][] = array(anchor('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['cd_gerencia_responsavel'].' - '.$item['ds_usuario_responsavel']), 'text-align:left;');
			$body[$i][] = array(anchor('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['cd_gerencia_substituto'].' - '.$item['ds_usuario_substituto']), 'text-align:left;');

			/*if((trim($row['fl_sumula']) == 'DE') OR (trim($row['fl_sumula']) == 'IN'))
			{
				$body[$i][] = form_dropdown('fl_resolucao_diretoria_'.$item['cd_pauta_sg_assunto'], array('' => 'Selecione', 'S' => 'Sim', 'N' => 'Não'), array($item['fl_resolucao_diretoria']), 'onchange="set_resolucao_diretoria('.$item['cd_pauta_sg_assunto'].')"');
			}*/
		}
		else 
		{
			$body[$i][] = $item['ds_aprovado'];
			$body[$i][] = $item['nr_item_sumula'];
			$body[$i][] = '';

			$body[$i][] = array($item['ds_diretoria'], 'text-align:left;');

			if(trim($row['fl_sumula']) == 'IN')
			{
				$body[$i][] = array($item['instancia_aprovacao'], 'text-align:left;');
			}

			$body[$i][] = (trim($item['nr_ano_numero_rds']) != '' ? $item['nr_ano_numero_rds'] : (trim($item['fl_aplica_rds']) == 'S' ? 'SIM' : 'NÃO'));
            $body[$i][] = $item['fl_ordem_fornecimento']; 
            $body[$i][] = $item['nr_ordem_fornecimento'];

			
			$body[$i][] = array(nl2br($item['ds_pauta_sg_assunto']), 'text-align:justify;');
			$body[$i][] = $item['nr_tempo'];

			$body[$i][] = array($item['cd_gerencia_responsavel'].' - '.$item['ds_usuario_responsavel'], 'text-align:left;');
			$body[$i][] = array($item['cd_gerencia_substituto'].' - '.$item['ds_usuario_substituto'], 'text-align:left;');

			/*if((trim($row['fl_sumula']) == 'DE') OR (trim($row['fl_sumula']) == 'IN'))
			{
				$body[$i][] = $item['ds_resolucao_diretoria'];
			}*/
		}
		
        $body[$i][] = array(nl2br($item['ds_decisao']), 'text-align:justify;');
        $body[$i][] = $item['fl_pendencia_gestao'];
        $body[$i][] = $ds_proxima_reuniao;
		$body[$i][] = $item['tl_arquivo'];

		$body[$i][] = $item['dt_encerramento'];
		
		if((trim($item['fl_removido']) == 'N') AND (trim($row['dt_aprovacao']) == ''))
		{
			$body[$i][] = 
				anchor('gestao/pauta_sg/anexo/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[arquivos]').br().
				(trim($item['dt_encerramento']) != '' 
					? 
					anchor('gestao/pauta_sg/apresentacao/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[apresentação]', 'target="_blank"').br().
					'<a href="javascript:void(0)" onclick="reabrir_assunto('.$item['cd_pauta_sg_assunto'].')">[reabrir]</a>'
					: 
					'
					<a href="javascript:void(0)" onclick="remover('.$item['cd_pauta_sg_assunto'].')">[remover]</a>
					<br/>
					<a href="javascript:void(0)" onclick="excluir('.$item['cd_pauta_sg_assunto'].')">[excluir]</a> 
					'
				).br().
                anchor('gestao/pauta_sg/capa_assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[capa]');
		}
		else if((trim($item['fl_removido']) == 'N') AND (trim($row['dt_aprovacao']) != ''))
		{
			$body[$i][] = anchor('gestao/pauta_sg/anexo/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[arquivos]').
			(trim($item['dt_encerramento']) != '' ? br().anchor('gestao/pauta_sg/apresentacao/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[apresentação]', 'target="_blank"') : '').br().
            anchor('gestao/pauta_sg/capa_assunto/'.$row['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[capa]');
		}
		else
		{
			$body[$i][] = 'Removido da Pauta';
		}

		$i ++;
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	if(trim($row['dt_aprovacao']) != '')
	{
		$grid->col_oculta = array(1);
	}

	echo aba_start($abas);
		echo form_start_box('default_box', 'Pauta');
			echo form_default_row('nr_ata', 'Nº da Ata:', '<label class="label label-inverse">'.$row['nr_ata'].'</label>');
			echo form_default_row('fl_sumula', 'Colegiado:', '<span class="'.$row['class_sumula'].'">'.$row['fl_sumula'].'</span>');
			
			echo form_default_row('link_pauta', 'Link para envio:', 'https://www.fundacaofamiliaprevidencia.com.br/link/?p='.$row['cd_pauta_sg_md5']);	

			if(trim($row['ds_tipo_reuniao']) != '')
			{
				echo form_default_row('ds_tipo_reuniao', 'Tipo Reunião:', $row['ds_tipo_reuniao']);
			}

			echo form_default_row('local', 'Local:', $row['local']);

			echo form_default_row('dt_reuniao', 'Dt. Reunião:', $row['dt_pauta'].' '.$row['hr_pauta']);

			if(trim($row['dt_pauta_sg_fim']) != '')
			{	
				echo form_default_row('dt_reuniao_fim', 'Dt. Reunião Encerramento:', $row['dt_pauta_sg_fim'].' '.$row['hr_pauta_sg_fim']);
			}

			if(trim($row['dt_envio_responsavel']) != '')
			{
				echo form_default_row('dt_envio_responsavel', 'Dt. Envio Responsáveis:', $row['dt_envio_responsavel']);
				echo form_default_row('ds_usuario_envio_responsavel', 'Usuário:', $row['ds_usuario_envio_responsavel']);
			}
			
			if(trim($row['dt_aprovacao']) != '')
			{
				echo form_default_row('dt_aprovacao', 'Dt. Encerramento:', $row['dt_aprovacao']);
				echo form_default_row('ds_usuario_aprovacao', 'Usuário:', $row['ds_usuario_aprovacao']);
			}

		echo form_end_box('default_box');
		echo form_command_bar_detail_start();

			if(trim($row['dt_aprovacao']) == '')
			{
				if(count($collection) > 0 AND intval($row['tl_sem_decisao']) == 0 AND trim($row['dt_envio_responsavel']) != '')
				{
					echo button_save('Encerrar', 'encerrar()', 'botao_verde');	
				}
				
				if(count($collection) > 0)
				{
					echo button_save('Enviar Emails Resp.', 'enviar()', 'botao_verde');	
				}
			}
/*
            if(trim($row['dt_aprovacao']) != '' AND trim($row['dt_envio_colegiado']) == '')
            {
                echo button_save('Enviar E-mail para o Colegiado', 'enviar_colegiado()', 'botao_verde');    
            }
*/
			if(trim($row['dt_aprovacao']) != '')
			{
				echo button_save('Reabrir a Pauta', 'reabrir()', 'botao_verde');
			}
			
			if(count($collection) > 0 OR trim($row['dt_aprovacao']) != '')
			{
				echo button_save('Pauta', 'pauta();', 'botao_verde');	
				echo button_save('Súmula', 'sumula();', 'botao_amarelo');		
			}
		echo form_command_bar_detail_end();
		
		if(trim($row['dt_aprovacao']) == '')
		{
			echo form_open('gestao/pauta_sg/assunto_salvar');
				echo form_start_box('default_assunto_box', 'Cadastro - Assunto');
					echo form_default_hidden('cd_pauta_sg', '', $row);	
					echo form_default_hidden('cd_pauta_sg_assunto', '', $assunto);	
					echo form_default_hidden('tp_colegiado', '', $row['fl_sumula']);	
					echo form_default_integer('nr_item_sumula', 'Nº do Item:(*)', $assunto);
					echo form_default_gerencia('cd_gerencia_responsavel', 'Ger. Responsável: (*)', $assunto['cd_gerencia_responsavel'], 'onchange="set_gerencia_substituto(this.value); get_usuarios(this.value, \'cd_usuario_responsavel\')"');
					echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $responsavel, $assunto['cd_usuario_responsavel']);
					echo form_default_gerencia('cd_gerencia_substituto', 'Ger. Substituto: (*)', $assunto['cd_gerencia_substituto'], 'onchange="get_usuarios(this.value, \'cd_usuario_substituto\')"');
					echo form_default_dropdown('cd_usuario_substituto', 'Substituto: (*)', $substituto, $assunto['cd_usuario_substituto']);
					echo form_default_dropdown('cd_diretoria', 'Diretoria: (*)', $diretoria, $assunto['cd_diretoria']);

					if(trim($row['fl_sumula']) == 'IN')
					{
						echo form_default_text('instancia_aprovacao', 'Instância de aprovação:', $assunto);
                    }
                    
                    echo form_default_dropdown('fl_aplica_rds', 'RDS: (*)', $drop, $assunto['fl_aplica_rds']);
                    echo form_default_integer_ano('nr_rds', 'nr_ano_rds', 'Número/Ano RDS:', $assunto['nr_rds'], $assunto['nr_ano_rds']);
					
					echo form_default_integer('nr_tempo', 'Tempo (min):', $assunto);

                    echo form_default_dropdown('fl_ordem_fornecimento', 'Aprovação de Contratação de Serviço: (*)', $drop, $assunto['fl_ordem_fornecimento']);

					echo form_default_textarea('ds_pauta_sg_assunto', 'Assunto: (*)', $assunto, 'style="height:80px;"');
					
					if(intval($assunto['cd_pauta_sg_assunto']) > 0)
					{
                        echo form_default_textarea('ds_decisao', 'Decisão:', $assunto, 'style="height:80px;"');
                        echo form_default_dropdown('fl_pendencia_gestao', 'Pendência de Gestão:', $drop, $assunto['fl_pendencia_gestao'], 'onchange="set_pendencia_gestao($(this).val())"');
                        echo form_default_gerencia('cd_gerencia_pendencia', 'Ger. Responsável:', $assunto['cd_gerencia_pendencia'], 
                        'onchange="get_usuarios(this.value, \'cd_usuario_pendencia\')"');
                        echo form_default_dropdown('cd_usuario_pendencia', 'Responsável:', $usuario_pendencia, $assunto['cd_usuario_pendencia']);
                        echo form_default_dropdown('fl_pautar_reuniao', 'Pautar em Outra Reunião:', $drop, $assunto['fl_pautar_reuniao'], 'onchange="set_pautar_reuniao($(this).val())"');
                        echo form_default_dropdown('tp_colegiado_pautar', 'Colegiado:', $colegiado, $assunto['tp_colegiado_pautar']);
                        echo form_default_dropdown('fl_proxima_reuniao', 'Próxima Reunião:', $drop, $assunto['fl_proxima_reuniao'], 'onchange="set_proxima_reuniao($(this).val())"');
                        echo form_default_mes_ano('nr_mes_pautar', 'nr_ano_pautar', 'Reunião de:', $assunto['dt_pautar']);
                        echo form_default_row('text', '', '<span style="font-style:italic;">Assunto vai entrar na próxima pauta cadastrada do mês selecionado</span>');
					}
				echo form_end_box('default_assunto_box');

				echo form_command_bar_detail_start();
					echo button_save('Salvar');	

					if(intval($assunto['cd_pauta_sg_assunto']) > 0)
					{
						echo button_save('Cancelar', 'cancelar()', 'botao_disabled');	
					}
				echo form_command_bar_detail_end();	
			echo form_close();
		}

		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>