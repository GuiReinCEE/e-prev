<?php
class agendar extends Controller
{
	var $token_acesso_eletro;
	
	function __construct()
    {
        parent::Controller();
		
		$this->token_acesso_eletro = md5('integracaoenviaremaileletro'); #7a2584226d7f72f3a83920be80b2f33e
		
		$this->load->model("agenda/agenda_model");
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;

		echo "API Agendar";
    }	

    public function setAgenda()
    {	
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
	
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;

		$ar_ret["fl_erro"]   = "N";
		$ar_ret["retorno"]   = "";
		$ar_ret["cd_agenda"] = 0;
		
		
		$args["token"]           = $this->input->post("token", TRUE); 
		$args["cd_agenda"]       = $this->input->post("cd_agenda", TRUE); #codigo da agenda
		$args["usuario"]         = $this->input->post("usuario", TRUE); #usuario criador
		$args["dt_agenda_ini"]   = $this->input->post("dt_agenda_ini", TRUE); # data inicial (DD/MM/YYYY)
		$args["dt_agenda_fim"]   = $this->input->post("dt_agenda_fim", TRUE); # data final (DD/MM/YYYY)
		$args["hr_ini"]          = $this->input->post("hr_ini", TRUE); # horario de inicio (HH24:MI)
		$args["hr_fim"]          = $this->input->post("hr_fim", TRUE); # horario de final do (HH24:MI)
		$args["assunto"]         = utf8_decode($this->input->post("assunto", TRUE)); 
		$args["local"]           = utf8_decode($this->input->post("local", TRUE));
		$args["texto"]           = utf8_decode($this->input->post("texto", TRUE));
		$args["fl_ocupado"]      = $this->input->post("fl_ocupado", TRUE); # S/N, marcar na agenda como ocupado 
		$args["qt_min_lembrete"] = $this->input->post("qt_min_lembrete", TRUE); # quantidade em minutos antes da data/horário inicial do lembrete
		$args["participantes"]   = $this->input->post("participantes", TRUE); #e-mails separados por ponto e virgula (;) sem espaços
		
		if($args["token"] == $this->token_acesso_eletro)
		{
			$FL_OK = TRUE;
			foreach($args as $key => $value)
			{
				if(trim($value) == "")
				{
					$ar_ret["fl_erro"]   = "S";
					$ar_ret["retorno"]   = "ERRO: campo ".$key." não informado";
					$ar_ret["cd_agenda"] = 0;				
					
					$FL_OK = FALSE;
				}
			}
			
			if($FL_OK)
			{
				$this->agenda_model->incluir($result, $args);
				$data = $result->row_array();
				
				$ar_ret["fl_erro"]   = "N";
				$ar_ret["retorno"]   = "Agendado";
				$ar_ret["cd_agenda"] = $data['cd_agenda'];				
			}
		}
		else
		{
			$ar_ret["fl_erro"]   = "S";
			$ar_ret["retorno"]   = "ERRO: token inválido (".$args["token"]."), acesso não permitido";
			$ar_ret["cd_agenda"] = 0;			
		}
		
		echo json_encode($ar_ret);
    }
	
    public function excluir()
    {	
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;

		$ar_ret["fl_erro"]   = "N";
		$ar_ret["retorno"]   = "";
		$ar_ret["cd_agenda"] = 0;
		
		$args["token"]      = $this->input->post("token", TRUE); 
		$args["usuario"]    = $this->input->post("usuario", TRUE); #usuario criador
		$args["cd_agenda"]  = $this->input->post("cd_agenda", TRUE); # código do agendamento
		
		if($args["token"] == $this->token_acesso_eletro)
		{		
			$FL_OK = TRUE;
			foreach($args as $key => $value)
			{
				if(trim($value) == "")
				{
					$ar_ret["fl_erro"]   = "S";
					$ar_ret["retorno"]   = utf8_encode("ERRO: campo ".$key." não informado");
					$ar_ret["cd_agenda"] = $args['cd_agenda'];				
					
					$FL_OK = FALSE;
				}
			}
			
			if($FL_OK)
			{
				$this->agenda_model->excluir($result, $args);
				$data = $result->row_array();
				
				$ar_ret["fl_erro"]   = "N";
				$ar_ret["ds_erro"]   = utf8_encode("Agendamento excluído");
				$ar_ret["cd_agenda"] = $args['cd_agenda'];				
			}
		}
		else
		{
			$ar_ret["fl_erro"]   = "S";
			$ar_ret["retorno"]   = "ERRO: token inválido (".$args["token"]."), acesso não permitido";
			$ar_ret["cd_agenda"] = 0;			
		}
		echo json_encode($ar_ret);
    }	
}
?>