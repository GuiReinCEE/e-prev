<?php
set_title('Circulares - Cadastro');
$this->load->view('header');
?>
<script>
<?=form_default_js_submit(Array('dt_circular', 'ds_circular', 'fl_situacao', 'cd_circular_abrangencia'))?>
    function ir_lista()
    {
        location.href='<?=site_url("gestao/circular")?>';
    }
	
	function excluir()
	{
		var confirmacao = 'Deseja EXCLUIR a Circular?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';	

		if(confirm(confirmacao))
		{
			location.href='<?=site_url("gestao/circular/excluir/".intval($row['cd_circular']))?>'
		}
	}
	
	function novo()
	{
		location.href='<?=site_url("gestao/circular/cadastro/")?>';
	}	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr[] = array('value' => 'N', 'text' => 'Normal');
$arr[] = array('value' => 'R', 'text' => 'Revogada');

echo aba_start( $abas );
    echo form_open('gestao/circular/salvar');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_circular', '', $row['cd_circular']);
			if(intval($row['cd_circular']) > 0)
			{
				echo form_default_integer_ano('nr_ano', 'nr_circular', 'Ano/Número:', $row['nr_ano'], $row['nr_circular']);
			}
			echo form_default_date('dt_circular', 'Dt. Circular:(*)', $row);
			echo form_default_textarea('ds_circular', 'Descrição:(*)', $row, 'style="width:350px; height:60px;"');
			echo form_default_dropdown('fl_situacao', 'Situação:(*)', $arr, array($row['fl_situacao']));
			echo form_default_dropdown_db('cd_circular_abrangencia', 'Abrangência:(*)', array('gestao.circular_abrangencia', 'cd_circular_abrangencia', 'ds_circular_abrangencia'), array($row['cd_circular_abrangencia']), '', '', TRUE);
			echo form_default_textarea('observacao', 'Observação:', $row, 'style="width:350px; height:60px;"');
			echo form_default_upload_iframe('arquivo', 'circular', 'Arquivo:', array($row['arquivo'],$row['arquivo_nome']), 'circular');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if(intval($row['cd_circular']) > 0)
			{
				echo button_save("Excluir", 'excluir();', 'botao_vermelho');
				echo button_save("Nova Circular", 'novo();', 'botao_disabled');
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>