<?php
set_title('Rescisão');
$this->load->view('header');
?>
<script>
function filtrar()
{
	if($('#dt_digita_demissao_ini').val() != '' && $('#dt_digita_demissao_ini').val() != '')
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post( '<?php echo site_url('atividade/rescisao_controle/listar'); ?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	else
	{
		alert('Informe o período da Dt Digita Rescisão');
	}
}

function configure_result_table()
{
    var ob_resul = new SortableTable(document.getElementById("tabela_lista"),
    [
	    null,
		'RE',
		'CaseInsensitiveString',
		'Number',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateBR',
		'DateTimeBR',
		null,
		'CaseInsensitiveString',
		'DateTimeBR',
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

function adicionar()
{
	var check = [];
	
	$('#check:checked').each(function(i, e) {
		check.push($(this).val());
	});
	
	if(check.length > 0)
	{
		if(confirm('Deseja adicionar os REs selecionados?'))
		{
			$.post( '<?php echo site_url('atividade/rescisao_controle/adicionar'); ?>',
			{
				'check[]' : check
			},
			function(data)
			{
				filtrar();
			});
		}
	}
	else
	{
		alert('Selecione no mínimo um participante');
	}
}

function remover(re)
{
	
	if(confirm('Deseja remover o RE?'))
	{
		$.post( '<?php echo site_url('atividade/rescisao_controle/remover'); ?>',
		{
			're' : re
		},
		function(data)
		{
			filtrar();
		});
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

function enviar()
{
	var confirmacao = 'ATENÇÃO esta ação é irreversível.\n\n' +
                'Confira a lista gerada antes de enviar os emails.\n\n' +
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';


	if(confirm(confirmacao))
	{
		$.post( '<?php echo site_url('atividade/rescisao_controle/enviar'); ?>',
		function(data)
		{
			filtrar();
		});
	}
}

$(function(){
	
	$('#dt_digita_demissao_ini_dt_digita_demissao_fim_shortcut').val('last30days');
	$('#dt_digita_demissao_ini_dt_digita_demissao_fim_shortcut').change();
	
	filtrar();
})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Adicionar', 'adicionar();');
$config['button'][] = array('Enviar E-mails', 'enviar();', '', 'botao_vermelho');

$arr_status[] = array('value' => 'E', 'text' => 'Enviado');
$arr_status[] = array('value' => 'A', 'text' => 'Aguardando Envio');
$arr_status[] = array('value' => 'N', 'text' => 'Não Enviado');

$arr_email[] = array('value' => 'S', 'text' => 'Sim');
$arr_email[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_empresa('cd_empresa', "", "Empresa :", 'P');
        echo filter_date_interval('dt_digita_demissao_ini', 'dt_digita_demissao_fim', 'Dt Digita Rescisão :');
		echo filter_dropdown('fl_status', 'Status :', $arr_status);
		echo filter_dropdown('fl_email', 'Possui E-mail :', $arr_email);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>