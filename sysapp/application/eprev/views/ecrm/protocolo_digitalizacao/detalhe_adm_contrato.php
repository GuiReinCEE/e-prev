<?php
    set_title('Protocolo para Digitalização - GFC');
    $this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/protocolo_digitalizacao') ?>";
    }
	
	function ir_relatorio()
    {
        location.href = "<?= site_url('ecrm/documento_protocolo/relatorio'); ?>";
    }

	function listar_documento()
    {
        if($("#cd_documento_protocolo").val() > 0)
        {
            $("#grid_documentos_content").html("<?= loader_html() ?>");

            $.post("<?= site_url('ecrm/protocolo_digitalizacao/listar_adm_contrato') ?>",
            {
                cd_documento_protocolo : $("#cd_documento_protocolo").val()
            }, 
            function(data)
            {
                $("#grid_documentos_content").html(data);
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
            'Number',
            'CaseInsensitiveString',
            'Number',
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
        ob_resul.sort(7, true);
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
                    $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/criar_protocolo'); ?>", 
                    {
                        tipo_protocolo : $("#tipo_protocolo").val(),
                        fl_contrato    : 'S'
                    }, 
                    function(data)
                    {
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_adm_contrato/"); ?>/'+data.cd_documento_protocolo;
                    },'json');
                }
            }
        }
    }
	
	function adicionar_documento(form)
    {
        if($('#fl_tipo_protocolo_contrato').val()=='')
        {
            alert('Informe o tipo de protocolo.');
            $('#banco').focus();
            return false;
        }
		
		if($('#ds_caminho_liquid').val()=='')
        {
            alert('Informe o caminho.');
            $('#banco').focus();
            return false;
        }
		
        if (($("#tipo_protocolo").val() == "D")  && (($("#arquivo_nome").val() == "") || ($("#arquivo").val() == "")))
        {
            alert('Nenhum arquivo foi anexado.');
            return false;			
        }
			
        $.post( '<?php echo site_url('/ecrm/protocolo_digitalizacao/adicionar_documento'); ?>',
		{
		    cd_documento_protocolo      : $('#cd_documento_protocolo').val(),
		    fl_tipo_protocolo_contrato  : $('#fl_tipo_protocolo_contrato').val(),
            nr_ano_contrato             : $('#nr_ano_contrato').val(),
			nr_id_contrato              : $('#nr_id_contrato').val(),
			observacao                  : $('#ds_observacao').val(),
			nr_folha                    : $('#nr_folha').val(),
			arquivo                     : $('#arquivo').val(),
			arquivo_nome                : $('#arquivo_nome').val(),
			cd_documento_protocolo_item : $("#cd_documento_protocolo_item").val(),
			fl_descartar                : $("#fl_descartar").val(),
            ds_caminho_liquid           : $("#ds_caminho_liquid").val(),
            fl_descarte           		: "N"
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

    function marcar(v)
    {
        if(v==2){ $('#nr_ano_contrato').focus(); }
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
                $("#fl_tipo_protocolo_contrato").val(obj.fl_tipo_protocolo_contrato);
                $("#nr_ano_contrato").val(obj.nr_ano_contrato);
                $("#nr_id_contrato").val(obj.nr_id_contrato);
                $("#ds_observacao").val(obj.observacao);
                $("#nr_folha").val(obj.nr_folha);
                $("#ds_caminho_liquid").val(obj.ds_caminho_liquid);
                $("#fl_descartar option[value='"+obj.fl_descartar+"']").attr('selected', 'selected');
                    
                if($("#tipo_protocolo").val() == "D")
                {
                    sucesso_arquivo(obj.arquivo + "|" + obj.arquivo_nome);
                }
            }
            else
            {
                alert("Erro ao editar.");
            }
        },"json");
    }	
	
    function excluir_documento(cd_documento_protocolo_item)
    {
        if(confirm("Excluir documento?"))
        {
            $.post("<?= site_url('ecrm/protocolo_digitalizacao/excluir_documento'); ?>", 
            {
                cd_documento_protocolo_item : cd_documento_protocolo_item
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
	
	function excluir_protocolo()
    {
		if($("#qt_total").val() > 0)
        {
			alert("Protocolo não pode ser excluído.\nHá documentos anexados no protocolo.")
		}
		else
		{
			if( confirm('Excluir protocolo?') )
			{
				location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/excluir_protocolo"); ?>/'+$('#cd_documento_protocolo').val();
			}
		}
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
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_adm_contrato/"); ?>/'+$('#cd_documento_protocolo').val();
                    }
                });
            }
        }
        else
        {
            alert("ERRO\n\nNão há documentos adicionados no protocolo.")
        }		
    }
	
    function limpar()
    {
        if($("#tipo_protocolo").val() == "D")
        {
            remover_arquivo_arquivo(true);
        }
        
        if( $('#liquid_fica_marcado').is(':checked') )
        {
    		$('#nr_ano_contrato').val('');
            $('#ds_observacao').val('');
            $('#nr_id_contrato').val('');
            $('#nr_folha').val('1');
        }
        else
        {
            $('#ds_caminho_liquid').val('');
            $('#nr_ano_contrato').val('');
            $('#ds_observacao').val('');
            $('#nr_id_contrato').val('');
            $('#nr_folha').val('1');
        }
    }
    	
	$(function(){
		$('#enviar_box').hide();
        $('#redirecionar_box').hide();

        $('#ds_caminho_liquid').before( "<input type='radio' id='liquid_fica_marcado' name='fica_marcado' onclick='marcar(2);' />&nbsp" );

        listar_documento();
	});
	
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
    $abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

    $ar_tipo[] = array('text' => 'Papel', 'value' => 'P');
    $ar_tipo[] = array('text' => 'Digital', 'value' => 'D');

    $ar_descartar[] = array('text' => 'Não', 'value' => 'N');
    $ar_descartar[] = array('text' => 'Sim', 'value' => 'S');

    $tipo_protocolo_contrato[] = array('text' => 'Serviços', 'value' => 'S');
    $tipo_protocolo_contrato[] = array('text' => 'Ordem de Fornecimento', 'value' => 'O');

    echo aba_start($abas);
    	echo form_open('ecrm/protocolo_digitalizacao/salvar');
    	echo form_start_box('default_box', 'Protocolo');
    		echo form_hidden('cd_documento_protocolo', intval($row['cd_documento_protocolo']));
    		echo form_hidden('cd_documento_protocolo_item', 0);
    		
    		if (intval($row["cd_documento_protocolo"]) > 0)
    		{
    			echo form_default_row('', "Protocolo: ", '<span class="label label-inverse">'.$row["ano"]."/".$row["contador"]."</span>");
    			echo form_default_row('', "Tipo: ", '<span class="label label-important">'.($row['tipo'] == "D" ? 'DIGITAL' : 'PAPEL')."</span>");				
    			echo form_default_text('', "Dt cadastro: ", $row['dt_cadastro'], "style='width:500px;border: 0px;' readonly");
    			echo form_default_text('', "Cadastro por: ", $row['nome_usuario_cadastro'], "style='width:500px;border: 0px;' readonly");			
    			echo form_hidden('tipo_protocolo', $row['tipo']);

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
    					echo form_default_text('dt_indexacao', "Dt Indexação: ", $row['dt_indexacao'], "style='width:500px;border: 0px;' readonly");
    				}
    			}
    		}
    		else
    		{
    			echo form_default_dropdown('tipo_protocolo', 'Tipo: (*)', $ar_tipo);
    			echo form_default_row('', '', comando('salvar_protocolo_button', 'Criar Protocolo', 'salvar_protocolo();'));
    		}

    	echo form_end_box("default_box");

    	if (intval($row['cd_documento_protocolo']) > 0 && $row['dt_envio'] == '')
    	{
    		echo form_start_box("documentos_box", "Adicionar Documento");

                echo form_default_dropdown('fl_tipo_protocolo_contrato', 'Tipo: (*)', $tipo_protocolo_contrato);
                echo form_default_text('ds_caminho_liquid', 'Caminho Liquid: (*)', '', 'style="width:500px;"');
    			echo form_default_text('nr_ano_contrato', 'Ano:', '', 'style="width:500px;"');
                echo form_default_text('ds_observacao', 'Descrição:', '', 'style="width:500px;"');
    			echo form_default_text('nr_id_contrato', 'ID:  (*)', '', 'style="width:500px;"');

    			if ($row["tipo"] == "P")
    			{
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
    		echo form_start_box('grid_documentos', 'Documentos Adicionados ao protocolo', false);
    		echo form_end_box('grid_documentos', false);
    	}

    	echo form_close();
    	echo br();
    echo aba_end();

$this->load->view('footer_interna');
?>