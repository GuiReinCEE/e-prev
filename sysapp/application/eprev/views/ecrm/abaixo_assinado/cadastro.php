<?php
	set_title('Abaixo Assinado - Cadastro');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array( 
                'dt_protocolo', 
                'cd_empresa', 
                'cd_registro_empregado', 
                'seq_dependencia', 
                'ds_nome', 
                'ds_descricao', 
                'ds_email', 
                'telefone_1', 
                'telefone_2' 
            )); ?>

    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado') ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/acompanhamento/'.$row['cd_abaixo_assinado']) ?>";
    }

    function ir_retorno()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/retorno/'.$row['cd_abaixo_assinado']) ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/anexo/'.$row['cd_abaixo_assinado']) ?>";
    }

	function carregar_dados_participante(data)
    {
		$('#ds_nome').val(data.nome);
		$('#cd_plano').val(data.cd_plano);
		
		if(data.email != '')
		{
			$('#ds_email').val(data.email);
		}
		else 
		{
			$('#ds_email').val(data.email_profissional);
		}
		
		$('#ds_telefone_1').val('('+data.ddd.substr(1,2)+') '+data.telefone);
		$('#ds_telefone_2').val('('+data.ddd_celular.substr(1,2)+') '+data.celular);			
	}
    
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_abaixo_assinado']) != 0)
    {
        $abas[] = array('aba_acompanhemtno', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
        $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
        $abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');
    }

	$c['emp']['id']    = 'cd_empresa';
	$c['re']['id']     = 'cd_registro_empregado';
	$c['seq']['id']    = 'seq_dependencia';
	$c['emp']['value'] = $row['cd_empresa'];
	$c['re']['value']  = $row['cd_registro_empregado'];
	$c['seq']['value'] = $row['seq_dependencia'];
	$c['caption']      = 'RE: (*)';
	$c['callback']     = 'carregar_dados_participante';

    echo aba_start($abas);
        echo form_open('ecrm/abaixo_assinado/salvar');
            echo form_start_box('default_cadastro_box', 'Cadastro');
                echo form_default_hidden('cd_abaixo_assinado', '', $row);
                if(intval($row['cd_abaixo_assinado']) > 0)
                {
                    echo form_default_row('', 'Ano/N°', '<span class="label label-inverse">'.$row['nr_numero_ano'].'</span>');
                }

                echo form_default_date('dt_protocolo', 'Dt. Protocolo: (*)', $row);

                if(intval($row['cd_abaixo_assinado']) > 0)
                {
                    echo form_default_row('', 'Dt. Limite Retorno:', '<span class="label label-important">'.$row['dt_limite_retorno'].'</span>');
                }

                echo form_default_participante_trigger($c);
                echo form_default_text('ds_nome', 'Nome: (*)', $row, 'style="width:500px;"');
                echo form_default_textarea('ds_descricao', 'Descrição: (*)', $row, 'style="width:500px; height:80px;"');
                echo form_default_text('ds_email', 'Email:', $row, 'style="width:500px;"');
                echo form_default_telefone('ds_telefone_1', 'Telefone 1:', $row, 'style="width:500px;"');
				echo form_default_telefone('ds_telefone_2', 'Telefone 2:', $row, 'style="width:500px;"');
                
                if(intval($row['cd_abaixo_assinado']) > 0)
                {
                    echo form_default_row('', '', '');
                    echo form_default_textarea('ds_acao', 'Ação:', $row, 'style="width:500px; height:80px;"');
                }
            echo form_end_box('default_cadastro_box');
            echo form_command_bar_detail_start();
            if($fl_permissao AND trim($row['dt_retorno']) == '')
            {
                echo button_save('Salvar');
            }		
        echo form_command_bar_detail_end();
        echo form_close();
		echo br();
    echo aba_end();

    $this->load->view('footer');
?>