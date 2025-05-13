<?php
set_title('Protocolo Correspondência Recebida');
$this->load->view('header');
?>
<script>

<?php echo form_default_js_submit(array(), 'valida_destino(form)');?>

function ir_lista()
{
    location.href='<?php echo site_url('/ecrm/correspondencia_recebida');?>';
}

function valida_destino(form)
{
	var cd_gerencia_destino               = $("#cd_gerencia_destino").val(); 
	var cd_correspondencia_recebida_grupo = $("#cd_correspondencia_recebida_grupo").val();
	
	if(((cd_gerencia_destino) == '' && (cd_correspondencia_recebida_grupo) == '') || ((cd_gerencia_destino) != '' && (cd_correspondencia_recebida_grupo) != ''))
	{
		alert('Informe Gerência ou grupo Destino.');
		
		return false;
	}
	else
	{
		$(form).submit();
	}

}

function excluir()
{
	if(confirm("ATENÇÃO\n\nDeseja excluir?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		location.href='<?php echo site_url("ecrm/correspondencia_recebida/excluir/".intval($row['cd_correspondencia_recebida'])); ?>';
	}
}

function salvar_correspondencia()
{	
	if($("#dt_correspondencia").val() == "")
	{
		alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[dt_correspondencia]" );
		$("#dt_correspondencia").focus();
		return false;
	}
	
	if($("#hr_correspondencia").val() == "")
	{
		alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[hr_correspondencia]" );
		$("#hr_correspondencia").focus();
		return false;
	}
	
	if($("#origem").val() == "")
	{
		alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[origem]" );
		$("#origem").focus();
		return false;
	}
	
	if($("#cd_correspondencia_recebida_tipo").val() == "")
	{
		alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_correspondencia_recebida_tipo]" );
		$("#cd_correspondencia_recebida_tipo").focus();
		return false;
	}
	
	$.post('<?php echo site_url('ecrm/correspondencia_recebida/salvar_item'); ?>',
	{
		cd_correspondencia_recebida      : $('#cd_correspondencia_recebida').val(),
		cd_correspondencia_recebida_item : $('#cd_correspondencia_recebida_item').val(),
		dt_correspondencia               : $('#dt_correspondencia').val(),
		hr_correspondencia               : $('#hr_correspondencia').val(),
		origem                           : $('#origem').val(),
		cd_correspondencia_recebida_tipo : $('#cd_correspondencia_recebida_tipo').val(),
		identificador                    : $('#identificador').val()
	},
	function(data)
	{
		cancelar_edicao(1);
		listar_itens();
	});
}

