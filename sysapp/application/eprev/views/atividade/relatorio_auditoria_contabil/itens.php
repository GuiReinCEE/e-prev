<?php
set_title('Relatórios de Auditoria Contábil');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('nr_numero_item', 'ds_relatorio_auditoria_contabil_item', 'cd_usuario_responsavel', 'cd_usuario_substituto', 'dt_limite'));
	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil"); ?>';
	}
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/cadastro/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
	
    function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/anexo/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
    
    function ir_acompanhamento()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/acompanhamento/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
    
	function enviar()
	{
		var confirmacao = 'Deseja enviar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/enviar/".$row['cd_relatorio_auditoria_contabil']); ?>';
		}
	}
    
    function excluir_item(cd_relatorio_auditoria_contabil_item)
	{
		var confirmacao = 'Deseja excluit?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/excluir_item/".$row['cd_relatorio_auditoria_contabil']); ?>/'+cd_relatorio_auditoria_contabil_item;
		}
	}
    
    function encaminhar_aprovacao()
    {
        var confirmacao = 'Deseja encaminhar para aprovação?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
    
        if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/encaminhar_aprovacao/".$row['cd_relatorio_auditoria_contabil']); ?>';
		}
    }
   
   function lista_itens()
   {
       $("#result_div").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('atividade/relatorio_auditoria_contabil/listar_itens');?>',
        {
            cd_relatorio_auditoria_contabil : $('#cd_relatorio_auditoria_contabil').val()
        },
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });
   }
   
   function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "Number",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR",
            "DateBR",
            "DateTimeBR",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
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
        ob_resul.sort(0, true);
    }
   
   $(function(){
        lista_itens();
    });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Itens', TRUE, 'location.reload();');

if((intval($row['qt_itens']) > 0) AND (intval($row['qt_itens_enviado']) == intval($row['qt_itens'])))
{
    $abas[] = array('aba_anexo', 'Anexos', FALSE, 'ir_anexo();');
}

$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

echo aba_start( $abas );
	echo form_open('atividade/relatorio_auditoria_contabil/salvar_item');
		echo form_start_box("default_relatorio_box", "Relatório");
			echo form_default_hidden('cd_relatorio_auditoria_contabil', "", $row);	
            echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']); 
            echo form_default_textarea('ds_relatorio_auditoria_contabil', 'Descrição :', $row, 'style="height:100px;"');
            echo form_default_upload_iframe('arquivo', 'relatorio_auditoria_contabil', 'Arquivo :', array($row['arquivo'], $row['arquivo_nome']), 'relatorio_auditoria_contabil', FALSE);
            if(trim($row['dt_encaminhamento']) != '')
            {
                echo form_default_row('dt_encaminhamento', 'Dt Encaminhamento p/ Aprovação :', $row['dt_encaminhamento']); 
            }
		echo form_end_box("default_relatorio_box");
        
        if(intval($row['qt_itens_enviado']) == 0)
        {
            echo form_start_box("default_box", "Cadastro");
                echo form_default_integer('nr_numero_item', 'Número :*');
                echo form_default_textarea('ds_relatorio_auditoria_contabil_item', 'Descrição :*', '', 'style="height:100px;"');
                echo form_default_usuario_ajax('cd_usuario_responsavel', '', '', "Responsável :*", "Gerência :*");
                echo form_default_usuario_ajax('cd_usuario_substituto', '', '', "Substituto :*", "Gerência :*");
                echo form_default_date('dt_limite', 'Dt. Limite :*');
            echo form_end_box("default_box");
        }
        
		echo form_command_bar_detail_start();
            if(intval($row['qt_itens_enviado']) == 0)
            {
                echo button_save("Salvar");
            }
            
            if((intval($row['qt_itens_enviado']) == 0) AND (intval($row['qt_itens']) > 0))
            {
                echo button_save("Enviar", 'enviar();', 'botao_verde');
            }
            
            if((intval($row['qt_itens']) > 0) AND (intval($row['qt_itens_enviado']) == intval($row['qt_itens'])) AND (trim($row['dt_encaminhamento']) == ''))  
            {
                echo button_save("Encaminhar p/ Aprovação", 'encaminhar_aprovacao();', 'botao_verde');
            }
		echo form_command_bar_detail_end();
	echo form_close();
    echo '<div id="result_div"></div>';
echo aba_end();
$this->load->view('footer_interna');
?>