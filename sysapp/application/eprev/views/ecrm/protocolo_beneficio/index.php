<?php
set_title('eCRM - Protocolo Benefício');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/protocolo_beneficio/listar'
		,{
			nr_protocolo: $('#nr_protocolo').val()
			,nr_ano: $('#nr_ano').val()
			,dt_inclusao_inicio: $('#dt_inclusao_inicio').val()
			,dt_inclusao_fim: $('#dt_inclusao_fim').val()
			,cd_empresa: $('#cd_empresa').val(),cd_registro_empregado: $('#cd_registro_empregado').val(),seq_dependencia: $('#seq_dependencia').val()
			,nome: $('#nome').val()
			,cd_protocolo_beneficio_assunto: $('#cd_protocolo_beneficio_assunto').val()
			,cd_protocolo_beneficio_forma_envio: $('#cd_protocolo_beneficio_forma_envio').val()
			,cd_usuario_inclusao: $('#cd_usuario_inclusao').val()
		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'CaseInsensitiveString','RE','CaseInsensitiveString','DateBR','CaseInsensitiveString','CaseInsensitiveString','CaseInsensitiveString'
	]);
	ob_resul.onsort = function()
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

function novo()
{
	location.href='<?php echo site_url("ecrm/protocolo_beneficio/detalhe/0"); ?>';
}

function excluir(id)
{
	if(confirm('Excluir?'))
	{
		url = '<?php echo site_url("ecrm/protocolo_beneficio/excluir/") ?>/'+id;
		$.post( url, {}, load );
	}
}

function mala()
{
	if( confirm('Gerar mala direta?') )
	{
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/protocolo_beneficio/mala_direta_salvar'
		,{
			nr_protocolo: $('#nr_protocolo').val()
			,nr_ano: $('#nr_ano').val()
			,dt_inclusao_inicio: $('#dt_inclusao_inicio').val()
			,dt_inclusao_fim: $('#dt_inclusao_fim').val()
			,cd_empresa: $('#cd_empresa').val(),cd_registro_empregado: $('#cd_registro_empregado').val(),seq_dependencia: $('#seq_dependencia').val()
			,nome: $('#nome').val()
			,cd_protocolo_beneficio_assunto: $('#cd_protocolo_beneficio_assunto').val()
			,cd_protocolo_beneficio_forma_envio: $('#cd_protocolo_beneficio_forma_envio').val()
			,cd_usuario_inclusao: $('#cd_usuario_inclusao').val()
		}
		,
		function( data )
		{
			if(data=='true'){ alert('Mala direta gerada com sucesso!\n\nAguarde alguns minutos e acesse o Eletro.'); }
			else{ alert('Problemas ao tentar salvar mala direta, tente novamente em alguns instantes.'); }
		}
	);
	}
}
</script>

<?php
$abas[] = ARRAY('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start( $abas );
$config=array();
if( gerencia_in( array('GB') ) )
{
	$config['button'][] = ARRAY('Novo', 'novo()');
	$config['button'][] = ARRAY('Integra Mala Direta', 'mala()');
}
echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros');
$nr_protocolo_e_ano = "<input type='text' name='nr_ano' id='nr_ano' value='' style='width:70px;' />/<input type='text' name='nr_protocolo' id='nr_protocolo' value='' style='width:70px;' />";
echo form_default_row( "protocolo_row", "Ano / Protocolo:", $nr_protocolo_e_ano ); /*    echo form_default_text('nr_protocolo', 'Protocolo');    //    echo form_default_text('nr_ano', 'Ano');    */
echo form_default_date_interval('dt_inclusao_inicio', 'dt_inclusao_fim', 'Data de inclusão', '01/'.date('m/Y'), date('d/m/Y'));
echo form_default_participante( ARRAY('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), 'Participante', FALSE, TRUE, FALSE );
echo form_default_text('nome', 'Nome');
echo form_default_dropdown_db('cd_protocolo_beneficio_assunto', 'Assunto', ARRAY('projetos.protocolo_beneficio_assunto', 'cd_protocolo_beneficio_assunto', 'ds_protocolo_beneficio_assunto'));
echo form_default_dropdown_db('cd_protocolo_beneficio_forma_envio', 'Forma de envio', ARRAY('projetos.protocolo_beneficio_forma_envio', 'cd_protocolo_beneficio_forma_envio', 'ds_protocolo_beneficio_forma_envio'));
echo form_default_dropdown('cd_usuario_inclusao', 'Usuário', $usuarios_dd);

echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	// filtrar();
</script>

<?php
$this->load->view('footer');
