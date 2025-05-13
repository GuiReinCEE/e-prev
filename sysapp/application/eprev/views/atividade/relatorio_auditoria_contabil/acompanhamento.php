<?php
set_title('Relatórios de Auditoria Contábil - Acompanhamento');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array('acompanhamento'));
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/cadastro/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
	
    function ir_itens()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/itens/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
	
    function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/anexo/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'CaseInsensitiveString',
			'CaseInsensitiveString',
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
        ob_resul.sort(2, true);
    }
	
	$(function(){
		configure_result_table();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Itens', FALSE, 'ir_itens();');
$abas[] = array('aba_anexo', 'Anexos', FALSE, 'ir_anexo();');
$abas[] = array('aba_anexo', 'Acompanhamento', TRUE, 'location.reload();');

$body = array();
$head = array( 
	'Acompanhamento',
	'Usuário',
    'Dt Inclusão'
);

foreach( $collection as $item )
{	
    $body[] = array(
		array($item['acompanhamento'], "text-align:justify;"),
		array($item['nome'], "text-align:left;"),
        $item['dt_inclusao']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('atividade/relatorio_auditoria_contabil/salvar_acompanhamento');
		echo form_start_box("default_relatorio_box", "Relatório");
            echo form_default_hidden("cd_relatorio_auditoria_contabil", "", $row['cd_relatorio_auditoria_contabil']);
            echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']); 
            echo form_default_textarea('ds_relatorio_auditoria_contabil', 'Descrição :', $row, 'style="height:100px;"');
            echo form_default_upload_iframe('arquivo', 'relatorio_auditoria_contabil', 'Arquivo :', array($row['arquivo'], $row['arquivo_nome']), 'relatorio_auditoria_contabil', FALSE);
		echo form_end_box("default_relatorio_box");
        echo form_start_box("default_box", "Acompanhamento");
            echo form_default_textarea('acompanhamento', 'Acompanhamento :');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
    echo $grid->render();
    echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>