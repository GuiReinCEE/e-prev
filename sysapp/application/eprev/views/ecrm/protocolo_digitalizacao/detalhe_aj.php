<?php
set_title('Protocolo para Digitalização - Jurídico');
$this->load->view('header');
?>
<script>
    function excluir_documento(cd)
    {
        if( confirm('Excluir documento?') )
        {
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/excluir_documento'); ?>";
            $.post( url, {cd_documento_protocolo_item:cd}, 
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

    function editar_documento(cd_documento_protocolo_item)
    {
        $("#cd_documento_protocolo_item").val(0);
        
        $.post('<?php echo site_url('/ecrm/protocolo_digitalizacao/editar_documento'); ?>',
        {
            cd_documento_protocolo_item : cd_documento_protocolo_item
        }, 
        function(data)
        {
            var obj = data;
            
            if(obj)                 
            {
                $("#adicionar_documento_btn").val("Salvar Documento");
                $("#cd_documento_protocolo_item").val(obj.cd_documento_protocolo_item);
                $("#cd_tipo_doc").val(obj.cd_tipo_doc);
                $("#cd_registro_empregado").val(obj.cd_registro_empregado);
                $("#cd_empresa").val(obj.cd_empresa);
                $("#nome_participante").val(obj.nome_participante);
                $("#seq_dependencia").val(obj.seq_dependencia);
                $("#ds_processo").val(obj.ds_processo);
                $("#ds_observacao").val(obj.observacao);
                $("#ds_caminho_liquid").val(obj.ds_caminho_liquid);
                $("#nr_folha").val(obj.nr_folha);
                $("#fl_descartar option[value='"+obj.fl_descartar+"']").attr('selected', 'selected');
                    
                if($("#tipo_protocolo").val() == "D")
                {
                    sucesso_arquivo(obj.arquivo + "|" + obj.arquivo_nome);
                }

                consultar_tipo_documentos_focus__cd_tipo_doc();

                if($("#nome_participante").val() == '') 
                {
                    consultar_participante_focus__cd_empresa();
                }
            }
            else
            {
                alert("Erro ao editar.");
            }
        },"json");
    }   
	
    function excluir_todos_documentos(cd)
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
	
    function listar_documento()
    {
        if( $('#cd_documento_protocolo').val()>0 )
        {
            document.getElementById("grid_documentos_content").innerHTML = "<?php echo loader_html(); ?>";
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/listar_documento_aj') ?>";
            $.post(url,
            {
                cd_documento_protocolo:$('#cd_documento_protocolo').val()
            }, 
            function(data)
            {
                $('#grid_documentos_content').html(data);
                $('#grid_documentos_content').focus();
                configure_result_table();
            }
        );
        }
    }

    function incluir_documento_por_processo()
    {
        if(
        ($('#w_processo').val()=='')
            &&
            ($('#w_carta_precatoria').val()=='')
            &&
            ($('#w_documento').val()=='')			
            &&			
            (
        ($('#w_periodo_inicio').val()=='')
            ||
            ($('#w_periodo_fim').val()=='')
    )
    )
        {
            alert('Informe pelo menos um dos campos:\n\n- Processo\n- Carta Precatória\n- Documento\n- Período\n\n');
            $('#w_processo').focus();
            return false;
        }		
		
        if( confirm('Adicionar documentos?') )
        {
            $('#incluir_button').hide();
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/adicionar_documentos_por_processo'); ?>";
            $.post( url, 
            {
                cd_documento_protocolo : $('#cd_documento_protocolo').val(), 
                cd_processo            : $('#w_processo').val(), 
                cd_carta_precatoria    : $('#w_carta_precatoria').val(), 
                cd_documento           : $('#w_documento').val(), 
                dt_inicio              : $('#w_periodo_inicio').val(), 
                dt_fim                 : $('#w_periodo_fim').val()
            }, 
            function(data)
            {
                $('#incluir_button').show();
                if(data)
                {
                    listar_documento();
                }
                else
                {
                    alert(data);
                }
            } );
        }
    }

    function enviar_protocolo()
    {
        if($("#qt_total").val() > 0)
        {		
            if( confirm('Enviar protocolo?') )
            {
                url="<?php echo site_url('ecrm/protocolo_digitalizacao/enviar_protocolo'); ?>";
                $.post( url, {cd_documento_protocolo:$('#cd_documento_protocolo').val()}, function(data){
                    if(data=='true')
                    {
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_aj/"); ?>/'+$('#cd_documento_protocolo').val();
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
                if( $('#cd_documento_protocolo').val()=='0' )
                {
                    if( confirm('Criar um novo protocolo?') )
                    {
                        url="<?php echo site_url('ecrm/protocolo_digitalizacao/criar_protocolo'); ?>";
                        $.post( url, 
                        {
                            tipo_protocolo : $("#tipo_protocolo").val()
                        }, 
                        function(data)
                        {
                            location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_aj/"); ?>/'+data.cd_documento_protocolo;					
                        }, 
                        'json');
                    }
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
		

        if(confirm('Adicionar?'))
        {
            $('#adicionar_documento_btn').hide();
            url="<?php echo site_url('ecrm/protocolo_digitalizacao/adicionar_documento_aj'); ?>";
            $.post( url,{
                cd_documento_protocolo_item:$('#cd_documento_protocolo_item').val(),
                cd_documento_protocolo:$('#cd_documento_protocolo').val()
                ,cd_empresa:$('#cd_empresa').val()
                ,cd_registro_empregado:$('#cd_registro_empregado').val()
                ,nome_participante:$('#nome_participante').val()
                ,seq_dependencia:$('#seq_dependencia').val()
                ,observacao:$('#ds_observacao').val()
                ,nr_folha:$('#nr_folha').val()
                ,cd_tipo_doc:$('#cd_tipo_doc').val()
                ,ds_processo:$('#ds_processo').val(),
                arquivo                : $('#arquivo').val(),
                arquivo_nome           : $('#arquivo_nome').val(),              
                fl_descartar           : $("#fl_descartar").val(),
                ds_caminho_liquid      : $("#ds_caminho_liquid").val(),
                ds_tempo_descarte      : $("#ds_tempo_descarte").val()
            }, function(data){
                $('#adicionar_documento_btn').show();
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
                }            });
        }
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
                $('#ds_processo').focus();
            }
        }
        else if( $('#participante_fica_marcado').is(':checked') )
        {
            $('#ds_processo').focus();
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

    function limpar()
    {
        if($("#tipo_protocolo").val() == "D")
        {
            remover_arquivo_arquivo(true);
        }
        
        $('#ds_processo').val('');
        $('#ds_observacao').val('');
        $('#nr_folha').val('1');
        $('#ds_caminho_liquid').val('');
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
            $('#cd_tipo_doc').focus();
            $('#ds_caminho_liquid').val('');

            $("#fl_descartar option[value='']").attr('selected', 'selected');
            $("#fl_descartar").removeAttr('disabled');
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
       
        listar_documento();
    }
	
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
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
        ob_resul.sort(5, true);
    }

    function marcar(v)
    {
        if(v==0){ $('#cd_tipo_doc').focus(); }
        if(v==1){ $('#cd_empresa').focus(); }
        if(v==2){ $('#cd_tipo_doc').focus(); }
    }
	
	$(function(){

	
        $('#cd_tipo_doc').before( "<input type='radio' id='cd_tipo_doc_fica_marcado' name='fica_marcado' onclick='marcar(0);' />&nbsp" );
        $('#cd_empresa').before( "<input type='radio' id='participante_fica_marcado' name='fica_marcado' onclick='marcar(1);' />&nbsp" );
        $('#ds_caminho_liquid').before( "<input type='radio' id='liquid_fica_marcado' name='fica_marcado' onclick='marcar(2);' />&nbsp" );
	});	
	
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
echo form_hidden('cd_documento_protocolo_item', 0);

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
        echo form_default_row("", "", br() . comando("enviar_button", "Enviar Protocolo", "enviar_protocolo(this.form)") . " " . comando("excluir_todos_documentos_btn", "Excluir o Protocolo", "excluir_todos_documentos();", array('class' => 'botao_vermelho')));
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
	echo form_hidden('cd_gerencia', 'Gerência', 'GJ');
    echo form_default_row('', '', comando('salvar_protocolo_button', 'Criar Protocolo', 'salvar_protocolo();'));
}
echo form_end_box("default_box");
// *** FIM DO BOX PRINCIPAL

if (intval($row['cd_documento_protocolo']) > 0 && $row['dt_envio'] == '')
{
    if ($row["tipo"] == "P")
    {
    /*
	echo form_start_box("varios_documentos_box", "Adicionar Documentos a parti:");
    echo form_default_integer('w_processo', 'Processo:'); # '2736420105040010'
    echo form_default_integer('w_carta_precatoria', 'Carta Precatória:'); # '1425001819995040025' 
    echo form_default_tipo_documento(array('id_codigo' => 'w_documento', 'id_nome' => 'w_documento_nome'));
    echo form_default_date_interval('w_periodo_inicio', 'w_periodo_fim', 'Período'); # , '01/01/2009', '31/12/2010' 
    echo form_default_row('', '', br() . comando('incluir_button', 'Adicionar documentos', 'incluir_documento_por_processo();', array('class' => 'botao_vermelho')));
    echo form_end_box("varios_documentos_box");
	*/
    }

    echo form_start_box("documentos_box", "Adicionar Documento");
	echo form_default_qrcode(array('id'=>'qrcode', 'caption'=>'QR Code:','callback'=>'qrcode_retorno','value'=>''));
    echo form_default_tipo_documento(array('caption' => 'Documento: ', 'callback_buscar' => 'callback_buscar_tipo_documento();'));
    echo form_default_text('ds_tempo_descarte', 'Prazo de Guarda:', '', 'style="width: 500px;" readonly');
	echo form_default_row('ds_tempo_descarte_load', 'Prazo de Guarda:', '<span id="load"></span>');

    echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'),'Participante (Emp/RE/Seq): ', false, true, true, 'callback_buscar_participante();');

    echo form_default_text('nome_participante','Nome: ', '', 'style="width:500px;"');
    echo form_default_text('ds_caminho_liquid', 'Caminho Liquid :', '', 'style="width:500px;"');

    echo form_default_integer('ds_processo', 'Processo:', '');

    echo form_default_text('ds_observacao', 'Observações:', '', 'style="width:500px;"');

    $ar_descartar[] = array('text' => 'Não', 'value' => 'N');
    $ar_descartar[] = array('text' => 'Sim', 'value' => 'S');
    echo form_default_dropdown('fl_descartar', 'Descartar:', $ar_descartar);

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
    echo form_start_box('grid_documentos', 'Documentos Adicionados ao protocolo', false);
    echo form_end_box('grid_documentos', false);
}
?>
<?php
echo aba_end();
echo form_close();
$this->load->view('footer_interna');
?>