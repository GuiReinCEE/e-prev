<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    // ---------------------------------------------
    if ( (trim($_POST['cd_atendimento']) != '') and (trim($_POST['cd_encaminhamento']) != '') )
    {
		$args['cd_usuario_logado'] = usuario_id();
		$args['cd_atendimento'] = $_POST['cd_atendimento'];
		$args['cd_encaminhamento'] = $_POST['cd_encaminhamento'];
		
		// *** Atendimento Encaminhamento : Cancelar/Encerrar
		$tthis->load->model('projetos/Atendimento_encaminhamento_model');
        if($_POST["tipo_operacao"]=="cancelar")
        {
            $tthis->Atendimento_encaminhamento_model->cancelar($args);
        }
        else if($_POST["tipo_operacao"]=="encerrar") 
        {
        	$tthis->Atendimento_encaminhamento_model->encerrar($args);
        }
        
        // *** Atendimento : Encaminhar
		$tthis->load->model('projetos/Atendimento_model');
        $tthis->Atendimento_model->encaminhar($args);
        
        // *** Redirecionar

		header("location: cad_encaminhamento_aberto.php?at=".intval($_POST['cd_atendimento']));			
	}
?>