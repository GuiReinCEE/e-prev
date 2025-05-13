<?php
set_title('Atividades - Cenário Legal');
$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/legal"); ?>';
    }
	
	function ir_atendimento()
    {
    	location.href='<?php echo site_url('atividade/atividade_atendimento_cenario_legal/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
        //location.href='<?php echo base_url(). "sysapp/application/migre/cad_atividade_atend.php?n=".$row['numero']."&aa=".$row['cd_gerencia_destino']; ?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url('atividade/atividade_historico/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url('atividade/atividade_anexo/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Solicitação', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Atendimento', FALSE, 'ir_atendimento();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

echo aba_start( $abas );
    echo form_open('atividade/atividade_solicitacao/salvar', 'method="post" id="formulario"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('numero', '', $row['numero']);
			echo form_default_hidden('cd_gerencia_destino', '', $row['cd_gerencia_destino']);
			echo form_default_row('numero', 'Número :', '<span class="label">'.trim($row['numero']).'</span>');
            echo form_default_row('dt_cad', 'Dt Solicitação :', $row['dt_cad']);
            echo form_default_row('pertinencia', 'Pertinência :', '<span class="label '.$row["cor_status"].'">'.wordwrap($row['pertinencia_status'], 50, br(), false).'</span>');
            echo form_default_row('link', 'Link :', '<a href="'.base_url('index.php/ecrm/informativo_cenario_legal/legislacao/'.$row["cd_edicao"].'/'.$row["cd_cenario"]).'" target="_blank" style="font-weight:bold;">[Ver o Cenário Legal]</a>');
            echo form_default_row('gerencia_destino', 'Gerência de Destino :', $row['gerencia_destino']);
            echo form_default_text('titulo', 'Título :', $row['titulo'], 'style="width:350px;"');
           	echo form_default_textarea('descricao', 'Descrição da Solicitação :', $row['descricao'], 'style="width:450px; height:150px;"');
            echo form_default_dropdown('cod_atendente', 'Atendente da Atividade :', $arr_atendente, $row['cod_atendente']);
            echo form_default_date('dt_limite', 'Dt. Limite :', $row['dt_limite']);
        echo form_end_box("default_box");
		echo form_command_bar_detail_start();  
			echo button_save("Imprimir", "window.print();", "botao_disabled");
		echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>