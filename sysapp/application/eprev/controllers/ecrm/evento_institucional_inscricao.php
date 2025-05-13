<?php

class evento_institucional_inscricao extends Controller
{
	var $ar_tp_inscrito = Array();
	
    function __construct()
    {
        parent::Controller();
		
		$this->ar_tp_inscrito[0] = "Não definido";
		$this->ar_tp_inscrito[1] = "Ex-presidentes";
		$this->ar_tp_inscrito[2] = "Colaboradores";
		$this->ar_tp_inscrito[3] = "Ex conselheiros";
		$this->ar_tp_inscrito[4] = "Entidades";
		$this->ar_tp_inscrito[5] = "Homenageados patrocinadoras";
		$this->ar_tp_inscrito[6] = "Homenageados participantes";
		$this->ar_tp_inscrito[7] = "Parceiros";
		$this->ar_tp_inscrito[8] = "Coral";
		$this->ar_tp_inscrito[9] = "Teatro";
		$this->ar_tp_inscrito[10] = "Terceiros";
		$this->ar_tp_inscrito[11] = "Conselho Deliberativo";
		$this->ar_tp_inscrito[12] = "Conselho Fiscal";
		$this->ar_tp_inscrito[13] = "Diretoria";
		$this->ar_tp_inscrito[14] = "Abrapp";
		$this->ar_tp_inscrito[15] = "Vencedor";
    }

