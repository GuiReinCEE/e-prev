<?php
class meu_retrato extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
        $this->load->model('meu_retrato/meu_retrato_model');		
    }

    function index()
    {
		if (gerencia_in(array('GAP','GA','AC', 'GRSC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
	
			$this->load->view('ecrm/meu_retrato/index.php', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function listar()
    {
		$result = null;
		$data   = array();
		$args   = array();

		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		
		manter_filtros($args);

        $this->meu_retrato_model->listar($result, $args);
		$data['collection'] = $result->result_array();
        $this->load->view('ecrm/meu_retrato/index_result.php', $data);
    }
}
?>