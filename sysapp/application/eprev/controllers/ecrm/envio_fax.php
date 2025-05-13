<?php
class envio_fax extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		
		$this->load->model("projetos/Envio_fax_model");

		$data['combo_usuario'] = $this->Envio_fax_model->listaUsuario();
		
        $this->load->view('ecrm/envio_fax/index.php',$data);
    }



    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Envio_fax_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		$args["nr_telefone"]           = $this->input->post("nr_telefone", TRUE);
		$args["cd_usuario"]            = $this->input->post("cd_usuario", TRUE);
		$args["dt_envio_inicio"]       = $this->input->post("dt_envio_inicio", TRUE);
		$args["dt_envio_fim"]          = $this->input->post("dt_envio_fim", TRUE);


		// --------------------------
		// listar ...

        $this->Envio_fax_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/envio_fax/partial_result', $data);
    }

	function detalhe($cd=0, $retorno="")
	{
		$data['fl_retorno'] = $retorno;
		$this->load->view('ecrm/envio_fax/detalhe',$data);
		
	}

	function salvar()
	{
		CheckLogin();

		$this->load->model("projetos/Envio_fax_model");

		$config['upload_path'] = './up/fax/';
		$config['allowed_types'] = 'pdf|txt|tif';
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			echo '<pre>';
			var_dump($error);
			echo '</pre>';
			exit;			
		}	
		else
		{
			$ar_file = array('upload_data' => $this->upload->data());
			$ar_campo['fax']   = $this->input->post("nr_telefone",TRUE);
			$ar_campo['fax']   = str_replace("(51)","",$ar_campo['fax']);
			$ar_campo['fax']   = str_replace(")","",$ar_campo['fax']);
			$ar_campo['fax']   = str_replace("(","",$ar_campo['fax']);
			$ar_campo['fax']   = trim($ar_campo['fax']);
			$ar_campo['email'] = $this->session->userdata('usuario')."@eletroceee.com.br";			
			$ar_campo['pdf']   = "@".$ar_file['upload_data']['full_path'];
			
			$ch = curl_init();
			$url_fax = "http://srvcentral.eletroceee.com.br/fax/sendFax.php";
			curl_setopt($ch, CURLOPT_URL, $url_fax );
			curl_setopt($ch, CURLOPT_POST, 1 );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $ar_campo);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$postResult = curl_exec($ch);
			$infoHeader = curl_getinfo($ch);
		
			$ar_status = Array(200,302);
			if (($postResult === false) or (!in_array($infoHeader['http_code'], $ar_status)))
			{
				echo '<pre>';
				$output = "No cURL data returned for ".$url_fax." [". $infoHeader['http_code']. "]";
				$output .= "<BR>CURL ERRO: ". curl_error($ch);
				$output .= "<BR>RETORNO: ". $postResult;
				echo $output;
				echo '</pre>';
				exit;
			}			
			
			if (curl_errno($ch))
			{
				echo '<pre>';
				echo "NÃO POSSÍVEL ENVIAR FAX";
				echo "<BR>ERRO CURL: ".curl_error($ch);
				echo "<BR>RESULTADO: ".$postResult;
				echo '</pre>';
				exit;
			}
			else
			{
				curl_close($ch);
			}
			
			
			$dados['cd_empresa']            = $this->input->post("cd_empresa",TRUE);
			$dados['cd_registro_empregado'] = $this->input->post("cd_registro_empregado",TRUE);
			$dados['seq_dependencia']       = $this->input->post("seq_dependencia",TRUE);
			$dados['nr_telefone']           = $this->input->post("nr_telefone",TRUE);
			$dados['ds_arquivo']            = $ar_file['upload_data']['file_name'];
			
			$saved = $this->Envio_fax_model->salvar($dados, $erros);

			if($saved)
			{
				redirect("ecrm/envio_fax/detalhe/0/OK", "refresh");
			}
			else
			{
				echo '<pre>';
				var_dump($erros);
				echo '</pre>';
				exit;
			}
		}		
		/*
		Array
		(
			[upload_data] => Array
				(
					[file_name] => 69c05e136e1a4362605acbf153251eae.pdf
					[file_type] => application/pdf
					[file_path] => C:/desenv/u/www/cieprev/up/fax/
					[full_path] => C:/desenv/u/www/cieprev/up/fax/69c05e136e1a4362605acbf153251eae.pdf
					[raw_name] => 69c05e136e1a4362605acbf153251eae
					[orig_name] => testefax.pdf
					[file_ext] => .pdf
					[file_size] => 5.14
					[is_image] => 
					[image_width] => 
					[image_height] => 
					[image_type] => 
					[image_size_str] => 
				)

		)
		*/
	}
}
