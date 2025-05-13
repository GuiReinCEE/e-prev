<?php
class documento_participante extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRSC')))
		{
			$this->load->view('ecrm/documento_participante/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }


    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Documento_participante_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		$args["dt_documento_ini"]      = $this->input->post("dt_documento_ini", TRUE);
		$args["dt_documento_fim"]      = $this->input->post("dt_documento_fim", TRUE);

		// --------------------------
		// listar ...

        $this->Documento_participante_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/documento_participante/partial_result', $data);
    }

	function documento($ds_arq)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$this->load->library('Nusoap_lib');
		
		$this->nusoap_client = new nusoap_client('http://10.63.255.16:1111/server.php'); 
		
		if($this->nusoap_client->fault)
		{
			exibir_mensagem("ERRO: ".$this->nusoap_client->fault);
		}
		else
		{
			if ($this->nusoap_client->getError())
			{
				exibir_mensagem("ERRO: ".$this->nusoap_client->getError);
			}
			else
			{
				$ar_parametro = array('ds_arq' => base64_decode($ds_arq));
				
				$resultado = $this->nusoap_client->call('converteImgParaPDF',$ar_parametro);
				
				if(base64_decode($resultado) != "ERRO")
				{
					header('Content-Type: application/pdf');
					header("Cache-Control: public, must-revalidate");
					header("Pragma: hack");
					header('Content-Disposition: inline; filename="doc.pdf"');
					header("Content-Transfer-Encoding: binary");
					echo base64_decode($resultado);
					exit;
				}
				else
				{
					exibir_mensagem("Não foi possível gerar o documento.");
				}
			}
		}
	}	
}
