<?php
	set_title('Portabilidade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante', 'cd_portabilidade_status')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/portabilidade/index') ?>";
    }

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $head = array(
        'Dt. Cadastro',
        'Usuário',
        'Status',
        'Acompanhamento',
        'Dt. Agendamento'
    );

    $body = array();

    foreach ($acompanhamento as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            '<label class="'.$item['ds_class_status'].'">'.$item['ds_portabilidade_status'].'</label>',
            array(nl2br($item['ds_portabilidade_acompanhamento']), 'text-align:justify'),
            $item['dt_agendamento_alerta']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
		echo form_open('ecrm/portabilidade/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_portabilidade', '', $row['cd_portabilidade']);
                if(intval($row['cd_portabilidade']) == 0)
                {
                    echo filter_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante'), 'RE: (*)');
                }
                else
                {
                    echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
                    echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
                    echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
                    echo form_default_hidden('nome_participante', '', $row['nome']);
                    echo form_default_row('', 'RE:', '<span class="label label-inverse">'.$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'].'</span>');
                    echo form_default_row('', 'Nome:', $row['nome']);
                    echo form_default_row('', 'Dt. Cadastro:', $row['dt_inclusao']);
                    echo form_default_row('', 'Usuário Cadastro:', $row['ds_usuario_inclusao']);
                }
                echo form_default_dropdown('cd_portabilidade_status', 'Status: (*)', $status, $row['cd_portabilidade_status']);
                echo form_default_textarea('ds_portabilidade_acompanhamento', 'Acompanhamento: (*)', '', 'style="height:150px;"');
                echo form_default_date('dt_agendamento_alerta', 'Dt. Angendamento:', '');
                echo form_default_row('', '', '<i>Data de Agendamento no Outlook</i>');
			echo form_end_box('default_box');	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');     	            
		    echo form_command_bar_detail_end();
		echo form_close();
        echo br();
        echo $grid->render();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>