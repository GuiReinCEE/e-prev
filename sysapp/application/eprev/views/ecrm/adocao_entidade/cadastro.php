<?php
set_title('Adoção de Entidades');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('ds_adocao_entidade', 'cd_adocao_entidade_periodo', 'fl_adocao_entidade_tipo'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/adocao_entidade"); ?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url("ecrm/adocao_entidade/acompanhamento/".intval($row['cd_adocao_entidade'])); ?>';
    }
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_adocao_entidade']) > 0)
{
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
}

$arr_tipo[] = array('value' => 'C', 'text' => 'Crianças');
$arr_tipo[] = array('value' => 'I', 'text' => 'Idosos');

echo aba_start( $abas );
    echo form_open('ecrm/adocao_entidade/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_adocao_entidade', '', $row['cd_adocao_entidade']);
			echo form_default_text('ds_adocao_entidade', 'Nome :*', $row, 'style="width:350px;"');
			if(intval($row['cd_adocao_entidade']) == 0)
			{
				echo form_default_dropdown_db('cd_adocao_entidade_periodo', 'Período :*', array('projetos.adocao_entidade_periodo', 'cd_adocao_entidade_periodo', 'ds_adocao_entidade_periodo'), array($row['cd_adocao_entidade_periodo']), '', '', TRUE);
			}
			else
			{
				echo form_default_row('cd_adocao_entidade_periodo', 'Período :*', $row['ds_adocao_entidade_periodo']);
			}
			echo form_default_dropdown('fl_adocao_entidade_tipo', 'Tipo :*', $arr_tipo, array($row['fl_adocao_entidade_tipo']));   
			
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
			echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>