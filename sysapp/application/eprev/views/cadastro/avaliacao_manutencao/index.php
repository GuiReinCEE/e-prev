<?php
set_title('Avaliação - Configurações para Avaliação ');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('/cadastro/avaliacao_manutencao/listar');?>',
	{
		periodo                      : $('#periodo').val(),
		tipo                         : $('#tipo').val(),
		cd_usuario_avaliado_gerencia : $('#cd_usuario_avaliado_gerencia').val(),
		cd_usuario_avaliado          : $('#cd_usuario_avaliado').val(),
		fl_publicado                 : $('#fl_publicado').val()
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
        'CaseInsensitiveString', 
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'Number',
		'NumberFloat',
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
	ob_resul.sort(0, false);
}


function editar_superior(t)
{
	var obj = $("#" + $(t).parent().get(0).id);

	var parent_linha  = obj.attr('linha');
	var parent_coluna = obj.attr('coluna');
	
	$('#'+parent_linha+'_'+parent_coluna+'-table-1').hide();
	$('#'+parent_linha+'_4-table-1').show();
}

function salvar_superior(cd_avaliacao_capa, t)
{
	var confirmacao = 'Confirma a alteração do Avaliador?\n\n' +
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';

	if(confirm(confirmacao))
	{
		var obj = $("#" + $(t).parent().get(0).id);

		var parent_linha  = obj.attr('linha');
		var parent_coluna = obj.attr('coluna');
		
		var cd_avaliador = $("#cd_avaliador_" + cd_avaliacao_capa).val(); 
		var ds_avaliador = $("#cd_avaliador_" + cd_avaliacao_capa + " option:selected").text(); 
		
		var editar = '<a href="javascript: void(0)" id="cd_avaliador_editar_'+cd_avaliacao_capa+'" onclick="editar_superior($(this));" title="Editar">[editar]</a>';
		
		$("#cd_avaliador_ajax_" + cd_avaliacao_capa).html("<?php echo loader_html("P"); ?>");
		
		$.post('<?php echo site_url("cadastro/avaliacao_manutencao/editar_superior")?>',
		{
			cd_avaliacao_capa : cd_avaliacao_capa,
			cd_avaliador      : cd_avaliador
		},
		function(data)
		{
			console.log(data);
			$("#cd_avaliador_ajax_" + cd_avaliacao_capa).empty();
			
			$('#'+parent_linha+'_3-table-1').html(ds_avaliador + ' ' + editar);
			$('#'+parent_linha+'_'+parent_coluna+'-table-1').hide();
			$('#'+parent_linha+'_3-table-1').show();
		});
	}
}

function cancelar_superior(t)
{
	var obj = $("#" + $(t).parent().get(0).id);

	var parent_linha  = obj.attr('linha');
	var parent_coluna = obj.attr('coluna');
	
	$('#'+parent_linha+'_'+parent_coluna+'-table-1').hide();
	$('#'+parent_linha+'_3-table-1').show();
}


function excluir(cd_avaliacao_capa)
{
    if(confirm('Você tem certeza que deseja excluir essa avaliação?'))
    {
        location.href='<?php echo site_url("cadastro/avaliacao_manutencao/excluir"); ?>/'+cd_avaliacao_capa;
    }
}

function reabrir(cd_avaliacao_capa)
{
    if(confirm('Você tem certeza que deseja reabrir a avaliação?'))
	{
        location.href='<?php echo site_url("cadastro/avaliacao_manutencao/reabrir"); ?>/'+cd_avaliacao_capa;
    }
}

function encerrar(cd_avaliacao_capa)
{
    if(confirm('Você tem certeza que deseja encerrar essa avaliação?'))
    {
       location.href='<?php echo site_url("cadastro/avaliacao_manutencao/encerrar"); ?>/'+cd_avaliacao_capa;
    }
}

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$tipo[] = array('text'=>'Horizontal', 'value'=>'H');
$tipo[] = array('text'=>'Vertical', 'value'=>'V');

$publicado[] = array('text'=>'Sim', 'value'=>'S');
$publicado[] = array('text'=>'Não', 'value'=>'N');

echo aba_start( $abas );
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_text('periodo', 'Período:', date('Y'));
		echo filter_usuario_ajax('cd_usuario_avaliado', '', '', "Avaliado: ", "Gerência  do Avaliado: ");
		echo filter_dropdown('tipo', 'Tipo:', $tipo);
		echo filter_dropdown('fl_publicado', 'Finalizada:', $publicado);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>