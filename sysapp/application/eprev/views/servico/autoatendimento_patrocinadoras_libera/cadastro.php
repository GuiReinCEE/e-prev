<?php
	set_title('Patrocinadoras Libera - Cadastro');
	$this->load->view('header');
?>
<script>
    <? if(intval($row['cd_patrocinadoras_libera']) == 0): ?>
		<?= form_default_js_submit(array('cd_empresa', 'nr_ordem', 'ds_patrocinadoras_libera', 'nr_ano')) ?>
	<? else: ?>
		<?= form_default_js_submit(array('nr_ordem', 'ds_patrocinadoras_libera', 'nr_ano')) ?>
	<? endif; ?>
	
    function ir_lista()
    {
        location.href = "<?= site_url('servico/autoatendimento_patrocinadoras_libera') ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	echo aba_start( $abas );
        echo form_open('servico/autoatendimento_patrocinadoras_libera/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_patrocinadoras_libera', '', $row['cd_patrocinadoras_libera']);
				if(intval($row['cd_patrocinadoras_libera']) == 0)
				{
					echo form_default_dropdown('cd_empresa', 'Empresa: (*)', $empresa);
				}
				else
				{
					echo form_default_row('sigla', 'Empresa:', $row['sigla'], 'style="width:300px;"');
				}
				echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
				echo form_default_text('ds_patrocinadoras_libera', 'Descrição: (*)', $row, 'style="width:300px;"');
				echo form_default_integer('nr_ano', 'Ano: (*)', $row);
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>