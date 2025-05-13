<?php
class Divulgacao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
        $this->load->model('projetos/Divulgacao_model');		
    }

    function index()
    {
		$result = null;
		$data   = array();
		$args   = array();
		
		$this->Divulgacao_model->gerencia($result, $args);
		$data['arr_gerencia'] = $result->result_array();

        $this->load->view('ecrm/divulgacao/index.php', $data);
    }

    function listar()
    {
		$result = null;
		$data   = array();
		$args   = array();

		$args["dt_divulgacao_inicio"] = $this->input->post("dt_divulgacao_inicio", TRUE);
		$args["dt_divulgacao_fim"]    = $this->input->post("dt_divulgacao_fim", TRUE);
		$args["cd_publico"]           = $this->input->post("cd_publico", TRUE);
		$args["cd_divisao"]           = $this->input->post("cd_divisao", TRUE);
		$args["nome"]                 = $this->input->post("nome", TRUE);
		
		manter_filtros($args);

		$this->Divulgacao_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		$this->load->view('ecrm/divulgacao/partial_result', $data);			
    }
	
	function listar_estatistica()
    {
		$result = null;
		$data   = array();
		$args   = array();
		
		$args["cd_divulgacao"] = intval($this->input->post("cd_divulgacao", TRUE));
		
		$ar_json['dt_ultimo_email_enviado'] = "";
		$ar_json['qt_participante']       = 0;
		$ar_json['qt_email_aguarda_env']  = 0;
		$ar_json['qt_email_nao_env']      = 0;
		$ar_json['qt_email_env']          = 0;
		$ar_json['qt_email']              = 0;
		$ar_json['qt_visualizacao']       = 0;
		$ar_json['qt_visualizacao_unica'] = 0;		
		
		foreach($ar_json as $k => $v)
		{
			$args['campo'] = $k;
			$this->Divulgacao_model->listar_estatistica($result, $args);
			$ar_data = $result->row_array();	

			$ar_json[$k] = $ar_data['valor'];
		}
		
		$ar_json['percentual_envio']       = "";
		#echo "<PRE>"; print_r($ar_json); exit;
		
		if((intval($ar_json["qt_email"]) == 0) and (intval($ar_json["qt_email_aguarda_env"]) == 0))
		{
			$perc_envio = 0;
		}
		elseif((intval($ar_json["qt_email"]) > 0) and (intval($ar_json["qt_email_aguarda_env"]) == 0))
		{
			$perc_envio = 100;
		}
		elseif((intval($ar_json["qt_email_aguarda_env"]) > 0) and ((intval($ar_json["qt_email_env"]) + (intval($ar_json["qt_email_nao_env"]))) == 0))
		{
			$perc_envio = 0;
		}		
		else
		{
			$perc_envio = ((intval($ar_json["qt_email"]) - intval($ar_json["qt_email_aguarda_env"])) * 100) / intval($ar_json["qt_email"]);
		}
		$ar_json['percentual_envio'] = progressbar(intval($perc_envio));
		
		
		#echo "<PRE>"; print_r($ar_json);
		
		echo json_encode($ar_json);
	}
	
	function cadastro($cd_divulgacao = 0)
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {		
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['cd_divulgacao'] = intval($cd_divulgacao);
			$args['cd_usuario']    = $this->session->userdata("codigo");
			
			if(intval($args['cd_divulgacao']) == 0)
			{
				$data['row'] = Array(
										'cd_divulgacao'         => 0,
										'id_rementente'         => 'F',
										'dt_ultimo_email_enviado' => '',
										'qt_email_enviado'      => 0,
										'qt_email_nao_enviado'  => 0,
										'fl_enviar_email'       => 'N',
										'fl_unico_destinatario' => 'N',
										'ds_remetente'          => '',
										'fl_agenda_email'       => 'N',
										'dt_agenda_email'       => '',
										'hr_agenda_email'       => '',
										'ds_assunto'            => '',
										'ds_url_link'           => '',
										'ds_texto'              => '',
										'email_avulsos'         => '',
										'qt_visualizacao_unica' => 0,
										'qt_visualizacao'       => 0,
										'qt_participante'       => 0
									 );
			}
			else
			{
				$this->Divulgacao_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->Divulgacao_model->grupo($result, $args);
			$data['ar_grupo'] = $result->result_array();

			$data['lita_negra'] = $this->Divulgacao_model->lista_negra(intval($cd_divulgacao));
			
			$this->load->view('ecrm/divulgacao/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}

	function salvar()
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_divulgacao']       = $this->input->post("cd_divulgacao", TRUE);
			$args['fl_enviar_email']     = $this->input->post("fl_enviar_email", TRUE);
			$args['id_rementente']       = $this->input->post("id_rementente", TRUE);
			$args['ds_remetente']        = $this->input->post("ds_remetente", TRUE);
			$args['fl_agenda_email']     = $this->input->post("fl_agenda_email", TRUE);
			$args['dt_agenda_email']     = $this->input->post("dt_agenda_email", TRUE);
			$args['hr_agenda_email']     = $this->input->post("hr_agenda_email", TRUE);
			$args['ds_assunto']          = $this->input->post("ds_assunto", TRUE);
			$args['ds_url_link']         = $this->input->post("ds_url_link", TRUE);
			$args['ds_texto']            = $this->input->post("ds_texto", TRUE);
			$args['email_avulsos']       = ($this->input->post("email_avulsos", TRUE) != '' ? trim(preg_replace('/\s+/', '', $this->input->post("email_avulsos", TRUE))) : "");
			$args['ar_divulgacao_grupo'] = $this->input->post("ar_divulgacao_grupo", TRUE);
			#LISTA NEGRA
			$ar_divulgacao_lista = $this->input->post("ar_divulgacao_lista", TRUE);
			
			#### FILTRA UNICO DESTINATARIO - EMAIL (OS: 54852) ####
			$args['fl_unico_destinatario'] = ($this->input->post("fl_unico_destinatario", TRUE) == "S" ? TRUE : FALSE);

			$args['ar_divulgacao_lista'] = (is_array($ar_divulgacao_lista) ? $ar_divulgacao_lista : array());
			$args['cd_usuario']          = $this->session->userdata("codigo");
			
			#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
			#echo "<PRE>".print_r($_POST,true)."</PRE>"; exit;
			
			$cd_divulgacao = $this->Divulgacao_model->salvar($result, $args);
			
			redirect("ecrm/divulgacao/cadastro/".intval($cd_divulgacao), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function emails($cd_divulgacao = 0, $fl_retornou = "N")
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {		
			$result = null;
			$data   = array();
			$args   = array();
			
			$data['cd_divulgacao'] = intval($cd_divulgacao);
			$data['fl_retornou']  = trim($fl_retornou);
			
			#echo "<PRE>".print_r($data,true)."</PRE>"; exit;
			
			$this->load->view('ecrm/divulgacao/emails.php', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
	
    function listarEmail()
    {
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {	
			$result = null;
			$data   = array();
			$args   = array();

			$args["cd_divulgacao"] = $this->input->post("cd_divulgacao", TRUE);
			$args["fl_lido"]       = $this->input->post("fl_lido", TRUE);
			$args["fl_retornou"]   = $this->input->post("fl_retornou", TRUE);
			$args["qt_pagina"]     = $this->input->post("qt_pagina", TRUE);
			$args["nr_pagina"]     = $this->input->post("nr_pagina", TRUE);
			$args["dt_email_ini"]  = $this->input->post("dt_email_ini", TRUE);
			$args["dt_email_fim"]  = $this->input->post("dt_email_fim", TRUE);
			$args["dt_envio_ini"]  = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]  = $this->input->post("dt_envio_fim", TRUE);			
			$args["email_enviado"] = $this->input->post("email_enviado", TRUE);
			$args["nome"]          = $this->input->post("nome", TRUE);

			manter_filtros($args);
			
			#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
			
			$this->Divulgacao_model->listarEmail($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/divulgacao/emails_result.php', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }	
	
	function tecnologia($cd_divulgacao = 0)
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {		
			$result = null;
			$data   = array();
			$args   = array();
			
			$data['cd_divulgacao'] = intval($cd_divulgacao);
			
			$this->load->view('ecrm/divulgacao/tecnologia.php', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
	
    function tecnologiaDados()
    {
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {	
			$this->load->library('charts');
			$result = null;
			$data   = array();
			$args   = array();

			$args["cd_divulgacao"] = $this->input->post("cd_divulgacao", TRUE);
			$args["dt_envio_ini"]  = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]  = $this->input->post("dt_envio_fim", TRUE);	
		
			manter_filtros($args);
			
			#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
			
			$nr_tam_grafico = 100;
			
			#### DEVICE TYPE ####
			$this->Divulgacao_model->tecnologiaDeviceType($result, $args);
			$data["ar_DeviceType"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_DeviceType"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}
			
			$data["img_DeviceType"] = "";
			if(count($ar_dado) > 0)
			{
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Device Type');	
				$data["img_DeviceType"] = $ar_image['name'];
			}

			#### DEVICE NAME ####
			$this->Divulgacao_model->tecnologiaDeviceName($result, $args);
			$data["ar_DeviceName"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_DeviceName"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}
			
			$data["img_DeviceName"] = "";
			if(count($ar_dado) > 0)
			{
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Mobile');	
				$data["img_DeviceName"] = $ar_image['name'];
			}			
			
			#### OS FAMILIA ####
			$this->Divulgacao_model->tecnologiaOSFamily($result, $args);
			$data["ar_OSFamily"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_OSFamily"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}
			
			$data["img_OSFamily"] = "";
			if(count($ar_dado) > 0)
			{
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'OS Family');	
				$data["img_OSFamily"] = $ar_image['name'];
			}
			
			
			#### OS NOME ####
			$this->Divulgacao_model->tecnologiaOSName($result, $args);
			$data["ar_OSName"] = $result->result_array();
			/*
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_OSName"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}
			
			$data["img_OSName"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'OS Name');	
				$data["img_OSName"] = $ar_image['name'];
			}
			*/

			#### CLIENTE TIPO ####
			$this->Divulgacao_model->tecnologiaUATipo($result, $args);
			$data["ar_UATipo"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_UATipo"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}	
			
			$data["img_UATipo"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Client Type');	
				$data["img_UATipo"] = $ar_image['name'];	
			}

			#### CLIENTE FAMILIA ####
			$this->Divulgacao_model->tecnologiaUAFamily($result, $args);
			$data["ar_UAFamily"] = $result->result_array();
			/*
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_UAFamily"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_UAFamily"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Client Family');	
				$data["img_UAFamily"] = $ar_image['name'];
			}
			*/
			
			$this->load->view('ecrm/divulgacao/tecnologia_result.php', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }	
	
	function participante($cd_divulgacao = 0)
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {		
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['cd_divulgacao'] = intval($cd_divulgacao);
			$data['cd_divulgacao'] = intval($cd_divulgacao);
			
			#### FILTROS ###
			$this->Divulgacao_model->comboEmpresa($result, $args);
			$data["ar_empresa"] = $result->result_array();

			$this->Divulgacao_model->comboPlano($result, $args);
			$data["ar_plano"] = $result->result_array();	

			$this->Divulgacao_model->comboSenha($result, $args);
			$data["ar_senha"] = $result->result_array();

			$this->Divulgacao_model->comboTipo($result, $args);
			$data["ar_tipo"] = $result->result_array();		

			$this->Divulgacao_model->comboTempoPlano($result, $args);
			$data["ar_tempo_plano"] = $result->result_array();

			$this->Divulgacao_model->comboIdade($result, $args);
			$data["ar_idade"] = $result->result_array();				
			
			$this->Divulgacao_model->comboRenda($result, $args);
			$data["ar_renda"] = $result->result_array();	

			$this->Divulgacao_model->comboUF($result, $args);
			$data["ar_uf"] = $result->result_array();			
			
			
			$this->load->view('ecrm/divulgacao/participante.php', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}

	function participanteDados()
    {
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {	
			$this->load->library('charts');
			$result = null;
			$data   = array();
			$args   = array();

			$args["cd_divulgacao"]  = $this->input->post("cd_divulgacao", TRUE);
			$args["dt_envio_ini"]   = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]   = $this->input->post("dt_envio_fim", TRUE);	
			$args["ar_empresa"]     = $this->input->post("ar_empresa", TRUE);	
			$args["ar_plano"]       = $this->input->post("ar_plano", TRUE);	
			$args["ar_tipo"]        = $this->input->post("ar_tipo", TRUE);	
			$args["ar_tempo_plano"] = $this->input->post("ar_tempo_plano", TRUE);	
			$args["ar_idade"]       = $this->input->post("ar_idade", TRUE);	
			$args["ar_renda"]       = $this->input->post("ar_renda", TRUE);	
			$args["ar_uf"]          = $this->input->post("ar_uf", TRUE);	
			$args["cd_sexo"]        = $this->input->post("cd_sexo", TRUE);	
			$args["cd_senha"]       = $this->input->post("cd_senha", TRUE);	
		
			manter_filtros($args);
			
			#echo "<PRE>".print_r($_POST,true)."</PRE>"; exit;
			#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
			
			$nr_tam_grafico = 80;
			
			#### EMPRESA ####
			$this->Divulgacao_model->participanteEmpresa($result, $args);
			$data["ar_empresa"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_empresa"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}
			
			$data["img_empresa"] = "";
			if(count($ar_dado) > 0)
			{
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Empresa');	
				$data["img_empresa"] = $ar_image['name'];
			}
			
			#### PLANO ####
			$this->Divulgacao_model->participantePlano($result, $args);
			$data["ar_plano"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_plano"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_plano"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Plano');	
				$data["img_plano"] = $ar_image['name'];
			}

			#### TEMPO PLANO ####
			$this->Divulgacao_model->participanteTempoPlano($result, $args);
			$data["ar_tempo_plano"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_tempo_plano"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_tempo_plano"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Tempo no Plano (anos)');	
				$data["img_tempo_plano"] = $ar_image['name'];
			}

			
			#### TIPO ####
			$this->Divulgacao_model->participanteTipo($result, $args);
			$data["ar_tipo"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_tipo"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_tipo"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Tipo');	
				$data["img_tipo"] = $ar_image['name'];
			}	

			#### TIPO SENHA ####
			$this->Divulgacao_model->participanteSenha($result, $args);
			$data["ar_senha"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_senha"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_senha"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Senha');	
				$data["img_senha"] = $ar_image['name'];
			}	

			#### SEXO ####
			$this->Divulgacao_model->participanteSexo($result, $args);
			$data["ar_sexo"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_sexo"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_sexo"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Sexo');	
				$data["img_sexo"] = $ar_image['name'];
			}			

			#### IDADE ####
			$this->Divulgacao_model->participanteIdade($result, $args);
			$data["ar_idade"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_idade"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_idade"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Idade');	
				$data["img_idade"] = $ar_image['name'];
			}

			#### RENDA ####
			$this->Divulgacao_model->participanteRenda($result, $args);
			$data["ar_renda"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_renda"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_renda"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'Renda');	
				$data["img_renda"] = $ar_image['name'];
			}

			#### UF ####
			$this->Divulgacao_model->participanteUF($result, $args);
			$data["ar_uf"] = $result->result_array();
			$ar_titulo = Array();
			$ar_dado = Array();
			foreach($data["ar_uf"] as $item)
			{
				$ar_titulo[] = $item['ds_item'];
				$ar_dado[]   = $item['qt_item'];	
			}

			$data["img_uf"] = "";
			if(count($ar_dado) > 0)
			{			
				$ar_image = Array();
				$ar_image = $this->charts->pieChart($nr_tam_grafico, $ar_dado, $ar_titulo, '', 'UF');	
				$data["img_uf"] = $ar_image['name'];
			}			
			
			$this->load->view('ecrm/divulgacao/participante_result.php', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }


	function participanteMapaCidade()
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {	
			$result = null;
			$data   = array();
			$args   = array();

			$args["cd_divulgacao"]  = $this->input->post("cd_divulgacao", TRUE);
			$args["dt_envio_ini"]   = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]   = $this->input->post("dt_envio_fim", TRUE);	
			$args["ar_empresa"]     = $this->input->post("ar_empresa", TRUE);	
			$args["ar_plano"]       = $this->input->post("ar_plano", TRUE);	
			$args["ar_tipo"]        = $this->input->post("ar_tipo", TRUE);	
			$args["ar_tempo_plano"] = $this->input->post("ar_tempo_plano", TRUE);	
			$args["ar_idade"]       = $this->input->post("ar_idade", TRUE);	
			$args["ar_renda"]       = $this->input->post("ar_renda", TRUE);	
			$args["ar_uf"]          = $this->input->post("ar_uf", TRUE);	
			$args["cd_sexo"]        = $this->input->post("cd_sexo", TRUE);	
			$args["cd_senha"]       = $this->input->post("cd_senha", TRUE);			
			
			#### MAPA CIDADE ####
			$this->Divulgacao_model->participanteMapaCidade($result, $args);
			$ar_mapa_cidade = $result->result_array();	

			$ar_json  = array();
			$nr_conta = 1;
			foreach($ar_mapa_cidade as $ar_reg)
			{
				$ar_json[] = array("id" => $nr_conta, "ds_cidade" => utf8_encode($ar_reg["ds_cidade"]), "latitude" => $ar_reg["latitude"], "longitude" => $ar_reg["longitude"]);
				$nr_conta++;
			}			
			
			echo json_encode($ar_json);exit;
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}
	
	function tecnologiaMapaCidade()
	{
		if (gerencia_in(array('AC', 'GE', 'GP')))
        {	
			$result = null;
			$data   = array();
			$args   = array();

			$args["cd_divulgacao"] = $this->input->post("cd_divulgacao", TRUE);
			$args["dt_envio_ini"]  = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]  = $this->input->post("dt_envio_fim", TRUE);		
			
			#### MAPA CIDADE ####
			$this->Divulgacao_model->tecnologiaMapaCidade($result, $args);
			$ar_mapa_cidade = $result->result_array();	

			$ar_json  = array();
			$nr_conta = 1;
			foreach($ar_mapa_cidade as $ar_reg)
			{
				$ar_json[] = array("id" => $nr_conta, "ds_cidade" => utf8_encode($ar_reg["ds_cidade"]), "latitude" => $ar_reg["latitude"], "longitude" => $ar_reg["longitude"]);
				$nr_conta++;
			}			
			
			echo json_encode($ar_json);exit;
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}	
	
}
