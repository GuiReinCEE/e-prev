<?php
	set_title('Logs de Jobs Postgres');
	$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('log') ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Log', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_start_box('default_box', 'Cadastro'); 
			echo form_default_row('', 'Cód Job Log:', $row['cd_job_log']);
			echo form_default_row('', 'Função:', $row['ds_funcao']);
			echo form_default_row('', 'Job:', $row['ds_job']);
			echo form_default_row('', 'Dt. Erro:', $row['dt_erro']);
            echo form_default_editor_code('ds_comando', 'Comando:', $row['ds_comando'], 'style="width:800px; height: 250px;"');
            echo form_default_editor_code('ds_erro', 'Erro:', $row['ds_erro'], 'style="width:800px; height: 400px;"');
        echo form_end_box('default_box');
  
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>