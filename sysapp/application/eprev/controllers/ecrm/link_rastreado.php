<?php
class link_rastreado extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/link_rastreado_model');
    }
	
    function index($cd_link = "", $ds_url = "")
    {
		if(gerencia_in(Array('GAP','GRI','GI')))
		{
			$args = Array();	
			$data = Array();	
			
			if((trim($cd_link) != "") and (intval($cd_link) > 0))
			{
				$data['ar_param']['ds_url'] = trim($cd_link);
			}
			else if(trim($ds_url) != "")
			{
				$data['ar_param']['ds_url'] = urldecode(base64_decode($ds_url));
			}
			else
			{
				$data['ar_param']['ds_url'] = "";
			}
			
			$this->load->view('ecrm/link_rastreado/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function linkLog()
    {
		if(gerencia_in(Array('GAP','GRI','GI')))
		{		
			$result = null;
			$data   = Array();
			$args   = Array();

			$args["ds_url"]    = trim($this->input->post("ds_url", TRUE));
			$args["dt_acesso_ini"] = $this->input->post("dt_acesso_ini", TRUE);
			$args["dt_acesso_fim"] = $this->input->post("dt_acesso_fim", TRUE);
			
			manter_filtros($args);
			
			$args["ds_url"] = str_replace("http://fceee.com.br/?","",$args["ds_url"]);
			$args["cd_link"] = str_replace("https://fceee.com.br/?","",$args["ds_url"]);

			$this->link_rastreado_model->cadastro($result, $args);
			$data['ar_cadastro'] = $result->row_array();			
			
			$this->link_rastreado_model->linkLog($result, $args);
			$data['ar_lista'] = $result->result_array();
			
			$this->link_rastreado_model->linkLogHora($result, $args);
			$data['ar_lista_hora'] = $result->result_array();			
			
			$data['tipo'] = "P";
			
			$this->load->view('ecrm/link_rastreado/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
    function linkLogDia()
    {
		if(gerencia_in(Array('GAP','GRI','GI')))
		{		
			$result = null;
			$data   = Array();
			$args   = Array();

			$args["ds_url"]    = trim($this->input->post("ds_url", TRUE));
			$args["dt_acesso"] = $this->input->post("dt_acesso", TRUE);
			
			manter_filtros($args);
			
			$args["ds_url"] = str_replace("http://fceee.com.br/?","",$args["ds_url"]);
			$args["cd_link"] = str_replace("https://fceee.com.br/?","",$args["ds_url"]);

			$this->link_rastreado_model->cadastro($result, $args);
			$data['ar_cadastro'] = $result->row_array();			
			
			$this->link_rastreado_model->linkLogDia($result, $args);
			$data['ar_lista'] = $result->result_array();
			
			$data['tipo'] = "D";
			
			$this->load->view('ecrm/link_rastreado/index_result_dia', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
	function gerar_index()
	{
		if(gerencia_in(Array('GAP','GRI','GI')))
		{
			$this->load->view('ecrm/link_rastreado/gerar_index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function listar_gerar()
	{
		if(gerencia_in(Array('GAP','GRI','GI')))
		{
			$result = null;
			$data   = Array();
			$args   = Array();
			
			$args['dt_ini'] = $this->input->post("dt_ini", TRUE);
			$args['dt_fim'] = $this->input->post("dt_fim", TRUE);
			
			manter_filtros($args);
			
			$this->link_rastreado_model->lista($result, $args);
			$data['collection'] = $result->result_array();
		
			$this->load->view('ecrm/link_rastreado/gerar_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function gerar($cd_link = '')
	{
		if(gerencia_in(Array('GAP','GRI','GI')))
		{
			$result = null;
			$data   = Array();
			$args   = Array();
			
			$args['cd_link'] = trim($cd_link);
			
			if(trim($args['cd_link']) == '')
			{
				$data['row'] = array(
					'cd_link' => '',
					'cd_empresa' => '',
					'cd_registro_empregado' => '',
					'seq_dependencia' => '',
					'ds_divulgacao_link' => '',
					'ds_url' => '',
					'link' => '',
					'nome' => ''
					);
			}
			else
			{
				$this->link_rastreado_model->carrega($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/link_rastreado/gerar', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_link()
	{
		if(gerencia_in(Array('GAP','GRI','GI')))
		{
			$result = null;
			$data   = Array();
			$args   = Array();
			
			$args["ds_divulgacao_link"]    = $this->input->post("ds_divulgacao_link", TRUE);
			$args["ds_url"]                = $this->input->post("ds_url", TRUE);
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->link_rastreado_model->gerar($result, $args);
			$arr = $result->row_array();	
			
			$args["ds_link"] = $arr["ds_link"];
			
			$args["cd_link"] = str_replace("http://fprev.com.br/?","",$args["ds_link"]);
			$args["cd_link"] = str_replace("https://fprev.com.br/?","",$args["cd_link"]);
			$args["cd_link"] = str_replace("http://10.63.255.222/fprev/?","",$args["cd_link"]);
			
			$this->link_rastreado_model->salva_link($result, $args);
			
			
			
			redirect("ecrm/link_rastreado/gerar_index/", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	
	function tecnologia($cd_link = 0)
	{
		if (gerencia_in(array('GRI','GI')))
        {		
			$result = null;
			$data   = array();
			$args   = array();
			
			$data['cd_link'] = intval($cd_link);
			
			$this->load->view('ecrm/link_rastreado/tecnologia.php', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
	
	function tecnologiaDados()
    {
		if (gerencia_in(array('GRI','GI')))
        {	
			$this->load->library('charts');
			$result = null;
			$data   = array();
			$args   = array();

			$args["cd_link"]       = $this->input->post("cd_link", TRUE);
			$args["dt_acesso_ini"] = $this->input->post("dt_acesso_ini", TRUE);
			$args["dt_acesso_fim"] = $this->input->post("dt_acesso_fim", TRUE);	
		
			manter_filtros($args);
			
			#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
			
			$nr_tam_grafico = 100;
			
			#### DEVICE TYPE ####
			$this->link_rastreado_model->tecnologiaDeviceType($result, $args);
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
			$this->link_rastreado_model->tecnologiaDeviceName($result, $args);
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
			$this->link_rastreado_model->tecnologiaOSFamily($result, $args);
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
			$this->link_rastreado_model->tecnologiaOSName($result, $args);
			$data["ar_OSName"] = $result->result_array();

			#### CLIENTE TIPO ####
			$this->link_rastreado_model->tecnologiaUATipo($result, $args);
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
			$this->link_rastreado_model->tecnologiaUAFamily($result, $args);
			$data["ar_UAFamily"] = $result->result_array();

			
			$this->load->view('ecrm/link_rastreado/tecnologia_result.php', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }
}
?>