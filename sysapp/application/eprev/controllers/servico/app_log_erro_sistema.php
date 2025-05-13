<?php
class App_log_erro_sistema extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
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
	    	$dir = '../webapp/srvautoatendimento/application/logs';

			if(is_dir($dir))
			{
				$dh = opendir($dir); 

				$i = 0;

				$data['collection'] = array();

				while (false !== ($filename = readdir($dh))) 
		        {
		            if (pathinfo($filename, PATHINFO_EXTENSION) == 'php') 
		            { 
		                $name = pathinfo($filename, PATHINFO_FILENAME);
		                
		                $arr = explode('-', $name);
		                
		                $data['collection'][$i]['ds_log'] = $name;
		                $data['collection'][$i]['dt_log'] = $arr[3].'/'.$arr[2].'/'.$arr[1];
		                
		                $i++;
		            }
        		} 

        		$this->load->view('servico/app_log_erro_sistema/index', $data);
			}
			else
			{
				exibir_mensagem('PASTA NÃO LOCALIZADA');
			}
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function log($filename)
    {
    	$ponteiro = fopen ('../webapp/srvautoatendimento/application/logs/'.$filename.'.php','r');

    	$name = pathinfo($filename, PATHINFO_FILENAME);
		                
        $arr = explode('-', $name);

        $dt_log = $arr[3].'/'.$arr[2].'/'.$arr[1];

    	$data['filename'] = $filename;

        $data['conteudo'] = '';
        
        $i = 0;

        while (!feof ($ponteiro)) 
        {
            $linha = fgets($ponteiro, 4096);

            $pos = strpos(trim($linha), 'ERROR');

            if ($pos === false) {} 
            else {
    			$data['conteudo'] .= '----------------------------------------------------------------------------------------------------------------------------------------------'.br();
			}

            $data['conteudo'] .= (trim($linha) != '' ? $linha.br() : '');
            $i ++;
        }

        fclose ($ponteiro);

        $this->load->view('servico/app_log_erro_sistema/log', $data);
    }
}