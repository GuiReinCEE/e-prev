<?php
set_title('Informativo do Cenário Legal');
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal"); ?>';
	}
	
	function ir_conteudo()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/conteudo/".$cd_edicao); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/conteudo_cadastro/".$cd_edicao."/".$cd_cenario); ?>';
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

        $.post( '<?php echo site_url('ecrm/informativo_cenario_legal/listar_anexo'); ?>',
		{
			cd_edicao  : '<?php echo $cd_edicao; ?>',
			cd_cenario : '<?php echo $cd_cenario; ?>'
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
	
	function excluir(cd_cenario_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("ecrm/informativo_cenario_legal/excluir_anexo/".$cd_edicao."/".$cd_cenario); ?>' + "/" + cd_cenario_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', $tit_capa , FALSE, 'ir_conteudo();');
$abas[] = array('aba_lista', 'Conteúdo', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Anexos' , TRUE, 'location.reload();');

echo aba_start($abas);
	if(trim($dt_envio_email) == '')
	{
		echo form_open('ecrm/informativo_cenario_legal/salvar_anexo');
			echo form_start_box("default_box", "Anexo");
				echo form_default_hidden("cd_edicao", "", $cd_edicao);
				echo form_default_hidden("cd_cenario", "", $cd_cenario);
                echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'cenario', 'validaArq');
				echo form_default_row('', '', '<i>Selecione o arquivo e depois clique no botão [Anexar arquivo]</i>');
				echo form_default_row('', '', '<i>Tamanho máximo por arquivo anexo é de 10 Mb</i>');
			echo form_end_box("default_box");
		echo form_close();
	}
echo'<div id="result_div"></div>';

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>