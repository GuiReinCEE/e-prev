<?php
set_title('Inscrições no SENGE');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('dt_nascimento', 'endereco', 'cep_complemento', 'cd_pacote'), 'valida_doc(form)');
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("planos/senge_inscricao"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/cadastro/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_contato()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/contato/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_documento()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/documento/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/anexo/".$row['cd_registro_empregado']); ?>';
    }
	
	function valida_doc(form)
	{
		if(($('#doc_1').val() == 0) && ($('#doc_225').val() > 0))
		{
			if(confirm('Foi identificado que o documento 1 não foi incluído.\n\nDeseja confirmar mesmo assim?'))
			{
				form.submit();
				return true;
			}
			else
			{
				return false;
			}
			
		}
		
		if($('#doc_225').val() == 0)
		{
			alert('Não foi incluído o documento Pedido de Inscrição');
			return false;
		}
		
		form.submit();
	}
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_lista', 'Documentos', FALSE, 'ir_documento();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', TRUE, 'location.reload();');

$irpf   = '';
$pacote = '';

if(intval($row['cd_pacote']) == 1) 
{
	$pacote = $row['cd_pacote'].' - Internet';
}
else if(intval($row['cd_pacote']) == 2)
{
	$pacote = $row['cd_pacote'].' - Correios';
}

if(intval($row['opt_irpf']) == 1) 
{
	$irpf = $row['opt_irpf'].' - Optou pela tabela regressiva';
}
elseif (intval($row['opt_irpf']) == 2) 
{
	$irpf = $row['opt_irpf'].' - NÃO optou pela tabela regressiva';
}

echo aba_start( $abas );
    echo form_open('planos/senge_inscricao/confirmar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
			echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
			echo form_default_hidden('dt_nascimento', '', $row['dt_nascimento']);
			echo form_default_hidden('endereco', '', $row['endereco']);
			echo form_default_hidden('cep_complemento', '', $row['cep_complemento']);
			echo form_default_hidden('cd_pacote', '', $row['cd_pacote']);
			echo form_default_hidden('doc_1', '', $row['doc_1']);
			echo form_default_hidden('doc_225', '', $row['doc_225']);
			
			echo form_default_row('re', 'RE :', $row['cd_registro_empregado']);
			echo form_default_row('nome', 'Nome :', $row['nome']);
			echo form_default_row('dt_nascimento_r', 'Dt Nascimento :', $row['dt_nascimento']);
			echo form_default_row('endereco_r', 'Endereço :', $row['endereco']);
			echo form_default_row('cep_r', 'CEP :', $row['cep_complemento']);
			
			echo form_default_row('dt_inscricao', 'Dt Inscrição :', '<span class="label">'.$row['dt_inscricao'].'</span>');
			echo form_default_row('dt_senge_confirmado', 'Dt Conferência SENGE :', '<span class="label label-success">'.$row['dt_senge_confirmado'].'</span>');
			echo form_default_row('dt_email_confirmado', 'Dt Confirmação do Email :', '<span class="label label-warning">'.$row['dt_email_confirmado'].'</span>');
			echo form_default_row('dt_documentacao_confirmada', 'Dt Conferência DAP :', '<span class="label label-info">'.$row['dt_documentacao_confirmada'].'</span>');
			echo form_default_row('irpf', 'Opção IRPF :', $irpf);
			echo form_default_row('pacote', 'Opção Pacote :', $pacote);
		echo form_end_box("default_box");	
        echo form_command_bar_detail_start();     
			if(trim($row['dt_documentacao_confirmada']) == '')
			{
				echo button_save("Confirmar Dados e Documentos OK");
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
	
echo aba_end();

$this->load->view('footer_interna');
?>