<?php
set_title('Eventos Institucionais - Lista');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function setIdentificacao(tp_inscrito,cd_inscricao)
{
	$("#lb_tp_inscrito_" + cd_inscricao).html("<?php echo loader_html("P"); ?>");
	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/evento_institucional_inscricao/setIdentificacao',
	{
		tp_inscrito: tp_inscrito,
		cd_inscricao: cd_inscricao
	},
	function(data)
	{
		$("#lb_tp_inscrito_" + cd_inscricao).html($("#tp_inscrito_" + cd_inscricao + " option:selected").text());
	});
}

function setPresente(fl_presente,cd_inscricao)
{
	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/evento_institucional_inscricao/setPresente',{
		fl_presente: fl_presente,
		cd_inscricao: cd_inscricao
	});
}

function load()
{
	if(($('#cd_eventos_institucionais').val() != "") || ($('#cd_empresa').val() != "" && $('#cd_registro_empregado').val() != "" && $('#seq_dependencia').val() != "") || ($('#nome').val() != ""))
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/evento_institucional_inscricao/listar',
		{
			cd_eventos_institucionais: $('#cd_eventos_institucionais').val(),
			//fl_desclassificado: $('#fl_desclassificado').val(),
			//fl_selecionado: $('#fl_selecionado').val(),
			//foto: $('#foto').val(),
			inscricao_inicio: $('#inscricao_inicio').val(),
			inscricao_fim: $('#inscricao_fim').val(),
			tipo: $('#tipo').val(),
			fl_presente : $('#fl_presente').val(),
			tp_inscrito : $('#tp_inscrito').val(),
			cd_empresa : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia : $('#seq_dependencia').val(),
			nome : $('#nome').val()
			
			
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	else
	{
		alert("Informe o Evento ou RE ou o Nome")
		$('#cd_eventos_institucionais').focus();
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',
		'CaseInsensitiveString',
		'RE',
		'CaseInsensitiveString',
		null,
		null,
		'CaseInsensitiveString',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
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
	ob_resul.sort(0, true);
}

	function emailCertificado(cd_eventos_institucionais_inscricao)
	{
		var aviso = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja ENVIAR o email do certificado?\n\n\nSIM clique [Ok]\n\nNÃO clique [Cancelar]\n\n";
		
		if(confirm(aviso))
		{
			location.href='<?php echo site_url("ecrm/ri_evento_institucional/emailCertificadoEventoIndividual"); ?>' + "/" + cd_eventos_institucionais_inscricao;
		}
	}


function novo()
{
    location.href='<?php echo site_url("ecrm/evento_institucional_inscricao/detalhe"); ?>';
}

function confirmar_site()
{
	location.href='<?php echo site_url("ecrm/evento_institucional_inscricao/confirmar_site"); ?>';	
}
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Nova Inscrição via Site', 'confirmar_site()');
$config['button'][]=array('Nova Inscrição', 'novo()');
$ar_tipo = Array(Array('text' => 'Inscrito', 'value' => 'I'),Array('text' => 'Acompanhante', 'value' => 'A')) ;

$ar_tipo_2 = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
echo aba_start( $abas );
	echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('cd_eventos_institucionais', 'Evento: ', $ar_evento);
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", Array(), TRUE, FALSE );
		echo filter_text('nome', 'Nome:','','style="width: 350px;"');
		echo filter_dropdown('tipo', 'Tipo: ', $ar_tipo);
		echo filter_dropdown('fl_presente', 'Presente: ', $ar_tipo_2);
		echo filter_dropdown('tp_inscrito', 'Identificação: ', $ar_tp_inscrito);
		echo filter_date_interval('inscricao_inicio', 'inscricao_fim', 'Data de Inscrição: ');
	echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(); 

$this->load->view('footer');
?>