<?php
set_title('Pré-venda - Cadastro');
$this->load->view('header');
?>
<script>
	function salvar(ob)
	{
		if($('#cd_empresa').val() == "")
		{
			alert("Informe a empresa!");
			$('#cd_empresa').focus();
			return false;
		}

		if($('#nome').val() == "")
		{
			alert("Informe o nome!");
			$('#nome').focus();
			return false;
		}
		
		if(($('#cpf').val() == "") && ($('#cd_empresa').val() == 19))
		{
			alert("Informe o CPF!");
			$('#cpf').focus();
			return false;
		}

		if(confirm("Salvar?"))
		{
			ob.submit();
		}
	}	
	
	function carregar_dados_participante(data)
	{
		$('#nome').val(data.nome);
		$('#cpf').val(data.cpf_mf);
		$('#cpf').focus();
	}	

	function novo()
	{
		location.href = '<?php echo site_url('ecrm/prevenda/abrir'); ?>';
	}

</script>
<?
echo form_open('ecrm/prevenda/salvar');


	$link_lista = site_url( 'ecrm/prevenda' );
	$link_cadastro = site_url("ecrm/prevenda/abrir/") . '/' . $record["cd_pre_venda"];
	$link_contato = site_url("ecrm/prevenda/contato/") . '/' . $record["cd_pre_venda"];
	$link_agenda = site_url("ecrm/prevenda/agenda/") . '/' . $record["cd_pre_venda"];

	$abas[0] = array('aba_lista', 'Lista', false, "redir('', '$link_lista')");
	$abas[1] = array('aba_cadastro', 'Cadastro', true, "redir('', '$link_cadastro')");

    if(intval($record['cd_pre_venda'])>0)
    {
        $abas[2] = array('aba_contato', 'Contato', false, "redir('', '$link_contato');");
        $abas[3] = array('aba_agenda', 'agenda', false, "redir('', '$link_agenda')");
    }
	$link_relatorio = site_url( 'ecrm/prevenda/relatorio' );
	$abas[4] = array('aba_relatorio', 'Relatório', false, "redir('', '$link_relatorio')");

    echo aba_start( $abas );

    echo form_start_box("cadastro", "Cadastro:");
	echo form_default_text('cd_pre_venda',"Código:", intval($record['cd_pre_venda']), "style='border: 0px;' readonly");	
	
	
	$c['emp']['id']='cd_empresa';
	$c['re']['id']='cd_registro_empregado';
	$c['seq']['id']='seq_dependencia';
	$c['emp']['value']=$record['cd_empresa'];
	$c['re']['value']=$record['cd_registro_empregado'];
	$c['seq']['value']=$record['seq_dependencia'];
	$c['caption']='Participante:*';
	$c['callback']='carregar_dados_participante';
	echo form_default_participante_trigger($c);	
	echo form_default_text("nome", "Nome:*", $record, "style='width:300px'");
	echo form_default_cpf("cpf", "CPF:*", $record, "style='width:300px'");
	
	
	
    echo form_end_box("cadastro");

    // Comandos
    echo form_command_bar_detail_start();
    if( gerencia_in( array('GCM') ) )
    {
	    echo button_save("Salvar", "salvar(this.form);");
	    if(intval($record['cd_pre_venda'])>0)
	    {
	    	echo button_delete('ecrm/prevenda/excluir', intval($record['cd_pre_venda']));
			echo button_save("Novo", "novo();","botao_amarelo");
	    }
    }
    echo form_command_bar_detail_end();
    // END: Comandos

echo form_close();

echo aba_end( 'cadastro');
echo $this->load->view('footer');
?>