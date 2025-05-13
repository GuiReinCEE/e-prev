<?php
set_title('Doc. Pré Cadastro');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post('<?php echo site_url('ecrm/documento_pre_protocolo/listar');?>',
	$("#filter_bar_form").serialize(),
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
	    null,
		'RE',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateTimeBR',
		'CaseInsensitiveString',
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
    ob_resul.sort(1, true);
}

function novo()
{
    location.href='<?php echo site_url("ecrm/documento_pre_protocolo/cadastro/"); ?>';
}

function tipo_protocolo()
{
	var fl_protoloco = $('#fl_protocolo').val();
	
	$('#fl_tipo_protocolo').val('');
	
	if(fl_protoloco == 'PD')
	{
		$('#fl_tipo_protocolo_row').show();
	}
	else
	{
		$('#fl_tipo_protocolo_row').hide();
	}
}

function gerar_protocolo(fl_protoloco, fl_tipo_protocolo)
{
	var check = [];
	
	$('#check:checked').each(function(i, e) {
		check.push($(this).val());
	});
	
	var confirmacao = 'Deseja ir para o protocolo?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

	if(check.length > 0)
	{
		if(confirm('Deseja gerar o protocolo?'))
		{
			if(fl_protoloco == 'PD')
			{
				$.post( '<?php echo site_url('ecrm/documento_pre_protocolo/gerar_protocolo_digitalizacao'); ?>',
				{
					'check[]'         : check,
					fl_tipo_protocolo : fl_tipo_protocolo
				},
				function(data)
				{
					filtrar();
					
					if(confirm(confirmacao))
					{
						location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/detalhe_atendimento"); ?>/'+data;
					}
				});
			}
			else
			{
				$.post( '<?php echo site_url('ecrm/documento_pre_protocolo/gerar_protocolo_interno'); ?>',
				{
					'check[]' : check
				},
				function(data)
				{
					filtrar();
					
					if(confirm(confirmacao))
					{
						location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno/detalhe"); ?>/'+data;
					}
				});
			}
			
			$('#fl_protocolo').val('');
			$('#fl_tipo_protocolo').val('');
			
			tipo_protocolo();
		}
	}
	else
	{
		alert('Selecione no mínimo um documento');
	}
}

function gerar()
{
	var fl_protoloco      = $('#fl_protocolo').val();
	var fl_tipo_protocolo = $('#fl_tipo_protocolo').val();
	
	if(fl_protoloco == '')
	{
		alert('Informe o tipo do protocolo.');
	}
	else
	{
		if(fl_protoloco == 'PD')
		{
			if( fl_tipo_protocolo == '')
			{
				alert('Informe se o protocolo é Papel ou Digital');
			}
			else
			{
				gerar_protocolo(fl_protoloco, fl_tipo_protocolo);
			}
		}
		else
		{
			gerar_protocolo(fl_protoloco, fl_tipo_protocolo);
		}
	}
}

function checkAllProtocolo()
{
	var ipts = $("#tabela_lista>tbody").find("input:checkbox");
	var check = document.getElementById("checkboxCheckAll");
 
	check.checked ?
		jQuery.each(ipts, function(){
		this.checked = true;
	}) :
		jQuery.each(ipts, function(){
		this.checked = false;
	});
}	

function excluir(cd_documento_pre_protocolo)
{
	if(confirm('Deseja Excluir?'))
	{
		$.post('<?php echo site_url('ecrm/documento_pre_protocolo/excluir'); ?>',
		{
			cd_documento_pre_protocolo : cd_documento_pre_protocolo
		},
		function(data)
		{
			filtrar();
		});
	}
}

$(function(){
	filtrar();
	tipo_protocolo();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_protocolo[] = array('value' => 'PI', 'text' => 'Protocolo Interno');
$arr_protocolo[] = array('value' => 'PD', 'text' => 'Protoloco Digitalização');

$arr_protocolo_digitalizacao[] = array('value' => 'P', 'text' => 'Papel');
$arr_protocolo_digitalizacao[] = array('value' => 'D', 'text' => 'Digital');

$config['button'][] = array('Novo Documento', 'novo()');
$config['button'][] = array('Gerar Protocolo', 'gerar()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
		echo filter_dropdown('fl_protocolo', 'Gerar Protocolo :', $arr_protocolo, '', 'onchange="tipo_protocolo(); filtrar();"');     
		echo filter_dropdown('fl_tipo_protocolo', 'Tipo :', $arr_protocolo_digitalizacao, '', 'onchange="filtrar();"');     
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>