<?php
set_title('Relatórios de Auditoria Contábil');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('ds_relatorio_auditoria_contabil'), 'valida_arquivo(form)');
	?>
        
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
        
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil"); ?>';
	}
    
    function ir_itens()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/itens/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
    
    function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/anexo/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
    
    function ir_acompanhamento()
    {
        location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/acompanhamento/".$row['cd_relatorio_auditoria_contabil']); ?>';
    }
	
	function enviar_gc()
	{
		var confirmacao = 'Deseja enviar o Relatório?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/enviar_gc/".$row['cd_relatorio_auditoria_contabil']); ?>';
		}
	}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if((trim($row['dt_envio_gc']) != '') AND (gerencia_in(array('GC'))))
{
    $abas[] = array('aba_lista', 'Itens', FALSE, 'ir_itens();');
}

if((intval($row['qt_itens']) > 0) AND (intval($row['qt_itens_enviado']) == intval($row['qt_itens'])) AND (trim($row['dt_envio_gc']) != ''))
{
    $abas[] = array('aba_anexo', 'Anexos', FALSE, 'ir_anexo();');
}

if(intval($row['cd_relatorio_auditoria_contabil']) > 0)
{
    $abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
}

echo aba_start( $abas );
	echo form_open('atividade/relatorio_auditoria_contabil/salvar');
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_relatorio_auditoria_contabil', "", $row);	
            if(intval($row['cd_relatorio_auditoria_contabil']) > 0)
            {
               echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']); 
            }
            echo form_default_textarea('ds_relatorio_auditoria_contabil', 'Descrição :*', $row, (trim($row['dt_envio_gc']) == '' ? 'style="width:300px;"' : 'style="font-weight: bold; width:300px; border: 0px;" readonly'));
            echo form_default_upload_iframe('arquivo', 'relatorio_auditoria_contabil', 'Arquivo :*', array($row['arquivo'], $row['arquivo_nome']), 'relatorio_auditoria_contabil', (trim($row['dt_envio_gc']) == '' ? TRUE : FALSE));
            
            if(trim($row['dt_aprovado']) != '')
            {
                echo form_default_date('dt_envio_sg', 'Dt enviado SG :', $row);
                echo form_default_date('dt_alchemy', 'Dt Alchemy :', $row);
            }
            
        echo form_end_box("default_box");
		echo form_command_bar_detail_start();
            if((trim($row['dt_envio_gc']) == '') OR (trim($row['dt_aprovado']) != ''))
            {
                echo button_save("Salvar");	
            }
            
            if((intval($row['cd_relatorio_auditoria_contabil']) > 0) AND(trim($row['dt_envio_gc']) == ''))
            {
                echo button_save("Enviar GC", 'enviar_gc();', 'botao_verde');	
            }
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>