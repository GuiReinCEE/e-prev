<?php
set_title('Tarefa - Histórico');
$this->load->view('header');
?>
<script>
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
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("atividade/tarefa_anexo/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}

	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('atividade/tarefa_historico/listar'); ?>',
		{
			cd_atividade : <?php echo $row['cd_atividade']; ?>,
			cd_tarefa    : <?php echo $row['cd_tarefa']; ?>
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
			'CaseInsensitiveString', 
			'DateTimeBR', 
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString'
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

$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_start_box( "default_box", "Tarefa" );
			echo form_default_text("atividade_os", "Atividade/Tarefa:", $row['cd_atividade'].' / '.$row['cd_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("status", "Status:", $row['status_atual'], 'style="width: 500px; border: 0px; font-weight:bold; color:'.trim($row['status_cor']).'" readonly');
			echo form_default_text("nome_tarefa", "Tipo da tarefa:", $row['nome_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("analista", "Analista:", $row['analista'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("programador", "Programador:", $row['programador'], 'style="width: 500px; border: 0px;" readonly');
		echo form_end_box("default_box");
	echo '<div id="result_div"></div>';
	echo br(); 
echo aba_end();
$this->load->view('footer_interna');
?>