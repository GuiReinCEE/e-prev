<?php
    set_title('Protocolo para Digitalização');
    $this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/protocolo_digitalizacao') ?>";
    }

    function ir_relatorio()
    {
        location.href = "<?= site_url('ecrm/protocolo_digitalizacao/relatorio') ?>";
    }

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
                $("#ds_caminho_liquid").val(obj.ds_caminho_liquid);
                $("#nr_folha").val(obj.nr_folha);
                $("#fl_descartar option[value='"+obj.fl_descartar+"']").attr('selected', 'selected');

				consultar_tipo_documentos_focus__cd_tipo_doc();
				consultar_participante_focus__cd_empresa();		
				
                //sucesso_arquivo(obj.arquivo + "|" + obj.arquivo_nome);
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

            $.post('<?php echo base_url() . index_page(); ?>/ecrm/protocolo_digitalizacao/listar_documento_padrao',
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
            'CaseInsensitiveString',
            'CaseInsensitiveString',
			'RE',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
            'CaseInsensitiveString',
            'Number',
            'CaseInsensitiveString',
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
        ob_resul.sort(6, true);
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
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_padrao/"); ?>/'+$('#cd_documento_protocolo').val();
                    }
                });
            }
        }
        else
        {
            alert("ERRO\n\nNão há documentos adicionados no protocolo.")
        }		
    }

	function excluir_protocolo()
    {
        if( confirm('Excluir o Protocolo?') )
        {
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/excluir_todos_documentos'); ?>";
            $.post( url, {cd_documento_protocolo:$('#cd_documento_protocolo').val()}, 
            function(data)
            {
                if(data)
                {
					location.href = '<?php echo site_url("ecrm/protocolo_digitalizacao/excluir_protocolo"); ?>/'+$('#cd_documento_protocolo').val();
                }
                else
                {
                    alert(data);
                }
            });
        }
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
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_padrao/"); ?>/'+data.cd_documento_protocolo;
                    }, 
                    'json');
                }
            }
        }
    }

    function adicionar_documento(form)
    {
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

        if($('#ds_caminho_liquid').val()=='')
        {
            alert('Informe o caminho no sistema LIQUID.');
            $('#cd_tipo_doc').focus();
            return false;
        }
			
         $.post("<?= site_url('/ecrm/protocolo_digitalizacao/adicionar_documento_padrao'); ?>",
         {
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
            fl_descartar           : $("#fl_descartar").val(),
            ds_caminho_liquid      : $("#ds_caminho_liquid").val(),
            ds_tempo_descarte      : $("#ds_tempo_descarte").val()
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
            $('#ds_caminho_liquid').val('');
            $('#cd_empresa').focus();
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            $('#cd_tipo_doc').val('');
            $('#nome_documento').val('');
            $('#ds_caminho_liquid').val('');
            $('#cd_tipo_doc').focus();

            $("#fl_descartar option[value='']").attr('selected', 'selected');
            $("#fl_descartar").removeAttr('disabled');
        }
        else if( $('#liquid_fica_marcado').is(':checked') )
        {
            $('#cd_tipo_doc').val('');
            $('#nome_documento').val('');
            $('#cd_empresa').val('');
            $('#cd_registro_empregado').val('');
            $('#seq_dependencia').val('');
            $('#nome_participante').val('');
            $('#cd_empresa').focus();
            $('#cd_tipo_doc').focus();

            $("#fl_descartar option[value='']").attr('selected', 'selected');
            $("#fl_descartar").removeAttr('disabled');
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
            $("#fl_descartar").removeAttr('disabled');
            $('#cd_tipo_doc').focus();
            $('#ds_caminho_liquid').val('');
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
                cd_documento_protocolo       : "<?php echo $row["cd_documento_protocolo"]; ?>", 
                cd_usuario_destino           : $('#cd_usuario_destino').val(), 
                cd_documento_protocolo_grupo : $('#cd_documento_protocolo_grupo').val()
            }, 
            function(data)
            { 
                if(data=='true')
                {
                    location.reload();
                }
                else{
                    alert(data);
                } 
            });
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
            $.post(url,{
                cd:v
            },
            function(data)
            { 
                if(data=='true')
                { 
                    carregar_grid(); 
                } 
                else 
                { 
                    alert('Falha ao tentar excluir!'); 
                } 
            });
        }
    }
	
    function marcar(v)
    {
        if(v==0){ $('#cd_tipo_doc').focus(); }
        if(v==1){ $('#cd_empresa').focus(); }
        if(v==2){ $('#cd_tipo_doc').focus(); }
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

        get_tempo_descarte();
    }
	
    function callback_buscar_participante()
    {
        if( $('#cd_tipo_doc_fica_marcado').is(':checked') )
        {
            $('#ds_caminho_liquid').focus();
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            if( $('#cd_tipo_doc').val()=='')
            {
                $('#cd_tipo_doc').focus();
            }
            else
            {
                $('#ds_caminho_liquid').focus();
            }
        }
        else
        {
            $('#ds_caminho_liquid').focus();
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
        }
    } 

	function get_tempo_descarte()
	{
		if($("#nome_documento").val() != '')
		{
			$("#ds_tempo_descarte_row").hide();
			$("#load").html("<?= loader_html('P') ?>");
			$("#ds_tempo_descarte_load_row").show();
		}

		$.post("<?= site_url('ecrm/protocolo_digitalizacao/get_tempo_descarte') ?>", 
		{
			cd_tipo_doc : $("#cd_tipo_doc").val()
		},
		function(data)
		{
			if($("#nome_documento").val() != '')
			{
				$("#ds_tempo_descarte_load_row").hide();

				var vl_arquivo_central = (data.result[0].vl_arquivo_central != '' ? data.result[0].vl_arquivo_central + " - " : "");

				var ds_tempo_descarte = vl_arquivo_central + data.result[0].id_arquivo_central;

				$("#ds_tempo_descarte").val(ds_tempo_descarte);
				$("#ds_tempo_descarte_row").show();
			
                if(ds_tempo_descarte == 'PERMANENTE')
                {
                    $("#fl_descartar").val('N');
                    $("#fl_descartar").attr('disabled', 'disabled');
                }
                else if(ds_tempo_descarte == 'NAO ARQUIVADO')
                {
                    $("#fl_descartar").val('S');
                    $("#fl_descartar").attr('disabled', 'disabled');
                }
                else
                {
                    $("#fl_descartar").removeAttr('disabled');
                }
            }
            else
            {
                $("#ds_tempo_descarte_row").hide();
                $("#ds_tempo_descarte").val("");
                $("#fl_descartar").removeAttr('disabled');
            }

		}, 'json');
	}  

    $(function(){
        $('#enviar_box').hide();
        $('#redirecionar_box').hide();

        <?php if ($row["dt_envio"] == ''): ?>
        $('#cd_tipo_doc').focus();
        <?php endif; ?>

        $('#cd_tipo_doc').before( "<input type='radio' id='cd_tipo_doc_fica_marcado' name='fica_marcado' onclick='marcar(0);' />&nbsp" );
        $('#cd_empresa').before( "<input type='radio' id='participante_fica_marcado' name='fica_marcado' onclick='marcar(1);' />&nbsp" );
        $('#ds_caminho_liquid').before( "<input type='radio' id='liquid_fica_marcado' name='fica_marcado' onclick='marcar(2);' />&nbsp" );

        listar_documento();
    });
