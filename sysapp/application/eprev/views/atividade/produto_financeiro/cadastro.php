<?php
set_title('Acompanhamento de Produtos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('dt_recebido', 'dt_conclusao', 'ds_produto', 'cd_produto_financeiro_origem', 'cd_reuniao_sg_instituicao', 'cd_usuario_responsavel', 'cd_usuario_revisor'));
?>
	
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro"); ?>';
    }
	
	function ir_etapas()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/etapas/".intval($row['cd_produto_financeiro'])); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/anexo/".intval($row['cd_produto_financeiro'])); ?>';
    }
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
if(intval($row['cd_produto_financeiro']) > 0)
{
	$abas[] = array('aba_lista', 'Etapas', FALSE, 'ir_etapas();');
	$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
}

if(((intval($row['cd_produto_financeiro']) > 0) AND (($this->session->userdata('codigo') == $row['cd_usuario_inclusao']) OR ($this->session->userdata('codigo') == $row['cd_usuario_responsavel']) 
	OR ($this->session->userdata('codigo') == $row['cd_usuario_revisor'])  OR (($this->session->userdata('divisao') == 'GIN') AND ($this->session->userdata('tipo') == 'G')))) OR (intval($row['cd_produto_financeiro']) == 0))
{
	$bool = true;
}
else
{
	$bool = false;
}

echo aba_start($abas);

echo form_open('atividade/produto_financeiro/salvar');

	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden("cd_produto_financeiro", "", $row['cd_produto_financeiro']);
		echo form_default_date("dt_recebido", 'Dt Recebido :*', $row['dt_recebido']);
		echo form_default_date("dt_conclusao", 'Dt Conclusão :*', $row['dt_conclusao']);
		echo form_default_text("ds_produto", 'Produto :*', $row['ds_produto'], 'style="width:500px;"');
		echo form_default_dropdown_db("cd_produto_financeiro_origem", "Origem :* ", Array('projetos.produto_financeiro_origem', 'cd_produto_financeiro_origem', 'ds_produto_financeiro_origem'), Array($row['cd_produto_financeiro_origem']), "", "", TRUE);
		echo form_default_dropdown_db("cd_reuniao_sg_instituicao", "Entidade/Fornecedor :* ", Array('projetos.reuniao_sg_instituicao', 'cd_reuniao_sg_instituicao', 'ds_reuniao_sg_instituicao'), Array($row['cd_reuniao_sg_instituicao']), "", "", TRUE);
		echo form_default_textarea("contato", 'Contato :', $row['contato'], 'style="height:100px;"');
		echo form_default_dropdown("cd_usuario_responsavel", "Responsável :* ", $arr_usuarios ,array($row['cd_usuario_responsavel']));
		echo form_default_dropdown("cd_usuario_revisor", "Revisor :* ", $arr_usuarios ,array($row['cd_usuario_revisor']));
	echo form_end_box("default_box");

echo form_command_bar_detail_start();
	 echo ($bool ?  button_save("Salvar") : '');
echo form_command_bar_detail_end();
echo form_close();

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>