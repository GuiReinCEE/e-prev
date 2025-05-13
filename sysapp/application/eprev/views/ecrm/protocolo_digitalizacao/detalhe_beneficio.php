<?php
set_title('Protocolo para Digitalização - GB');
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
    

    function listar_documento()
    {
        if( $('#cd_documento_protocolo').val()>0 )
        {
            $('#grid_documentos_content').html("<?php echo loader_html(); ?>");
			
            $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/listar_documento') ?>",
			{
				cd_documento_protocolo:$('#cd_documento_protocolo').val()
			}, 
			function(data)
			{
				$('#grid_documentos_content').html(data);
				$('#grid_documentos_content').focus();
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
			'RE',
			'CaseInsensitiveString',
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
					cd_documento_protocolo:$('#cd_documento_protocolo').val()
				}, 
				function(data)
				{
                    if(data=='true')
                    {
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_beneficio/"); ?>/'+$('#cd_documento_protocolo').val();
                    }
                } );
            }
        }
        else
        {
            alert("ERRO\n\nNão há documentos adicionados no protocolo.")
            return false;
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
                    $.post( url, 
                    {
                            tipo_protocolo : $("#tipo_protocolo").val()
                    }, function(data){
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_beneficio/"); ?>/'+data.cd_documento_protocolo;					
                    }, 'json' );
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

        if (
				($('#ds_processo').val() == "")
				&&
				($('#cd_empresa').val() == "" && $('#cd_registro_empregado').val()=='' && $('#seq_dependencia').val()=='')
			)
        {
            alert('Informe a Empresa/RE/Sequência ou Número do Processo');
            $('#cd_empresa').focus();
            return false;
        }

        if($('#ds_caminho_liquid').val()=='')
        {
            alert('Informe o caminho do LIQUID.');
            $('#ds_caminho_liquid').focus();
            return false;
        }

        var myObj = new Object();
        
        var i             = 0;
        var arquivo_count = $('#arquivo_m_count').val();
        
        myObj['cd_documento_protocolo']      = $('#cd_documento_protocolo').val();
        myObj['cd_empresa']                  = $('#cd_empresa').val();
        myObj['cd_registro_empregado']       = $('#cd_registro_empregado').val();
        myObj['seq_dependencia']             = $('#seq_dependencia').val();
        myObj['cd_documento']                = $('#cd_tipo_doc').val();
        myObj['observacao']                  = $('#ds_observacao').val();
        myObj['nr_folha']                    = $('#nr_folha').val();
        myObj['ds_processo']                 = $('#ds_processo').val();
        myObj['cd_documento_protocolo_item'] = $('#cd_documento_protocolo_item').val();
        myObj['ds_caminho_liquid']           = $('#ds_caminho_liquid').val();
        myObj['fl_descartar']                = $('#fl_descartar').val();
        myObj['ds_tempo_descarte']                = $('#ds_tempo_descarte').val();
        myObj['arquivo_m']                   = arquivo_count;

        while(i < arquivo_count)
        {
            myObj['arquivo_m_'+i+'_name']    = $('input[name=arquivo_m_'+i+'_name]').val();
            myObj['arquivo_m_'+i+'_tmpname'] = $('input[name=arquivo_m_'+i+'_tmpname]').val();
            
            i++;
        }

        if (($("#tipo_protocolo").val() == "D")  && (i == 0) && $('#cd_documento_protocolo_item').val() == 0)
        {
            alert('Nenhum arquivo foi anexado.');
            return false;           
        }

        if(confirm('Adicionar?'))
        {
            $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/adicionar_documento_beneficio'); ?>", myObj, 
			function(data)
			{
                if(data=='true')
				{ 
                    $("#adicionar_documento_btn").val("Adicionar Documento");
                    $("#cd_documento_protocolo_item").val(0);
                    $("#arquivo_m_row").show();
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
    }

    function limpar()
    {
        if($("#tipo_protocolo").val() == "D")
        {
            //remover_arquivo_arquivo(true);

            $('#arquivo_m').pluploadQueue().splice();
            $('#arquivo_m').pluploadQueue().refresh();

            $('.plupload_buttons').show();
            $('.plupload_upload_status').hide();
        }        
        
        $('#ds_observacao').val('');
        $('#qrcode').val('');
        $('#nr_folha').val('1');
        
        
        if(! $('#cd_tipo_doc_fica_marcado').is(':checked') )
        {
            $('#cd_tipo_doc').val('');
            $('#nome_documento').val('');

            $("#fl_descartar option[value='']").attr('selected', 'selected');
            $("#fl_descartar").removeAttr('disabled');
        }

        if(! $('#participante_fica_marcado').is(':checked') )
        {
            $('#cd_empresa').val('');
            $('#cd_registro_empregado').val('');
            $('#seq_dependencia').val('');
            $('#nome_participante').val('');
        }
        
        if(! $('#processo_fica_marcado').is(':checked') )
        {
            $('#ds_processo').val('');
        }
        
        if(! $('#liquid_fica_marcado').is(':checked') )
        {
            $('#ds_caminho_liquid').val('');
        }
    }


    function enviar(f)
    {
	
        if(confirm("Enviar?"))
        {
            url = "<?php echo site_url('ecrm/protocolo_digitalizacao/enviar'); ?>";
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
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/excluir_item'); ?>";
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
        if(v==2){ $('#ds_processo').focus(); }
        if(v==3){ $('#cd_tipo_doc').focus(); }
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

    $(document).ready( rodar_ao_iniciar );

    function rodar_ao_iniciar()
    {
        $('#enviar_box').hide();
        $('#redirecionar_box').hide();

        <?php if ($row["dt_envio"] == ''): ?>
                $('#cd_tipo_doc').focus();
        <?php endif; ?>

        $('#cd_tipo_doc').before( "<input type='checkbox' id='cd_tipo_doc_fica_marcado' name='fica_marcado' onclick='marcar(0);' />&nbsp" );
        $('#cd_empresa').before( "<input type='checkbox' id='participante_fica_marcado' name='fica_marcado' onclick='marcar(1);' />&nbsp" );
        $('#ds_processo').before( "<input type='checkbox' id='processo_fica_marcado' name='fica_marcado' onclick='marcar(2);' />&nbsp" );
        $('#ds_caminho_liquid').before( "<input type='checkbox' id='liquid_fica_marcado' name='fica_marcado' onclick='marcar(3);' />&nbsp" );
        listar_documento();
    }

    function callback_buscar_tipo_documento()
    {
        if( $('#cd_tipo_doc_fica_marcado').is(':checked') )
        {
            $('#cd_empresa').focus();
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            $('#ds_processo').focus();
        }
        else if( $('#processo_fica_marcado').is(':checked') )
        {
            $('#cd_empresa').focus();
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
            $('#ds_processo').focus();
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            $('#ds_processo').focus();
        }
        else if( $('#processo_fica_marcado').is(':checked') )
        {
            if( $('#ds_processo').val()=='' )
            {
                $('#ds_processo').focus();
            }
            else
            {
                $('#ds_observacao').focus();
            }
        }
        else
        {
            $('#ds_processo').focus();
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
	echo form_default_hidden("tipo_protocolo","", $row['tipo']);

    // *** data de envio
    if ($row["dt_envio"] == '' && usuario_id() == intval($row['cd_usuario_cadastro']))
    {
        echo form_default_row("", "", br() . comando("enviar_button", "Enviar Protocolo", "enviar_protocolo(this.form)"). button_save("Excluir Protocolo", "excluir_protocolo(this.form)", "botao_vermelho"));
    }
    else
    {
        echo form_default_row("", "Envio", $row['dt_envio'] . ' por ' . $row['nome_usuario_envio']);

        if ($row['dt_ok'] != '')
        {
            echo form_default_row("dt_ok", "Recebimento", $row["dt_ok"] . ' por ' . $row["nome_usuario_ok"]);
        }
        if ($row['dt_indexacao'] != '')
        {
            echo form_default_row("dt_indexacao", "Indexação", $row["dt_indexacao"] . ' por ' . $row["nome_usuario_indexacao"]);
        }
    }
    // *** data de envio
}
else
{
    $ar_tipo = Array(Array('text' => 'Papel', 'value' => 'P'), Array('text' => 'Digital', 'value' => 'D'));
    echo form_default_dropdown('tipo_protocolo', 'Tipo: *', $ar_tipo);
	echo form_hidden('cd_gerencia', 'Gerência', 'GB');
    echo form_default_row('', '', comando('salvar_protocolo_button', 'Criar Protocolo', 'salvar_protocolo();'));
}
echo form_end_box("default_box");
// *** FIM DO BOX PRINCIPAL

if ((intval($row['cd_documento_protocolo']) > 0) and ($row['dt_envio'] == ''))
{
    echo form_start_box("documentos_box", "Adicionar Documento");
    echo form_default_hidden('cd_documento_protocolo_item', 'Item', 0);
	echo form_default_qrcode(array('id'=>'qrcode', 'caption'=>'QR Code:','callback'=>'qrcode_retorno','value'=>''));
    echo form_default_tipo_documento(array('callback_buscar' => 'callback_buscar_tipo_documento();'));
    echo form_default_text('ds_tempo_descarte', 'Prazo de Guarda:', '', 'style="width: 500px;" readonly');
	echo form_default_row('ds_tempo_descarte_load', 'Prazo de Guarda:', '<span id="load"></span>');
    echo form_default_participante(
        array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante')
        , 'RE do participante'
        , false
        , false
        , true
        , 'callback_buscar_participante();'
    );
    
    echo form_default_text('ds_processo', 'Processo', '');
    echo form_default_text('ds_caminho_liquid', 'Caminho Liquid :', '', 'style="width:500px;"');
    echo form_default_text('ds_observacao', 'Observações', '', 'style="width:500px;"');
    
    $ar_descartar[] = array('text' => 'Não', 'value' => 'N');
    $ar_descartar[] = array('text' => 'Sim', 'value' => 'S');
    echo form_default_dropdown('fl_descartar', 'Descartar:', $ar_descartar);
    
    echo form_default_integer('nr_folha', 'Nr Páginas', 1);
	
	
    if ($row["tipo"] == "D")
    {
        //echo form_default_upload_iframe('arquivo', 'protocolo_digitalizacao_' . intval($row["cd_documento_protocolo"]), 'Arquivo:', '', '');
        echo form_default_upload_multiplo('arquivo_m', 'Arquivo :', 'protocolo_digitalizacao_' . intval($row["cd_documento_protocolo"]));
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