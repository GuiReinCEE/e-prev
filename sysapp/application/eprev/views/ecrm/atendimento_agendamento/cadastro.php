<?php
set_title('Cadastro Atendimento');
$this->load->view('header');
?>
<script>

	<?= form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome', 'cpf', 'email', 'telefone_1', 'dt_agenda','cd_atendimento_agendamento_tipo'), 'valida_form(form)') ?>

	function valida_form(form)
    {
    	var fl_submit = true;

        if($("#tipo_"+$("#cd_atendimento_agendamento_tipo").val()).val() == "S" && $("#ds_tipo").val() == "")
        {
            alert("Especifique o Agendamento para")
            fl_submit = false;
        }

        if(fl_submit)
        {
            form.submit();
        }
    }

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_agendamento') ?>";
	}

	function part_callb(data)
	{
		$("#nome_item").html(data.nome);
		$("#cpf_item").html(data.cpf);
		$("#email").val(data.email);
		$("#telefone_1").val(data.ddd + "" + data.telefone);
		$("#telefone_2").val(data.ddd_celular + "" + data.celular);
	}

	function especificar()
	{
		if($("#tipo_"+$("#cd_atendimento_agendamento_tipo").val()).val() == "S")
		{
			$("#ds_tipo_row").show();
		}
		else
		{
			$("#ds_tipo_row").hide();
		}
	}

	$(function(){
		if($("#cd_registro_empregado").val() > 0)
		{
			consultar_participante__cd_empresa();
		}

		especificar();
	});		
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');


	$config['callback']     = 'part_callb';
	$config['emp']['value'] = $row['cd_empresa'];
	$config['re']['value']  = $row['cd_registro_empregado'];
	$config['seq']['value'] = $row['seq_dependencia'];
	$config['row_id']       = 'participante_row';
	$config['caption']      = 'Participante:';

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_agendamento/salvar_cadastro');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_atendimento', '', $row['cd_atendimento']);
				foreach ($tipo as $key => $item) 
				{
					echo form_default_hidden('tipo_'.$item['value'], '', $item['fl_especificar']);
				}
				echo form_default_participante_trigger($config);
				echo form_default_row('nome', 'Nome:', '', 'style="width:400px;"');
				echo form_default_row('cpf', 'CPF:', '', 'style="width:400px;"');
				echo form_default_text('email', 'E-mail:(*)', '', 'style="width:400px;"');
				echo form_default_text('telefone_1', 'Telefone 1:(*)', '', 'style="width:400px;"');
				echo form_default_text('telefone_2', 'Telefone 2:', '', 'style="width:400px;"');
				echo form_default_dropdown('dt_agenda', 'Data Agendamento:(*)', $data_agenda);
				echo form_default_dropdown('cd_atendimento_agendamento_tipo', 'Tipo:(*)', $tipo, '', 'onchange="especificar();"');
				echo form_default_text('ds_tipo', 'Especificar:(*)', '', 'style="width:400px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
		echo form_close();

		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
