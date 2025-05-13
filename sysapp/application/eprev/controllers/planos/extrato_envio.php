<?php
class extrato_envio extends Controller
{
    function __construct()
    {
        parent::Controller();
		CheckLogin();
    }
	
    function index($cd_plano = "", $cd_plano_empresa = "", $nr_extrato="", $nr_mes="" , $nr_ano="", $dt_envio="")
    {
		#http://10.63.255.222/controle_projetos/cad_email_marketing.php?&op=I&fl_extrato=S&dt_mes=09/2010&dt_envio=19/10/2010&cd_emp=9
		
		if(gerencia_in(array('GFC','GP','GI')))
		{
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$data['cd_plano']         = $cd_plano;
			$data['cd_plano_empresa'] = $cd_plano_empresa;
			$data['nr_mes']           = $nr_mes;
			$data['nr_ano']           = $nr_ano;
			$data['nr_extrato']       = $nr_extrato;
			$data['dt_envio']         = (trim($dt_envio) != "" ? str_replace("-","/",trim($dt_envio)) : date("d/m/Y"));
			
			$this->load->view('planos/extrato_envio/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function listar()
    {
		if(gerencia_in(array('GFC','GP','GI')))
		{
			$this->load->model('projetos/extrato_envio_model');
			$result = null;
			$args   = Array();
			$data   = Array();

			$data["cd_empresa"]       = $this->input->post("cd_plano_empresa", TRUE);
			$data["cd_empresa"]       = $this->input->post("cd_plano_empresa", TRUE);
			$data["cd_plano"]         = $this->input->post("cd_plano", TRUE);
			$data["nr_mes"]           = $this->input->post("nr_mes", TRUE);
			$data["nr_ano"]           = $this->input->post("nr_ano", TRUE);
			$data["nr_extrato"]       = $this->input->post("nr_extrato", TRUE);
			
			$args["cd_plano_empresa"] = $this->input->post("cd_plano_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"]       = $this->input->post("cd_plano_empresa", TRUE);
			$args["cd_plano"]         = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]           = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]           = $this->input->post("nr_ano", TRUE);
			$args["dt_envio"]         = $this->input->post("dt_envio", TRUE);
			$args["nr_extrato"]       = $this->input->post("nr_extrato", TRUE);
			
			manter_filtros($args);			

			$this->extrato_envio_model->controle($result, $args);
			$data['ar_controle'] = $result->row_array();			
			
			if($data['ar_controle']['fl_enviado'] == "N")
			{
				$args['fl_email'] = "";
				$this->extrato_envio_model->cadastroListar($result, $args);
				$data['ar_lista_total'] = $result->result_array();	

				$args['fl_email'] = "N";
				$this->extrato_envio_model->cadastroListar($result, $args);
				$data['ar_lista_sem_email'] = $result->result_array();					
				
				$args['fl_email'] = "S";
				$this->extrato_envio_model->cadastroListar($result, $args);
				$data['ar_lista'] = $result->result_array();
				
				$data["dt_envio"] = $this->input->post("dt_envio", TRUE);	
			}
			else
			{
				$args['fl_email'] = "";
				$this->extrato_envio_model->cadastroListar($result, $args);
				$data['ar_lista_total'] = $result->result_array();		

				$this->extrato_envio_model->controleListar($result, $args);

				$data['ar_lista'] = $result->result_array();
				$data["dt_envio"] = $data['ar_lista'][0]["dt_envio"];
			}
			
			$this->extrato_envio_model->controleEmail($result, $args);
			$data['ar_email'] = $result->row_array();
			
			$this->load->view('planos/extrato_envio/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	

    function enviar()
    {
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/extrato_envio_model');
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_participante"] = $this->input->post("cd_participante", TRUE);
			$args["cd_empresa"]      = $this->input->post("r_cd_empresa", TRUE);
			$args["cd_plano"]        = $this->input->post("r_cd_plano", TRUE);
			$args["nr_mes"]          = $this->input->post("r_nr_mes", TRUE);
			$args["nr_ano"]          = $this->input->post("r_nr_ano", TRUE);
			$args["dt_envio"]        = $this->input->post("r_dt_envio", TRUE);
			$args["dt_agenda"]       = $this->input->post("r_dt_agenda", TRUE);
			$args["nr_extrato"]      = $this->input->post("r_nr_extrato", TRUE);
			$args["cd_usuario"]      = $this->session->userdata('codigo');
			manter_filtros($args);
			
			#echo "<PRE>".print_r($args,true)."</PRE>";exit;
			
			$this->extrato_envio_model->controleInsere($result, $args);
			redirect("planos/extrato_envio/index/".$args["cd_plano"]."/".$args["cd_empresa"]."/".$args["nr_extrato"]."/".$args["nr_mes"]."/".$args["nr_ano"]."/".str_replace("/","-",$args["dt_envio"]), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
}
