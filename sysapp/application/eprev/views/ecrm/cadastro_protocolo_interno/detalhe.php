<?php 
set_title('Protocolo Interno - Cadastro');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("cd_documento_recebido_tipo", "fk","cd_documento_recebido_tipo_solic")); ?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno"); ?>';
	}

	function adicionar_documento(form)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			return false;
		}

		if($('#cd_tipo_doc').val()=='')
		{
			alert('Informe o tipo de documento.');
			$('#cd_tipo_doc').focus();
			return false;
		}

        if($('#cd_tipo').val() == 2 && ($('#arquivo_nome').val() == '' || $('#arquivo').val() == ''))
        {
            alert('Nenhum arquivo foi anexado.');
        }
        else
        {		
            //if(confirm('Adicionar?'))
            //{

            	if($('#cd_tipo_doc').val() == 30)
            	{
            		if(confirm('Pensão alimentícia (OK para Sim e CANCELAR para Não?)?'))
            		{
            			alert('Encaminhar documento também para GAP Folha');

            			adicionar_doucmento_fnc();
            		}
            		else
            		{
            			adicionar_doucmento_fnc();
            		}

            	}
            	else
            	{
            		adicionar_doucmento_fnc();
            	}

                
            //}
		}
	}

	function adicionar_doucmento_fnc()
	{
		$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/adicionar_documento'); ?>",
		{
			cd_documento_recebido      : $('#cd_documento_recebido').val(),
			cd_empresa                 : $('#cd_empresa').val(),
			cd_registro_empregado      : $('#cd_registro_empregado').val(),
			seq_dependencia            : $('#seq_dependencia').val(),
			nome_participante          : $('#nome_participante').val(),
			ds_observacao              : $('#ds_observacao').val(),
			nr_folha                   : $('#nr_folha').val(),
			cd_tipo_doc                : $('#cd_tipo_doc').val(),
			arquivo                    : $('#arquivo').val(),
			arquivo_nome               : $('#arquivo_nome').val(),
			cd_documento_recebido_item : $("#cd_documento_recebido_item").val()
			
		}, 
		function(data)
		{
			carregar_grid();
			limpar();

			$('#qrcode').focus();
				});
	}

	function limpar()
	{
		remover_arquivo_arquivo(true);

		$('#ds_observacao').val('');
		$('#nr_folha').val('1');
        $("#adicionar_documento_btn").val("Adicionar Documento");
        $("#cd_documento_recebido_item").val(0);

		if( $('#cd_tipo_doc_fica_marcado').is(':checked') )
		{
			$('#cd_empresa').val('');
			$('#cd_registro_empregado').val('');
			$('#seq_dependencia').val('');
			$('#nome_participante').val('');
			$('#cd_empresa').focus();
		}
		else if( $('#participante_fica_marcado').is(':checked') )
		{
			$('#cd_tipo_doc').val('');
			$('#nome_documento').val('');
			$('#cd_tipo_doc').focus();
		}
		else
		{
			$('#cd_empresa').val('');
			$('#cd_registro_empregado').val('');
			$('#seq_dependencia').val('');
			$('#nome_participante').val('');
			$('#cd_empresa').focus();	
			$('#cd_tipo_doc').val('');
			$('#nome_documento').val('');
			$('#cd_tipo_doc').focus();			
		}
	}

	function cancelar_destino()
	{
		$('#default_box').show();
		$('#enviar_box').hide();
		$('#save_form').show();
		$('#redirecionar_box').hide();
	}

	function check_recebido()
	{
		if(($("#dt_ok").val() == "") && ($('#qt_total').val() != 0) && ($('#qt_recebido').val() == $('#qt_total').val()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
	
	function receber(fl_receber)
	{
		if(fl_receber)
		{
			var confirmacao = 'Encerrar o Protocolo agora?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';			
		}
		else
		{
			var confirmacao = 'ATENÇÃO\n\n- Todos documentos ainda NÃO recebidos, serão marcados como Recebidos por você.\n\n\nEncerrar o Protocolo agora?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';		
		}
		
		if(confirm(confirmacao))
		{
			$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/receber'); ?>",
			{
				cd_documento_recebido : $('#cd_documento_recebido').val()
			}, 
			function(data)
			{
				location.reload();
			});
		}
		else
		{
			return false;
		}
	}

	function cancelar_recebimento_documento()
	{
		$('#cd_documento_recebido_item').val('');
		$('#ds_observacao_recebimento').val('');
		$('#grid_documentos').show();
		$('#default_box').show();
	}

	function receber_documento_()
	{
		/*
		$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/receber_documento'); ?>",
		{
			cd_documento_recebido_item : $('#cd_documento_recebido_item').val(),
			ds_observacao_recebimento  : $('#ds_observacao_recebimento').val()
		}, 
		function(data)
		{
			if(data == 'true')
			{
				carregar_grid();
				
			}
			else
			{
				alert(data);
			}
		});
		
		cancelar_recebimento_documento();
		*/
	}
	


	function escolher_destino()
	{
		$('#default_box').hide();
		$('#save_form').hide();
		$('#enviar_box').show();

		limpar_formulario_envio();
	}

	function escolher_destino_redir()
	{
		$('#default_box').hide();
		$('#redirecionar_box').show();

		limpar_formulario_envio();
	}

	function limpar_formulario_envio()
	{
		$('#cd_usuario_destino_redir').val('');
		$('#cd_usuario_destino_redir_gerencia').val('');
		$('#cd_documento_recebido_grupo_redir').val('');

		$('#cd_usuario_destino').val('');
		$('#cd_usuario_destino_redir').val('');
		$('#cd_documento_recebido_grupo').val('');
	}

	function enviar(f)
	{
		if($("#qt_total").val() > 0)
		{
			if(confirm("Enviar?"))
			{
				$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/enviar'); ?>",
				{
					cd_documento_recebido       : $('#cd_documento_recebido').val(),
					cd_usuario_destino          : $('#cd_usuario_destino').val(),
					cd_documento_recebido_grupo : $('#cd_documento_recebido_grupo').val(),
					fl_pedido_beneficio			: $('#fl_pedido_beneficio').val(),
					fl_certidao_obito			: $('#fl_certidao_obito').val(),
					fl_doc_indentificacao		: $('#fl_doc_indentificacao').val(),
					fl_conta_corrente			: $('#fl_conta_corrente').val(),
					fl_ordem_pagamento			: $('#fl_ordem_pagamento').val(),
					fl_carta_concessao			: $('#fl_carta_concessao').val(),
					dt_concessao				: $('#dt_concessao').val(),
					fl_comprovante_beneficio	: $('#fl_comprovante_beneficio').val(),
					fl_certidao_pis				: $('#fl_certidao_pis').val(),
					fl_substituto_pis			: $('#fl_substituto_pis').val(),
					ds_tipo_documento			: $('#ds_tipo_documento').val(),
					fl_nome_titular				: $('#fl_nome_titular').val(),
					fl_nome_dependente			: $('#fl_nome_dependente').val(),
					fl_situacao					: $('#fl_situacao').val(),
					fl_pagamento_anterior		: $('#fl_pagamento_anterior').val(),
					fl_carimbo					: $('#fl_carimbo').val(),
					relacao						: $('#relacao').val()
				}, 
				function(data)
				{ 
					if(data == 'true')
					{
						location.reload();
					}
					else
					{
						alert(data);
					} 
				});
			}
			else
			{
				return false;
			}
		}
		else
		{
			alert("ERRO\n\nNão há documentos adicionados no protocolo.")
			return false;
		}
	}

	function redirecionar(f)
	{
		if(confirm("Redirecionar?"))
		{
			$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/redirecionar'); ?>",
			{
				cd_documento_recebido       : $('#cd_documento_recebido').val(),
				cd_usuario_destino          : $('#cd_usuario_destino_redir').val(),
				cd_documento_recebido_grupo : $('#cd_documento_recebido_grupo_redir').val()
			}, 
			function(data)
			{
				if(data=='true')
				{
					$('#cd_usuario_destino_redir_gerencia').val('');
					$('#cd_usuario_destino_redir').val('');
					$('#cd_documento_recebido_grupo_redir').val('');

					location.reload();
				}
				else
				{
					alert(data);
				}
			});
		}
		else
		{
			return false;
		}
	}

	function excluir_item(cd_documento_recebido_item)
	{
		if(confirm('Excluir?'))
		{
			$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/excluir_item'); ?>",
			{
				cd_documento_recebido_item : cd_documento_recebido_item
			},
			function(data)
			{ 
				carregar_grid(); 
			});
		}
	}
    
    function editar_documento(cd)
	{
		$("#cd_documento_recebido_item").val(0);
		$.post('<?php echo base_url().index_page(); ?>/ecrm/cadastro_protocolo_interno/editar_documento',
		{
			cd_documento_recebido_item : cd
		}, 
		function(data)
		{
			var obj = data;
			if(obj)					
			{
				$("#adicionar_documento_btn").val("Salvar Documento");
				$("#cd_documento_recebido_item").val(obj.cd_documento_recebido_item);
				$("#cd_tipo_doc").val(obj.cd_tipo_doc);
				$("#cd_empresa").val(obj.cd_empresa);
				$("#cd_registro_empregado").val(obj.cd_registro_empregado);
				$("#seq_dependencia").val(obj.seq_dependencia);
				$("#ds_observacao").val(obj.ds_observacao);
				$("#nr_folha").val(obj.nr_folha);
				$("#nome_participante").val(obj.nome);
				
				if(obj.arquivo != "")
				{
					sucesso_arquivo(obj.arquivo + "|" + obj.arquivo_nome);
				}
				else
				{
					remover_arquivo_arquivo(true);
				}
			}
			else
			{
				alert("Erro ao editar.");
			}
		},"json");
	}	

	function carregar_grid()
	{
		$('#div_documentos').html("<?php echo loader_html(); ?>");
		
		$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/detalhe_grid'); ?>", 
		{
			cd                     : $('#cd_documento_recebido').val(),
			fl_recebido            : $('#fl_recebido').val(),
			fl_tipo_novo_protocolo : $('#fl_tipo_novo_protocolo').val()
		}, 
		function(data)
		{
			$('#div_documentos').html(data);
			
			if(check_recebido())
			{
				receber(check_recebido());
			}
			
			configure_result_table_documentos();
		});
	}
	
    function configure_result_table_documentos()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_documento"),
        [
            'CaseInsensitiveString',
            'RE',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
            'DateTimeBR',
            'CaseInsensitiveString',
            'Number',
            'CaseInsensitiveString',
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
        ob_resul.sort(2, false);
    }	
	

	function ir_relatorio()
	{
		location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/relatorio'); ?>";
	}

	function marcar(v)
	{
		if(v==0){ $('#cd_tipo_doc').focus(); }
		if(v==1){ $('#cd_empresa').focus(); }
	}
	
	function rodar_ao_iniciar()
	{
		$('#enviar_box').hide();
		$('#redirecionar_box').hide();

		<?php if($row["dt_envio"]==''): ?>
			$('#cd_tipo_doc').focus();
		<?php endif; ?>

		carregar_grid();

		$('#cd_tipo_doc').before( "<input type='radio' id='cd_tipo_doc_fica_marcado' name='fica_marcado' onclick='marcar(0);' />&nbsp" );
		$('#cd_empresa').before( "<input type='radio' id='participante_fica_marcado' name='fica_marcado' onclick='marcar(1);' />&nbsp" );
	}

    function seleciona_tipo()
    {
        var tipo = $('#tipo').val();
		if(tipo == 1)
        {
            $('#inscricao_box').hide();
            $('#beneficio_box').show();
        }
        else if(tipo == 2)
        {
            $('#inscricao_box').show();
            $('#beneficio_box').hide();
			$('#checklist_box').hide();
			$('#relacao').val(''); 
			$('input[value="Enviar Documentos (escolher destino)"]').show();
        }
        else
        {
            $('#beneficio_box').hide();
            $('#inscricao_box').hide();
			$('#checklist_box').hide();
			$('#relacao').val('');
			$('input[value="Enviar Documentos (escolher destino)"]').show();
        }
    }

    function seleciona_beneficio(beneficio)
    {	
		if(beneficio == 4)
        {
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			$('#checklist_box').show();
			$('#fl_ordem_pagamento').val('');
			$('#fl_ordem_pagamento_row').hide();
			$('#fl_substituto_pis').val('');
			$('#fl_substituto_pis_row').hide();
			$('#ds_tipo_documento').val('');
			$('#ds_tipo_documento_row').hide();
			$('#fl_nome_titular').val('');
			$('#fl_nome_titular_row').hide();
			$('#fl_nome_dependente').val('');
			$('#fl_nome_dependente_row').hide();
			$('#fl_situacao').val('');
			$('#fl_situacao_row').hide();
			$('#fl_carimbo').val('');
			$('#fl_carimbo_row').hide();
			$('#fl_pagamento_anterior').val('');
			$('#fl_pagamento_anterior_row').hide();
        }
		else
		{
			$('input[value="Enviar Documentos (escolher destino)"]').show();
			$('#checklist_box').hide();
		}
		
        if(beneficio != '')
        {
            $.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/beneficio_grid'); ?>", 
			{
				beneficio: beneficio
			}, 
			function(data)
			{
				$('#div_beneficio').html(data);
			});
        }
    }

	function seleciona_pedido_beneficio(pedido_beneficio)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_pedido_beneficio').val('');
		}
		else
		{
			if(pedido_beneficio == 'S')
			{
				seleciona_documento(39, "PEDIDO DE BENEFÍCIO");
			}	
		}		
	}
	
	function seleciona_certidao_obito(certidao_obito)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_certidao_obito').val('');
		}
		else
		{
			if(certidao_obito == 'S')
			{
				seleciona_documento(30, "CERTIDÃO DE ÓBITO");
			}	
		}		
	}
	
	function seleciona_doc_identificacao(doc_identificacao)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_doc_indentificacao').val('');
		}
		else
		{
			if(doc_identificacao == 'S')
			{
				seleciona_documento(1, "DOCUMENTO DE IDENTIFICAÇÃO");
			}	
		}		
	}
	
	function seleciona_carta_concessao(carta_concessao)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_carta_concessao').val('');
		}
		else
		{
			if(carta_concessao == 'S')
			{
				seleciona_documento(27, "CARTA DE CONCESSÃO DO INSS");
			}	
		}		
	}
	
	function seleciona_conta_corrente(conta_corrente)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_conta_corrente').val('');
		}
		else
		{
			if(conta_corrente == 'S')
			{
				seleciona_documento(248, "COMPROVANTE DE CONTA CORRENTE");					
				$('#fl_ordem_pagamento').val('');
				$('#fl_ordem_pagamento_row').hide();
			}
			else if(conta_corrente == 'N')
			{
				$('#fl_ordem_pagamento_row').show();
			}
		}		
	}
	
	function seleciona_comprovante_beneficio(comprovante_beneficio)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_comprovante_beneficio').val('');
		}
		else
		{
			if(comprovante_beneficio == 'S')
			{
				seleciona_documento(243, "COMPROVANTE/EXTRATO INSS");
			}	
		}		
	}
	
	function seleciona_substituto_pis(substituto_pis)
	{
		if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
		{
			alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
			$('#cd_empresa').focus();
			$('#fl_substituto_pis').val('');
		}
		else
		{
			if(substituto_pis == 'S')
			{
				seleciona_documento(470, "DOCUMENTO SUBSTITUTO DO PIS/PASEP");
				
				$('#ds_tipo_documento_row').show();
				$('#fl_nome_titular_row').show();
				$('#fl_nome_dependente_row').show();
				$('#fl_situacao_row').show();
				$('#fl_carimbo_row').show();
			}
			else if(substituto_pis == 'N')
			{
				$('#ds_tipo_documento').val('');
				$('#ds_tipo_documento_row').hide();
				$('#fl_nome_titular').val('');
				$('#fl_nome_titular_row').hide();
				$('#fl_nome_dependente').val('');
				$('#fl_nome_dependente_row').hide();
				$('#fl_situacao').val('');
				$('#fl_situacao_row').hide();
				$('#fl_carimbo').val('');
				$('#fl_carimbo_row').hide();		
			}
			else
			{
				$('#ds_tipo_documento').val('');
				$('#ds_tipo_documento_row').hide();
				$('#fl_nome_titular').val('');
				$('#fl_nome_titular_row').hide();
				$('#fl_nome_dependente').val('');
				$('#fl_nome_dependente_row').hide();
				$('#fl_situacao').val('');
				$('#fl_situacao_row').hide();
				$('#fl_carimbo').val('');
				$('#fl_carimbo_row').hide();		
			}
		}		
	}
	
	function seleciona_certidao_pis_pasep(certidao_pis_pasep)
	{
		if(certidao_pis_pasep == 'S')
		{
			if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') && $('#nome_participante').val()=='' )
			{
				alert('Informe a Empresa/RE/Sequência ou preencha o Nome');
				$('#cd_empresa').focus();
				$('#fl_certidao_pis').val('');
			}
			else
			{
				seleciona_documento(29, "CERTIDÃO PIS/PASEP");	
			}
			
			$('#fl_substituto_pis').val('');
			$('#fl_substituto_pis_row').hide();	
		}
		else if(certidao_pis_pasep == 'N')
		{
			$('#fl_substituto_pis_row').show();		
		}
		else
		{
			$('#fl_substituto_pis').val('');
			$('#fl_substituto_pis_row').hide();
		}
	}
	
	function seleciona_situacao(situacao)
	{
		if(situacao == 'C')
		{
			$('#fl_pagamento_anterior_row').show();
		}
		else
		{
			$('#fl_pagamento_anterior').val('');
			$('#fl_pagamento_anterior_row').hide();
		}
	}
	
	function validar_checklist()
	{
		var dt_concessao;

        var dt_minima = new Date();
        dt_minima.zeroTime();

		if($('#fl_pedido_beneficio').val()!='S')
		{
			alert('O campo [Pedido de Benefício] deve ser [SIM] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_certidao_obito').val()!='S')
		{
			alert('O campo [Certidão de Óbito] deve ser [SIM] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_doc_indentificacao').val()!='S')
		{
			alert('O campo [Doc Identificação/CPF] deve ser [SIM] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_conta_corrente').val()=='')
		{
			alert('O campo [Comprov. Conta Corrente] deve ser [DIFERENTE DE NULO] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_conta_corrente').val()=='N' && $('#fl_ordem_pagamento').val()!='S')
		{
			alert('O campo [Ordem de Pagamento] deve ser [SIM] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_carta_concessao').val()!='S')
		{
			alert('O campo [Carta Concessão INSS] deve ser [SIM] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}

		if($('#dt_concessao').val()=='')
		{
			alert('O campo [Data da Concessão] deve ser informado para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		else
		{
			dt_concessao = Date.fromString($('#dt_concessao').val());
			dt_concessao.addDays(+60);
        	dt_concessao.zeroTime()
		}
		
		if(($('#fl_comprovante_beneficio').val()!='S') && (!(dt_minima <= dt_concessao)))
		{
			alert('O campo [Comprovante que Benefício está ATIVO] deve ser [SIM] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_certidao_pis').val()=='')
		{
			alert('O campo [Certidão PIS/PASEP] deve ser [DIFERENTE DE NULO] para habilitar o envio do protocolo.');
            $('input[value="Enviar Documentos (escolher destino)"]').hide();
			return false;
		}
		
		if($('#fl_certidao_pis').val()=='N')
		{
			if($('#fl_certidao_pis').val()=='N' && $('#fl_substituto_pis').val()!='S')
			{
				alert('O campo [Documento Substituto do PIS/PASEP] deve ser [SIM] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
		}
		
		if($('#fl_substituto_pis').val()=='S')
		{
			if($('#fl_substituto_pis').val()=='S' && $('#ds_tipo_documento').val()=='')
			{
				alert('O campo [Tipo de Documento] deve ser [DIFERENTE DE NULO] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
			
			if($('#fl_substituto_pis').val()=='S' && $('#fl_nome_titular').val()!='S')
			{
				alert('O campo [Nome do Titular Falecido] deve ser [SIM] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
			
			if($('#fl_substituto_pis').val()=='S' && $('#fl_nome_dependente').val()!='S')
			{
				alert('O campo [Nome do Dependente] deve ser [SIM] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
			
			if($('#fl_substituto_pis').val()=='S' && $('#fl_situacao').val()=='')
			{
				alert('O campo [Situação] deve ser [DIFERENTE DE NULO] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
			
			if($('#fl_substituto_pis').val()=='S' && $('#fl_situacao').val()=='C' && $('#fl_pagamento_anterior').val()!='S')
			{
				alert('O campo [Existe pagamento Anterior] deve ser [SIM] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
			
			if($('#fl_substituto_pis').val()=='S' && $('#fl_carimbo').val()!='S')
			{
				alert('O campo [Carimbo] deve ser [SIM] para habilitar o envio do protocolo.');
				$('input[value="Enviar Documentos (escolher destino)"]').hide();
				return false;
			}
		}
		
		alert('Check list OK.');
		
        $('input[value="Enviar Documentos (escolher destino)"]').show();
	}
	
    function seleciona_documento(cd_documento, nome_documento)
    {
        $('#cd_tipo_doc').val(cd_documento);
        $('#nome_documento').val(nome_documento);
        $("#participante_fica_marcado").attr("checked", "checked");

        adicionar_documento('documentos_box');
    }

    function seleciona_inscricao()
    {
        $.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/inscricao_grid'); ?>", 
		{
			cd_plano_empresa : $('#cd_plano_empresa').val(),
			cd_plano         : $('#cd_plano').val()
		}, 
		function(data)
		{
			$('#div_inscricao').html(data);
		});
    }
	
	function callback_buscar_tipo_documento()
	{
		if( $('#cd_tipo_doc_fica_marcado').is(':checked') )
		{
			if($('#cd_empresa').val()=='')
			{
				$('#cd_empresa').focus();
			}
			else
			{
				$('#ds_observacao').focus();
			}
		}
		else if( $('#participante_fica_marcado').is(':checked') )
		{
			$('#ds_observacao').focus();
		}
		else
		{
			$('#cd_empresa').focus();
		}
	}	
	
	function callback_buscar_participante()
	{
		if( $('#cd_tipo_doc_fica_marcado').is(':checked') )
		{
			$('#ds_observacao').focus();
		}
		else if( $('#participante_fica_marcado').is(':checked') )
		{
			if( $('#cd_tipo_doc').val()=='')
			{
				$('#cd_tipo_doc').focus();
			}
			else
			{
				$('#ds_observacao').focus();
			}
		}
		else
		{
			$('#ds_observacao').focus();
		}
	}	
	
	function checkAll()
	{
		var ipts = $("#tabela_documento>tbody").find("input:checkbox:visible");
		var check = document.getElementById("checkboxCheckAll");
	 
		check.checked ?
			jQuery.each(ipts, function(){
				this.checked = true;
			}) :
			jQuery.each(ipts, function(){
				this.checked = false;
			});
	}	
	
	function getCheck()
	{
		var ipts = $("#tabela_documento>tbody").find("input:checkbox:checked");
		
		$("#ar_proto_selecionado").val("");
		
		jQuery.each(ipts, function(){
			//alert(this.name + " => " + this.value);
			if(jQuery.trim($("#ar_proto_selecionado").val()) == "")
			{
				$("#ar_proto_selecionado").val(this.value);
			}
			else
			{
				$("#ar_proto_selecionado").val($("#ar_proto_selecionado").val() + "," + this.value);
			}
		})
	}	
	
	function protocoloDigitalizacao()
	{
		if($("#ar_proto_selecionado").val() != "")
		{		
			if(confirm("Deseja GERAR Protocolo de Digitalização?"))
			{
				document.getElementById('form_protocolo').action = "<?php echo site_url('ecrm/cadastro_protocolo_interno/protocolo_gerar'); ?>";
				document.getElementById('form_protocolo').method = "post";
				document.getElementById('form_protocolo').target = "_self";
				$("#form_protocolo").submit();
			}		
		}
		else
		{
			alert("Selecione pelo menos um documento");
		}
	}
	
	function protocoloInterno()
	{
		if($("#ar_proto_selecionado").val() != "")
		{		
			if(confirm("Deseja GERAR Protocolo Interno?"))
			{
				document.getElementById('form_protocolo').action = "<?php echo site_url('ecrm/cadastro_protocolo_interno/protocolo_interno_gerar'); ?>";
				document.getElementById('form_protocolo').method = "post";
				document.getElementById('form_protocolo').target = "_self";
				$("#form_protocolo").submit();
			}		
		}
		else
		{
			alert("Selecione pelo menos um documento");
		}
	}
	
	function novo_protocolo()
	{
		getCheck();
		
		if($('#fl_tipo_novo_protocolo').val() == 'D')
		{
			protocoloDigitalizacao();
		}
		else if($('#fl_tipo_novo_protocolo').val() == 'P')
		{
			protocoloDigitalizacao();
		}
		else if($('#fl_tipo_novo_protocolo').val() == 'I')
		{
			protocoloInterno();
		}
		else
		{
			alert('Selecione o tipo do protocolo para ser gerado.');
			$('#tipo_novo_protocolo').focus();
		}

	}
	
	function excluir_justificar(cd_documento_recebido_item)
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/justificar_exclusao'); ?>/"+ cd_documento_recebido_item;
    }
	
	function ir_resumo()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/resumo'); ?>";
    }
	
	function devolver(cd_protocolo)
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno/devolver"); ?>' + "/" + cd_protocolo;
	}	
	
	function salvar_obs_recebimento(cd_documento_recebido_item)
	{
		$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/salvar_obs_recebimento'); ?>", 
		{
			cd_documento_recebido_item : cd_documento_recebido_item,
			ds_observacao_recebimento  : $("#obs_recebimento_"+cd_documento_recebido_item).val()
		}, 
		function(data){});
	}
	
	function receber_documento(cd_documento_recebido_item, nr_folha_pdf)
	{
		if(nr_folha_pdf > 1)
		{
			var confirmacao = 'Atenção este documento possui mais de uma página, deseja receber?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

			if(confirm(confirmacao))
			{
				receber_documento_ajax(cd_documento_recebido_item);
			}
		}
		else
		{
			receber_documento_ajax(cd_documento_recebido_item);
		}	
	}

	function receber_documento_ajax(cd_documento_recebido_item)
	{
		$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/receber_documento'); ?>", 
		{
			cd_documento_recebido_item : cd_documento_recebido_item
		}, 
		function(data)
		{
			var obj = data;
			
			if(obj)
			{
				var obs = $("#obs_recebimento_"+cd_documento_recebido_item).val();
				$("#obs_recebimento_"+cd_documento_recebido_item).attr('readonly', true);
				$("#obs_recebimento_"+cd_documento_recebido_item).hide();
				$("#span_obs_recebimento_"+cd_documento_recebido_item).html(obs);
				
				$("#qt_recebido").val(parseInt($("#qt_recebido").val()) + 1);
				
				$("#receber_documento_"+cd_documento_recebido_item).html("Recebido em "+obj.dt_recebimento+" por "+obj.guerra+" da "+obj.divisao+" ");
				
				if(check_recebido())
				{
					receber(check_recebido());
				}
			}
		}, 'json');
	}
	
	function informarRE(t,cd_documento_recebido_item)
	{
		$(t).hide();
		$("#campo_informarRE_"+cd_documento_recebido_item).show();
	}
	
    function carregar_dados_participante(cd_documento_recebido_item,emp,re,seq,nome)
    {
		//console.log(cd_documento_recebido_item + " | " + emp + " | " + re + " | " + seq + " | " + nome);
		
		$("#informarRE_"+cd_documento_recebido_item+"_cd_empresa").focus();
		
		if(jQuery.trim(nome) != "")
		{
			if(confirm('Confirma o RE informado?'))
			{
				$.post("<?php echo site_url('ecrm/cadastro_protocolo_interno/salvar_re'); ?>", 
				{
					cd_documento_recebido_item : cd_documento_recebido_item,
					cd_empresa                 : emp,
					cd_registro_empregado      : re,
					seq_dependencia            : seq,
					nome                       : nome
				}, 
				function(data)
				{
					rodar_ao_iniciar();
				});		
			}
		}
		else
		{
			alert("Participante NÃO ENCONTRADO");
		}
	}
	
	function qrcode_retorno(data)
	{
		if(data.result)
		{
			$("#cd_empresa").val(data.cd_empresa);
			$("#cd_registro_empregado").val(data.cd_registro_empregado);
			$("#seq_dependencia").val(data.seq_dependencia);
			$("#cd_tipo_doc").val(data.cd_digitalizacao);
			
			consultar_tipo_documentos_focus__cd_tipo_doc();
			consultar_participante_focus__cd_empresa();

			setTimeout(function(){ qrcode_add() }, 200);
		}
	}
	

	function qrcode_add()
	{
		if($("#nome_participante").val() != '')
		{
			adicionar_documento('documentos_box');
		}
	}

    function excluir_protocolo()
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno/excluir"); ?>'+ "/" + $("#cd_documento_recebido").val();
        }
    }

    function reabrir()
    {
        if(confirm("Deseja reabrir este documento?"))
        {
            location.href = "<?= site_url('ecrm/cadastro_protocolo_interno/reabrir/'.$row['cd_documento_recebido']) ?>";
        } 
    }
	
	$(function(){
		rodar_ao_iniciar();
		<?php (trim($row['dt_ok']) != '' ? '$("#btProtDigitalizacao").show();' : '$("#btProtDigitalizacao").hide();') ?>
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_resumo', 'Resumo', false, 'ir_resumo();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

$arr_tipo[] = array('text' => 'Normal', 'value' => '0' );
$arr_tipo[] = array('text' => 'Benefício', 'value' => '1' );
$arr_tipo[] = array('text' => 'Inscrição', 'value' => '2' );

$arr_tipo_protocolo[] = array('value' => 'D', 'text' => 'Protocolo Digitalização (Digital)');
$arr_tipo_protocolo[] = array('value' => 'P', 'text' => 'Protocolo Digitalização (Papel)');
$arr_tipo_protocolo[] = array('value' => 'I', 'text' => 'Protocolo Interno');

$arr_checklist[] = array('value' => 'S', 'text' => 'Sim');
$arr_checklist[] = array('value' => 'N', 'text' => 'Não');

$arr_situacao[] = array('value' => 'A', 'text' => 'Ativo');
$arr_situacao[] = array('value' => 'C', 'text' => 'Cessado');

echo aba_start( $abas );
	echo form_open('ecrm/cadastro_protocolo_interno/salvar','id="form_protocolo" method="post"');
		echo form_hidden('cd_documento_recebido', intval($row['cd_documento_recebido']));
		echo form_hidden('cd_documento_recebido_item');
		echo form_hidden('ar_proto_selecionado');

		// *** INÍCIO DO BOX PRINCIPAL
		echo form_start_box( "default_box", "Protocolo Interno" );
			if(intval($row["cd_documento_recebido"])>0)
			{
				echo form_default_text("protocolo", "Número: ", $row["nr_documento_recebido"], 'style="border: 0px; width: 500px; font-weight: bold;" readonly' );	
				echo form_default_text("ds_tipo", "Tipo: ", $row["ds_tipo"], 'style="border: 0px; width: 500px;" readonly' );	
				echo form_default_hidden( "cd_tipo", "", $row["cd_documento_recebido_tipo"] );
				echo form_default_text("dt_cadastro", "Protocolado em: ", $row["dt_cadastro"] . ' por ' . $row["nome_usuario_cadastro"], 'style="border: 0px; width: 500px;" readonly' );
				
				if(($row['dt_devolucao'] != "") and ($row["dt_envio"] == ""))
				{
					echo form_default_text("dt_devolucao", "Devolvido em: ", $row["dt_devolucao"] . ' por ' . $row["devolvido_por"], 'style="color: red; font-weight: bold; border: 0px; width: 500px;" readonly' );
					echo form_default_textarea("devolucao_descricao", "Justificativa da devolução: ", $row["devolucao_descricao"], " style='height: 70px; border: 1px solid gray;' readonly");
				}

				// *** data de envio
				if(($row["dt_envio"] == '') and ($fl_permissao_cadastro))
				{
					echo form_default_dropdown("cd_documento_recebido_tipo_solic", "Tipo de Solicitação GCM *", $tipo_solicitacao, $row['cd_documento_recebido_tipo_solic']);

					echo form_default_row("escolher_destino", "", br().
						button_save("Enviar Documentos (escolher destino)", "escolher_destino(this.form)", "botao_verde").
						button_save("Excluir o Protocolo", "excluir_protocolo()", "botao_vermelho")
					);
				}
				else
				{
					if($row['dt_redirecionamento']!='')
					{
						if($row["nome_usuario_destino"]!='')
						{
							echo form_default_text("dt_redirecionamento", "Redirecionado em: ", $row["dt_redirecionamento"] . ' para ' . $row["nome_usuario_destino"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );
						}
						elseif($row["grupo_destino_nome"]!='')
						{
							echo form_default_text("dt_envio", "Encaminhado em: ", $row["dt_envio"] . ' para o grupo ' . $row["grupo_destino_nome"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );
						}
					}
					else
					{
						if($row["nome_usuario_destino"]!='')
						{
							echo form_default_text("dt_envio", "Encaminhado em: ", $row["dt_envio"] . ' para ' . $row["nome_usuario_destino"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );				
						}
						elseif($row["grupo_destino_nome"]!='')
						{
							echo form_default_text("dt_envio", "Encaminhado em: ", $row["dt_envio"] . ' para o grupo ' . $row["grupo_destino_nome"], 'style="color: green; font-weight: bold; border: 0px; width: 500px;" readonly' );
						}
					}

					// *** recebimento pelo destino
					if($row['dt_ok']!='')
					{
						echo form_default_text("dt_ok", "Encerrado em: ", $row["dt_ok"] . ' por ' . $row["nome_usuario_ok"], 'style="color: blue; font-weight: bold; border: 0px; width: 500px;" readonly' );
					}
					else
					{
						echo form_default_hidden("dt_ok","","");
					}

					// *** comando receber e comando redirecionar
					$grupo_usuarios = array(); 
					foreach($row['grupo_destino'] as $it)
					{ 
						$grupo_usuarios[] = intval($it['cd_usuario']); 
					}
					
					if(($row['dt_ok']=='') and ( (usuario_id() == intval($row['cd_usuario_destino'])) or (in_array(usuario_id(),$grupo_usuarios))))
					{
						echo form_default_row("","", 
							br()
							.button_save("Redirecionar os documentos","escolher_destino_redir()","botao_disabled")
							.nbsp()
							.button_save("Devolver o Protocolo","devolver(".intval($row["cd_documento_recebido"]).");","botao_vermelho")
							.nbsp()				
							.button_save("Encerrar o Protocolo","receber(check_recebido());")
						);
					}
				}
				// *** data de envio
			}
			else
			{
				echo form_default_dropdown_db("cd_documento_recebido_tipo", "Tipo *", array( "projetos.documento_recebido_tipo", "cd_documento_recebido_tipo", "ds_tipo" ), array( $row["cd_documento_recebido_tipo"] ), "", "", FALSE, ""); 
				echo form_default_dropdown("cd_documento_recebido_tipo_solic", "Tipo de Solicitação GCM *", $tipo_solicitacao); 
			}
        echo form_end_box("default_box");


			echo form_command_bar_detail_start('save_form');
				if(intval($row['cd_documento_recebido'])==0 or (($row["dt_envio"] == '') and ($fl_permissao_cadastro)))
				{
					echo button_save();
				}
				#### BOTOES CRIAR PROTOCOLO ####
				if( intval($row['cd_documento_recebido'])==0 )
				{
					echo button_save("Cancelar","ir_lista();","botao_disabled");
				}
			echo form_command_bar_detail_end();
	

        if(trim($row['dt_ok']) != '' && intval($row['cd_usuario_ok']) == intval($cd_usuario))
		{
            echo form_command_bar_detail_start();
                echo button_save("Reabrir","reabrir()","botao_verde");
            echo form_command_bar_detail_end();
        }

		#### ESCOLHER DESTINO ####
		echo form_start_box( "enviar_box", "Escolher destino e Enviar documentos" );
			echo form_default_usuario_ajax( "cd_usuario_destino", "Destino" );
			echo form_default_row( "", "", " <b>OU</b> " );
			echo form_default_dropdown_db( "cd_documento_recebido_grupo", "Grupo", array( "projetos.documento_recebido_grupo", "cd_documento_recebido_grupo", "ds_nome" ) );
			echo form_default_row( "", "",
					br().
					comando("enviar_button", "Enviar os documentos", "enviar(this.form)").
					nbsp().
					button_save("Cancelar","cancelar_destino()","botao_disabled")
				);
		echo form_end_box( "enviar_box" );

		#### REDIRECIONAR DESTINO ####
		echo form_start_box( "redirecionar_box", "Escolher destino e Redirecionar documentos");
			echo form_default_usuario_ajax("cd_usuario_destino_redir", "Destino");
			echo form_default_row( "", "", " <b>OU</b> " );
			echo form_default_dropdown_db( "cd_documento_recebido_grupo_redir", "Grupo", array( "projetos.documento_recebido_grupo", "cd_documento_recebido_grupo", "ds_nome" ) );
			echo form_default_row( "", "", 
					br().
					comando("redirecionar_button", "Redirecionar os documentos", "redirecionar(this.form)").
					nbsp().
					button_save("Cancelar","cancelar_destino()","botao_disabled")
				);
		echo form_end_box( "redirecionar_box" );

		#### ADICIONAR DOCUMENTOS POR TIPO ####
		if((trim($row["dt_envio"]) == '') and (intval($row['cd_documento_recebido']) > 0) and (intval($row["cd_documento_recebido_tipo"]) != 2) and ($fl_permissao_cadastro))
		{
			echo form_start_box( "tipo_box", "Tipo" );
			echo form_default_dropdown('tipo', 'Tipo:', $arr_tipo,'', 'onchange="seleciona_tipo()"');
			echo form_end_box("tipo_box");

			echo form_start_box( "beneficio_box", "Benefício", true,  false, 'style="display:none"');
			echo form_default_dropdown('relacao', 'Relacão:', $beneficio, '', 'onchange="seleciona_beneficio(this.value);"');
			echo form_default_row('tabela_beneficio', '', '<div id="div_beneficio"></div>');
			echo form_end_box("beneficio_box");
			
			echo form_start_box("checklist_box", "Check List", true,  false, 'style="display:none"');
			echo form_default_dropdown('fl_pedido_beneficio', 'Pedido de Benefício:', $arr_checklist, '', 'onchange="seleciona_pedido_beneficio(this.value);"');
			echo form_default_dropdown('fl_certidao_obito', 'Certidão de Óbito:', $arr_checklist, '', 'onchange="seleciona_certidao_obito(this.value);"');
			echo form_default_dropdown('fl_doc_indentificacao', 'Doc Identificação/CPF:', $arr_checklist, '', 'onchange="seleciona_doc_identificacao(this.value);"');
			echo form_default_dropdown('fl_conta_corrente', 'Comprov. Conta Corrente:', $arr_checklist, '', 'onchange="seleciona_conta_corrente(this.value);"');
			echo form_default_dropdown('fl_ordem_pagamento', 'Ordem de Pagamento:', $arr_checklist);
			echo form_default_dropdown('fl_carta_concessao', 'Carta Concessão INSS:', $arr_checklist, '', 'onchange="seleciona_carta_concessao(this.value);"');
			echo form_default_date('dt_concessao', 'Data da Concessão:');
			echo form_default_dropdown('fl_comprovante_beneficio', 'Comprovante que Benefício está ATIVO:', $arr_checklist, '', 'onchange="seleciona_comprovante_beneficio(this.value);"');
			echo form_default_row('','(*60 dias da concessão – para todos)', '');
			echo form_default_dropdown('fl_certidao_pis', 'Certidão PIS/PASEP:', $arr_checklist, '', 'onchange="seleciona_certidao_pis_pasep(this.value);"');
			echo form_default_dropdown('fl_substituto_pis', 'Documento Substituto do PIS/PASEP:', $arr_checklist, '', 'onchange="seleciona_substituto_pis(this.value);"');
			echo form_default_text('ds_tipo_documento', 'Tipo de Documento:', '', 'style="width:300px;"');
			echo form_default_dropdown('fl_nome_titular', 'Nome do Titular Falecido:', $arr_checklist);
			echo form_default_dropdown('fl_nome_dependente', 'Nome do Dependente:', $arr_checklist);
			echo form_default_dropdown('fl_situacao', 'Situação:', $arr_situacao, '', 'onchange="seleciona_situacao(this.value);"');
			echo form_default_dropdown('fl_pagamento_anterior', 'Existe pagamento Anterior:', $arr_checklist);
			echo form_default_dropdown('fl_carimbo', 'Carimbo:', $arr_checklist);
			echo form_default_row('', '', button_save("Validar Check List", "validar_checklist();"));
			echo form_end_box("checklist_box");

			echo form_start_box( "inscricao_box", "Inscrição", true,  false, 'style="display:none"' );
				echo form_default_plano_ajax('cd_plano', '', '', 'Plano:', 'Empresa:', "","AND cd_empresa NOT IN (4,5)");
				echo form_default_row("","",comando("buscar_inscricao_btn", "Buscar", "seleciona_inscricao();"));
			echo form_default_row('tabela_inscricao', '', '<div id="div_inscricao"></div>');
			echo form_end_box("inscricao_box");
		}

		#### FORMULARIO PARA ADICIONAR DOCUMENTOS ####
		if( intval($row['cd_documento_recebido']) > 0 )
		{
			if(($row['dt_envio'] == '') and ($fl_permissao_cadastro))
			{
				echo form_start_box( "documentos_box", "Adicionar Documento" );
					echo form_default_qrcode(array('id'=>'qrcode', 'caption'=>'QR Code:','callback'=>'qrcode_retorno','value'=>''));
					
					echo form_default_upload_iframe('arquivo','documento_recebido','Arquivo: ', '', '');
					echo form_default_tipo_documento(array('caption' => 'Documento: ', 'callback_buscar'=>'callback_buscar_tipo_documento();'));
					echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'),'Participante (Emp/RE/Seq): ', false, true, true, 'callback_buscar_participante();');
					echo form_default_text('nome_participante','Nome: ', '', 'style="width:500px;"');
					echo form_default_text('ds_observacao','Observações: ', '', 'style="width:500px;"');
					echo form_default_integer('nr_folha','Nr folhas: ',1);
					
					echo form_default_row("","",br().comando("adicionar_documento_btn", "Adicionar Documento", "adicionar_documento(this.form);"));
				echo form_end_box("documentos_box");
			}
				
			echo form_start_box('grid_documentos', 'Documentos Adicionados ao Protocolo',false);
				echo form_hidden('fl_recebido', 'T');
				echo form_radio('recebido', 'T', true, 'onclick="$(\'#fl_recebido\').val(this.value); carregar_grid();"').'Todos'.nbsp(5);
				echo form_radio('recebido', 'S', false, 'onclick="$(\'#fl_recebido\').val(this.value); carregar_grid();"').'Recebidos'.nbsp(5);
				echo form_radio('recebido', 'N', false, 'onclick="$(\'#fl_recebido\').val(this.value); carregar_grid();"').'Não Recebidos'.nbsp(5);
				echo br(2);
				
				echo form_hidden('fl_tipo_novo_protocolo', '');
				if(trim($row['dt_ok']) != '')
				{
					echo form_default_dropdown('tipo_novo_protocolo', 'Gerar Novo:', $arr_tipo_protocolo, '', 'onchange="$(\'#fl_tipo_novo_protocolo\').val(this.value); carregar_grid();"');
				}
				echo '<div id="div_documentos"></div>';
			echo form_end_box('grid_documentos',false);
		}
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view('footer_interna');
?>