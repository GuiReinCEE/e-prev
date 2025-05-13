<?php
set_title('Deliberações do Conselho - Cadastro');
$this->load->view('header');
?>
<script>
<?=form_default_js_submit(Array('nr_deliberacao_conselho', 'nr_ano', 'dt_deliberacao_conselho', 'ds_deliberacao_conselho', 'nr_ata', 'fl_situacao', 'cd_deliberacao_conselho_abrangencia'), 'valida_arquivo(form);')?>
    function ir_lista()
    {
        location.href='<?=site_url("gestao/deliberacao_conselho")?>';
    }
        
    function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
	
	function excluir()
	{
		var confirmacao = 'Deseja EXCLUIR a Deliberação?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';	

		if(confirm(confirmacao))
		{
			location.href='<?=site_url("gestao/deliberacao_conselho/excluir/".intval($row['cd_deliberacao_conselho']))?>'
		}
	}
	
	function novo()
	{
		location.href='<?=site_url("gestao/deliberacao_conselho/cadastro/")?>';
	}	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr[] = array('value' => 'N', 'text' => 'Normal');
$arr[] = array('value' => 'R', 'text' => 'Revogada');

echo aba_start( $abas );
    echo form_open('gestao/deliberacao_conselho/salvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_deliberacao_conselho', '', $row['cd_deliberacao_conselho']);
			echo form_default_integer_ano('nr_ano', 'nr_deliberacao_conselho', 'Ano/Número:(*)', $row['nr_ano'], $row['nr_deliberacao_conselho']);
			echo form_default_date('dt_deliberacao_conselho', 'Dt. Deliberação:(*)', $row);
			echo form_default_textarea('ds_deliberacao_conselho', 'Descrição:(*)', $row, 'style="width:500px; height:60px;"');
			echo form_default_integer('nr_ata', 'Nr Ata:(*)', $row);
			echo form_default_dropdown('fl_situacao', 'Situação:(*)', $arr, array($row['fl_situacao']));
			echo form_default_dropdown_db('cd_deliberacao_conselho_abrangencia', 'Abrangência:(*)', array('gestao.deliberacao_conselho_abrangencia', 'cd_deliberacao_conselho_abrangencia', 'ds_deliberacao_conselho_abrangencia'), array($row['cd_deliberacao_conselho_abrangencia']), '', '', TRUE);
			echo form_default_textarea('observacao', 'Observação:', $row, 'style="width:500px; height:60px;"');
			echo form_default_upload_iframe('arquivo', 'deliberacao_conselho', 'Arquivo:(*)', array($row['arquivo'],$row['arquivo_nome']), 'deliberacao_conselho');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if(intval($row['cd_deliberacao_conselho']) > 0)
			{
				echo button_save("Excluir", 'excluir();', 'botao_vermelho');
				echo button_save("Nova Deliberação", 'novo();', 'botao_disabled');
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>