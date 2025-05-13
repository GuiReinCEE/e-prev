<?php
set_title('Atas CCI - Etapas GIN - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array(
		'ds_atas_cci_etapas_investimento', 
		'qt_dias',
		'fl_dia_util',
		'ds_assunto',
		'ds_texto',
		'fl_responsavel',
		'email')
	);
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/atas_cci_etapas_investimento"); ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr_drop[] = array('value' => 'N', 'text' => 'Não');
$arr_drop[] = array('value' => 'S', 'text' => 'Sim');

echo aba_start($abas);
    echo form_open('gestao/atas_cci_etapas_investimento/salvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_atas_cci_etapas_investimento', '', $row['cd_atas_cci_etapas_investimento']);
			echo form_default_text('ds_atas_cci_etapas_investimento', 'Descrição :*', $row, 'style="width:400px;"');
			echo form_default_integer('qt_dias', 'Qt dia(s) a partir da Dt Reunião CCI :*', $row);
			echo form_default_dropdown('fl_dia_util', 'Dia útil :*', $arr_drop, array($row['fl_dia_util']));
			echo form_default_dropdown('fl_responsavel', 'Enviar e-mail Responsável :*', $arr_drop, array($row['fl_responsavel']));
			echo form_default_textarea('email', 'Enviar e-mail Para :*'.br(2)."<i>Informar e-mail's separados<BR>por ponto e vírgula ( ; )</i>", $row, 'style="height:120px;"');
			echo form_default_text('ds_assunto', 'Assunto do e-mail :*', $row, 'style="width:400px;"');
			echo form_default_textarea('ds_texto', 'Texto do e-mail :*', $row, 'style="height:120px;"');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
			echo button_save("Salvar");       
		echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>