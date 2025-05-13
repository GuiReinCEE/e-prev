<?php
set_title('Resoluções de Diretoria - Cadastro');
$this->load->view('header');
?>
<script>
<?=form_default_js_submit(Array('nr_resolucao_diretoria', 'nr_ano', 'dt_resolucao_diretoria', 'ds_resolucao_diretoria', 'nr_ata', 'fl_situacao', 'cd_resolucao_diretoria_abrangencia'), 'valida_arquivo(form);')?>
    function ir_lista()
    {
        location.href='<?=site_url("gestao/resolucao_diretoria")?>';
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
		var confirmacao = 'Deseja EXCLUIR a Resolução?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';	

		if(confirm(confirmacao))
		{
			location.href='<?=site_url("gestao/resolucao_diretoria/excluir/".intval($row['cd_resolucao_diretoria']))?>'
		}
	}
    
	function novo()
	{
		location.href='<?=site_url("gestao/resolucao_diretoria/cadastro/")?>';
	}		
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr[] = array('value' => 'N', 'text' => 'Normal');
$arr[] = array('value' => 'R', 'text' => 'Revogada');

echo aba_start( $abas );
    echo form_open('gestao/resolucao_diretoria/salvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_resolucao_diretoria', '', $row['cd_resolucao_diretoria']);
			echo form_default_integer_ano('nr_ano', 'nr_resolucao_diretoria', 'Ano/Número:(*)', $row['nr_ano'], $row['nr_resolucao_diretoria']);
			echo form_default_date('dt_resolucao_diretoria', 'Dt. Resolução:(*)', $row);
			echo form_default_textarea('ds_resolucao_diretoria', 'Descrição:(*)', $row, 'style="width:500px; height:60px;"');
			echo form_default_integer('nr_ata', 'Nr Ata:(*)', $row);
			echo form_default_text('rds', 'RDS:', $row,'style="width:500px;"');
			echo form_default_text('area', 'Área/Div./Ger.:', $row,'style="width:500px;"');
			echo form_default_dropdown('fl_situacao', 'Situação:(*)', $arr, array($row['fl_situacao']));
			echo form_default_dropdown_db('cd_resolucao_diretoria_abrangencia', 'Abrangência:(*)', array('gestao.resolucao_diretoria_abrangencia', 'cd_resolucao_diretoria_abrangencia', 'ds_resolucao_diretoria_abrangencia'), array($row['cd_resolucao_diretoria_abrangencia']), '', '', TRUE);
			echo form_default_textarea('observacao', 'Observação:', $row, 'style="width:500px; height:60px;"');
			echo form_default_upload_iframe('arquivo', 'resolucao_diretoria', 'Arquivo:(*)', array($row['arquivo'],$row['arquivo_nome']), 'resolucao_diretoria');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if(intval($row['cd_resolucao_diretoria']) > 0)
			{
				echo button_save("Excluir", 'excluir();', 'botao_vermelho');
				echo button_save("Nova Resolução", 'novo();', 'botao_disabled');
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>