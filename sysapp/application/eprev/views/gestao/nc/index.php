<?php
set_title('Não Conformidades');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
		$('#result_div').html("<?= loader_html() ?>");

        $.post('<?= site_url('/gestao/nc/listar') ?>',
		$('#filter_bar_form').serialize(),
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
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateBR',
            'DateBR',
            'DateBR',
            'DateBR',
            'DateBR',
            'DateTimeBR'

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

    function novo()
    {
        location.href='<?= site_url("gestao/nc/cadastro") ?>';
    }

    function diretoria_change( ob )
    {
        $.post('<?= site_url("gestao/nc/gerencia_dropdown_ajax") ?>',
		{
			diretoria: ob.value
		},
		function(data)
		{
			$('#gerencia_div').html(data);
		});
    }
	
	$(function(){
		filtrar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova não conformidade', 'novo()');

echo aba_start($abas);
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('diretoria', 'Diretoria :', $diretoria_dd, '', "onchange='diretoria_change(this);'");
		echo filter_dropdown('gerencia', 'Gerência :', $gerencia_dd);
		//echo filter_dropdown('processo', 'Processo :', $processo_dd);
		echo filter_processo('processo', 'Processo :');
		echo filter_dropdown('cd_nao_conformidade_origem_evento', 'Origem Evento :', $ar_origem_evento);
		echo filter_dropdown('status', 'Encerrado :', $status_dd);
		echo filter_dropdown('implementada', 'Implementada :', $implementada_dd);
		echo filter_dropdown('prorrogada', 'Prorrogada :', $prorrogada_dd);
		echo filter_date_interval('limite_apre_ac_inicio', 'limite_apre_ac_fim', 'Dt. limite para apresentação da AC :');
		echo filter_date_interval('proposta_inicio', 'proposta_fim', 'Dt. da proposta/prorrogação :');
		echo filter_date_interval('dt_prop_verif_ini', 'dt_prop_verif_fim', 'Dt. validação eficácia :');
		echo filter_date_interval('dt_encerramento_ini', 'dt_encerramento_fim', 'Dt. encerramento :');

        echo filter_date_interval('dt_cadastro_ini', 'dt_cadastro_fim', 'Dt. Cadastro :');
        echo filter_date_interval('dt_implementacao_ini', 'dt_implementacao_fim', 'Dt. Implementação :');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');