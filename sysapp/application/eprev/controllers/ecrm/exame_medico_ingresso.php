<?php
class exame_medico_ingresso extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GAP','GAD')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');
			$args = Array();	
			$data = Array();			
			$this->load->view('ecrm/exame_medico_ingresso/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function exameMedicoListar()
    {
        CheckLogin();
		if(gerencia_in(array('GAP','GAD')))
		{		
			$this->load->model('projetos/Exame_medico_ingresso_model');

			$result = null;
			$data = Array();
			$args = Array();

			$args["dt_inclusao_ini"] = $this->input->post("dt_inclusao_ini", TRUE);
			$args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);
			$args["dt_envio_ini"]    = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]    = $this->input->post("dt_envio_fim", TRUE);			
			$args["dt_recebido_ini"] = $this->input->post("dt_recebido_ini", TRUE);
			$args["dt_recebido_fim"] = $this->input->post("dt_recebido_fim", TRUE);			
			
			manter_filtros($args);
			
			$this->Exame_medico_ingresso_model->exameMedicoListar( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/exame_medico_ingresso/index_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	

    function detalhe($cd_exame_medico_ingresso = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP','GAD')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');
			$args=array();	
			$data['cd_exame_medico_ingresso'] = intval($cd_exame_medico_ingresso);
			
			if(intval($cd_exame_medico_ingresso) == 0)
			{
				$data['row'] = Array('cd_exame_medico_ingresso'=>0,
								   'nome' => '', 
								   'cd_empresa' => '', 
								   'cd_registro_empregado' => '', 
								   'seq_dependencia' => '', 
								   'telefone' => '', 
								   'celular' => '', 
								   'telefone_comercial' => '', 
								   'email' => '', 
								   'pedido_inscricao_local' => '', 
								   'dt_envio_exame' => '', 
								   'cd_usuario_envio_exame' => '',  
								   'dt_recebido_exame' => '', 
								   'cd_usuario_recebido_exame' => '',  
								   'dt_inclusao' => '', 
								   'cd_usuario_inclusao' => '',  
								   'dt_exclusao' => '', 
								   'cd_usuario_exclusao	' => ''
				                     );
			}
			else
			{
				$args['cd_exame_medico_ingresso'] = intval($cd_exame_medico_ingresso);
				$this->Exame_medico_ingresso_model->exameMedico($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/exame_medico_ingresso/detalhe.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function exameMedicoSalvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP','GAD')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_exame_medico_ingresso"] = $this->input->post("cd_exame_medico_ingresso", TRUE);
			$args["cd_empresa"]               = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"]    = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]          = $this->input->post("seq_dependencia", TRUE);
			$args["nome"]                     = $this->input->post("nome", TRUE);
			$args["telefone"]                 = $this->input->post("telefone", TRUE);
			$args["celular"]                  = $this->input->post("celular", TRUE);
			$args["telefone_comercial"]       = $this->input->post("telefone_comercial", TRUE);
			$args["email"]                    = $this->input->post("email", TRUE);
			$args["pedido_inscricao_local"]   = $this->input->post("pedido_inscricao_local", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$cd_exame_medico_ingresso_new = $this->Exame_medico_ingresso_model->exameMedicoSalvar( $result, $args );
			redirect("ecrm/exame_medico_ingresso/detalhe/".$cd_exame_medico_ingresso_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function exameMedicoExcluir($cd_exame_medico_ingresso = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_exame_medico_ingresso"] = intval($cd_exame_medico_ingresso);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			$this->Exame_medico_ingresso_model->exameMedicoExcluir( $result, $args );
			redirect("ecrm/exame_medico_ingresso/detalhe/".intval($cd_exame_medico_ingresso), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

    function exameMedicoEnviar($cd_exame_medico_ingresso = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_exame_medico_ingresso"] = intval($cd_exame_medico_ingresso);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			$this->Exame_medico_ingresso_model->exameMedicoEnviar( $result, $args );
			redirect("ecrm/exame_medico_ingresso/detalhe/".intval($cd_exame_medico_ingresso), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

    function exameMedicoReceber($cd_exame_medico_ingresso = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GAD')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_exame_medico_ingresso"] = intval($cd_exame_medico_ingresso);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			$this->Exame_medico_ingresso_model->exameMedicoReceber( $result, $args );
			redirect("ecrm/exame_medico_ingresso/detalhe/".intval($cd_exame_medico_ingresso), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
    
    function acompanhamento($cd_exame_medico_ingresso = 0)
    {
		CheckLogin();
		if(gerencia_in(array('GAP','GAD')))
		{
			$this->load->model('projetos/Exame_medico_ingresso_model');
			$args=array();	
			$args['cd_exame_medico_ingresso'] = intval($cd_exame_medico_ingresso);
			$data['cd_exame_medico_ingresso'] = intval($cd_exame_medico_ingresso);

			$this->Exame_medico_ingresso_model->acompanhamentoListar( $result, $args );
			$data['ar_acompanhamento'] = $result->result_array();		

			$this->load->view('ecrm/exame_medico_ingresso/acompanhamento.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
    function acompanhamentoSalvar()
    {
		CheckLogin();
		if(gerencia_in(array('GAP','GAD')))
		{		
			$this->load->model('projetos/Exame_medico_ingresso_model');
			
			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_exame_medico_ingresso"] = $this->input->post("cd_exame_medico_ingresso", TRUE);
			$args["acompanhamento"]           = $this->input->post("acompanhamento", TRUE);
			$args["cd_usuario"]               = $this->session->userdata('codigo');
			
			$this->Exame_medico_ingresso_model->acompanhamentoSalvar($result, $args);
			redirect("ecrm/exame_medico_ingresso/acompanhamento/".$args["cd_exame_medico_ingresso"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }	
}
