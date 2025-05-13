<?php
set_title('Acompanhamento de Projetos - WBS');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('cd_acomp'));
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	

	function ir_lista_reunicao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}
	
	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('atividade/acompanhamento/listar_wbs'); ?>',
		{
			cd_acomp : $('#cd_acomp').val()
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
			'DateTimeBR', 
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
        ob_resul.sort(0, true);
    }
	
	function excluir_anexo(cd_acompanhamento_wbs)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o anexo?\n\n"))
		{
			$.post( '<?php echo site_url('atividade/acompanhamento/excluir_wbs'); ?>',
			{
				cd_acompanhamento_wbs : cd_acompanhamento_wbs
			},
			function(data)
			{
				load();
			});
		}
	}
	
	$(function(){
		load();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_lista_reunicao();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_reuniao', 'WBS', TRUE, 'location.reload();');
$abas[] = array('aba_previsao', 'Previsão', FALSE, 'ir_previsao();');	
	
$status = "Projeto em andamento";
$cor_status = "blue";

if (trim($row['dt_encerramento']) != '') 
{
	$status = 'Projeto encerrado em: '. $row['dt_encerramento'];
	$cor_status = "red";
}	

if (trim($row['dt_cancelamento']) != '') 
{
	$status = 'Projeto cancelado em: '. $row['dt_cancelamento'];
	$cor_status = "red";
}
	
echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_wbs');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );
		echo form_end_box("default_box");
		if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
		{
			echo form_start_box( "default_wbs_box", "WBS" );
				echo form_default_upload_iframe('arquivo', 'acompanhamento_wbs', 'Arquivo :*', '', 'acompanhamento_wbs', false, '$("form").submit();');
			echo form_end_box("default_wbs_box");
		}
		echo form_command_bar_detail_start();
		echo form_command_bar_detail_end();	
	echo form_close();		
	echo br();
	echo'<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>