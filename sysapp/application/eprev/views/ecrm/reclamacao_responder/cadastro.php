<?php
set_title('Reclamação Análise - Responder');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/reclamacao_responder"); ?>';
    }
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			null,
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
			"CaseInsensitiveString",
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateTimeBR",
			"DateTimeBR"
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
		ob_resul.sort(4, true);
	}

	function enviar()
	{
		if(confirm('Deseja enviar?'))
		{
			location.href='<?php echo site_url("ecrm/reclamacao_analise/enviar/".intval($row['cd_reclamacao_analise'])); ?>';
		}
	}	
	
	function listar_reclamacao()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('ecrm/reclamacao_responder/listar_reclamacao');?>',
		{
			cd_reclamacao_analise : $('#cd_reclamacao_analise').val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	
	function retorno()
	{
		if(confirm('Deseja Confirmar o Parecer da Gerência?'))
		{
			location.href='<?php echo site_url("ecrm/reclamacao_responder/retorno/".intval($row['cd_reclamacao_analise'])); ?>';
		}
	}
	
	$(function(){
		listar_reclamacao();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
    echo form_start_box( "default_box", "Cadastro" );
        echo form_default_hidden('cd_reclamacao_analise', '', $row);
        echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']);
        echo form_default_row('row_cd_reclamacao_analise_classifica', 'Classificação :', $row['ds_reclamacao_analise_classifica']);
        echo form_default_row('row_cd_usuario_responsavel', 'Responsável :', $row['responsavel']);
        echo form_default_row('row_cd_usuario_substituto', 'Substituto :', $row['substituto']);
        echo form_default_row('row_dt_limite', 'Dt Limite :', '<span class="label '.(trim($row['dt_prorrogacao']) == "" ? "label-important" : "").'">'.$row['dt_limite'].'</span>');
        echo form_default_row('row_dt_prorrogacao', 'Dt Prorrogação :', '<span class="label label-important">'.$row['dt_prorrogacao'].'</span>');
        echo form_default_row('row_observacao', 'Observação :', nl2br($row['observacao']));
        echo form_default_row('row_qt_itens', 'Qt Itens :', intval($row['qt_itens']));
        echo form_default_row('row_qt_itens_respondidos', 'Qt Itens Respondidos :', intval($row['qt_itens_respondidos']));
      echo form_end_box("default_box");
    echo form_command_bar_detail_start();     			
        if((trim($row['dt_retorno']) == '') AND (intval($row['qt_itens']) == intval($row['qt_itens_respondidos'])))
        {
            echo button_save("Parecer da Gerência", 'retorno()', 'botao_vermelho');
        }
    echo form_command_bar_detail_end();
	echo '<div id="result_div"></div>';
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>