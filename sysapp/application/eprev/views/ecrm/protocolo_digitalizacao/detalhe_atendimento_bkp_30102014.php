<?php
set_title('Protocolo para Digitaliza��o - GAP');
$this->load->view('header');
?>
<script>
    function excluir_documento(cd)
    {
        if(confirm('Excluir documento?'))
        {
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/excluir_documento'); ?>";
            $.post(url, 
            {
                cd_documento_protocolo_item : cd
            }, 
            function(data)
            {
                if(data)
                {
                    listar_documento();
                }
                else
                {
                    alert(data);
                }
            });
        }
    }
	
    function editar_documento(cd)
    {
        $("#cd_documento_protocolo_item").val(0);
        $.post('<?php echo base_url() . index_page(); ?>/ecrm/protocolo_digitalizacao/editar_documento',
        {
            cd_documento_protocolo_item : cd
        }, 
        function(data)
        {
            var obj = data;
            if(obj)					
            {
                $("#adicionar_documento_btn").val("Salvar Documento");
                $("#cd_documento_protocolo_item").val(obj.cd_documento_protocolo_item);
                $("#cd_tipo_doc").val(obj.cd_tipo_doc);
                $("#cd_empresa").val(obj.cd_empresa);
                $("#cd_registro_empregado").val(obj.cd_registro_empregado);
                $("#seq_dependencia").val(obj.seq_dependencia);
                $("#ds_observacao").val(obj.observacao);
                $("#nr_folha").val(obj.nr_folha);
                $("#fl_descartar option[value='"+obj.fl_descartar+"']").attr('selected', 'selected');
                    
                sucesso_arquivo(obj.arquivo + "|" + obj.arquivo_nome);
            }
            else
            {
                alert("Erro ao editar.");
            }
        },
        "json"
    );
    }	
	
    function listar_documento()
    {
        if($('#cd_documento_protocolo').val() > 0)
        {
            $('#grid_documentos_content').html("<?php echo loader_html(); ?>");

            $.post('<?php echo base_url() . index_page(); ?>/ecrm/protocolo_digitalizacao/listar_documento_atendimento',
            {
                cd_documento_protocolo : $('#cd_documento_protocolo').val()
            }, 
            function(data)
            {
                $('#grid_documentos_content').html(data);
                configure_result_table();
            });
        }
    }
	
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
			'RE',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
            'CaseInsensitiveString',
            'Number',
            'CaseInsensitiveString',
            null
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
        ob_resul.sort(3, true);
        ob_resul.sort(3, true);
    }	
	

    function enviar_protocolo()
    {
        if($("#qt_total").val() > 0)
        {		
            if( confirm('Enviar protocolo?') )
            {
                $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/enviar_protocolo'); ?>", 
                {
                    cd_documento_protocolo : $('#cd_documento_protocolo').val()
                }, 
                function(data)
                {
                    if(data=='true')
                    {
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_atendimento/"); ?>/'+$('#cd_documento_protocolo').val();
                    }
                });
            }
        }
        else
        {
            alert("ERRO\n\nN�o h� documentos adicionados no protocolo.")
        }		
    }
	
	function excluir_protocolo()
    {
		if($("#qt_total").val() > 0)
        {
			alert("Protocolo n�o pode ser exclu�do.\nH� documentos anexados no protocolo.")
		}
		else
		{
			if( confirm('Excluir protocolo?') )
			{
				location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/excluir_protocolo"); ?>/'+$('#cd_documento_protocolo').val();
			}
		}
    }

    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao"); ?>';
    }

    function salvar_protocolo()
    {
        if($('#cd_documento_protocolo').val() == '0')
        {
            if($("#tipo_protocolo").val() == "")
            {
                alert("Informe o tipo de protocolo");
                $("#tipo_protocolo").focus();
            }
            else
            {
                if( confirm('Criar um novo protocolo?') )
                {
                    url="<?php echo site_url('ecrm/protocolo_digitalizacao/criar_protocolo'); ?>";
                    $.post(url, 
                    {
                        tipo_protocolo : $("#tipo_protocolo").val()
                    }, 
                    function(data)
                    {
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_atendimento/"); ?>/'+data.cd_documento_protocolo;
                    }, 
                    'json');
                }
            }
        }
    }

    function adicionar_documento(form)
    {
        if( ($('#cd_empresa').val()=='' && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='') )
        {
            alert('Informe a Empresa/RE/Sequ�ncia.');
            $('#cd_empresa').focus();
            return false;
        }

        if($('#cd_tipo_doc').val()=='')
        {
            alert('Informe o tipo de documento.');
            $('#cd_tipo_doc').focus();
            return false;
        }
		
        if (($("#tipo_protocolo").val() == "D")  && (($("#arquivo_nome").val() == "") || ($("#arquivo").val() == "")))
        {
            alert('Nenhum arquivo foi anexado.');
            return false;			
        }
			
        $.post('<?php echo base_url() . index_page(); ?>/ecrm/protocolo_digitalizacao/verifica_participante'
        ,{
            cd_empresa            : $('#cd_empresa').val(),
            cd_registro_empregado : $('#cd_registro_empregado').val(),
            seq_dependencia       : $('#seq_dependencia').val()
        },
        function(data)
        {
            var obj = data;
            if(obj)					
            {
                if(obj.fl_participante > 0)
                {
                    $.post( '<?php echo base_url() . index_page(); ?>/ecrm/protocolo_digitalizacao/adicionar_documento'
                    ,{
                        cd_documento_protocolo : $('#cd_documento_protocolo').val(),
                        cd_empresa             : $('#cd_empresa').val(),
                        cd_registro_empregado  : $('#cd_registro_empregado').val(),
                        seq_dependencia        : $('#seq_dependencia').val(),
                        observacao             : $('#ds_observacao').val(),
                        nr_folha               : $('#nr_folha').val(),
                        cd_documento           : $('#cd_tipo_doc').val(),
                        arquivo                : $('#arquivo').val(),
                        arquivo_nome           : $('#arquivo_nome').val(),
                        cd_documento_protocolo_item : $("#cd_documento_protocolo_item").val(),
                        fl_descartar           : $("#fl_descartar").val()
                    }, 
                    function(data)
                    {
                        if(data == 'true')
                        { 
                            $("#adicionar_documento_btn").val("Adicionar Documento");
                            $("#cd_documento_protocolo_item").val(0);
                            listar_documento(); 
                            limpar(); 
                        } 
                        else 
                        { 
                            alert(data);
                            alert('Falha ao tentar salvar.'); 
                        }
                    });
                }
                else
                {
                    alert("Participante n�o encontrado.\n\nVerifique Empresa/RE/Sequ�ncia informado.\n\n");
                    $('#cd_empresa').focus();
                }
            }
            else
            {
                alert("ERRO\n\n" + data);
            }
        }
        ,"json"
    );			
		
    }

    function limpar()
    {
        if($("#tipo_protocolo").val() == "D")
        {
            remover_arquivo_arquivo(true);
        }
        
        $('#ds_observacao').val('');
        $('#nr_folha').val('1');
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
            $('#cd_tipo_doc').val('');
            $('#nome_documento').val('');
            $('#cd_empresa').val('');
            $('#cd_registro_empregado').val('');
            $('#seq_dependencia').val('');
            $('#nome_participante').val('');
            $("#fl_descartar option[value='']").attr('selected', 'selected');
            $('#cd_tipo_doc').focus();
            
        }
    }

    function enviar(f)
    {
        if(confirm("Enviar?"))
        {
            url = "<?php echo site_url('ecrm/cadastro_protocolo_interno/enviar'); ?>";
            $.post( url,
            {
                cd_documento_protocolo: "<?php echo $row["cd_documento_protocolo"]; ?>"
                , cd_usuario_destino: $('#cd_usuario_destino').val()
                , cd_documento_protocolo_grupo: $('#cd_documento_protocolo_grupo').val()
            }, function(data){ if(data=='true'){location.reload();}else{alert(data);} });
        }
        else
        {
            return false;
        }
    }

    function excluir_item(v)
    {
        if(confirm('Excluir?'))
        {
            url="<?php echo site_url('ecrm/cadastro_protocolo_interno/excluir_item'); ?>";
            $.post(url,{cd:v},function(data){ 
                if(data=='true'){ carregar_grid(); } else { alert('Falha ao tentar excluir!'); } 
            });
        }
    }

    function ir_relatorio()
    {
        location.href="<?php echo site_url('ecrm/documento_protocolo/relatorio'); ?>";
    }
	
    function marcar(v)
    {
        if(v==0){ $('#cd_tipo_doc').focus(); }
        if(v==1){ $('#cd_empresa').focus(); }
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
    
        if($('#nome_documento').val() != '')
        {
            
            $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/descartar'); ?>",
            {
                cd_tipo_doc : $('#cd_tipo_doc').val(),
				cd_divisao  : '<?php echo $this->session->userdata('divisao'); ?>'
            },
            function(data)
            { 
                $("#fl_descartar option[value='"+data+"']").attr('selected', 'selected');
            });
            
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

    $(document).ready( rodar_ao_iniciar );

    function rodar_ao_iniciar()
    {
        $('#enviar_box').hide();
        $('#redirecionar_box').hide();

        <?php if ($row["dt_envio"] == ''): ?>
        $('#cd_tipo_doc').focus();
        <?php endif; ?>

        $('#cd_tipo_doc').before( "<input type='radio' id='cd_tipo_doc_fica_marcado' name='fica_marcado' onclick='marcar(0);' />&nbsp" );
        $('#cd_empresa').before( "<input type='radio' id='participante_fica_marcado' name='fica_marcado' onclick='marcar(1);' />&nbsp" );

        listar_documento();
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start($abas);

echo form_open('ecrm/protocolo_digitalizacao/salvar');
echo form_hidden('cd_documento_protocolo', intval($row['cd_documento_protocolo']));

// *** IN�CIO DO BOX PRINCIPAL
echo form_start_box("default_box", "Protocolo");

if (intval($row["cd_documento_protocolo"]) > 0)
{
	echo form_default_row('', "Protocolo: ", '<span class="label label-inverse">'.$row["ano"]."/".$row["contador"]."</span>");
	echo form_default_row('', "Tipo: ", '<span class="label label-important">'.($row['tipo'] == "D" ? 'DIGITAL' : 'PAPEL')."</span>");	
    echo form_default_text('', "Dt cadastro: ", $row['dt_cadastro'], "style='width:500px;border: 0px;' readonly");
    echo form_default_text('', "Cadastro por: ", $row['nome_usuario_cadastro'], "style='width:500px;border: 0px;' readonly");
    echo form_hidden('tipo_protocolo', $row['tipo']);

    // *** data de envio
    if ($row["dt_envio"] == '' && usuario_id() == intval($row['cd_usuario_cadastro']))
    {
		$conf['class'] = 'botao_vermelho';
	
        echo form_default_row("", "", br() . comando("enviar_button", "Enviar Protocolo", "enviar_protocolo(this.form)").' '. comando("excluir_button", "Excluir Protocolo", "excluir_protocolo(this.form)", $conf));
    }
    else
    {
        echo form_default_text('', "Dt Envio: ", $row['dt_envio'], "style='width:500px;border: 0px;' readonly");
        echo form_default_text('', "Enviado por: ", $row['nome_usuario_envio'], "style='width:500px;border: 0px;' readonly");

        if ($row['dt_ok'] != '')
        {
            echo form_default_text('', "Dt Recebido: ", $row['dt_ok'], "style='width:500px;border: 0px;' readonly");
            echo form_default_text('', "Recebido por: ", $row['nome_usuario_ok'], "style='width:500px;border: 0px;' readonly");
        }
        if ($row['dt_indexacao'] != '')
        {
            echo form_default_text('dt_indexacao', "Dt Indexa��o: ", $row['dt_indexacao'], "style='width:500px;border: 0px;' readonly");
        }
    }
	
	
    // *** data de envio
}
else
{
    $ar_tipo = Array(Array('text' => 'Papel', 'value' => 'P'), Array('text' => 'Digital', 'value' => 'D'));
    echo form_default_dropdown('tipo_protocolo', 'Tipo: *', $ar_tipo);
    echo form_default_row('', '', comando('salvar_protocolo_button', 'Criar Protocolo', 'salvar_protocolo();'));
}
echo form_end_box("default_box");
// *** FIM DO BOX PRINCIPAL

if (intval($row['cd_documento_protocolo']) > 0 && $row['dt_envio'] == '')
{
    echo form_start_box("documentos_box", "Adicionar Documento");
    echo form_default_hidden('cd_documento_protocolo_item', 'Item', 0);
    echo form_default_tipo_documento(array('caption' => 'Documento: ', 'callback_buscar' => 'callback_buscar_tipo_documento();'));
    echo form_default_participante(
        array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante')
        , 'Participante (Emp/RE/Seq):'
        , false
        , false
        , true
        , 'callback_buscar_participante();'
    );
    echo form_default_text('ds_observacao', 'Observa��es:', '', 'style="width:500px;"');

    if ($row["tipo"] == "P")
    {
        $ar_descartar[] = array('text' => 'N�o', 'value' => 'N');
        $ar_descartar[] = array('text' => 'Sim', 'value' => 'S');
        echo form_default_dropdown('fl_descartar', 'Descartar:', $ar_descartar);
    }
    else
    {
        echo form_default_hidden('fl_descartar', "", "");
    }


    echo form_default_integer('nr_folha', 'Nr folhas:', 1);

    if ($row["tipo"] == "D")
    {
        echo form_default_upload_iframe('arquivo', 'protocolo_digitalizacao_' . intval($row["cd_documento_protocolo"]), 'Arquivo:', '', '');
    }
    else
    {
        echo form_default_hidden('arquivo', "", "");
        echo form_default_hidden('arquivo_nome', "", "");
    }

    echo form_default_row("", "", br() . comando("adicionar_documento_btn", "Adicionar Documento", "adicionar_documento(this.form);"));
    echo form_end_box("documentos_box");
}
if (intval($row['cd_documento_protocolo']) > 0)
{
    echo
    form_start_box('grid_documentos', 'Documentos Adicionados ao protocolo', false)
    . form_end_box('grid_documentos', false);
}
?>
<script>
    // $('{PRIMEIRO_CAMPO}').focus();
</script>
<?php
echo aba_end();
// FECHAR FORM
echo form_close();
$this->load->view('footer_interna');