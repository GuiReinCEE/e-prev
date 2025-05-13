<?php
set_title('Atualiza Intranet');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function load()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('ecrm/intranet/listar'); ?>',
	{
		cd_gerencia : '<?php echo $cd_gerencia; ?>',
		cd_intranet : $("#cd_intranet_filtro").val()
	},
	function(data)
	{
		$('#result_div').html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',
		'CaseInsensitiveString',
		null,
		null,
		null,
		'Number',
		null,
		'DateTimeBR',
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
	ob_resul.sort(5, false);
}

function novo()
{
	location.href='<?php echo site_url('ecrm/intranet/cadastro/'.$cd_gerencia); ?>';
}

$(function(){
	filtrar();
})

function editar_ordem(cd_intranet)
{
	$("#valor_ordem_" + cd_intranet).hide(); 
	$("#editar_ordem_" + cd_intranet).hide(); 

	$("#salvar_ordem_" + cd_intranet).show(); 
	$("#nr_ordem_" + cd_intranet).show(); 
	$("#nr_ordem_" + cd_intranet).focus();	
}

function salvar_ordem(cd_intranet)
{
	$("#ajax_ordem_valor_"+cd_intranet).html("<?= loader_html("P") ?>");
	
	$.post( '<?php echo site_url("ecrm/intranet/editar_ordem_principal")?>',
	{
		cd_intranet : cd_intranet,
		cd_gerencia : '<?php echo $cd_gerencia; ?>',
        nr_ordem    : $("#nr_ordem_" + cd_intranet).val()	
	},
	function(data)
	{
		$("#ajax_ordem_valor_" + cd_intranet).empty();
			
		$("#nr_ordem_" + cd_intranet).hide();
		
		$("#salvar_ordem_" + cd_intranet).hide(); 
		
        $("#valor_ordem_" + cd_intranet).html($("#nr_ordem_" + cd_intranet).val()); 
		$("#valor_ordem_" + cd_intranet).show(); 

		$("#editar_ordem_" + cd_intranet).show(); 
	});
	
}

function setItemPai(cd_intranet,cd_intranet_pai)
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('ecrm/intranet/setItemPai'); ?>',
	{
		cd_intranet     : cd_intranet,
		cd_intranet_pai : cd_intranet_pai
	},
	function(data)
	{
		filtrar();
	});	
}

function verSubitem(cd_intranet)
{
	$("#cd_intranet_filtro").val(cd_intranet);
	filtrar();
}

function voltarPai()
{
	$("#cd_intranet_filtro").val($("#cd_intranet_voltar").val());
	filtrar();	
}

</script>
<input type="hidden" name="cd_intranet_filtro" id="cd_intranet_filtro" value="0">
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Voltar para o Item Superior', 'voltarPai()', 'btVoltarSubItemIntranet', 'botao_disabled');
$config['button'][] = array('Novo Item', 'novo()');

$config['filter'] = false;

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end(); 

$this->load->view('footer');
?>