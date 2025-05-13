<?php
set_title('Treinamento');
$this->load->view('header');
?>
<script type="text/javascript">
function novo()
{
    location.href='<?php echo base_url().index_page(); ?>/cadastro/treinamento_colaborador/cadastro';
}

function pdf()
{
    filter_bar_form.method = "post";
    filter_bar_form.action = '<?php echo base_url() . index_page(); ?>/cadastro/treinamento_colaborador/pdf';
    filter_bar_form.target = "_blank";
    filter_bar_form.submit();
}

function filtrar()
{
	load();
}

function load()
{
	$("#result_div").html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url("cadastro/treinamento_colaborador/listar"); ?>/',
    {
		numero                          : $('#numero').val(),
		ano                             : $('#ano').val(),
        nome                            : $('#nome').val(),
        dt_inicio_ini                   : $('#dt_inicio_ini').val(),
        dt_inicio_fim                   : $('#dt_inicio_fim').val(),
        dt_final_ini                    : $('#dt_final_ini').val(),
        dt_final_fim                    : $('#dt_final_fim').val(),
        cd_treinamento_colaborador_tipo : $('#cd_treinamento_colaborador_tipo').val(),
        cd_empresa                      : $('#cd_empresa').val(),
        cd_registro_empregado           : $('#cd_registro_empregado').val(),
        seq_dependencia                 : $('#seq_dependencia').val(),
        nome_colaborador                : $('#nome_colaborador').val(),
        fl_avaliacoes_preenchidos       : $('#fl_avaliacoes_preenchidos').val(),
        fl_cadastro_rh       			: $('#fl_cadastro_rh').val(),
        fl_bem_estar                    : $('#fl_bem_estar').val(),
        fl_certificado                  : $('#fl_certificado').val()

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
		'CaseInsensitiveString',
		'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'DateBR',
		'DateBR',
        'CaseInsensitiveString',
        'NumberFloatBR',
        'Number',
        null,
        null,
        null,
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
	ob_resul.sort(6, true);
}

	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', true, 'location.reload();' );

echo aba_start( $abas );

    $config['button'][]=array('Novo Treinamento', 'novo()');
    $config['button'][]=array('Gerar PDF', 'pdf()');

    echo form_list_command_bar($config);

    echo form_start_box_filter();
        echo filter_integer('numero', 'Número: ');
        echo filter_integer('ano', 'Ano: ', date('Y'));
        echo filter_text('nome', 'Nome do Evento: ');
        echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt Inicio:');
        echo filter_date_interval('dt_final_ini', 'dt_final_fim', 'Dt Final:');
        echo filter_dropdown('cd_treinamento_colaborador_tipo', 'Tipo: ', $arr_tipo);
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_colaborador'), "Colaborador (RE):", '', TRUE, TRUE );
		echo filter_text('nome_colaborador', "Colaborador (Nome):", '', "style='width:100%;'" );
		echo filter_dropdown('fl_avaliacoes_preenchidos', 'Avaliações Preenchidos:', $drop);
		echo filter_dropdown('fl_cadastro_rh', 'Subsidio FCEEE:', $drop);
        echo filter_dropdown('fl_bem_estar','Bem-Estar', $drop);
        echo filter_dropdown('fl_certificado','Certificado Indexado', $drop);
    echo form_end_box_filter();

	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	echo br(5);	
echo aba_end();

$this->load->view('footer');
?>