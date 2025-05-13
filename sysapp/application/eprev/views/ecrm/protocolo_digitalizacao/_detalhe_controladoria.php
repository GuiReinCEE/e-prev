<?php
set_title('Protocolo para Digitalização - GC');
$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao"); ?>';
    }
	
	function ir_relatorio()
    {
        location.href="<?php echo site_url('ecrm/documento_protocolo/relatorio'); ?>";
    }

	function listar_documento()
    {
        if($('#cd_documento_protocolo').val() > 0)
        {
            $('#grid_documentos_content').html("<?php echo loader_html(); ?>");

            $.post('<?php echo site_url('/ecrm/protocolo_digitalizacao/listar_documento_controladoria'); ?>',
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
                        tipo_protocolo : $("#tipo_protocolo").val()
                    }, 
                    function(data)
                    {
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_controladoria/"); ?>/'+data.cd_documento_protocolo;
                    },'json');
                }
            }
        }
    }
	
	function adicionar_documento(form)
    {
        if($('#banco').val()=='')
        {
            alert('Informe o banco de dados.');
            $('#banco').focus();
            return false;
        }
		
		if($('#caminho').val()=='')
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
		    banco                       : $('#banco').val(),
			caminho                     : $('#caminho').val(),
			observacao                  : $('#ds_observacao').val(),
			nr_folha                    : $('#nr_folha').val(),
			arquivo_m                   : $('#arquivo_m_count').val(),
			cd_documento_protocolo_item : $("#cd_documento_protocolo_item").val(),
			fl_descartar                : $("#fl_descartar").val()
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
                $("#banco").val(obj.banco);
                $("#caminho").val(obj.caminho);
                $("#ds_observacao").val(obj.observacao);
                $("#nr_folha").val(obj.nr_folha);
                $("#fl_descartar option[value='"+obj.fl_descartar+"']").attr('selected', 'selected');
                    
                sucesso_arquivo(obj.arquivo + "|" + obj.arquivo_nome);
            }
            else
            {
                alert("Erro ao editar.");
            }
        },"json");
    }	
	
    function excluir_documento(cd_documento_protocolo_item)
    {
        if(confirm('Excluir documento?'))
        {
            $.post("<?php echo site_url('ecrm/protocolo_digitalizacao/excluir_documento'); ?>", 
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
                        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_controladoria/"); ?>/'+$('#cd_documento_protocolo').val();
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
        
		$('#banco').val('');
		$('#caminho').val('');
        $('#ds_observacao').val('');
        $('#nr_folha').val('1');
    }
    	
	$(function(){
		$('#enviar_box').hide();
        $('#redirecionar_box').hide();

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

echo aba_start($abas);
	echo form_open('ecrm/protocolo_digitalizacao/salvar');
	echo form_start_box("default_box", "Protocolo");
		echo form_hidden('cd_documento_protocolo', intval($row['cd_documento_protocolo']));
		echo form_hidden('cd_documento_protocolo_item', 0);
		
		if (intval($row["cd_documento_protocolo"]) > 0)
		{
			echo form_default_text('protocolo', "Protocolo: ", $row["ano"] . "/" . $row["contador"] . " - " . $row["tipo"], "style='font-weight:bold; width:500px;border: 0px;' readonly");
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
			echo form_default_dropdown('tipo_protocolo', 'Tipo: *', $ar_tipo);
			echo form_default_row('', '', comando('salvar_protocolo_button', 'Criar Protocolo', 'salvar_protocolo();'));
		}
	echo form_end_box("default_box");

	if (intval($row['cd_documento_protocolo']) > 0 && $row['dt_envio'] == '')
	{
		echo form_start_box("documentos_box", "Adicionar Documento");
			echo form_default_text_autocomplete('banco', 'Banco de Dados:', site_url('ecrm/protocolo_digitalizacao/gc_banco_autocomplete/'), '', '', 'style="width:500px;"');
			echo form_default_text_autocomplete('caminho', 'Caminho:', site_url('ecrm/protocolo_digitalizacao/gc_caminho_autocomplete/'), '', '', 'style="width:500px;"');
			echo form_default_text('ds_observacao', 'Observações:', '', 'style="width:500px;"');

			if ($row["tipo"] == "P")
			{
				echo form_default_dropdown('fl_descartar', 'Descartar:', $ar_descartar);
			}
			else
			{
				echo form_default_hidden('fl_descartar', "", "");
			}

			echo form_default_integer('nr_folha', 'Nr folhas:', 1);

			if ($row["tipo"] == "D")
			{
				echo 'protocolo_digitalizacao_' . intval($row["cd_documento_protocolo"]);
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
		echo form_start_box('grid_documentos', 'Documentos Adicionados ao protocolo', false);
		echo form_end_box('grid_documentos', false);
	}

	echo form_close();
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>