</script>
<style>
	#ds_tempo_descarte_row, #ds_tempo_descarte_load_row
	{
		display: none;
	}
</style>
<?php
    $abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
    $abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);

echo form_open('ecrm/protocolo_digitalizacao/salvar');
echo form_hidden('cd_documento_protocolo', intval($row['cd_documento_protocolo']));

// *** INÍCIO DO BOX PRINCIPAL
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
        echo form_default_row("", "", br() . comando("enviar_button", "Enviar Protocolo", "enviar_protocolo(this.form)"). button_save("Excluir Protocolo", "excluir_protocolo(this.form)", "botao_vermelho"));
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
            echo form_default_text('dt_indexacao', "Dt Indexação: ", $row['dt_indexacao'], "style='width:500px;border: 0px;' readonly");
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
	echo form_default_qrcode(array('id'=>'qrcode', 'caption'=>'QR Code:','callback'=>'qrcode_retorno','value'=>''));
    echo form_default_tipo_documento(array('caption' => 'Documento: ', 'callback_buscar' => 'callback_buscar_tipo_documento();'));
    echo form_default_text('ds_tempo_descarte', 'Prazo de Guarda:', '', 'style="width: 500px;" readonly');
	echo form_default_row('ds_tempo_descarte_load', 'Prazo de Guarda:', '<span id="load"></span>');
    echo form_default_participante(
        array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante')
        , 'Participante (Emp/RE/Seq):'
        , false
        , false
        , true
        , 'callback_buscar_participante();'
    );
    echo form_default_text('ds_caminho_liquid', 'Caminho Liquid :', '', 'style="width:500px;"');
    echo form_default_text('ds_observacao', 'Observações:', '', 'style="width:500px;"');

    if ($row["tipo"] == "P")
    {
        $ar_descartar[] = array('text' => 'Não', 'value' => 'N');
        $ar_descartar[] = array('text' => 'Sim', 'value' => 'S');
        echo form_default_dropdown('fl_descartar', 'Descartar:', $ar_descartar);
    }
    else
    {
        echo form_default_hidden('fl_descartar', "", "");
    }


    echo form_default_integer('nr_folha', 'Nr Páginas:', 1);

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


echo br(10);

echo aba_end();
// FECHAR FORM
echo form_close();
$this->load->view('footer_interna');