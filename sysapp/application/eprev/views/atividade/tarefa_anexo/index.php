<?php
set_title('Tarefa - Anexos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/tarefa"); ?>';
    }
	
	function ir_atividade()
	{
		location.href='<?php echo base_url(). "sysapp/application/migre/cad_atividade_atend.php?n=".$row['cd_atividade']."&aa="; ?>';
	}
	
	function ir_definicao()
	{
		location.href='<?php echo site_url("atividade/tarefa/cadastro/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_execucao()
	{
		location.href='<?php echo site_url("atividade/tarefa_execucao/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_checklist()
	{
		location.href='<?php echo site_url("atividade/tarefa_checklist/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_historico()
	{
		location.href='<?php echo site_url("atividade/tarefa_historico/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
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

        $.post( '<?php echo site_url('atividade/tarefa_anexo/listar'); ?>',
		{
			codigo    : $('#codigo').val(),
			cd_tarefa : $('#cd_tarefa').val()
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
			location.href='<?php echo site_url("atividade/tarefa_anexo/excluir/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>' + "/" + cd_atividade_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Atividade', FALSE, 'ir_atividade()');
$abas[] = array('aba_lista', 'Definição', FALSE, 'ir_definicao();');
$abas[] = array('aba_lista', 'Execução', FALSE, 'ir_execucao();');
if(trim($row['fl_checklist']) == 'S')
{
	$abas[] = array('aba_lista', 'Checklist', FALSE, 'ir_checklist();');
}
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

echo aba_start($abas);
	echo form_open('atividade/tarefa_anexo/salvar');
		echo form_start_box( "default_box", "Tarefa" );
			echo form_default_text("atividade_os", "Atividade/Tarefa:", $row['cd_atividade'].' / '.$row['cd_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("status", "Status:", $row['status_atual'], 'style="width: 500px; border: 0px; font-weight:bold; color:'.trim($row['status_cor']).'" readonly');
			echo form_default_text("nome_tarefa", "Tipo da tarefa:", $row['nome_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("analista", "Analista:", $row['analista'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("programador", "Programador:", $row['programador'], 'style="width: 500px; border: 0px;" readonly');
		echo form_end_box("default_box");

		echo form_start_box("default_box", "Anexo");
			echo form_default_hidden("cd_atividade", "", $row['cd_atividade']);
			echo form_default_hidden("cd_tarefa", "", $row['cd_tarefa']);
			echo form_default_hidden("codigo", "", $row['codigo']);
			
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'tarefa_anexo', 'validaArq');
			
			echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
		echo form_end_box("default_box");
	echo form_close();
echo'<div id="result_div"></div>';

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>