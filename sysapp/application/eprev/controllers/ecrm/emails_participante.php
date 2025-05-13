<?php
class emails_participante extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "")
    {
		if(gerencia_in(array('GAP','GRI','GF', 'GTI', 'GRSC')))
		{
			$data['cd_empresa']            = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia']       = $seq_dependencia;			
			$this->load->view('ecrm/emails_participante/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }


    function listar()
    {
		$this->load->model('projetos/Emails_participante_model');

		$result = null;
		$data = Array();
		$args = Array();

		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		$args["dt_email_ini"]          = $this->input->post("dt_email_ini", TRUE);
		$args["dt_email_fim"]          = $this->input->post("dt_email_fim", TRUE);
		$args["dt_envio_ini"]          = $this->input->post("dt_envio_ini", TRUE);
		$args["dt_envio_fim"]          = $this->input->post("dt_envio_fim", TRUE);
		$args["cpf"]                   = $this->input->post("cpf", TRUE);

		manter_filtros($args);
		
		$this->Emails_participante_model->listar( $result, $args );
		$data['collection'] = $result->result_array();		
		
		$this->load->view('ecrm/emails_participante/partial_result', $data);	
    }
}
