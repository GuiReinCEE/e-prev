<?php 
set_title('Protocolo Correspondência Recebida');
$this->load->view('header'); 
?>
<script>
function ir_lista()
{
	location.href='<?php echo site_url('/ecrm/correspondencia_recebida');?>';
}

function listar_itens()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
	$.post( '<?php echo site_url('/ecrm/correspondencia_recebida/listar_itens_receber');?>',
	{
		cd_correspondencia_recebida : $('#cd_correspondencia_recebida').val()
	},
	function(data)
	{
		$("#result_div").html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'DateTimeBR',
		
		'CaseInsensitiveString',
		'CaseInsensitiveString',
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
	ob_resul.sort(0, true);
}

function informar_re(cd_correspondencia_recebida_item)
{
	$("#span_re_"+cd_correspondencia_recebida_item).hide();
	$("#nome_participante_"+cd_correspondencia_recebida_item).show();
	$("#campo_re_"+cd_correspondencia_recebida_item).show();
}

function carregar_dados_participante(cd_correspondencia_recebida_item, emp, re, seq, nome)
{
	//console.log(cd_correspondencia_recebida_item + " | " + emp + " | " + re + " | " + seq + " | " + nome);
	
	$("#re_"+cd_correspondencia_recebida_item+"_cd_empresa").focus();

	if(jQuery.trim(nome) != "")
	{
		if(confirm("ATENÇÃO\n\Confirma o RE informado?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/correspondencia_recebida/salvar_re'); ?>", 
			{
				cd_correspondencia_recebida_item : cd_correspondencia_recebida_item,
				cd_empresa                       : emp,
				cd_registro_empregado            : re,
				seq_dependencia                  : seq
			}, 
			function(data)
			{
				listar_itens();
			});		
		}
	}
	else
	{
		alert("Participante NÃO ENCONTRADO");
	}
}

function limpar_re(cd_correspondencia_recebida_item)
{
    if(confirm("ATENÇÃO\n\Confirma limpar o RE?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
    {
        $.post("<?php echo site_url('ecrm/correspondencia_recebida/salvar_re'); ?>", 
        {
                cd_correspondencia_recebida_item : cd_correspondencia_recebida_item,
                cd_empresa                       : '',
                cd_registro_empregado            : '',
                seq_dependencia                  : ''
        }, 
        function(data)
        {
                listar_itens();
        });
    }
}

function receber(cd_correspondencia_recebida_item)
{
	var bol = true;
	
	if($("#re_"+cd_correspondencia_recebida_item+"_cd_empresa").val() == '')
	{
		if(!confirm("ATENÇÃO\n\nDeseja receber sem informar o RE?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			bol = false;
		}
	}
	
	if(bol)
	{
		if($('#tl_recebido').val() == 1)
		{
			receber_todos();
		}
		else
		{
			$.post("<?php echo site_url('ecrm/correspondencia_recebida/receber_correspondencia'); ?>", 
			{
				cd_correspondencia_recebida      : $('#cd_correspondencia_recebida').val(),
				cd_correspondencia_recebida_item : cd_correspondencia_recebida_item
			}, 
			function(data)
			{
				$('#tl_recebido').val(data);
				listar_itens();
			});	
		}
	}
	else
	{
		informar_re(cd_correspondencia_recebida_item);
		
		$("#re_"+cd_correspondencia_recebida_item+"_cd_empresa").focus();
	}
}

function recusar_ok(cd_correspondencia_recebida_item)
{
	if(confirm("ATENÇÃO\n\nDeseja aceitar a recusa da Correspondências?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		$.post("<?php echo site_url('ecrm/correspondencia_recebida/recusar_ok'); ?>", 
		{
			cd_correspondencia_recebida_item : cd_correspondencia_recebida_item
		}, 
		function(data)
		{
			listar_itens();
		});	
	}
}

function recusar(cd_correspondencia_recebida_item)
{
	if(confirm("ATENÇÃO\n\nDeseja recusar a Correspondências?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		location.href='<?php echo site_url('/ecrm/correspondencia_recebida/recusar');?>/'+$('#cd_correspondencia_recebida').val()+'/'+cd_correspondencia_recebida_item;
	}
}

function receber_todos()
{
	if(confirm("ATENÇÃO\n\nDeseja receber TODAS as Correspondências?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		$.post("<?php echo site_url('ecrm/correspondencia_recebida/receber_todas_correspondencia'); ?>", 
		{
			cd_correspondencia_recebida : $('#cd_correspondencia_recebida').val()
		}, 
		function(data)
		{
			location.reload();
		});	
	}
}

function encerrar()
{
	if(confirm("ATENÇÃO\n\nDeseja encerrar as Correspondências?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		$.post("<?php echo site_url('ecrm/correspondencia_recebida/receber_todas_correspondencia'); ?>", 
		{
			cd_correspondencia_recebida : $('#cd_correspondencia_recebida').val()
		}, 
		function(data)
		{
			location.reload();
		});	
	}
}

function ir_relatorio()
{
	location.href='<?php echo site_url("ecrm/correspondencia_recebida/relatorio/"); ?>';
}

function nova_correspondeica()
{
	var itens = [];
	
	$("input[name='item[]']:checked").each(function() {
		itens.push($(this).val()); 
    });
	
	if(itens.length > 0)
	{
		$.post( '<?php echo site_url('/ecrm/correspondencia_recebida/correspondecia_items_recusados'); ?>',
		{
			'itens[]' : itens
		},
		function(data)
		{
			location.href='<?php echo site_url("ecrm/correspondencia_recebida/cadastro/"); ?>/'+data;
		});
	}
	else
	{
		alert('Selecione um ítem.');
	}
}

$(function(){
	listar_itens();
})
</script>
<?
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_lista', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_detalhe', 'Receber', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/correspondencia_recebida/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden('tl_recebido', $tl_recebido);
			echo form_hidden('cd_correspondencia_recebida', $row['cd_correspondencia_recebida']);
			echo form_default_text('ano_numero', "Ano/Número:", $row['ano_numero'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );	
			echo form_default_text('dt_inclusao', "Dt Cadastro:", $row['dt_inclusao'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );	
			echo form_default_text('usuario_cadastro', "Usuário Cadastro:", $row['usuario_cadastro'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );	
			echo form_default_text('dt_envio', "Dt Envio:", $row['dt_envio'], "style='font-weight: bold;width:300px;border: 0px; color:green;' readonly" );	
			echo form_default_text('usuario_envio', "Usuário Envio:", $row['usuario_envio'], "style='font-weight: bold;width:300px;border: 0px; color:green;' readonly" );		
			if(trim($row['cd_gerencia_destino']) != '')
			{
				echo form_default_text('gerencia_destino', "Gerência Destino:", $row['gerencia_destino'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );
			}
			
			if(intval($row['cd_correspondencia_recebida_grupo']) > 0)
			{
				echo form_default_text('correspondencia_recebida_grupo', "Grupo Destino:", $row['grupo'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );
			}
			echo form_default_text('dt_recebido', "Dt Recebido:", $row['dt_recebido'], "style='font-weight: bold;width:300px;border: 0px; color:blue' readonly" );	
			echo form_default_text('usuario_recebido', "Usuário Recebido:", $row['usuario_recebido'], "style='font-weight: bold;width:300px;border: 0px; color:blue' readonly" );	
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			if(intval($tl_recebido) > 0)
			{
				echo button_save("Receber Todas Correspondências", 'receber_todos()', 'botao_vermelho');
			}
			else if(trim($row['dt_recebido']) == '')
			{
				echo button_save("Encerrar", 'encerrar()', 'botao_disabled');
			}
        echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
echo aba_end();

$this->load->view('footer_interna');
?>