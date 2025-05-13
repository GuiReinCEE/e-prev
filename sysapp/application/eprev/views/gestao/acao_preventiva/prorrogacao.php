<?php
set_title('Ação Preventiva');
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit(Array('dt_prorrogacao', 'motivo'));
?>
    function ir_lista()
	{
		location.href='<?php echo site_url("gestao/acao_preventiva"); ?>';
	}

    function ir_acao(nr_ano, nr_ap)
	{
		location.href='<?php echo site_url("gestao/acao_preventiva/cadastro/"); ?>' + "/" + nr_ano + "/" + nr_ap;
	}

    function ir_acompanhamento(nr_ano, nr_ap)
	{
		location.href='<?php echo site_url("gestao/acao_preventiva/acompanhamento/"); ?>' + "/" + nr_ano + "/" + nr_ap;
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('gestao/acao_preventiva/anexo/'.$nr_ano.'/'.$nr_ap); ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateBR",
			"CaseInsensitiveString"
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

    function gerar_pdf()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?php echo site_url("/gestao/acao_preventiva/gerar_pdf"); ?>';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}
	
	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Ação Preventiva', FALSE, "ir_acao('".$nr_ano."', '".$nr_ap."');");
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, "ir_acompanhamento('".$nr_ano."', '".$nr_ap."');");
$abas[] = array('aba_lista', 'Prorrogação', TRUE, "location.reload();");
$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

$body=array();
$head = array(
	'Dt. Prorrogação',
	'Motivo'
);

foreach($ar_prorrogacao as $item )
{
	$body[] = array(
		$item["dt_prorrogacao"],
		array($item["motivo"],"text-align:justify;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/acao_preventiva/salvar_prorrogacao', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('nr_ap', "nr_ap", $nr_ap, "style='width:100%;border: 0px;' readonly");
            echo form_default_hidden('nr_ano', "nr_ano", $nr_ano, "style='width:100%;border: 0px;' readonly");
            echo form_default_hidden('cd_preventiva_prorrogacao', "Código:", $cd_preventiva_acompanhamento, "style='width:100%;border: 0px;' readonly" );
            echo form_default_text('numero_cad_ap', "Número:", $nr_ano.'/'.$nr_ap, "style='font-weight: bold; width:100%;border: 0px;' readonly" );
            echo form_default_date('dt_prorrogacao', "Dt Prorrogação:");
            echo form_default_textarea('motivo', "Motivo:*", '', "style='width:500px;'");
        echo form_end_box("default_box");

        echo form_command_bar_detail_start();
            if(count($implementacao) > 0 AND count($cancelamento) > 0)
            {
                echo button_save("Salvar");
            }
            echo button_save("Imprimir","gerar_pdf()","botao_disabled");
        echo form_command_bar_detail_end();
    echo form_close();
    
    echo $grid->render();

	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>