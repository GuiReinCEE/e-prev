<?php
class Lista_telefonica extends Controller {

	function __construct()
    {
        parent::Controller();
    }

    private function get_permissao()
    {
        CheckLogin();

        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index()
    {
    	if($this->get_permissao())
        {
	    	$this->load->model('asterisk/lista_telefonica_model');
			
			$data = array();
			
	        $data['collection'] = $this->lista_telefonica_model->listar();
			
			$this->load->view('servico/lista_telefonica/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function csv()
	{
		$this->load->model('asterisk/lista_telefonica_model');
		
        $collection = $this->lista_telefonica_model->listar();
        
        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
		header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Type: application/download');

		header('Content-Disposition: attachment;filename=lista_'.date('Ymd').'.csv');
		header('Content-Transfer-Encoding: binary');

        foreach ($collection as $key => $item) 
        {
        	echo $item['nome'].','.$item['nr_ramal'].','.$item['grupo'].','.$item['email'].','.$item['default_address'].','.$item['default_address_type']."\r\n";
        }
	}
	
	public function json()
	{
		#C:\Users\coliveira\AppData\Roaming\uTech Tecnologia\uTech Softphone\contacts.json
		
		$this->load->model('asterisk/lista_telefonica_model');
		
        $collection = $this->lista_telefonica_model->listar();
	   
		
        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
		header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Type: application/download');

		header('Content-Disposition: attachment;filename=lista_'.date('Ymd').'.contacts.json');
		header('Content-Transfer-Encoding: binary');
		
		
		$ar_ret = Array();
		
        foreach ($collection as $key => $item) 
        {
        	if(intval($item['nr_ramal']) > 0)
			{
				$ar_ret[] = Array (
									"avatar" => "",
									"extension" => $item['nr_ramal'],
									"monitor" => false,
									"name" => utf8_encode(str_replace('"','',$item['nome']))
								  );
			}			
        }
		
		#echo "<PRE>"; #print_r($ar_ret);
		
		echo json_encode($ar_ret);
	}

	public function xmlMicroSIP()
	{
		
		
		#if (!preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
		#{
		#	exit;
		#}		
		
		$this->load->model('asterisk/lista_telefonica_model');
		
        $collection = $this->lista_telefonica_model->listar();
		
        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
		header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		#header('Content-Type: application/force-download');
		#header('Content-Type: application/octet-stream');
		#header('Content-Type: application/download');
		header('Content-Type: charset=utf-8');
		#header("Content-Type: application/xml");
		#header('Content-Disposition: attachment;filename=Contacts.xml');
		#header('Content-Transfer-Encoding: binary');	
        
/*
<?xml version="1.0"?>
<contacts  refresh="0">
<contact name="teste" number="3188" firstname="" lastname="" phone="" mobile="" email="" address="" city="" state="" zip="" comment="" id="" info="" presence="0" starred="0" directory="0"/>
</contacts>
*/

		echo '<?xml version="1.0"?>'.chr(13).chr(10);
		echo '<contacts>'.chr(13).chr(10);
        foreach ($collection as $key => $item) 
        {
			#if($item['nome'] != "display-name")
			if (!in_array(str_replace('"','',trim($item['nome'])), array("display-name","ZZ")))
			{
				echo '<contact name='.str_replace("&","e",utf8_encode($item['nome'])).' number="'.$item['nr_ramal'].'" firstname="" lastname="" phone="" mobile="" email="" address="" city="" state="" zip="" comment="" id="" info="" presence="0" starred="0" directory="0"/>'.chr(13).chr(10);
			}
        }
		echo '</contacts>';
		
		exit;
	}	
	
}