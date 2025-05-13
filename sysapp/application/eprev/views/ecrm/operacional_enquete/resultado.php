<?php
set_title('Pesquisa - Resultado');
$this->load->view('header');
?>
<script>
    function resultadoResumo()
    {
        $("#obResumo").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoResumo');?>',
        $("#formRelatorioPesquisa").serialize(),
        function(data)
        {
            $("#obResumo").html(data);
        });
    }   
    
    function resultadoAgrupamento()
    {
        $("#obAgrupamento").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoAgrupamento');?>',
        $("#formRelatorioPesquisa").serialize(),
        function(data)
        {
            $("#obAgrupamento").html(data);
        });
    }	
	
    function resultadoQuestaoResumo()
    {
        $("#obQuestaoResumo").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoQuestaoResumo');?>',
        $("#formRelatorioPesquisa").serialize(),
        function(data)
        {
            $("#obQuestaoResumo").html(data);
        });
    }	

    function resultadoQuestao()
    {
        $("#obQuestao").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoQuestao');?>',
        $("#formRelatorioPesquisa").serialize(),
        function(data)
        {
            $("#obQuestao").html(data);
        });
    }	
	
    function enviarFormRelatorio()
	{
		$('#formRelatorioPesquisa').submit();
	}
    
	function resultadoVerGrafico(cd_pergunta)
	{
        $("#windowPadraoConteudo").html("");
		$.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoVerGrafico');?>',
        {
			cd_enquete        : $("#cd_enquete").val(),
			dt_referencia_ini : $("#dt_referencia_ini").val(),
			dt_referencia_fim : $("#dt_referencia_fim").val(),
			cd_pergunta       : cd_pergunta
		},
        function(data)
        {
			$("#windowPadraoConteudo").html(data);
			windowPadraoShow();
        });		
	}	
	
	function resultadoVerComentario(cd_pergunta)
	{
        $("#windowPadraoConteudo").html("");
		$.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoVerComentario');?>',
        {
			cd_enquete        : $("#cd_enquete").val(),
			dt_referencia_ini : $("#dt_referencia_ini").val(),
			dt_referencia_fim : $("#dt_referencia_fim").val(),
			cd_pergunta       : cd_pergunta
		},
        function(data)
        {
			$("#windowPadraoConteudo").html(data);
			windowPadraoShow();
        });		
	}
	
	function resultadoVerComplemento(cd_pergunta, cd_resposta)
	{
        $("#windowPadraoConteudo").html("");
		$.post('<?php echo site_url('/ecrm/operacional_enquete/resultadoVerComplemento');?>',
        {
			cd_enquete        : $("#cd_enquete").val(),
			dt_referencia_ini : $("#dt_referencia_ini").val(),
			dt_referencia_fim : $("#dt_referencia_fim").val(),
			cd_pergunta       : cd_pergunta,
			cd_resposta       : cd_resposta
		},
        function(data)
        {
			$("#windowPadraoConteudo").html(data);
			windowPadraoShow();
        });		
	}	
	
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/operacional_enquete") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/cadastro/".intval($ar_cadastro['cd_enquete'])) ?>';
    }

    function ir_estrutura()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/estrutura/".intval($ar_cadastro['cd_enquete'])) ?>';
    }

	function filtarResultado()
	{
		resultadoResumo();
		resultadoAgrupamento();
		resultadoQuestaoResumo();
		resultadoQuestao();
	}
	
    $(function() {
		filtarResultado();
    }); 
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura',FALSE, 'ir_estrutura();');
$abas[] = array('aba_resultado', 'Resultados', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('ecrm/operacional_enquete/relatorioPDF', array("method" => "post", "id" => "formRelatorioPesquisa", "target" => "_blank"));
        echo form_start_box("default_box", "");
            echo form_default_hidden('cd_enquete', '', $ar_cadastro['cd_enquete']);
            echo form_default_row('cd_enquete_label', "Pesquisa:", '<span class="label label-success">'.$ar_cadastro["cd_enquete"].' - '.$ar_cadastro["ds_titulo"].'</span>');
            echo form_default_row("","","");
			echo form_default_date_interval("dt_referencia_ini", "dt_referencia_fim", "Período das respostas:");
			
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
            echo button_save("Filtrar","filtarResultado()", "botao_disabled");
            echo button_save("Relatório PDF","enviarFormRelatorio()", "botao_amarelo");
        echo form_command_bar_detail_end();          
		
        echo form_start_box("resumo_box", "Resumo",FALSE);
            echo '<div id="obResumo"></div>';
        echo form_end_box("resumo_box");     		
		
        echo form_start_box("agrupamento_box", "Resultado por Agrupamento",FALSE);
            echo '<div id="obAgrupamento"></div>';
        echo form_end_box("agrupamento_box");   	

        echo form_start_box("resumo_questao_box", "Resumo por Questão",FALSE);
            echo '<div id="obQuestaoResumo"></div>';
        echo form_end_box("resumo_questao_box"); 		
		
        echo form_start_box("questao_box", "Resultado por Questão",FALSE);
            echo '<div id="obQuestao"></div>';
        echo form_end_box("questao_box"); 		
		
    echo form_close();
    echo br(10);    
echo aba_end();

$this->load->view('footer_interna');
?>