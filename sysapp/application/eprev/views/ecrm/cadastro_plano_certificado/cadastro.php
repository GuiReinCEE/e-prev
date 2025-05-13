<?php
set_title('Cadastro Planos Certificado');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(array('nome_certificado'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/cadastro_plano_certificado"); ?>';
    }
	
	function imprimir()
	{
		location.href='<?php echo site_url("ecrm/cadastro_plano_certificado/imprimir/".$row['cd_plano']."/".$row['versao_certificado']); ?>';
	}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('ecrm/cadastro_plano_certificado/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_plano', '', $row);
            echo form_default_hidden('versao_certificado', '', $row);
			echo form_default_text('cd_plano_h', "Plano :", $row['descricao'], "style='font-weight: bold;width:100%;border: 0px;' readonly" );
			echo form_default_text('versao_certificado_h', "Versão :", $row['versao_certificado'], "style='font-weight: bold;width:100%;border: 0px;' readonly" );
			echo form_default_text('nome_certificado', 'Nome Certificado :*', $row, 'style="width:400px;"');
			echo form_default_text('cd_spc', 'Código SPC :', $row);
            echo form_default_date('dt_aprovacao_spc', 'Dt Aprovação SPC :', $row);
			echo form_default_date_interval('dt_inicio', 'dt_final', 'Período Vigência Regulamento :', $row['dt_inicio'], $row['dt_final']); 
			echo form_default_integer('pos_imagem', 'Posição :', $row);
			echo form_default_integer('largura_imagem', 'Largura :', $row);
			echo form_default_integer('nr_largura_logo', 'Largura Logo :', $row);
			echo form_default_integer('nr_altura_logo', 'Altura Logo :', $row);
			echo form_default_integer('nr_x_logo', 'Posição X Logo :', $row);
			echo form_default_numeric('nr_fonte_verso', 'Fonte Verso :', number_format($row['nr_fonte_verso'],2,',','.'));
			echo form_default_numeric('nr_altura_linha_verso', 'Altura Linha Verso :', number_format($row['nr_altura_linha_verso'],2,',','.'));
			echo form_default_text('presidente_nome', 'Nome Presidente :', $row, 'style="width:400px;"');
			echo form_default_text('presidente_assinatura', 'Assinatura Presidente :', $row, 'style="width:400px;"');
			echo form_default_row('', '', '<img src="'.base_url().'img/certificado/'.$row['presidente_assinatura'].'"/>');
            echo form_default_textarea('coluna_1', 'Texto Coluna 1:', $row);
            echo form_default_textarea('coluna_2', 'Texto Coluna 2:', $row);
			echo form_default_row('', 'Logo :', '<img src="'.base_url().'img/certificado_logo_plano_'.$row['cd_plano'].'.jpg"/>');
		echo form_end_box( "default_box" );
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
            echo button_save("Imprimir Verso", 'imprimir()', 'botao_disabled');
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>