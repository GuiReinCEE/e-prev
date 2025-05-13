<?php
set_title('Relacionamento - Contato Anexos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/relacionamento_empresa"); ?>';
    }
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/cadastro/". $cd_empresa); ?>';
	}
	
	function ir_contato()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/contato/".intval($cd_empresa)); ?>';
	}
	
	function ir_pessoas()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/pessoas/".intval($cd_empresa)); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/anexo/". $cd_empresa); ?>';
	}
	
	function ir_agenda()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/agenda/".intval($cd_empresa)); ?>';
	}
	
	function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
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
	
	function listar_anexos()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('ecrm/relacionamento_empresa/listar_contato_anexo'); ?>',
		{
			cd_empresa_contato : $('#cd_empresa_contato').val()
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
		
	function excluir_anexo(cd_empresa_contato_anexo)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir o anexo?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post("<?php echo site_url('ecrm/relacionamento_empresa/excluir_contato_anexo'); ?>", 
			{
				cd_empresa_contato_anexo : cd_empresa_contato_anexo
			}, 
			function(data)
			{ 
				listar_anexos();
			});
		}
	}
	
	$(function(){
		listar_anexos();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_empresa', 'Empresa', FALSE, 'ir_cadastro();');
$abas[] = array('aba_contato', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_pessoa', 'Contato Anexo', TRUE, 'location.reload();');
$abas[] = array('aba_agenda', 'Agenda', FALSE, 'ir_agenda();');
$abas[] = array('aba_pessoa', 'Pessoa', FALSE, 'ir_pessoas();');
$abas[] = array('aba_pessoa', 'Anexo', FALSE, 'ir_anexo();');

echo aba_start($abas);
	echo form_open('ecrm/relacionamento_empresa/salvar_contato_anexo');
		echo form_start_box("default_box", "Anexo");
			echo form_default_hidden("cd_empresa", "", $cd_empresa);
			echo form_default_hidden("cd_empresa_contato", "", $cd_empresa_contato);
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'relacionamento_empresa', 'validaArq');
			echo form_default_row('', '', '<i>Selecione o arquivo e depois clique no botão [Anexar arquivo]</i>');
			echo form_default_row('', '', '<i>Tamanho máximo por arquivo anexo é de 10 Mb</i>');
		echo form_end_box("default_box");
	echo form_close();
	echo'<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>