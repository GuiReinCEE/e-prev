<?php
set_title('Acompanhamento de Produtos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/cadastro/".intval($row['cd_produto_financeiro'])); ?>';
    }
	
	function ir_etapas()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/etapas/".intval($row['cd_produto_financeiro'])); ?>';
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
	
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo base_url() . index_page(); ?>/atividade/produto_financeiro/listar_anexos',
		{
			cd_produto_financeiro : $('#cd_produto_financeiro').val()
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
			'CaseInsensitiveString', 
            'DateTimeBR', 
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
        ob_resul.sort(1, false);
    }
	
	function excluir(cd_produto_financeiro_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("atividade/produto_financeiro/excluir_anexo/".$row['cd_produto_financeiro']); ?>' + "/" + cd_produto_financeiro_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
$abas[] = array('aba_lista', 'Etapas', FALSE, 'ir_etapas();');
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');

if(((intval($row['cd_produto_financeiro']) > 0) AND (($this->session->userdata('codigo') == $row['cd_usuario_inclusao']) OR ($this->session->userdata('codigo') == $row['cd_usuario_responsavel']) 
	OR ($this->session->userdata('codigo') == $row['cd_usuario_revisor'])  OR (($this->session->userdata('divisao') == 'GIN') AND ($this->session->userdata('tipo') == 'G')))) OR (intval($row['cd_produto_financeiro']) == 0))
{
	$bool = true;
}
else
{
	$bool = false;
}

echo aba_start($abas);
	echo form_open('atividade/produto_financeiro/salvar_anexo');
		echo form_default_hidden("cd_produto_financeiro", "", $row['cd_produto_financeiro']);
		if($bool)
		{
			echo form_start_box("default_box", "Anexo");
				
				echo form_default_upload_iframe('arquivo', 'produto_financeiro', 'Arquivo :*', '', 'produto_financeiro', false, '$("form").submit();');
			echo form_end_box("default_box");
		}
		/*
		echo form_command_bar_detail_start();
			 echo ($bool ?  button_save("Salvar") : '');
		echo form_command_bar_detail_end();
		*/
	echo form_close();
echo'<div id="result_div"></div>';

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>