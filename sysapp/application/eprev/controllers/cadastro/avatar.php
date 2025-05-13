<?php
class avatar extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		$this->load->model("projetos/avatar_model");
    }

    function index($cd_usuario = "")
    {	
		CheckLogin();
		
		$result = null;
		$args   = Array();
		$data   = Array();

		if(((gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")) OR ($this->session->userdata('indic_05') == "S"))
		{
			if(trim($cd_usuario) == "")
			{
				$cd_usuario = md5($this->session->userdata("codigo"));
			}
			else
			{
				$cd_usuario = md5(trim($cd_usuario));
			}
		}
		else
		{
			$cd_usuario = md5($this->session->userdata("codigo"));
		}
		
		$args['usuario']    = "";
		$args['cd_usuario'] = $cd_usuario;
		
		$this->avatar_model->usuario($result, $args);
		$data['ar_usuario'] = $result->row_array();	
		
		$this->avatar_model->carrega($result, $args);
		$data['ar_avatar'] = $result->row_array();	
		
		if(empty($data['ar_avatar']))
		{
			$data['ar_avatar']['arquivo']        = "";
			$data['ar_avatar']['arquivo_nome']   = "";
			$data['ar_avatar']['arquivo_avatar'] = "";
			$data['ar_avatar']['crop_x']         = "";
			$data['ar_avatar']['crop_y']         = "";
			$data['ar_avatar']['crop_largura']   = "";
			$data['ar_avatar']['crop_altura']    = "";
		}
		
		$this->load->view("cadastro/avatar/index", $data);
    }

	function salvar()
	{
		CheckLogin();
		
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$tamanho_w = 500;
		$tamanho_h = 500;
		$arq_img = './up/avatar/'.$this->input->post("arquivo", TRUE);
		$arq_usu = $this->input->post("usuario", TRUE);
		$ext_img = pathinfo($arq_img, PATHINFO_EXTENSION);		

		if(in_array(strtolower($ext_img),array("png","gif","jpg","jpeg")))
		{
			#### CALCULAR PROPORCAO ####
			list($largura, $altura) = getimagesize($arq_img); 
			$X = (intval($this->input->post("avatar_x", TRUE)) * $largura) / intval($this->input->post("img_w", TRUE));
			$Y = (intval($this->input->post("avatar_y", TRUE)) * $altura) / intval($this->input->post("img_h", TRUE));
			$W = (intval($this->input->post("avatar_w", TRUE)) * $largura) / intval($this->input->post("img_w", TRUE));
			$H = (intval($this->input->post("avatar_h", TRUE)) * $altura) / intval($this->input->post("img_h", TRUE));		
			
			#### DEFINE O TIPO ####
			$ar_info = getimagesize($arq_img);
			$imgtype = strtolower(image_type_to_mime_type($ar_info[2]));
			
			$ob_img = null;
			if($imgtype == 'image/jpeg')
			{
				$ob_img = imagecreatefromjpeg($arq_img);
			}
			elseif($imgtype == 'image/gif')
			{
				$ob_img = imagecreatefromgif($arq_img);
			}
			elseif($imgtype == 'image/png')
			{
				$ob_img = imagecreatefrompng($arq_img);
			}			
			
			if($ob_img)
			{
				$ob_img_new = ImageCreateTrueColor($tamanho_w, $tamanho_h);
				imagecopyresampled($ob_img_new, $ob_img, 0, 0, $X, $Y, $tamanho_w, $tamanho_h, $W, $H);
				
				$arquivo_avatar = $arq_usu."_".md5(uniqid(rand(), true)).".png";
				imagepng($ob_img_new, "./up/avatar/".$arquivo_avatar);				

				imagedestroy($ob_img);		
				imagedestroy($ob_img_new);	
			
				$args["cd_usuario"]          = $this->input->post("cd_usuario", TRUE);
				$args["arquivo"]             = $this->input->post("arquivo", TRUE);
				$args["arquivo_nome"]        = $this->input->post("arquivo_nome", TRUE);
				$args["arquivo_avatar"]      = $arquivo_avatar;
				$args["crop_x"]              = $this->input->post("avatar_x", TRUE);
				$args["crop_y"]              = $this->input->post("avatar_y", TRUE);
				$args["crop_largura"]        = $this->input->post("avatar_w", TRUE);
				$args["crop_altura"]         = $this->input->post("avatar_h", TRUE);
				$args["cd_usuario_inclusao"] = $this->session->userdata("codigo");

				$this->avatar_model->salvar($result, $args);
				$this->session->set_userdata(array('avatar' => $arquivo_avatar));
				redirect("cadastro/avatar/index/".$args["cd_usuario"], "refresh");
			}
		}
		else
		{
			exibir_mensagem("ERRO<br><br>TIPO (".strtoupper($ext_img).") DE ARQUIVO NÃO PERMITIDO<BR><BR>SOMENTE .jpg, .png ou .gif<BR><BR>");
		}
	}
	
	function img($usuario = "_", $cd_usuario = "")
	{
		#### INTEGRACAO ELETRO ####
		
		$result = null;
		$args   = Array();
		$data   = Array();		
		
		if($usuario == '_')
		{
			$usuario = '';
		}
		$args['usuario']    = $usuario;
		$args['cd_usuario'] = $cd_usuario;
		
		$this->avatar_model->usuario($result, $args);
		$ar_usuario = $result->row_array();	

		$avatar_arquivo = $ar_usuario["avatar"];
		
		if(trim($avatar_arquivo) == "")
		{
			$avatar_arquivo = $ar_usuario["usuario"].".png";
		}
		
		if(!file_exists( "./up/avatar/".$avatar_arquivo))
		{
			$avatar_arquivo = "user.png";
		}	
		
		$nome_imagem = './up/avatar/'.$avatar_arquivo;
		
		if($handle = @fopen($nome_imagem, 'r'))
		{
			$data = fread($handle, filesize($nome_imagem));
			fclose($handle);
			
			header('Content-type: image/png');
			echo($data);
		}		
	}
}