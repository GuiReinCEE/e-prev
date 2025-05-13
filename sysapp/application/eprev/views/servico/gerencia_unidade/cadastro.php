<?php
	set_title('Ger�ncia - Unidade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('codigo', 'nome', 'tipo', 'dt_vigencia_ini')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('servico/gerencia_unidade') ?>";
    }

    function ir_unidade()
    {
        location.href = "<?= site_url('servico/gerencia_unidade/unidade/'.$row['codigo']) ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(trim($row['codigo']) != '')
    {
    	$abas[] = array('aba_unidade', 'Unidade', FALSE, 'ir_unidade();');
    }

    $atividade = array(
    	array('value' => 'N', 'text' => 'N�o'),
    	array('value' => 'S', 'text' => 'Sim')
	);

    echo aba_start($abas);
        echo form_open('servico/gerencia_unidade/salvar');
            echo form_start_box('default_box', 'Cadastro');		
            	echo form_default_hidden('codigo_h', '', $row['codigo']);	
    			
                if(trim($row['codigo']) == '')
                {
                    echo form_default_text('codigo', 'C�digo: (*)', $row);
                }
                else
                {
                	echo form_default_row('', 'C�digo:', '<label class="label label-inverse">'.$row['codigo'].'</label>');
                }
                
                echo form_default_text('nome', 'Descri��o: (*)', $row, 'style="width:300px;"');
                echo form_default_dropdown('tipo', 'Tipo: (*)', $tipo, $row['tipo']);
                echo form_default_date('dt_vigencia_ini', 'Vig�ncia: (*)', $row);
                echo form_default_dropdown('area', 'Diretoria:', $diretoria, $row['area']);

                if(trim($row['codigo']) != '')
                {
                	echo form_default_date('dt_vigencia_fim', 'Vig�ncia Fim: ', $row);
                }

                echo form_default_dropdown('fl_atividade', 'Abri Atividade:', $atividade, $row['fl_atividade']);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>