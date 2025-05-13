<?php
class emprestimo_concedido extends Controller
{
	var $ar_conf;
	
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->ar_conf = getListner();
    }

	function index($cd_contrato = 0, $fl_mostrar_header = "S", $usuario_confirmacao = "A")
	{
		if(intval($cd_contrato) > 0)
		{
			$data['usuario_confirmacao'] = (strtoupper($usuario_confirmacao) != "A" ? "P" : "A"); ## ATENDENTE OU PARTICIPANTE
			$data['fl_mostrar_header'] = (strtoupper($fl_mostrar_header) != "N" ? "S" : "N"); ## EXIBIR CABECALHO E MENU
			$data['SKT_IP']    = $this->ar_conf['IP'];
			$data['SKT_PORTA'] = $this->ar_conf['PORTA'];
			
			$data['row'] = $this->getSocket("fnc_busca_inf_contrato;".intval($cd_contrato));

			#echo "<PRE>"; 
			#print_r($this->ar_conf); 
			#print_r($data); 
			#exit;
			
			$this->load->view('ecrm/emprestimo_concedido/index', $data);
		}
	}

	private function getSocket($cmd = "")
	{
		if(trim($cmd))
		{
			$ar_retorno = Array();
			
			$this->load->plugin('socketfc');
			$ob_socket = new socketfc();
			$ob_socket->SetRemoteHost($this->ar_conf["IP"]);
			$ob_socket->SetRemotePort($this->ar_conf["PORTA"]);
			$ob_socket->SetBufferLength(262144); // 256KB
			$ob_socket->SetConnectTimeOut(1);	
			
			if ($ob_socket->Connect()) 
			{
				$skt_retorno = $ob_socket->Ask($cmd);
				
				if ($ob_socket->Error()) 
				{
					echo("ERRO 3:".br(2).$fnc.br(2).$ob_socket->GetErrStr()); 
					exit;
					#exibir_mensagem("ERRO 3:".br(2).$fnc.br(2).$ob_socket->GetErrStr());
				}	
				else
				{
					$ob_dom = new DOMDocument();
					$ob_dom->loadXML('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$skt_retorno);
					$ob_fld = $ob_dom->getElementsByTagName("fld");				
				
					$ds_erro = $ob_socket->getFieldValueXML($ob_fld, 'ERR');
					
					if(trim($ds_erro) == "NULL") 
					{	
						
						foreach($ob_fld as $campo) 
						{
							$campo->nodeValue = utf8_decode($campo->nodeValue);
							$ar_retorno[strtolower($campo->getAttribute('id'))] = $campo->nodeValue;
							
							#echo $campo->getAttribute('id')." => ".$campo->nodeValue.br(1); 
						}						
						
						return $ar_retorno;
					}
					else
					{
						echo("ERRO 4:".br(2).utf8_decode($ds_erro)); 
						exit;
						#exibir_mensagem("ERRO 4:".br(2).$ds_erro);
					}					
				}
			}
			else 
			{
				echo("ERRO 2:".br(2).$fnc.br(2).$ob_socket->GetErrStr());
				exit;
				#exibir_mensagem("ERRO 2:".br(2).$fnc.br(2).$ob_socket->GetErrStr());
			}		
        }
		else 
		{
			echo("ERRO 1: Sem dados para executar");
			exit;
			#exibir_mensagem("ERRO 1: Sem dados para executar");
			
		}		
	}	
}
