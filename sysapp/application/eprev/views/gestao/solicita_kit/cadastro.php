<?php
set_title('Eleições - Solicita Kit');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome', 'endereco', 'fl_endereco_atualizado', 'cd_eleicao' , 'cd_solicita_kit_tipo'), 'callback()');
?>
    function lista()
    {
        location.href='<?php echo site_url("gestao/solicita_kit"); ?>';
    }
	
	function callback()
	{
		$.post( '<?php echo site_url('/gestao/solicita_kit/verifica_cadastro'); ?>',
		{
			cd_empresa            : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia       : $('#seq_dependencia').val(),
			cd_eleicao            : $('#cd_eleicao').val()
		},
		function(data)
		{
			if(data == 0)
			{
				alert("Participante não faz parte do cadastro eleições.");
				return false;
			}
			else
			{
				$('form').submit();
			}
		});
	}
	
	function carregar_dados_participante(data)
    {
        $('#nome').val(data.nome);

        $('#endereco').val(data.logradouro);
		//$('#complemento').val(data.complemento);
		$('#cep').val(data.cep+'-'+data.complemento_cep);
		$('#cidade').val(data.cidade);
		$('#uf').val(data.unidade_federativa);
    }
	
	$(function(){
		if(($('#cd_empresa').val() != '') && ($('#cd_registro_empregado').val() != '') && ($('#seq_dependencia').val() != ''))
		{
			consultar_participante__cd_empresa();
		}
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Cadastro', TRUE, 'location.reload();');

$ar_endereco[] = array('value' => 'S', 'text' => 'Sim');
$ar_endereco[] = array('value' => 'N', 'text' => 'Não');

echo aba_start($abas);

echo form_open('gestao/solicita_kit/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_solicita_kit', '', $row['cd_solicita_kit']);
		$c['emp']['id'] = 'cd_empresa';
		$c['re']['id'] = 'cd_registro_empregado';
		$c['seq']['id'] = 'seq_dependencia';
		$c['emp']['value'] = $row['cd_empresa'];
		$c['re']['value'] = $row['cd_registro_empregado'];
		$c['seq']['value'] = $row['seq_dependencia'];
		$c['caption'] = 'Participante:*';
		$c['callback'] = 'carregar_dados_participante';
		echo form_default_participante_trigger($c);
		echo form_default_text("nome", "Nome:", $row['nome'], 'style="width:500px; border:0" readonly=""');
		echo form_default_text("endereco", "Endereço:", '', 'style="width:500px; border:0" readonly=""');
		#echo form_default_text("complemento", "Complemento:", '', "style='width:500px;'");
		echo form_default_text("cep", "CEP:", '', 'style="width:500px; border:0" readonly=""');
		echo form_default_text("cidade", "Cidade:", '', 'style="width:500px; border:0" readonly=""');
		echo form_default_text("uf", "UF:", '', 'style="width:500px; border:0" readonly=""');
		echo form_default_dropdown('cd_solicita_kit_tipo', 'Tipo:*', $arr_tipo, array($row['cd_solicita_kit_tipo']));
		echo form_default_dropdown('fl_endereco_atualizado', 'Atualizou Endereço:*', $ar_endereco, array($row['fl_endereco_atualizado']));
		echo form_default_dropdown('cd_eleicao', 'Eleição:*', $arr_eleicao);
		
		
	echo form_end_box("default_box");
	
	echo form_command_bar_detail_start();
		echo button_save("Salvar");
    echo form_command_bar_detail_end();
	
	echo br(2);
echo form_close();
echo aba_end();