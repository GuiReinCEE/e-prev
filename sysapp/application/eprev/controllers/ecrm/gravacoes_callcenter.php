<?php
class gravacoes_callcenter extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "")
    {
		CheckLogin();
	
		if(gerencia_in(array('GRSC','DE')))
		{
			$data['cd_empresa']            = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia']       = $seq_dependencia;
			$this->load->view('ecrm/gravacoes_callcenter/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }


    function listar()
    {
        CheckLogin();
		if(gerencia_in(array('GRSC','DE')))
		{		
			$this->load->model('projetos/Gravacoes_callcenter_model');

			$data['collection'] = array();
			$result = null;

			// --------------------------
			// filtros ...

			$args=array();

			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["dt_gravacao_ini"]       = $this->input->post("dt_gravacao_ini", TRUE);
			$args["dt_gravacao_fim"]       = $this->input->post("dt_gravacao_fim", TRUE);

			// --------------------------
			// listar ...

			$this->Gravacoes_callcenter_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('ecrm/gravacoes_callcenter/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }


}
