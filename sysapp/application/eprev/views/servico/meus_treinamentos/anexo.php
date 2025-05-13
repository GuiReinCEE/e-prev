<?php
	set_title('Meus Treinamentos - Anexo');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('fl_certificado'), 'valida_arquivo(form)') ?>

    function valida_arquivo(form)
    {
        var fl_certificado = $("#fl_certificado").val();

        if(($("#arquivo").val() == "") && ($("#arquivo_nome").val() == "") && (fl_certificado == "S"))
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else if (($("#ds_justificativa").val() == "") && (fl_certificado == "N"))
        {
            alert("Informe a Justificativa.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function ir_lista()
    {
        location.href = "<?= site_url('servico/meus_treinamentos/index') ?>";
    }

    function ir_documento()
    {
        location.href = "<?= site_url('servico/meus_treinamentos/documento/'.$row['cd_treinamento_colaborador_item']) ?>";
    }

	function habilita_certificado()
	{
		var fl_certificado = $("#fl_certificado").val();
		
		if(fl_certificado == "S")
		{
            $("#arquivo_row").show();
			$("#ds_justificativa_row").hide();
            $("#ds_justificativa").val("");
		}
		else if(fl_certificado == "N")
		{
            $("#arquivo_row").hide();
			$("#ds_justificativa_row").show();
		}
        else
        {
            $("#arquivo_row").hide();
            $("#ds_justificativa_row").hide();
        }
	}

	$(function(){
		habilita_certificado();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_anexo', 'Anexo', TRUE , 'location.reload();');
    $abas[] = array('aba_documento', 'Documento', FALSE, 'ir_documento();');

    $certificado = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );

    echo aba_start($abas);
        echo form_open('servico/meus_treinamentos/salvar_anexo');
            echo form_start_box('default_box', 'Cadastro'); 
                echo form_default_hidden('cd_treinamento_colaborador_item', '', $row['cd_treinamento_colaborador_item']);
                echo form_default_row('', 'Numero:', $row['numero']);
                echo form_default_row('', 'Nome:', $row['nome']);
                echo form_default_row('', 'Promotor:', $row['promotor']);
                echo form_default_row('', 'Tipo:', $row['ds_treinamento_colaborador_tipo']);
                echo form_default_row('', 'Dt. Inicio:', $row['dt_inicio']);
                echo form_default_row('', 'Dt. Final:', $row['dt_final']);
            echo form_end_box('default_box');    

            echo form_start_box('default_sistema_box', ' Cadastro');
            	echo form_default_dropdown('fl_certificado', 'Certificado: (*)', $certificado, array($row['fl_certificado']), 'onclick="habilita_certificado()"'); 
                echo form_default_upload_iframe('arquivo', 'certificado_treinamento', 'Anexo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'certificado_treinamento');
                echo form_default_textarea('ds_justificativa', 'Justificativa: (*)', $row['ds_justificativa']);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
            echo form_command_bar_detail_end();

	$this->load->view('footer');
?>