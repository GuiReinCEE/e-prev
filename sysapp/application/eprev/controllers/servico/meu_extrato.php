<?php
class meu_extrato extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('public/controles_extratos_model');
    }

    function index()
    {
        $this->load->view('servico/meu_extrato/index');
    }

    function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
	
		$args['cd_registro_empregado'] = $this->session->userdata('cd_registro_empregado');
				
		$this->controles_extratos_model->participante($result, $args);
		$participante = $result->row_array();

		$args['cd_empresa'] = $participante['cd_empresa'];
		$args['cd_plano']   = $participante['cd_plano'];
		
		$this->controles_extratos_model->planos($result, $args);
		$planos_patrocinadoras = $result->row_array();
		
		$this->controles_extratos_model->patrocinadora($result, $args);
		$patrocinadora = $result->row_array();
		
		$args["cd_empresa"] = 9;
		$args["cd_registro_empregado"] = intval($participante['cd_registro_empregado']);
		$args["cd_plano"] = intval( $participante['cd_plano'] );

        $this->controles_extratos_model->listar( $result, $args );
		$data['collection'] = $result->result_array();
  
		$data['cd_registro_empregado'] = $participante['cd_registro_empregado'];
		$data['cd_plano']              = $participante['cd_plano'];
		$data['cd_indexador']          = $planos_patrocinadoras['cd_indexador'];
		$data['tp_patrocinadora']      = $patrocinadora['tipo_cliente'];

		$this->load->view('servico/meu_extrato/partial_result', $data);
    }
	
	function imprimir($cd_registro_empregado, $cd_plano, $nr_extrato, $cd_indexador, $tp_patrocinadora, $data_base)
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
				$ar_parametro = array(
					'cd_plano'         => $cd_plano,
					'cd_emp'           => "9",
					'cd_re'            => $cd_registro_empregado,
					'cd_seq'           => "0",
					'nr_extrato'       => $nr_extrato,
					'nr_indexador'     => $cd_indexador,
					'tp_patrocinadora' => $tp_patrocinadora,
					'dt_base_extrato'  => $data_base
				);
				
				$resultado = $this->nusoap_client->call('extratoPDF',$ar_parametro);
				
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
?>