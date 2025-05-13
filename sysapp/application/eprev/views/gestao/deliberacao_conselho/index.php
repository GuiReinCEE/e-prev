<?php
set_title('Deliberações do Conselho');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?=loader_html()?>");
	
    $.post('<?=site_url('gestao/deliberacao_conselho/listar')?>',
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
		'CaseInsensitiveString',
		null,
		'DateBR',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'Number',
		'CaseInsensitiveString',
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
    ob_resul.sort(1, true);
}

function checkAll()
{
	var ipts = $("#table-1>tbody").find("input:checkbox");
	var check = document.getElementById("checkboxCheckAll");
	
	check.checked ?
		jQuery.each(ipts, function(){
			this.checked = true;
		}) :
		jQuery.each(ipts, function(){
			this.checked = false;
		});
}

function divulgar()
{
	var confirmacao = 'Deseja DIVULGAR e enviar o e-mail para todos?\n\n'+
					  'Clique [Ok] para Sim\n\n'+
					  'Clique [Cancelar] para Não\n\n';	

	if(confirm(confirmacao))
	{	
	
		var ipts = $("#table-1>tbody").find("input:checkbox:checked");
		
		var arr = [];
		
		jQuery.each(ipts, function(){
			arr.push($(this).val());
		});

		if(arr.length > 0)
		{
			$("#result_div").html("<?=loader_html()?>");
			$.post('<?=site_url('gestao/deliberacao_conselho/divulgar')?>',
			{
				"arr[]" : arr
			},
			function(data)
			{
				filtrar();
			});
		}
		else
		{
			alert("Nenhuma Deliberação foi selecionada.");
		}
	}
}

function novo()
{
    location.href='<?=site_url("gestao/deliberacao_conselho/cadastro/")?>';
}

$(function(){
	if($("#dt_ini").val() == "")
	{
		$("#dt_ini_dt_fim_shortcut").val("currentYear");
		$("#dt_ini_dt_fim_shortcut").change();
	}

	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config = Array();

if(gerencia_in(array('SG')))
{
	$config['button'][] = array('Nova Deliberação', 'novo()');
	$config['button'][] = array('Divulgar', 'divulgar()');
}

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
	echo filter_integer_ano('nr_ano', 'nr_deliberacao_conselho', 'Ano/Número:');
	echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Deliberação:');
	echo filter_text('ds_deliberacao_conselho', 'Descrição:', '', 'style="width:300px;"');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>