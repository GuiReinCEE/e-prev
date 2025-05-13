<?php
	set_title('Sistema de Avaliação - Abertura');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_usuario_avaliador')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/cadastro/'.$row['cd_avaliacao']) ?>";
	}

	function ir_avaliacao()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/avaliacao/'.$row['cd_avaliacao']) ?>";
	}

	function set_usuario()
	{
        $.post("<?= site_url('cadastro/rh_avaliacao_abertura/get_usuario') ?>",
        {
            cd_gerencia : $("#cd_gerencia").val()
        },
        function(data)
        {
			var select = $('#cd_usuario_avaliador'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
            });
        }, "json", true);
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_avaliacao', 'Avaliações', FALSE, 'ir_avaliacao();');
	$abas[] = array('aba_avaliado', 'Avaliado', TRUE, 'location.reload();');

	$avatar_arquivo = $row['avatar'];
    
    if(trim($avatar_arquivo) == '')
    {
        $avatar_arquivo = $row['ds_usuario'].'.png';
    }
    
    if(!file_exists('./up/avatar/'.$avatar_arquivo))
    {
        $avatar_arquivo = 'user.png';
    }

	echo aba_start($abas);
		echo form_start_box('default_box', 'Avaliação');
	        echo form_default_row('', 'Ano:', '<span class="label label-inverse">'.$avaliacao['nr_ano_avaliacao'].'</span>');
	        echo form_default_row('', 'Dt. Ínicio:', $avaliacao['dt_inicio']);
	        echo form_default_row('', 'Dt. Encerramento:', $avaliacao['dt_encerramento']);
	        if(trim($avaliacao['dt_envio_email']) != '')
	        {
	            echo form_default_row('', 'Dt. Envio:', $avaliacao['dt_envio_email']);
	            echo form_default_row('', 'Usuário Envio:', $avaliacao['ds_usuario_envio_email']);
	        }
	    echo form_end_box('default_box');
		echo form_open('cadastro/rh_avaliacao_abertura/salvar_avaliador');
		    echo form_start_box('default_box', 'Avaliado');
		        echo form_default_row('', 'Foto atual:', '<a href="'.site_url('cadastro/avatar/index/'.intval($row['cd_usuario'])).'" title="Clique aqui para ajustar a foto"><img height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>');
		        echo form_default_row('', 'Gerência:', $row['ds_gerencia']);
		        echo form_default_row('', 'Nome:', $row['ds_nome']);
		        echo form_default_row('', 'Dt. Admissão:', $row['dt_admissao']);
		        echo form_default_row('', 'Cargo / Área de Atuação:', $row['ds_cargo_area_atuacao']);
		        echo form_default_row('', 'Classe:', $row['ds_classe']);
		        echo form_default_hidden('cd_avaliacao_usuario', '', $row['cd_avaliacao_usuario']);
		        echo form_default_dropdown('cd_gerencia', 'Gerência Avaliador:', $gerencia, $row['ds_area_avaliador'], 'onchange="set_usuario();"');
		        echo form_default_dropdown('cd_usuario_avaliador', 'Avaliador:', $usuario, $row['cd_usuario_avaliador']);
		    echo form_end_box('default_box');
	        echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
	    echo form_close();
	echo aba_end();
	$this->load->view('footer');
?>