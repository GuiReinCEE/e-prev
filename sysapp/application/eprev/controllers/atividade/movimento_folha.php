<?php
class movimento_folha extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($dt_referencia = "")
    {
		$dt_referencia = (trim($dt_referencia) == "" ? date("Y-m-d") : $dt_referencia);
		$dt_referencia = strtotime($dt_referencia);
		$dt_referencia = date("d/m/Y",$dt_referencia);

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
				#### Relatуrio de Controle de Atualizaзгo - Link enviado por email ####
				$ar_parametro = array("ls_parametros" => "BENR3012.rep;p_dt_envio_email=".$dt_referencia);
				$resultado = $this->nusoap_client->call('execReportPDF',$ar_parametro);

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
					exibir_mensagem("Nгo foi possнvel gerar o documento.");
				}
			}
		}		
	}
}
?>