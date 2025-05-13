<?php
class gerar extends Controller
{
	function __construct()
    {
        parent::Controller();
    }
	
    function texto($texto = "")
    {
		$this->load->plugin('qrcode');
		$dir = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";		
		$arq = "qr_".random_string().'.png';
		
		$qrcode = new QRcode(utf8_encode($texto), "L");
		$qrcode->disableBorder();
		$qrcode->displayPNG(58, array(255,255,255), array(0,0,0), $dir.$arq);

		header("location: ".base_url()."charts/".$arq);

		exit;
    }	
	
	
    function qrcode()
    {
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
        
        #print_r($_POST); exit;
		
        $args    = Array();
        $data    = Array();
        $ar_ret  = Array();
        $result  = null;		
		
		$args['texto']   = trim($this->input->post("texto", TRUE));
		$args['tamanho'] = (intval(trim($this->input->post("tamanho", TRUE))) > 0 ? intval(trim($this->input->post("tamanho", TRUE))) : 58);
        
        $ar_ret["dt_log"]  = date("Y-m-d H:i:s");
        $ar_ret["fl_erro"] = "N";
        $ar_ret["ds_erro"] = "";		
		$ar_ret["img_b64"] = "";
		
		if(trim($args['texto']) != "")
		{
			$this->load->plugin('qrcode');
			$dir = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";		
			$arq = "qr_".random_string().'.png';
			
			$qrcode = new QRcode($args['texto'], "L");
			$qrcode->disableBorder();
			$qrcode->displayPNG($args['tamanho'], array(255,255,255), array(0,0,0), $dir.$arq);
		
			// Converte a imagem para Base64
			$ar_ret["img_b64"] = base64_encode(file_get_contents($dir.$arq));		
		}
		else
		{
			$ar_ret["fl_erro"] = "S";
			$ar_ret["ds_erro"] = "Texto nao informado";			
		}
		
		echo json_encode($ar_ret);  

		exit;
    }	
}