    function index()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'DE', 'GC', 'GN')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $qr_sql = "
                SELECT cd_evento AS value, 
                       TO_CHAR(dt_inicio,'[ DD/MM/YYYY ] - ') || nome   AS text
                  FROM projetos.eventos_institucionais 
                 WHERE cd_tipo='EVEI' 
                 ORDER BY dt_inicio DESC, nome ASC
					  ";
            $result = $this->db->query($qr_sql);
            $data['ar_evento'] = $result->result_array();
			
			$ar_tp = Array();
			foreach($this->ar_tp_inscrito as $k => $v)
			{
				$ar_tp[] = Array('text' => $v, 'value' => $k);
			}
			$data['ar_tp_inscrito'] = $ar_tp;

            $this->load->view('ecrm/evento_institucional_inscricao/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'DE', 'GC', 'GN')))
        {
            $this->load->model('projetos/Eventos_institucionais_inscricao_model');

            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_eventos_institucionais"] = intval($this->input->post("cd_eventos_institucionais", TRUE));
            $args["dt_inscricao_inicio"]       = $this->input->post("inscricao_inicio", TRUE);
            $args["dt_inscricao_fim"]          = $this->input->post("inscricao_fim", TRUE);
            $args["tipo"]                      = $this->input->post("tipo", TRUE);
            $args["fl_presente"]               = $this->input->post("fl_presente", TRUE);
            $args["tp_inscrito"]               = $this->input->post("tp_inscrito", TRUE);
            $args["cd_empresa"]                = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"]     = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]           = $this->input->post("seq_dependencia", TRUE);
            $args["nome"]                      = $this->input->post("nome", TRUE);

			#echo "<PRE>".print_r($_POST,true)."</PRE>"; exit;
			
            manter_filtros($args);

            $this->Eventos_institucionais_inscricao_model->listar($result, $args);

            $data['collection'] = $result->result_array();
			
			$data['ar_tp_inscrito'] = $this->ar_tp_inscrito;

            $this->load->view('ecrm/evento_institucional_inscricao/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function setIdentificacao()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'GN')))
        {
            $sql = "
                UPDATE projetos.eventos_institucionais_inscricao
                   SET tp_inscrito = {tp_inscrito}
                 WHERE cd_eventos_institucionais_inscricao = {cd_inscricao}
			";
            esc("{tp_inscrito}", $this->input->post("tp_inscrito", TRUE), $sql, 'int');
            esc("{cd_inscricao}", $this->input->post("cd_inscricao", TRUE), $sql, 'int');

            $query = $this->db->query($sql);

            // echo $sql;

            redirect('ecrm/evento_institucional_inscricao', 'refresh');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function setPresente()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'GN')))
        {
            $sql = "
                UPDATE projetos.eventos_institucionais_inscricao
                   SET fl_presente = '" . $this->input->post("fl_presente", TRUE) . "'
                 WHERE cd_eventos_institucionais_inscricao = {cd_inscricao}
			";
            esc("{cd_inscricao}", $this->input->post("cd_inscricao", TRUE), $sql, 'int');

            $query = $this->db->query($sql);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function confirmar_site()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'GN')))
        {
            $sql = "
                SELECT e.cd_evento, 
                       e.nome, 
                       TO_CHAR(e.dt_inicio,'DD/MM/YYYY') as dt_inicio, 
                       e.local_evento, 
                       nome_cidade
                  FROM projetos.eventos_institucionais e
                  JOIN expansao.cidades c 
                    ON c.cd_municipio_ibge = e.cd_cidade 
                   AND sigla_uf = 'RS'
                 WHERE e.cd_tipo= 'EVEI'
                   AND dt_exclusao IS NULL 
                   AND (CASE WHEN COALESCE(e.qt_inscricao,0) = 0 OR e.qt_inscricao > (SELECT COUNT(*)
                                                                                        FROM projetos.eventos_institucionais_inscricao eii
                                                                                       WHERE eii.dt_exclusao IS NULL
                                                                                         AND eii.cd_eventos_institucionais = e.cd_evento) 
                             THEN 'S'
                             ELSE 'N'
                        END) = 'S'
                   AND (CASE WHEN CURRENT_TIMESTAMP BETWEEN COALESCE(e.dt_ini_inscricao,CURRENT_TIMESTAMP) AND COALESCE(e.dt_fim_inscricao,CURRENT_TIMESTAMP)
                   AND CURRENT_TIMESTAMP < e.dt_inicio
                       THEN 'S' 
                       ELSE 'N' 
                       END) = 'S'					   
                 ORDER BY e.nome DESC  ";
            $q = $this->db->query($sql);

            $data = array();
            $data['eventos'] = $q->result_array();
            $this->load->view("ecrm/evento_institucional_inscricao/confirmar_site", $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function detalhe($cd = 0)
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'DE', 'GC', 'GN')))
        {
            $this->load->model('projetos/eventos_institucionais_inscricao_model');

            $result = null;
            $data = Array();
            $args = Array();

            if (intval($cd) == 0)
            {
                $args['cd_eventos_institucionais'] = 0;

                $data['row'] = Array('cd_eventos_institucionais' => 0,
                  'cd_eventos_institucionais_inscricao' => 0,
                  'cd_empresa' => '',
                  'cd_registro_empregado' => '',
                  'seq_dependencia' => '',
                  'tipo' => 'I',
                  'identificacao' => '0',
                  'nome' => '',
                  'empresa' => '',
                  'telefone' => '',
                  'email' => '',
                  'endereco' => '',
                  'cidade' => '',
                  'uf' => '',
                  'cep' => '',
                  'obs' => '',
                  'motivo' => '',
                  'selecionado' => 'N',
                  'desclassificado' => 'N',
				  'fl_perm' => 'S',
				  'cpf' => ''
                );

                $data['anexo'] = Array();
            }
            else
            {
                $args['cd_eventos_institucionais_inscricao'] = intval($cd);
                $this->eventos_institucionais_inscricao_model->carrega($result, $args);
                $data['row'] = $result->row_array();

                $this->eventos_institucionais_inscricao_model->anexo($result, $args);
                $data['anexo'] = $result->result_array();

                $args['cd_eventos_institucionais'] = $data['row']['cd_eventos_institucionais'];
            }
            
            $ar_tp = Array();
            foreach($this->ar_tp_inscrito as $k => $v)
            {
                    $ar_tp[] = Array('text' => $v, 'value' => $k);
            }
            $data['ar_identificacao'] = $ar_tp;

            $this->eventos_institucionais_inscricao_model->lista_evento($result, $args);

            $data['eventos'] = $result->result_array();

            $this->load->view("ecrm/evento_institucional_inscricao/detalhe", $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function evento()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'DE', 'GC', 'GN')))
        {
            $this->load->model('projetos/eventos_institucionais_inscricao_model');

            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_eventos_institucionais"] = intval($this->input->post("cd_eventos_institucionais", TRUE));

            $this->eventos_institucionais_inscricao_model->descricao_evento($result, $args);
            $colletion = $result->row_array();

            echo "Data: " . $colletion['dt_inicio']
            . "<BR>Local: " . $colletion['local_evento']
            . "<BR>Cidade: " . $colletion['nome_cidade'];
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar()
    {
        CheckLogin();

        $this->load->model('projetos/eventos_institucionais_inscricao_model');

        $result = null;
        $data = Array();
        $args = Array();

        $args["cd_eventos_institucionais_inscricao"] = $this->input->post("cd_eventos_institucionais_inscricao", TRUE);
        $args["cd_eventos_institucionais"] = $this->input->post("cd_eventos_institucionais", TRUE);
        $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
        $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
        $args["seq_dependencia"] = $this->input->post("seq_dependencia", TRUE);
        $args["nome"] = $this->input->post("nome", TRUE);
        $args["cpf"] = $this->input->post("cpf", TRUE);
        $args["telefone"] = $this->input->post("telefone", TRUE);
        $args["email"] = $this->input->post("email", TRUE);
        $args["observacao"] = $this->input->post("obs", TRUE);
        $args["cadastro_por"] = $this->session->userdata('usuario');
        $args["tipo"] = $this->input->post("tipo", TRUE);
        $args["endereco"] = $this->input->post("endereco", TRUE);
        $args["cidade"] = $this->input->post("cidade", TRUE);
        $args["cep"] = $this->input->post("cep", TRUE);
        $args["uf"] = $this->input->post("uf", TRUE);
        $args["desclassificado"] = $this->input->post("desclassificado", TRUE);
        $args["selecionado"] = $this->input->post("selecionado", TRUE);
        $args["motivo"] = $this->input->post("motivo", TRUE);
        $args["identificacao"] = $this->input->post("identificacao", TRUE);
        $args["empresa"] = $this->input->post("empresa", TRUE);

        $cd_eventos_institucionais_inscricao = $this->eventos_institucionais_inscricao_model->salvar($result, $args);

        redirect("ecrm/evento_institucional_inscricao/detalhe/" . $cd_eventos_institucionais_inscricao, "refresh");
    }

    function delete($cd)
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'GN')))
        {
            $this->load->model('projetos/eventos_institucionais_inscricao_model');

            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_eventos_institucionais_inscricao'] = intval($cd);

            $this->eventos_institucionais_inscricao_model->delete($result, $args);

            redirect("ecrm/evento_institucional_inscricao", "refresh");
        }
    }
    
    function participacoes()
    {
        CheckLogin();
        if (gerencia_in(array('SG', 'GRI', 'GAP', 'GN')))
        {
            $this->load->model('projetos/Eventos_institucionais_inscricao_model');

            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_registro_empregado"] = intval($this->input->post("cd_registro_empregado", TRUE));
            $args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
            $args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);

            manter_filtros($args);

            $this->Eventos_institucionais_inscricao_model->participacoes($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('ecrm/evento_institucional_inscricao/participacoes_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

}

?>