<?php
class emprestimo_confirma extends Controller
{
	var $ar_conf;
	
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->ar_conf = getListner();
    }

	function simulacao_atendimento($id_simulacao = "", $nr_prestacoes = 0)
	{
		echo $id_simulacao." | ".$nr_prestacoes;
		#https://www.e-prev.com.br/controle_projetos/confirma_emprestimo_dap.php?call=fnc_busca_inf_concessao&session_id=6f2d64acd4485077d3ecd6db9c340b2e&num_prestacoes=1
		
		if((trim($id_simulacao) != "") AND (intval($nr_prestacoes) > 0))
		{
			$url = "https://www.e-prev.com.br/controle_projetos/confirma_emprestimo_dap.php?call=fnc_busca_inf_concessao&session_id=".$id_simulacao."&num_prestacoes=".$nr_prestacoes;
			header('Location: '.$url);
			exit;
		}
		else
		{
			echo("ERRO 1: Sem dados para executar (id_sumulacao e nr_prestacoes)");
			exit;			
		}
	}

	function index($id_simulacao, $nr_prestacoes, $fl_mostrar_header = "S")
	{
		if((trim($id_simulacao) != "") AND (intval($nr_prestacoes) > 0))
		{
			$data['SKT_IP']            = $this->ar_conf['IP'];
			$data['SKT_PORTA']         = $this->ar_conf['PORTA'];
			$data['id_simulacao']      = trim($id_simulacao);
			$data['fl_mostrar_header'] = (strtoupper($fl_mostrar_header) != "N" ? "S" : "N"); ## EXIBIR CABECALHO E MENU

			#### DADOS DA SIMULACAO ####
			$data['row'] = $this->getSocket("fnc_busca_inf_concessao;".trim($id_simulacao).";".intval($nr_prestacoes));
			
			#### TIPO DE CONTRATO ####
			$data['contrato'] = $this->getSocket("fnc_tp_senha_callcenter;".$data['row']['cd_empresa'].";".$data['row']['cd_registro_empregado'].";".$data['row']['seq_dependencia']);

			#### LISTA DE BANCOS ####
			$data['banco'] = $this->getSocket("fnc_combo_bancos;".$data['row']['cd_instituicao']);

			#### LISTA DE AGENCIAS ####
			$data['agencia'] = $this->getSocket("fnc_combo_agencias;".$data['row']['cd_instituicao'].";".$data['row']['cd_agencia']);			
			
			
			#$send = "fnc_combo_agencias;$banco;$agencia";

			#echo "<PRE>"; print_r($this->ar_conf); 	print_r($data); exit;		

			$this->load->view('ecrm/emprestimo_confirma/index', $data);
		}
	}
	
    function agencia()
    {
		#### MONTA LISTA DE AGENCIA ####
		$result = null;
		$data = Array();
		$args = Array();
		
		$args["cd_instituicao"] = $this->input->post("cd_instituicao", TRUE);
		$args["cd_agencia"]     = $this->input->post("cd_agencia", TRUE);

		$ar_agencia = $this->getSocket("fnc_combo_agencias;".$args["cd_instituicao"].";".$args["cd_agencia"]);			
		
		echo json_encode($ar_agencia);
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
							if ($campo->getAttribute('tp') == 'LST') 
							{
								$campo->nodeValue = utf8_decode($campo->nodeValue);
								$ar_retorno['list'][] = array("value" => $campo->getAttribute('value'), "text" => $campo->nodeValue, "selected" => $campo->getAttribute('selected'));
							}
							else
							{
								$campo->nodeValue = utf8_decode($campo->nodeValue);
								$ar_retorno[strtolower($campo->getAttribute('id'))] = $campo->nodeValue;
							}
							
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
