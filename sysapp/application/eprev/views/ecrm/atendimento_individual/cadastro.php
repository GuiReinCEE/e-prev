<?php
set_title('Atendimento Invidualizado');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/atendimento_individual"); ?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url("ecrm/atendimento_individual/acompanhamento/".intval($row['cd_atendimento_individual'])); ?>';
    }
	
	function encaminhar()
    {
		if(confirm('Deseja encaminhar?'))
		{
			location.href='<?php echo site_url("ecrm/atendimento_individual/encaminhar/".intval($row['cd_atendimento_individual'])); ?>';
		}
    }
	
	function encerrar()
    {
		if(confirm('Deseja encerrar?'))
        {
            $.post('<?php echo site_url('ecrm/atendimento_individual/encerrar'); ?>',
            {
                cd_atendimento_individual : $('#cd_atendimento_individual').val()
            },
            function(data)
            {
                location.href='<?php echo site_url("ecrm/atendimento_individual"); ?>';
            });
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_atendimento_individual']) > 0)
{
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
}
$arr_participante = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome');

$arr_participante_value = array(
	'cd_empresa'            => $row['cd_empresa'], 
	'cd_registro_empregado' => $row['cd_registro_empregado'], 
	'seq_dependencia'       => $row['seq_dependencia']
);

echo aba_start( $abas );
    echo form_open('ecrm/atendimento_individual/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atendimento_individual', '', $row['cd_atendimento_individual']);
			if(intval($row['cd_atendimento_individual']) > 0)
			{
				echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']);
			}
			echo form_default_participante($arr_participante, 'Participante (Emp/RE/Seq) :*', $arr_participante_value, true, true, 'callback_participante();');
			echo form_default_text('nome', 'Nome :*', $row, 'style="width:350px;"');
			echo form_default_textarea('ds_observacao', 'Observação :', $row, 'style="height:150px;"');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
			if(trim($row['dt_encaminhamento']) == '')
			{
				echo button_save("Salvar");
			}
			if((intval($row['cd_atendimento_individual']) > 0) AND (trim($row['dt_encaminhamento']) == ''))
			{
				echo button_save("Iniciar", 'encaminhar();', 'botao_verde');
			}
			if((intval($row['cd_atendimento_individual']) > 0) AND (trim($row['dt_encaminhamento']) != '') AND (trim($row['dt_encerramento']) == ''))
			{
				echo button_save("Encerrar", 'encerrar();', 'botao_vermelho');
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>