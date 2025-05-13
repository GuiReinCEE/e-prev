<?php
class igp_amplo extends Controller
{	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('igp_2015/igp_amplo_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GC')))
		{
			$args   = array();
			$data   = array();
			$result = null;

	        $this->load->view('igp_2015/igp_amplo/index',$data);
		}
    }
	
	function listar()
    {
		if(gerencia_in(array('GC')))
        {
			$args   = array();
			$data   = array();
			$result = null;

			$args["nr_ano"] = (intval($this->input->post("nr_ano", true)) == 0 ? date("Y") : intval($this->input->post("nr_ano", true)));
			$data["ar_mes"] = $this->input->post("ar_mes", true);
		
			manter_filtros($args);
			
			$this->igp_amplo_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$data["ar_oculta"] = Array();
			$x = 1;
			for ($i = 1; $i <= 12; $i++) 
			{
				if(!in_array($i, $data["ar_mes"])) 
				{
					$data["ar_oculta"][] = $x + 2;
					$data["ar_oculta"][] = $x + 3;
				}	
				$x+= 2;
			}
			
			$data["ar_tot"]["01_vl_mes_igp"] = "";
			$data["ar_tot"]["02_vl_mes_igp"] = "";
			$data["ar_tot"]["03_vl_mes_igp"] = "";
			$data["ar_tot"]["04_vl_mes_igp"] = "";
			$data["ar_tot"]["05_vl_mes_igp"] = "";
			$data["ar_tot"]["06_vl_mes_igp"] = "";
			$data["ar_tot"]["07_vl_mes_igp"] = "";
			$data["ar_tot"]["08_vl_mes_igp"] = "";
			$data["ar_tot"]["09_vl_mes_igp"] = "";
			$data["ar_tot"]["10_vl_mes_igp"] = "";
			$data["ar_tot"]["11_vl_mes_igp"] = "";
			$data["ar_tot"]["12_vl_mes_igp"] = "";			
			
			foreach($data['collection'] as $item)
			{
				if(trim($item["01_vl_mes_igp"]) != ""){ $data["ar_tot"]["01_vl_mes_igp"]+= floatval($item["01_vl_mes_igp"]); }
				if(trim($item["02_vl_mes_igp"]) != ""){ $data["ar_tot"]["02_vl_mes_igp"]+= floatval($item["02_vl_mes_igp"]); }
				if(trim($item["03_vl_mes_igp"]) != ""){ $data["ar_tot"]["03_vl_mes_igp"]+= floatval($item["03_vl_mes_igp"]); }
				if(trim($item["04_vl_mes_igp"]) != ""){ $data["ar_tot"]["04_vl_mes_igp"]+= floatval($item["04_vl_mes_igp"]); }
				if(trim($item["05_vl_mes_igp"]) != ""){ $data["ar_tot"]["05_vl_mes_igp"]+= floatval($item["05_vl_mes_igp"]); }
				if(trim($item["06_vl_mes_igp"]) != ""){ $data["ar_tot"]["06_vl_mes_igp"]+= floatval($item["06_vl_mes_igp"]); }
				if(trim($item["07_vl_mes_igp"]) != ""){ $data["ar_tot"]["07_vl_mes_igp"]+= floatval($item["07_vl_mes_igp"]); }
				if(trim($item["08_vl_mes_igp"]) != ""){ $data["ar_tot"]["08_vl_mes_igp"]+= floatval($item["08_vl_mes_igp"]); }
				if(trim($item["09_vl_mes_igp"]) != ""){ $data["ar_tot"]["09_vl_mes_igp"]+= floatval($item["09_vl_mes_igp"]); }
				if(trim($item["10_vl_mes_igp"]) != ""){ $data["ar_tot"]["10_vl_mes_igp"]+= floatval($item["10_vl_mes_igp"]); }
				if(trim($item["11_vl_mes_igp"]) != ""){ $data["ar_tot"]["11_vl_mes_igp"]+= floatval($item["11_vl_mes_igp"]); }
				if(trim($item["12_vl_mes_igp"]) != ""){ $data["ar_tot"]["12_vl_mes_igp"]+= floatval($item["12_vl_mes_igp"]); }
			}			
			
			$ar_legenda = array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez");
			$ar_titulo  = array();			
			$ar_dado    = array();			
			$ar_value   = array();
			
			foreach($data["ar_tot"] as $key => $value)
			{
				$ar_value[] = $value;
			}		

			for ($i = 1; $i <= 12; $i++) 
			{
				if(in_array($i, $data["ar_mes"])) 
				{
					$ar_titulo[]  = $ar_legenda[$i-1];
					$ar_dado[0][] = $ar_value[$i-1];
					$ar_dado[1][] = 100;
				}	
			}			
			
			$this->load->plugin('pchart');
			
			$data["grafico"] = linechart($ar_titulo, $ar_dado, array("Resultado","Meta"), 600, 300, array("M"=>1));
			#$data["grafico"] = group_barchart($ar_titulo, $ar_dado, array("",""), array("Resultado","Meta"), 750, 300, array("M"=>1));
			
			$this->load->view('igp_2015/igp_amplo/index_result', $data);
        }
    }

}
?>