<?php
set_title('Relatórios de Auditoria Contábil - Anexos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
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
    
    function ir_acompanhamento()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/acompanhamento/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
	
	function valida_arquivo(form)
    {

		if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
	
	function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
	}
	
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('atividade/relatorio_auditoria_contabil/listar_anexo'); ?>',
		{
			cd_relatorio_auditoria_contabil : $('#cd_relatorio_auditoria_contabil').val()
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
        ob_resul.sort(1, true);
    }
	
	function excluir(cd_relatorio_auditoria_contabil_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/excluir_anexo/".$row['cd_relatorio_auditoria_contabil']); ?>' + "/" + cd_relatorio_auditoria_contabil_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Itens', FALSE, 'ir_itens();');
$abas[] = array('aba_anexo', 'Anexos', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

echo aba_start($abas);
	echo form_open('atividade/relatorio_auditoria_contabil/salvar_anexo');
		echo form_start_box("default_relatorio_box", "Relatório");
            echo form_default_hidden("cd_relatorio_auditoria_contabil", "", $row['cd_relatorio_auditoria_contabil']);
            echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']); 
            echo form_default_textarea('ds_relatorio_auditoria_contabil', 'Descrição :', $row, 'style="height:100px;"');
            echo form_default_upload_iframe('arquivo', 'relatorio_auditoria_contabil', 'Arquivo :', array($row['arquivo'], $row['arquivo_nome']), 'relatorio_auditoria_contabil', FALSE);
		echo form_end_box("default_relatorio_box");
        if(trim($row['dt_encaminhamento']) == '')
        {
            echo form_start_box("default_box", "Anexo");
                echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'relatorio_auditoria_contabil', 'validaArq');
                echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
            echo form_end_box("default_box");
        }
	echo form_close();
    echo'<div id="result_div"></div>';
    echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>