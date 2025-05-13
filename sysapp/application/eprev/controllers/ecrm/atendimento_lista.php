<?php
class atendimento_lista extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($emp_ga='', $re_ga='', $seq_ga='', $id_atendente = '', $dt = '', $tp_atendimento = '')
    {
		CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;			
			
			$data['tipo_dd'][] = array( 'value'=>'', 'text'=>'Todos' );
			$data['tipo_dd'][] = array( 'value'=>'A', 'text'=>'Pessoal/Telefônico com Gravação/Email' );
			$data['tipo_dd'][] = array( 'value'=>'C', 'text'=>'Consulta' );
			$data['tipo_dd'][] = array( 'value'=>'P', 'text'=>'Pessoal' );
			$data['tipo_dd'][] = array( 'value'=>'T', 'text'=>'Telefônico' );
			$data['tipo_dd'][] = array( 'value'=>'E', 'text'=>'Email' );
		
			$data['obs_dd'][] = array( 'value'=>'', 'text'=>'Todos' );
			$data['obs_dd'][] = array( 'value'=>'R', 'text'=>'Reclamação/Sugestão' );
			$data['obs_dd'][] = array( 'value'=>'O', 'text'=>'Observação/Elogio' );
			$data['obs_dd'][] = array( 'value'=>'E', 'text'=>'Encaminhamento' );
			$data['obs_dd'][] = array( 'value'=>'T', 'text'=>'Retorno' );
			
			$data['atendente_dd'] = $this->Atendimento_model->listar_atendente_dd();
			
			$this->Atendimento_model->listar_programa_dd($result, $args);
			$data['programa_dd'] = $result->result_array();
					
            $data['emp'] = (intval($emp_ga) == 0 ? '' : intval($emp_ga));
            $data['re']  = (intval($re_ga) == 0 ? '' : intval($re_ga));
            $data['seq'] = (intval($seq_ga) == 0 ? '' : intval($seq_ga));
            $data['id_atendente'] = $id_atendente;
            $data['tp_atendimento'] = $tp_atendimento;
            #$data['dt'] = $dt;
            
			$this->load->view('ecrm/atendimento_lista/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;
			
            $args["cd_atendimento"]        = $this->input->post("cd_atendimento", TRUE);
			$args["dt_inicio"]             = $this->input->post("dt_inicio", TRUE);
			$args["dt_fim"]                = $this->input->post("dt_fim", TRUE);
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = intval($this->input->post("cd_registro_empregado", TRUE));
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["id_atendente"]          = intval($this->input->post("id_atendente", TRUE));
			$args["tipo_atendimento"]      = $this->input->post("tipo_atendimento", TRUE);
			$args["obs"]                   = $this->input->post("obs", TRUE);
			$args["cd_programa_fceee"]     = $this->input->post("cd_programa_fceee", TRUE);

			#manter_filtros($args);

			if(trim($args["cd_empresa"])=="") $args["cd_empresa"]=-1;
			if(trim($args["seq_dependencia"])=="") $args["seq_dependencia"]=-1;			
			
			$this->Atendimento_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
            
			$this->load->view('ecrm/atendimento_lista/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function detalhe($cd=0)
	{
		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			exibir_mensagem('Seu usuário não consegue visualizar as telas desse sistema, por favor consulte a Gerência de Atendimento ao Participante para as obter as informações que necessita.');
		}
		else
		{
			$sql = " SELECT * FROM projetos.atendimento ";
			$row=array();
			$query = $this->db->query( $sql . ' LIMIT 1 ' );
			$fields = $query->field_data();
			foreach( $fields as $field )
			{
				$row[$field->name] = '';
			}

			if( intval($cd)>0 )
			{
				$sql .= " WHERE cd_atendimento=".intval($cd)." ";
				$query=$this->db->query($sql);
				$row=$query->row_array();
			}

			if($row) $data['row'] = $row;
			$this->load->view('ecrm/atendimento_lista/detalhe', $data);
		}
	}

    function atendente()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $data['tipo_dd'][] = array( 'value'=>'', 'text'=>'Todos' );
			$data['tipo_dd'][] = array( 'value'=>'C', 'text'=>'Consulta' );
			$data['tipo_dd'][] = array( 'value'=>'P', 'text'=>'Pessoal' );
			$data['tipo_dd'][] = array( 'value'=>'T', 'text'=>'Telefônico' );
			$data['tipo_dd'][] = array( 'value'=>'E', 'text'=>'Email' );

            $this->load->view('ecrm/atendimento_lista/atendente.php', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar_atendente()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->library('charts');
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["dt_inicio"]        = $this->input->post("dt_inicio", TRUE);
			$args["dt_fim"]           = $this->input->post("dt_fim", TRUE);
			$args["tipo_atendimento"] = $this->input->post("tipo_atendimento", TRUE);

            manter_filtros($args);

            $this->Atendimento_model->listar_atendente( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/atendimento_lista/atendente_result', $data);
            
        }
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function data()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $data['tipo_dd'][] = array( 'value'=>'', 'text'=>'Todos' );
			$data['tipo_dd'][] = array( 'value'=>'C', 'text'=>'Consulta' );
			$data['tipo_dd'][] = array( 'value'=>'P', 'text'=>'Pessoal' );
			$data['tipo_dd'][] = array( 'value'=>'T', 'text'=>'Telefônico' );
			$data['tipo_dd'][] = array( 'value'=>'E', 'text'=>'Email' );

            $this->load->view('ecrm/atendimento_lista/data.php', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar_data()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->library('charts');
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["dt_inicio"]        = $this->input->post("dt_inicio", TRUE);
			$args["dt_fim"]           = $this->input->post("dt_fim", TRUE);
			$args["tipo_atendimento"] = $this->input->post("tipo_atendimento", TRUE);

            manter_filtros($args);

            $this->Atendimento_model->listar_data( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/atendimento_lista/data_result', $data);
        }
    }

    function tipo()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $data['tipo_dd'][] = array( 'value'=>'', 'text'=>'Todos' );
			$data['tipo_dd'][] = array( 'value'=>'C', 'text'=>'Consulta' );
			$data['tipo_dd'][] = array( 'value'=>'P', 'text'=>'Pessoal' );
			$data['tipo_dd'][] = array( 'value'=>'T', 'text'=>'Telefônico' );
			$data['tipo_dd'][] = array( 'value'=>'E', 'text'=>'Email' );

            $data['atendente_dd'] = $this->Atendimento_model->listar_atendente_dd();

            $this->load->view('ecrm/atendimento_lista/tipo.php', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar_tipo()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->library('charts');
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["dt_inicio"]        = $this->input->post("dt_inicio", TRUE);
			$args["dt_fim"]           = $this->input->post("dt_fim", TRUE);
			$args["tipo_atendimento"] = $this->input->post("tipo_atendimento", TRUE);
            $args["id_atendente"]     = intval($this->input->post("id_atendente", TRUE));

            manter_filtros($args);

            $this->Atendimento_model->listar_tipo( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/atendimento_lista/tipo_result', $data);
        }
    }

    function programa()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $data['tipo_dd'][] = array( 'value'=>'', 'text'=>'Todos' );
			$data['tipo_dd'][] = array( 'value'=>'C', 'text'=>'Consulta' );
			$data['tipo_dd'][] = array( 'value'=>'P', 'text'=>'Pessoal' );
			$data['tipo_dd'][] = array( 'value'=>'T', 'text'=>'Telefônico' );
			$data['tipo_dd'][] = array( 'value'=>'E', 'text'=>'Email' );

            $data['atendente_dd'] = $this->Atendimento_model->listar_atendente_dd();

            $this->load->view('ecrm/atendimento_lista/programa.php', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar_programa()
    {
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->library('charts');
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["dt_inicio"]        = $this->input->post("dt_inicio", TRUE);
			$args["dt_fim"]           = $this->input->post("dt_fim", TRUE);
			$args["tipo_atendimento"] = $this->input->post("tipo_atendimento", TRUE);
            $args["id_atendente"]     = intval($this->input->post("id_atendente", TRUE));

            manter_filtros($args);

            $this->Atendimento_model->listar_programa( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/atendimento_lista/programa_result', $data);
        }
    }

    function atendimento($cd)
    {
        CheckLogin();

        if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');

            $args['cd_atendimento'] = $cd;

            $this->Atendimento_model->atendimento( $result, $args );
			$data['row'] = $result->row_array();
            
			$this->load->view('ecrm/atendimento_lista/atendimento', $data);
		}
    }

    function listar_atend_encaminhamento()
    {
        if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["cd_atendimento"] = $this->input->post("cd_atendimento", TRUE);

            manter_filtros($args);

            $this->Atendimento_model->encaminhamento( $result, $args );
			$data['encaminhamento'] = $result->result_array();
            
            if(count($data['encaminhamento']) > 0)
            {
                $this->load->view('ecrm/atendimento_lista/listar_atend_encaminhamento_result', $data);
            }
			
        }
        
    }

    function listar_atend_reclamacao()
    {
        if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["cd_atendimento"] = $this->input->post("cd_atendimento", TRUE);

            manter_filtros($args);

            $this->Atendimento_model->reclamacoes( $result, $args );
			$data['reclamacao'] = $result->result_array();

            if(count($data['reclamacao']) > 0)
            {
                $this->load->view('ecrm/atendimento_lista/listar_atend_reclamacao_result', $data);
            }

        }
    }

    function listar_atend_sugestao()
    {
        if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["cd_atendimento"] = $this->input->post("cd_atendimento", TRUE);

            manter_filtros($args);

            $this->Atendimento_model->reclamacoes_sugestoes( $result, $args );
			$data['sugestao'] = $result->result_array();

            if(count($data['sugestao']) > 0)
            {
                $this->load->view('ecrm/atendimento_lista/listar_atend_sugestao_result', $data);
            }

        }
    }

    function busca_atendimento()
    {
        if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
			$this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

            $args["cd_atendimento"] = $this->input->post("cd_atendimento", TRUE);

            manter_filtros($args);

            $this->Atendimento_model->busca_atendimento( $result, $args );
			$data['atendimento'] = $result->result_array();

            if(count($data['atendimento']) > 0)
            {
                $this->load->view('ecrm/atendimento_lista/listar_atend_adentimento_result', $data);
            }

        }
    }
	
	function gravacaoXcally($cd_atendimento = 0)
	{
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

			$cd_xcally = 169;

            $args["cd_atendimento"] = intval($cd_atendimento);


            $this->Atendimento_model->gravacaoXcally( $result, $args );
			$data = $result->row_array();
			$cd_xcally = intval($data['id']);
			$nome_arquivo = trim($data['nome_arquivo']);
			#echo $cd_xcally; exit;
			#print_r($data); exit;

			$url = "http://10.63.250.239/gravacoes/".$nome_arquivo;
						
			if($this->checarGravacao($url))
			if (1 == 1)
			{
				#### SERVIDOR REPOSITORIO DE GRAVACOES 10.63.250.239 ####
				echo '	SRVGRAVACOES<BR>
						<audio controls autoplay>
						  <source src="'.site_url("ecrm/atendimento_lista/getGravacao/".$nome_arquivo).'" type="audio/wav">
						</audio>			
						<br><br>
						<a href="'.site_url("ecrm/atendimento_lista/getGravacao/".$nome_arquivo).'" target="_blank">Download</a>
					 ';				
				
				#header("Location: $url");
				exit();							
			}
			else
			{
				#### SERVIDOR CALLCENTER XCALLY 10.63.255.117 ####
				echo '	SRVCALL<BR>
						<audio controls autoplay>
						  <source src="'.site_url("ecrm/atendimento_lista/gravacaoXcallyArquivo/".$cd_xcally).'" type="audio/wav">
						</audio>			
						<br><br>
						<a href="'.site_url("ecrm/atendimento_lista/gravacaoXcallyArquivo/".$cd_xcally).'" target="_blank">Download</a>
					 ';
			}
			exit;
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function getGravacao($nome_arquivo) 
	{
		$url = "http://10.63.250.239/gravacoes/".$nome_arquivo;
		header("Content-Type: audio/wav");
		readfile($url); // Lê e envia o conteúdo do áudio como se estivesse no mesmo servidor
	}
	
	private function checarGravacao($url) 
	{
	    // Inicializa o cURL
		$ch = curl_init($url);

		// Configura o cURL para retornar apenas os cabeçalhos da resposta
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		// Executa a solicitação cURL
		$response = curl_exec($ch);

		// Verifica se houve erro na solicitação
		if (curl_errno($ch)) {
			curl_close($ch);
			return false;
		}

		// Obtém o código de resposta HTTP
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		// Fecha a conexão cURL
		curl_close($ch);

		// Verifica se o código de resposta é 200 e o tipo de conteúdo é audio/wav
		if ($httpCode === 200 && ($contentType === 'audio/wav' || $contentType === 'audio/x-wav')) {
			return  true;
		}
		
		return false;
	}	
		
	function gravacaoXcallyArquivo($cd_xcally = 0)
	{
        CheckLogin();

		if(gerencia_in(array('GRSC','DE', 'GTI')))
		{
            $this->load->model('projetos/Atendimento_model');
			$data   = Array();
			$args   = Array();
			$result = null;

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_setopt_array($ch, array(
				  CURLOPT_URL => 'https://10.63.255.117/api/voice/recordings/'.intval($cd_xcally).'/download',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
					'Authorization: Basic YWRtaW46IVhjYWxseTE5MjM='
				  ),
			));
			
			$resultado = curl_exec($ch);
			
			header('Content-Description: audio/wav');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="gravacao.wav"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			ob_clean();
			flush();
			echo $resultado;
			exit;				
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	

}
?>