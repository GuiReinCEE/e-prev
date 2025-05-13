<?php
set_title('Cronograma - Cadastro de Grupos');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('ds_atividade_cronograma_grupo'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/atividade_cronograma_grupo"); ?>';
    }   

	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("atividade/atividade_cronograma_grupo/excluir/".intval($row['cd_atividade_cronograma_grupo'])); ?>';
        }
	}
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('atividade/atividade_cronograma_grupo/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atividade_cronograma_grupo', "", $row);
			echo form_default_text('ds_atividade_cronograma_grupo', "Grupo:* ", $row, "style='width:300px;'" );		
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if(intval($row['cd_atividade_cronograma_grupo']) > 0)
			{
				echo button_save("Excluir", "excluir()", "botao_vermelho");
			}
        echo form_command_bar_detail_end();
    
    echo form_close();

    echo br();	

echo aba_end();

$this->load->view('footer_interna');
?>