<?php
set_title('Atividade - Histórico');
$this->load->view('header');
?>
<script>
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

    function ir_script()
    {
        location.href='<?php echo site_url('atividade/atividade_script/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }
	
	
	function ir_anexo()
    {
        location.href='<?php echo site_url('atividade/atividade_anexo/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }
		
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('atividade/atividade_historico/listar'); ?>',
		{
			cd_atividade : <?php echo $cd_atividade; ?>
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

	function prioridade_historico()
    {
		$('#result_div_prioridade_historico').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('atividade/atividade_historico/prioridade_historico'); ?>',
		{
			cd_atividade : <?php echo $cd_atividade; ?>
		},
        function(data)
        {
			$('#result_div_prioridade_historico').html(data);
            cfg_result_table_prioridade_historico();
        });
    }
	
	function cfg_result_table_prioridade_historico()
    {
        var ob_resul = new SortableTable(document.getElementById("prioridade_historico"),
        [
			'Number', 
			'Number', 
			'CaseInsensitiveString',
			'DateTimeBR', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			'Number', 
			'Number', 
			'Number', 
			'Number'
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
        ob_resul.sort(3, true);
    }	
	
	$(function(){
		load();
		prioridade_historico();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Solicitação', FALSE, 'ir_solicitacao()');
$abas[] = array('aba_lista', 'Atendimento', FALSE, 'ir_atendimento();');
if($this->session->userdata('divisao') == 'GS'){
    $abas[] = array('aba_lista', 'Script', FALSE, 'ir_script();');
}
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
$abas[] = array('aba_lista', 'Histórico', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_start_box("default_box", "Atividade");
		echo form_default_text("cd_atividade", "Atividade:", $cd_atividade,'style="font-weight:bold; width: 500px; border: 0px;" readonly');
	echo form_end_box("default_box");

	echo form_start_box("default_box1", "Histórico");
		echo form_default_row("a", "", '<div id="result_div"></div>');
	echo form_end_box("default_box1");

	if(gerencia_in(array('GS')))
	{
		echo form_start_box("default_box2", "Histórico Prioridade");
			echo form_default_row("a", "", '<div id="result_div_prioridade_historico"></div>');
		echo form_end_box("default_box2");
	}
	echo br(5);	
echo aba_end();
$this->load->view('footer_interna');
?>