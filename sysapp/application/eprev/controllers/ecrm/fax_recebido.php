<?php
class fax_recebido extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
        CheckLogin();
        if(gerencia_in(Array('GP','GFC')))
        {
            $this->load->model('asterisk/Fax_recebido_model');
			
			$args   = Array();
            $data   = Array();
            $result = null;
			
			$this->Fax_recebido_model->destinoCombo( $result, $args );
			$data['ar_destino'] = $result->result_array();			
			
            $this->load->view('ecrm/fax_recebido/index.php',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
    function listar()
    {
        CheckLogin();
        if(gerencia_in(Array('GP','GFC')))
        {		
            $this->load->model('asterisk/Fax_recebido_model');

            $args   = Array();
            $data   = Array();
            $result = null;

            $args["dt_ini"] = $this->input->post("dt_ini", TRUE);
            $args["dt_fim"] = $this->input->post("dt_fim", TRUE);
            $args["destino"]  = $this->input->post("destino", TRUE);
			$args["ar_email"] = Array();
			
			if(gerencia_in(array("I")))
			{
				$args["ar_email"] = Array();
			}
			elseif($this->session->userdata('divisao') == "GP")
			{
				$args["ar_email"][] = "gapfax@eletroceee.com.br";
				$args["ar_email"][] = "gffax@eletroceee.com.br";
			}
			else
			{
				$args["ar_email"][] = strtolower($this->session->userdata('divisao'))."fax@eletroceee.com.br";
			}

            manter_filtros($args);	

            $this->Fax_recebido_model->listar($result, $args);
            $data['ar_lista'] = $result->result_array();

            $this->load->view('ecrm/fax_recebido/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
    
    function acompanhamento($cd_fax = 0)
    {
        CheckLogin();

        if(gerencia_in(Array('GP','GFC')))
        {
            $this->load->model('asterisk/fax_recebido_model');
            
            $args   = Array();
            $data   = Array();
            $result = null;
            
            $args["cd_fax"] = intval($cd_fax);
            $data["cd_fax"] = intval($cd_fax);
            
            $this->fax_recebido_model->listar_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->load->view('ecrm/fax_recebido/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_acompanhamento()
    {
        CheckLogin();

        if(gerencia_in(Array('GP','GFC')))
        {
            $this->load->model('asterisk/fax_recebido_model');
            
            $args   = Array();
            $data   = Array();
            $result = null;
            
            $args["cd_fax"]     = $this->input->post("cd_fax", TRUE);
            $args["descricao"]  = $this->input->post("descricao", TRUE);
            $args['cd_usuario'] = $this->session->userdata('codigo');
            
            $this->fax_recebido_model->salvar_acompanhamento($result, $args);
            
            redirect("ecrm/fax_recebido/", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function ver($cd_fax = 0)
    {
        CheckLogin();

        if(gerencia_in(Array('GP','GFC')))
        {			
            $this->load->model('asterisk/Fax_recebido_model');
            $this->load->library('Nusoap_lib');

            $this->nusoap_client = new nusoap_client('http://srvcentral.eletroceee.com.br/fx/arq.php'); 

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
                    $args   = Array();
                    $data   = Array();
                    $result = null;				

                    $args['cd_fax'] = intval($cd_fax);

                    #### BUSCA NOME DO ARQUIVO ####
                    $this->Fax_recebido_model->getFAX($result, $args);
                    $ar_fax = $result->row_array();				

                    if(count($ar_fax) > 0)
                    {
                        #### BUSCA PDF DO FAX ####
                        $ar_parametro = array('ds_arq'=>$ar_fax['arquivo']);
                        $resultado = $this->nusoap_client->call('getFAX',$ar_parametro);

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
                    else
                    {
                        exibir_mensagem("Não foi encontrado o nome do arquivo.");
                    }
                }
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
    }
	
    function geraFAX($cd_fax = 0, $dir = "../cieprev/up/fax/")
    {
        CheckLogin();

        if(gerencia_in(Array('GAP','GFC')))
        {			
            $this->load->model('asterisk/Fax_recebido_model');
            $this->load->library('Nusoap_lib');

            $this->nusoap_client = new nusoap_client('http://srvcentral.eletroceee.com.br/fx/arq.php'); 

            if($this->nusoap_client->fault)
            {
                return false;
            }
            else
            {
                if ($this->nusoap_client->getError())
                {
                    #exibir_mensagem("ERRO: ".$this->nusoap_client->getError);
                    return false;
                }
                else
                {
                    $args   = Array();
                    $data   = Array();
                    $result = null;				

                    $args['cd_fax'] = intval($cd_fax);

                    #### BUSCA NOME DO ARQUIVO ####
                    $this->Fax_recebido_model->getFAX($result, $args);
                    $ar_fax = $result->row_array();				

                    if(count($ar_fax) > 0)
                    {
                        #### BUSCA PDF DO FAX ####
                        $ar_parametro = array('ds_arq'=>$ar_fax['arquivo']);
                        $resultado = $this->nusoap_client->call('getFAX',$ar_parametro);

                        if(base64_decode($resultado) != "ERRO")
                        {
                            #### ESCREVE ARQUIVO ####
                            $ds_arq = str_replace("recvq/","",str_replace(".tif","",$ar_fax['arquivo'])).".pdf";
                            $ob_arq = fopen($dir.$ds_arq, "w");
                            fwrite($ob_arq, base64_decode($resultado));
                            fclose($ob_arq); 							
                            return $ds_arq;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
            }
        }
        else
        {
            return false;
        }	
    }
	
    function protocoloInterno()
    {
        CheckLogin();

        if(gerencia_in(Array('GP','GFC')))
        {
            $this->load->model('projetos/Digitalizado_model');
            $this->load->model('projetos/documento_recebido_model');

            $arq_selecionado  = $this->input->post("arq_selecionado", TRUE);
            $doc_selecionado  = $this->input->post("doc_selecionado", TRUE);
            $part_selecionado = $this->input->post("part_selecionado", TRUE);

            $ar_arq  = explode(",",$arq_selecionado);
            $ar_doc  = explode(",",$doc_selecionado);
            $ar_part = array_map("explodePipe", explode(",",$part_selecionado));

            #echo "<PRE>".print_r($ar_arq,true)."</PRE>"; #exit;
            #echo  "ssss   <PRE>".print_r($ar_doc,true)."</PRE>"; exit;
            #echo "<PRE>".print_r($ar_part,true)."</PRE>"; #exit;

            #### GERA ARQUIVOS ####
            $ar_arq_lista = Array();
            $nr_conta = 0;
            foreach($ar_arq as $item)
            {
                $dir = "../cieprev/up/fax/";
                $arq = $this->geraFAX($item,$dir);
                if(!$arq)
                {
                    exibir_mensagem("Não foi gerar arquivo.");
                }
                else
                {
                    list($f,$e) = explode('.', $arq);
                    $ar_arq_lista[$nr_conta]['name']      = $f;
                    $ar_arq_lista[$nr_conta]['ext']       = $e;
                    $ar_arq_lista[$nr_conta]['file_name'] = $arq;
                    $ar_arq_lista[$nr_conta]['file']      = $dir.$arq;
                    $ar_arq_lista[$nr_conta]['cd_fax']    = $item;
                }
                $nr_conta++;
            }

            $args   = Array();
            $data   = Array();
            $result = null;

            #echo "<PRE>".print_r($ar_arq_lista,true)."</PRE>"; #exit;

            $ar_documento = Array();
            $nr_conta = 0;
            foreach($ar_arq_lista as $ar_item)
            {
                $arq = md5($ar_item['name']).".".$ar_item['ext'];
                $ar_documento[] = array(
                                        'cd_documento_recebido_item' => 0,
                                        'cd_tipo_doc'           => $ar_doc[$nr_conta],
                                        'cd_empresa'            => $ar_part[$nr_conta][0],
                                        'cd_registro_empregado' => $ar_part[$nr_conta][1],
                                        'seq_dependencia'       => $ar_part[$nr_conta][2],
                                        'nome'                  => $ar_part[$nr_conta][3],
                                        'ds_observacao'         => "",
                                        'nr_folha'              => 1,
                                        'arquivo'               => $arq,
                                        'arquivo_nome'          => $ar_item['file_name'],
                                        'cd_usuario_cadastro'   => usuario_id(),
                                        'cd_fax'                => $ar_item['cd_fax']
                                   );

                #### COPIAR ARQUIVO PARA PASTA ###
                copy($ar_item['file'], "../cieprev/up/documento_recebido/".$arq);
                @unlink($ar_item['file']);
                $nr_conta++;
            }

            #echo "<PRE>".print_r($ar_documento,true)."</PRE>"; exit;

            #### GERA PROTOCOLO ####
            if(count($ar_documento) > 0)
            {
                $args = Array();
                $result = null;

                $args['cd_documento_recebido']      = 0;
                $args["cd_documento_recebido_tipo"] = 2; #FAX
                $args["cd_usuario_cadastro"]        = usuario_id();
                $args["cd_usuario"]                 = usuario_id();

                $cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $args);		

                if($cd_documento_recebido > 0)
                {
                    #### ADICIONA DOCUMENTOS ####			
                    foreach($ar_documento as $ar_item)
                    {
                        $ar_item["cd_documento_recebido"] = $cd_documento_recebido;
						$ar_item["cd_usuario"] = $ar_item['cd_usuario_cadastro'];
                        $this->documento_recebido_model->adicionar_documento($result, $ar_item);
                        
                        $args["cd_fax"]     = $ar_item['cd_fax'];
                        $args["descricao"]  = "RE: ".$ar_item['cd_empresa'].'/'.$ar_item['cd_registro_empregado'].'/'.$ar_item['seq_dependencia']."\n";
                        $args["descricao"] .= "Nome: ".$ar_item['nome']."\n";
                        $args["descricao"] .= "Nr Protocolo Interno: ".$ar_item['cd_tipo_doc'];
                        $args['cd_usuario'] = $ar_item['cd_usuario'];
                        
                        $this->load->model('asterisk/fax_recebido_model');
                        
                        $this->fax_recebido_model->salvar_acompanhamento($result, $args);
                        
                    }

                    redirect("ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh");
                }
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
    }	

	public function get_tempo_descarte($cd_documento = 0)
	{
        $args = array();
        $url  = 'http://10.63.255.217:8080/ords/ordsws/tabela_temporalidade_doc/tempo_descarte/index';

        $args = array(
            'id'          => '8dcfac716cf69a12255d63a4abf8b485',
            'cd_tipo_doc' => $cd_documento
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

    	$vl_arquivo_central = (trim($json['result'][0]['vl_arquivo_central']) != '' ? trim($json['result'][0]['vl_arquivo_central']).' - ' : '');
		$ds_tempo_descarte  = $vl_arquivo_central . $json['result'][0]['id_arquivo_central'];
		$id_classificacao_info_doc  = $json['result'][0]['id_classificacao_info_doc'];

        return array('ds_tempo_descarte' => $ds_tempo_descarte, 'id_classificacao_info_doc' => $id_classificacao_info_doc);
	}
	
    function protocoloDigitalizacao()
    {
        CheckLogin();

        if(gerencia_in(Array('GP','GFC')))
        {
            $this->load->model('projetos/Digitalizado_model');

            $arq_selecionado  = $this->input->post("arq_selecionado", TRUE);
            $doc_selecionado  = $this->input->post("doc_selecionado", TRUE);
            $part_selecionado = $this->input->post("part_selecionado", TRUE);

            $ar_arq  = explode(",",$arq_selecionado);
            $ar_doc  = explode(",",$doc_selecionado);
            $ar_part = array_map("explodePipe", explode(",",$part_selecionado));

            #echo "<PRE>".print_r($ar_arq,true)."</PRE>"; #exit;
            #echo "<PRE>".print_r($ar_doc,true)."</PRE>"; #exit;
            #echo "<PRE>".print_r($ar_part,true)."</PRE>"; #exit;			

            #### GERA ARQUIVOS ####
            $ar_arq_lista = Array();
            $nr_conta = 0;
            foreach($ar_arq as $item)
            {
                $dir = "../cieprev/up/fax/";
                $arq = $this->geraFAX($item,$dir);
                if(!$arq)
                {
                    exibir_mensagem("Não foi gerar arquivo.");
                }
                else
                {
                    list($f,$e) = explode('.', $arq);
                    $ar_arq_lista[$nr_conta]['name']      = $f;
                    $ar_arq_lista[$nr_conta]['ext']       = $e;
                    $ar_arq_lista[$nr_conta]['file_name'] = $arq;
                    $ar_arq_lista[$nr_conta]['file']      = $dir.$arq;
                    $ar_arq_lista[$nr_conta]['cd_fax']    = $item;
                }
                $nr_conta++;
            }			

            $args   = Array();
            $data   = Array();
            $result = null;

            #echo "<PRE>".print_r($ar_arq_lista,true)."</PRE>"; #exit;

            $ar_documento = Array();
            $nr_conta = 0;
            foreach($ar_arq_lista as $ar_item)
            {
                $ar_documento[] = array(
                                    'cd_tipo_doc'           => $ar_doc[$nr_conta],
                                    'cd_empresa'            => $ar_part[$nr_conta][0],
                                    'cd_registro_empregado' => $ar_part[$nr_conta][1],
                                    'seq_dependencia'       => $ar_part[$nr_conta][2],
                                    'nome'                  => $ar_part[$nr_conta][3],
                                    'arquivo'               => $ar_item['file'],
                                    'arquivo_nome'          => $ar_item['name'],
                                    'arquivo_extensao'      => $ar_item['ext'],
                                    'arquivo_completo'      => $ar_item['file_name'],
                                    'cd_usuario_cadastro'   => usuario_id(),
                                    'cd_fax'                => $ar_item['cd_fax']
                               );
                $nr_conta++;
            }

            #echo "<PRE>".print_r($ar_documento,true)."</PRE>"; exit;

            #### GERA PROTOCOLO ####
            if(count($ar_documento) > 0)
            {
                $this->load->model('projetos/Documento_protocolo_model');

                #### PROTOCOLO DIGITAL CERTIFICADOS ####
                $args['cd_usuario_cadastro'] = intval(usuario_id());
				$args['cd_gerencia']         = $this->session->userdata('divisao');
                $args['ano'] = date('Y');
                $args['tipo_protocolo'] = "D";
                $args['fl_contrato'] = "";
                $ar_protocolo = Array();
                $this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);		

                if(count($ar_documento) > 0)
                {
                    #### ADICIONA DOCUMENTOS ####
                    foreach($ar_documento as $ar_reg)
                    {
						$tempo_descarte = $this->get_tempo_descarte($ar_reg['cd_tipo_doc']);
						
                        $prot = Array();
                        $prot['cd_documento_protocolo']      = intval($ar_protocolo['cd_documento_protocolo']);
                        $prot['cd_documento_protocolo_item'] = 0;
                        $prot['cd_empresa']                  = $ar_reg['cd_empresa'];
                        $prot['cd_registro_empregado']       = $ar_reg['cd_registro_empregado'];
                        $prot['seq_dependencia']             = $ar_reg['seq_dependencia'];
                        $prot['nr_folha']                    = 1;
                        $prot['cd_usuario_cadastro']         = intval(usuario_id());
                        $prot['observacao']                  = "";
                        $prot['ds_processo']                 = "";		
                        $prot['cd_documento']                = $ar_reg['cd_tipo_doc'];

                        $arq = $ar_reg['cd_empresa']."_".$ar_reg['cd_registro_empregado']."_".$ar_reg['seq_dependencia']."_".$ar_reg['cd_tipo_doc']."_".uniqid(time()).".pdf";
                        $dir = "up/protocolo_digitalizacao_".intval($ar_protocolo['cd_documento_protocolo'])."/";

                        $prot['arquivo']                     = $arq;
                        $prot['arquivo_nome']                = $arq;		

                        $prot['ds_tempo_descarte']           = utf8_decode($tempo_descarte['ds_tempo_descarte']);
						$prot['id_classificacao_info_doc']   = utf8_decode($tempo_descarte['id_classificacao_info_doc']);		

                        #### COPIAR ARQUIVO PARA PASTA ####
                        copy($ar_reg['arquivo'], "../cieprev/".$dir.$arq);	
                        @unlink($ar_reg['arquivo']);						

                        $this->Documento_protocolo_model->adicionaDocumento($result, $prot);		
                        
                        $args["cd_fax"]     = $ar_reg['cd_fax'];
                        $args["descricao"]  = "RE: ".$ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia']."\n";
                        $args["descricao"] .= "Nome: ".$ar_reg['nome']."\n";
                        $args["descricao"] .= "Nr Protocolo Digitalização: ".$ar_reg['cd_tipo_doc'];
                        $args['cd_usuario'] = $ar_reg['cd_usuario_cadastro'];
                        
                        $this->load->model('asterisk/fax_recebido_model');
                        
                        $this->fax_recebido_model->salvar_acompanhamento($result, $args);
                    }
                }

                redirect("ecrm/protocolo_digitalizacao/detalhe_atendimento/".$ar_protocolo['cd_documento_protocolo'], "refresh");
            }

        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }	
    }	
}
