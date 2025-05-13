<?php
set_title('Plano Fiscal - Indicadores PGA - Diretoria Assinar');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_plano_fiscal_indicador', 'cd_diretoria'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_indicador/diretoria_assinar"); ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('gestao/plano_fiscal_indicador/salvar_diretoria_assinar', 'name="filter_bar_form_cadastro"');
        echo form_start_box("default_box", "Parece");
            echo form_default_hidden('cd_plano_fiscal_indicador', '', $row['cd_plano_fiscal_indicador']);
			echo form_default_hidden('cd_diretoria', '', $row['cd_diretoria']);
            echo form_default_row('', 'Ano/Mês :', $row['nr_ano_mes']);
			echo form_default_row('', 'Diretoria :', '<span class="label label-inverse">'.$row['cd_diretoria'].'</span>');
			echo form_default_row('', 'Dt. Limite :', '<span class="'.trim($row['class_dt_limite']).'">'.$row['dt_limite_diretoria'].'</span>');
			
			if(trim($row['dt_inclusao']) != '')
			{
				echo form_default_row('', 'Dt. Assinatura :', '<span class="label label-success">'.$row['dt_inclusao'].'</span>');
				echo form_default_row('', 'Usuário  :', $row['nome']);
			}
        
		echo form_end_box("default_box");
		echo form_start_box("default_pdf_box", "PDF");
			echo '<center><iframe id="iframeParecer" height="600px;" width="100%;"  style="height:500px width:600px;" src="'.site_url("gestao/plano_fiscal_indicador/imprimirPDF/".$row['cd_plano_fiscal_indicador']).'"></iframe></center>';
		echo form_end_box("default_pdf_box");
			echo form_command_bar_detail_start();   
				if((trim($row['dt_inclusao']) == '') AND (trim($row['dt_encerra']) == '') )
				{
					echo button_save("Assinar");
				}
			echo form_command_bar_detail_end();
    echo form_close();
    echo br(3);	
echo aba_end();

$this->load->view('footer_interna');
?>