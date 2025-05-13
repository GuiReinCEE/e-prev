<?php
set_title('Relatórios de Auditoria Contábil - Aprovar');
$this->load->view('header');
?>
<script>
	function recusar()
	{
		var confirmacao = 'Deseja recusar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/recusar/".$row['cd_relatorio_auditoria_contabil']); ?>';
		}
	}
    
    function confirmar_aprovacao()
	{
		var confirmacao = 'Deseja aprovar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/confirmar_aprovacao/".$row['cd_relatorio_auditoria_contabil']); ?>';
		}
	}
    
</script>
<?php
$abas[] = array('aba_cadastro', 'Aprovar', TRUE, 'location.reload();');

$body = array();
$head = array( 
	'Código',
	'Dt Inclusão',
	'Arquivo',
	'Usuário'
);

foreach( $collection as $item )
{	
    $body[] = array(
		$item['cd_relatorio_auditoria_contabil_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/relatorio_auditoria_contabil/'.$item['arquivo'], $item['arquivo_nome'], array('target' => "_blank")), "text-align:left;"),
		$item['nome']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_start_box("default_box", "Cadastro");
        echo form_default_hidden("cd_relatorio_auditoria_contabil", "", $row['cd_relatorio_auditoria_contabil']);
        echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']); 
        echo form_default_textarea('ds_relatorio_auditoria_contabil', 'Descrição :', $row, 'style="height:100px;"');
        echo form_default_upload_iframe('arquivo', 'relatorio_auditoria_contabil', 'Arquivo :', array($row['arquivo'], $row['arquivo_nome']), 'relatorio_auditoria_contabil', FALSE);
        if(trim($row['dt_aprovado']) != '')
        {
            echo form_default_row('dt_aprovado', 'Dt. Aprovado :', $row['dt_aprovado']); 
            echo form_default_row('usuario_aprovado', 'Usuário Aprovado :', $row['usuario_aprovado']); 
        }
        
        if(trim($row['dt_recusar']) != '')
        {
            echo form_default_row('dt_recusar', 'Dt. Recusado :', $row['dt_recusar']); 
            echo form_default_row('usuario_recusar', 'Usuário Recusado :', $row['usuario_recusar']); 
        }
    echo form_end_box("default_box");
    echo form_command_bar_detail_start();
    
        if((trim($row['dt_aprovado']) == '') AND (trim($row['dt_recusar']) == '') AND (trim($row['dt_encaminhamento']) != ''))
        {
            echo button_save("Aprovado", 'confirmar_aprovacao();', 'botao_verde');
            echo button_save("Recusar", 'recusar();', 'botao_vermelho');
        }
        
    echo form_command_bar_detail_end();
    echo $grid->render();
echo aba_end();
$this->load->view('footer_interna');
?>