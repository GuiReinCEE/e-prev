<?php
class Home extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	function index()
	{	
		CheckLogin();
		$args   = Array();
		$data   = Array();
		$result = null;

		$data['fl_exibe_banner_pesquisa_ti'] = "N";
		$qr_sql = " 
					 SELECT COUNT(*) AS fl_respondeu 
					   FROM	projetos.usuarios_enquetes ue, 
							projetos.enquetes e
					  WHERE	ue.cd_enquete        = 507 
						AND cd_usuario           = ".intval($this->session->userdata('codigo'))." 
						AND ue.cd_enquete        = e.cd_enquete 
						AND e.controle_respostas = 'U'
				  ";		
		$ob_resul = $this->db->query($qr_sql);
	    $ar_reg = $ob_resul->row_array();	
		if(count($ar_reg) > 0)
		{
			if($ar_reg['fl_respondeu'] == 0) 
			{
				$qr_sql = " 
							SELECT CASE WHEN e.dt_fim < CURRENT_TIMESTAMP 
									THEN 'SIM'
									ELSE 'NAO'
							   END AS fl_encerrada
							  FROM projetos.enquetes e
							 WHERE e.cd_enquete = 507 			
					   ";
				$ob_resul = $this->db->query($qr_sql);
				$ar_reg = $ob_resul->row_array();	
				if(count($ar_reg) > 0)
				{				
					if ($ar_reg['fl_encerrada'] == 'NAO') 
					{
						$data['fl_exibe_banner_pesquisa_ti'] = "S";
					}
				}
			}
		}

		$this->load->view('home/workspace',$data);
	}
	
	function test()
	{
		if(CheckLogin())
		{
			echo "teste";
		}
	}
}
?>