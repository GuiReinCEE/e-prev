<?php 
set_title('eCRM - Protocolo Benefício');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array(
		array('nome','str'), array('cep','str'), array('uf','str'), array('cidade','str'), array('endereco',''), array('bairro','str'), array('cd_protocolo_beneficio_assunto','int'), array('cd_protocolo_beneficio_forma_envio','int')
	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/protocolo_beneficio"); ?>';
	}

	function consulta_cep(data)
	{
		$('#uf').val(data.uf);
		filtrar_cidade( data.uf, data.cidade );
		$('#endereco').val(data.endereco);
		$('#bairro').val(data.bairro);
	}

	function filtrar_cidade( uf, cidade )
	{
		if( uf!='' )
		{
			url = "<?php echo site_url(  'ecrm/protocolo_beneficio/listar_cidade_para_detalhe_ajax'  ); ?>";
			$.post( url, {cd_uf:uf}, function(data){ $('#cidade_div').html(data); if(cidade!=""){ $("#cidade").val(cidade); } } );
		}
		else
		{
			$('#cidade_div').html(  "<div id='cidade_div' style='font-size:12px;'>Selecione uma UF</div>"  );
		}
	}

	function carregar_dados_participante(data)
	{
		$('#nome').val( data.nome )
		$('#cep').val( data.cep + '-' + data.complemento_cep );
		$('#endereco').val( data.logradouro );
		$('#bairro').val( data.bairro );
		$('#uf').val( data.unidade_federativa );
		filtrar_cidade( data.unidade_federativa, data.cidade );
		//$('cidade').val( data.cidade );
	}
	
	function qrcode_retorno(data)
	{
		if(data.result)
		{
			$("#cd_empresa").val(data.cd_empresa);
			$("#cd_registro_empregado").val(data.cd_registro_empregado);
			$("#seq_dependencia").val(data.seq_dependencia);

			consultar_participante_focus__cd_empresa();
		}
	}		
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open( 'ecrm/protocolo_beneficio/salvar', array('id'=>'frm','name'=>'frm') );
echo form_hidden( 'cd_protocolo_beneficio', intval($row['cd_protocolo_beneficio']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "eCRM - Protocolo Benefício" );

if( intval($row['cd_protocolo_beneficio'])>0 )
{
	$nr_protocolo_e_ano = "<input type='text' name='nr_ano' id='nr_ano' value='" . $row['nr_ano'] . "' style='width:70px;border-style:none;' readonly='readonly' /> / <input type='text' name='nr_protocolo' id='nr_protocolo' value='" . $row['nr_protocolo'] . "' style='width:70px;border-style:none;' readonly='readonly' />";
	echo form_default_row( "protocolo_row", "Ano/Protocolo:", $nr_protocolo_e_ano );
}

$c['emp']['id']='cd_empresa';
$c['re']['id']='cd_registro_empregado';
$c['seq']['id']='seq_dependencia';
$c['emp']['value']=$row['cd_empresa'];
$c['re']['value']=$row['cd_registro_empregado'];
$c['seq']['value']=$row['seq_dependencia'];
$c['caption']='Participante';
$c['callback']='carregar_dados_participante';

echo form_default_qrcode(array('id'=>'qrcode', 'caption'=>'QR Code:','callback'=>'qrcode_retorno','value'=>''));
echo form_default_participante_trigger($c);

echo form_default_text("nome", "Nome: *", $row, "style='width:300px;'", "0");
echo form_default_cep("cep", "CEP: *", $row, array("db"=>TRUE, "callback_function"=>"consulta_cep","return_type"=>"json"));
echo form_default_dropdown("uf", "UF: *", $uf_dd, array($row["uf"]), "onchange='filtrar_cidade(this.value);'");
echo form_default_row("cidade_row", "Cidade: *", "<div id='cidade_div' style='font-size:12px;'><select id='cidade' name='cidade'><option value=''></option><></select></div>");
echo form_default_text("endereco", "Endereço: *", $row, "style='width:300px;'", "0");
echo form_default_text("bairro", "Bairro: *", $row, "style='width:300px;'", "0");

echo form_default_dropdown_db("cd_protocolo_beneficio_assunto", "Assunto: *", array( "projetos.protocolo_beneficio_assunto", "cd_protocolo_beneficio_assunto", "ds_protocolo_beneficio_assunto" ), array( $row["cd_protocolo_beneficio_assunto"] ), "", "", TRUE);
echo form_default_dropdown_db("cd_protocolo_beneficio_forma_envio", "Forma de envio: *", array( "projetos.protocolo_beneficio_forma_envio", "cd_protocolo_beneficio_forma_envio", "ds_protocolo_beneficio_forma_envio" ), array( $row["cd_protocolo_beneficio_forma_envio"] ), "", "", TRUE);

echo form_default_textarea("observacao","Observação:",$row);

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();

#if( gerencia_in( array('GB') ) )
#{
	echo button_save();
#}
if( intval($row['cd_protocolo_beneficio'])>0 )
{
	if( gerencia_in( array('GB') ) )
	{
		echo button_delete("ecrm/protocolo_beneficio/excluir", $row["cd_protocolo_beneficio"]);
	}
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/protocolo_beneficio')."'; }");
echo form_command_bar_detail_end();
?>
<script>
	<?php if( $row["uf"]!="" ): ?>
	filtrar_cidade("<?php echo $row['uf'] ?>", "<?php echo $row['cidade'] ?>");
	<?php endif; ?>
</script>
<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
