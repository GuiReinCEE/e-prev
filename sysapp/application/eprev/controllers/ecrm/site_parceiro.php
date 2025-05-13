<?php
class site_parceiro extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$args = Array();	
			$data = Array();	
			$this->load->view('ecrm/site_parceiro/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        CheckLogin();
		if(gerencia_in(array('GRI')))
		{		
			$this->load->model('projetos/Site_parceiro_model');

			$result = null;
			$data = Array();
			$args = Array();
			
			#manter_filtros($args);
			
			$this->Site_parceiro_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/site_parceiro/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }		
		
	function detalhe($cd_site_parceiro = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Site_parceiro_model');
			$data = Array();	
			$args = Array();	
			$data['cd_site_parceiro'] = intval($cd_site_parceiro);
			
			if(intval($cd_site_parceiro) == 0)
			{
				$data['row'] = Array('cd_site_parceiro' => 0,
									'nome' => '', 
									'url' => '', 
									'imagem' => '', 
									'dt_libera' => '', 
									'cd_usuario_libera' => '', 
									'dt_inclusao' => '', 
									'cd_usuario_inclusao' => '', 
									'dt_exclusao' => '', 
									'cd_usuario_exclusao' => '',
									'fl_libera' => '',
									'nr_ordem' => 0
				                     );
			}
			else
			{
				$args['cd_site_parceiro'] = intval($cd_site_parceiro);
				$this->Site_parceiro_model->cadastro($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/site_parceiro/detalhe.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function salvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Site_parceiro_model');
			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_site_parceiro"] = $this->input->post("cd_site_parceiro", TRUE);
			$args["nome"]             = $this->input->post("nome", TRUE);
			$args["url"]              = $this->input->post("url", TRUE);
			$args["img_parceiro"]     = $this->input->post("img_parceiro", TRUE);
			$args["fl_libera"]        = $this->input->post("fl_libera", TRUE);
			$args["nr_ordem"]         = $this->input->post("nr_ordem", TRUE);
			$args["cd_usuario"]       = $this->session->userdata('codigo');			
			
			if(intval($args["cd_site_parceiro"]) > 0)
			{
				if(trim($args["img_parceiro"]) != "")
				{
					$ar_tmp = explode(".",$args["img_parceiro"]);
					if($ar_tmp[1] == "jpg")
					{
						list($width, $height) = getimagesize("./up/site_parceiro/".$args["img_parceiro"]); 
						
						if($height != 50)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual: A = $height px
									<br>
									<br>
									Tamanho máximo: A = 50 px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/site_parceiro/".$args["img_parceiro"], "./../eletroceee/img/site_parceiro/".$args["img_parceiro"]);
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/site_parceiro/".$args["img_parceiro"]);
					
					#### UPDATE ####
					$qr_sql = " 
								UPDATE projetos.site_parceiro
								   SET img_parceiro = ".(trim($args['img_parceiro']) == "" ? "NULL" : "'".$args['img_parceiro']."'")."
								 WHERE cd_site_parceiro = ".intval($args['cd_site_parceiro'])."			
							  ";		
					$this->db->query($qr_sql);					
				}			
			}

			$cd_site_parceiro_new = $this->Site_parceiro_model->salvar($result, $args);
			redirect("ecrm/site_parceiro/detalhe/".$cd_site_parceiro_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function excluir($cd_site_parceiro = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Site_parceiro_model');

			$result = null;
			$args = Array();
			$data = Array();

			$args["cd_site_parceiro"] = $cd_site_parceiro;
			$args["cd_usuario"] = $this->session->userdata('codigo');
			$this->Site_parceiro_model->excluir($result, $args);
		
			redirect("ecrm/site_parceiro/", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	
}
