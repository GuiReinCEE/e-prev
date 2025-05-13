<?php
class contrato_avaliacao_resultado extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_avaliacao=0)
    {
		CheckLogin();

        $this->load->model('consultas/Contrato_resultado_controle_model');
        $this->load->model('consultas/Contrato_resultado_resposta_model');
        $this->load->model('consultas/Contrato_resultado_final_model');

        $data['cd_avaliacao'] = intval($cd_avaliacao);

		$data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$count = 0;
		$args=array();

		$args["avaliacao"] = intval($cd_avaliacao);


		// --------------------------
		// listar ...

		// RESULTADO

        $this->Contrato_resultado_controle_model->listar( $result, $count, $args );

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

		// RESPOSTAS

		$this->Contrato_resultado_resposta_model->listar( $result, $args );
		$data['collection_resposta'] = array();

        if( $result )
        {
            $data['collection_resposta'] = $result->result_array();
        }

		// FINAL

		$this->Contrato_resultado_final_model->listar( $result, $count, $args );
		$data['resultado_final'] = 0;
        if( $result )
        {
			$rf = $result->row_array();
			if($rf)
			{
	            $data['resultado_final'] = $rf['vl_resultado'];
			}
        }

        // --------------------------

		$this->load->view('cadastro/contrato_avaliacao_resultado/index.php', $data);
    }
}
