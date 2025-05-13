<?php
set_title('Atividade - Anexos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/minhas"); ?>';
    }
	
	function ir_solicitacao()
    {
    	location.href='<?php echo site_url('atividade/atividade_solicitacao/index/'.$cd_gerencia.'/'.$cd_atividade);?>';
    }
	
	function ir_atendimento()
    {
    	location.href='<?php echo site_url('atividade/atividade_atendimento/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url('atividade/atividade_historico/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }

    function ir_script()
    {
        location.href='<?php echo site_url('atividade/atividade_script/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
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

        $.post( '<?php echo site_url('atividade/atividade_anexo/listar'); ?>',
		{
			cd_atividade : $('#cd_atividade').val()
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
	
	function excluir(cd_atividade_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("atividade/atividade_anexo/excluir/".$cd_atividade."/".$cd_gerencia); ?>' + "/" + cd_atividade_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Solicitação', FALSE, 'ir_solicitacao()');
$abas[] = array('aba_lista', 'Atendimento', FALSE, 'ir_atendimento();');
if($this->session->userdata('divisao') == 'GS')
{
    $abas[] = array('aba_lista', 'Script', FALSE, 'ir_script();');
}
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

echo aba_start($abas);
	echo form_open('atividade/atividade_anexo/salvar');
		echo form_start_box("default_box", "Anexo");
			echo form_default_hidden("cd_gerencia", "", $cd_gerencia);
			echo form_default_text("cd_atividade", "Atividade:", $cd_atividade,'style="font-weight:bold; width: 500px; border: 0px;" readonly');
			echo form_default_row('', '', '');
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'atividade_anexo', 'validaArq');
			echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
		echo form_end_box("default_box");
	echo form_close();
echo'<div id="result_div"></div>';

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>