function listar_itens()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post( '<?php echo site_url('/ecrm/correspondencia_recebida/listar_itens');?>',
	{
		cd_correspondencia_recebida : $('#cd_correspondencia_recebida').val(),
		dt_envio                    : $('#dt_envio').val()
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
		'DateTimeBR',
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

function excluir_item(cd_correspondencia_recebida_item)
{
	if(confirm("ATENÇÃO\n\nDeseja excluir?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		$.post('<?php echo site_url('ecrm/correspondencia_recebida/excluir_item'); ?>',
		{
			cd_correspondencia_recebida      : $('#cd_correspondencia_recebida').val(),
			cd_correspondencia_recebida_item : cd_correspondencia_recebida_item
		},
		function(data)
		{
			listar_itens();
		});
	}
}

function editar_item(cd_correspondencia_recebida_item)
{
	$.post( '<?php echo site_url('ecrm/correspondencia_recebida/carrega_item'); ?>',
	{
		cd_correspondencia_recebida_item : cd_correspondencia_recebida_item
	},
	function(data)
	{
		if(data)
		{
			$('#cd_correspondencia_recebida_item').val(cd_correspondencia_recebida_item);
			$('#dt_correspondencia').val(data.dt_correspondencia);
			$('#hr_correspondencia').val(data.hr_correspondencia);
			$('#origem').val(data.origem);
			$('#cd_correspondencia_recebida_tipo').val(data.cd_correspondencia_recebida_tipo);
			$('#identificador').val(data.identificador);

			
			$('#btn_salvar').attr('value', 'Salvar');
			$('#btn_cancelar').show();
		}
	}, 'json');
}

function cancelar_edicao(fl_tipo)
{
	$('#cd_correspondencia_recebida_item').val(0);
	$('#dt_correspondencia').val('<?php echo date('d/m/Y') ?>');
	$('#cd_empresa').val('');
	$('#cd_registro_empregado').val('');
	$('#seq_dependencia').val('');
	$('#origem').val('');
	$('#identificador').val('');

	if(fl_tipo == 0)
	{
		$('#hr_correspondencia').val('');
		$('#cd_correspondencia_recebida_tipo').val('');
	}
	
	$('#btn_salvar').attr('value', 'Adicionar');
	$('#btn_cancelar').hide();
}

function enviar()
{
	if(confirm("ATENÇÃO\n\nDeseja enviar?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
	{
		location.href='<?php echo site_url("ecrm/correspondencia_recebida/enviar/".intval($row['cd_correspondencia_recebida'])); ?>';
	}
}

function carregar_dados_participante(data)
{
	$('#origem').val(data.nome);
	$('#cd_correspondencia_recebida_tipo').focus();
}

function ir_relatorio()
{
    location.href='<?php echo site_url("ecrm/correspondencia_recebida/relatorio/"); ?>';
}

$(function(){
	if($('#cd_correspondencia_recebida').val() > 0)
	{
		listar_itens();
	}
})

</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'ir_lista();' );
$abas[] = array('aba_lista', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$fl_cadastro_destino = false;

if((trim($row['cd_gerencia_destino']) == '') AND (trim($row['cd_correspondencia_recebida_grupo']) == ''))
{
	$fl_cadastro_destino = true;
}

echo aba_start( $abas );
	echo form_open('ecrm/correspondencia_recebida/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden('cd_correspondencia_recebida', $row['cd_correspondencia_recebida']);
			
			if($fl_cadastro_destino)
			{
				echo form_default_dropdown('cd_gerencia_destino', 'Gerência Destino :', $arr_gerencia, array($row['cd_gerencia_destino']));
				echo form_default_row('', '', '<b>OU</b>');
				echo form_default_dropdown('cd_correspondencia_recebida_grupo', 'Grupo :', $arr_grupo, array($row['cd_correspondencia_recebida_grupo']));
			}
			else if(intval($row['cd_correspondencia_recebida']) > 0)
			{
				echo form_default_text('ano_numero', "Ano/Número:", $row['ano_numero'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );	
				echo form_default_text('dt_inclusao', "Dt Cadastro:", $row['dt_inclusao'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );	
				echo form_default_text('usuario_cadastro', "Usuário Cadastro:", $row['usuario_cadastro'], "style='font-weight: bold;width:300px;border: 0px;' readonly" );	
				echo form_default_text('dt_envio', "Dt Envio:", $row['dt_envio'], "style='font-weight: bold;width:300px;border: 0px; color:green;' readonly" );	
				echo form_default_text('usuario_envio', "Usuário Envio:", $row['usuario_envio'], "style='font-weight: bold;width:300px;border: 0px; color:green;' readonly" );	
				echo form_default_text('dt_recebido', "Dt Recebido:", $row['dt_recebido'], "style='font-weight: bold;width:300px;border: 0px; color:blue' readonly" );	
				echo form_default_text('usuario_recebido', "Usuário Recebido:", $row['usuario_recebido'], "style='font-weight: bold;width:300px;border: 0px; color:blue' readonly" );	
				
				if(trim($row['cd_gerencia_destino']) != '')
				{
					echo form_default_dropdown('cd_gerencia_destino', 'Gerência Destino :', $arr_gerencia, array($row['cd_gerencia_destino']));
				}
				
				if(trim($row['cd_correspondencia_recebida_grupo']) != '')
				{
					echo form_default_dropdown('cd_correspondencia_recebida_grupo', 'Grupo :', $arr_grupo, array($row['cd_correspondencia_recebida_grupo']));
				}
			}
			
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			if((intval($row['cd_correspondencia_recebida']) == 0) OR ((trim($row['cd_gerencia_destino']) == '') AND (trim($row['cd_correspondencia_recebida_grupo']) == '')))
			{
				echo button_save("Salvar");
			}
			
            if((intval($row['cd_correspondencia_recebida']) > 0) AND (trim($row['dt_envio']) == '') AND ((trim($row['cd_gerencia_destino']) != '') OR (trim($row['cd_correspondencia_recebida_grupo']) != '')))
            {
				echo button_save("Enviar", 'enviar();', 'botao_verde', 'id="btn_enviar" '.((intval($row['tl_item']) > 0) ? '' : 'style="display:none"'));

                echo button_save("Excluir", 'excluir()', 'botao_vermelho');
            } 
        echo form_command_bar_detail_end();
	echo form_close();
	if((!$fl_cadastro_destino) AND (trim($row['dt_envio']) == ''))
	{
		echo form_start_box( "default_box_correspondencia", "Correspondência" );
			echo form_hidden('cd_correspondencia_recebida', $row['cd_correspondencia_recebida']);
			echo form_hidden('cd_correspondencia_recebida_item', 0);
			echo form_default_date('dt_correspondencia', 'Dt Correspondência: *', date('d/m/Y'));
			echo form_default_time('hr_correspondencia', 'Hr Correspondência: *');
			
			echo form_default_text('origem', 'Origem: *', '', 'style="width:500px;"');
			echo form_default_dropdown('cd_correspondencia_recebida_tipo', 'Tipo: *', $arr_tipos);
			echo form_default_text('identificador', 'Identificador:', '', 'style="width:500px;"');
		echo form_end_box("default_box_correspondencia");
		echo form_command_bar_detail_start();  
			if(trim($row['dt_envio']) == '' AND (trim($row['cd_gerencia_destino']) != '') OR (trim($row['cd_correspondencia_recebida_grupo']) != ''))
			{
				echo button_save('Adicionar', 'salvar_correspondencia()', 'botao', 'id="btn_salvar"');
				echo button_save('Cancelar', 'cancelar_edicao(0)', 'botao_disabled', 'style="display:none;" id="btn_cancelar"');
			}
		echo form_command_bar_detail_end();
	}
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>