<?php
class Login extends Controller
{
	private $return_page='';

	function __construct()
	{
		parent::Controller();
		session_start();

		$this->configurar_pagina_de_retorno();
	}

	function index($ir_para = "")
	{
		$fl_auto_login = $this->simplelogin->login( "", "", $_SERVER['REMOTE_ADDR']);
		
		if($fl_auto_login)
		{
			redirect($location);
			echo $location;			
		}
		else
		{
			if(trim($this->return_page) != "")
			{
				$ir_para = trim($this->return_page);
			}
			else if(trim($ir_para) != "")
			{
				$ir_para = base64_decode($ir_para);
			}
			else
			{
				$ir_para = "";
			}
			
			$ir_para = (trim($ir_para) == "/cieprev/index.php" ? "" : $ir_para);
			
			header( "Location:".base_url_eprev() . "index.php?ir_para=".base_url()."index.php/".$ir_para);
		}
		
		//header( "Location:".base_url_eprev() . "index.php" );
		/*$data['return_page'] = $this->return_page; $this->load->view('home/login', $data);*/
	}

	function aduana()
	{
		$passport = $_POST['cieprev_passport'];
		$location = $_POST['cieprev_location'];

		$b = $this->simplelogin->login( "", "", "", $passport );

		if( !$b )
		{
			redirect('login');
			echo 'login';
		}
		else
		{
			redirect($location);
			echo $location;
		}
	}

	private function configurar_pagina_de_retorno()
	{
		// Acesso a página interna do ePrev
		$eprev_return_page = "";
		if(isset($_SESSION['return_page']))
		{
			$eprev_return_page = $_SESSION['return_page'];
		}

		if($eprev_return_page!="")
		{
			$this->return_page = $eprev_return_page;
		}
		else
		{
			$this->return_page = $this->session->userdata('return_page');
		}

		$this->return_page = str_replace("/cieprev/index.php", "", $this->return_page);
	}

	function entrar()
	{
		$u = $this->input->post('user_text');
		$p = $this->input->post('pass_text');
		// $i = $this->input->post('interatividade_check');

		// echo "teste"; exit;
		$b = $this->simplelogin->login( $u, $p );
		if($b)
		{
			if( $i=='S' )
			{
				$q = $this->db->query( "

					UPDATE projetos.usuarios_controledi 
					SET opt_interatividade='S'
					WHERE codigo = " . $this->session->userdata('codigo') . "

				;" );
			}

			$_SESSION['usuario'] = $this->session->userdata('usuario');
			if($this->return_page!='')
			{
				$_SESSION['return_page'] = "";
				$this->session->set_userdata(array('return_page'=>''));

				if( preg_match('/\.php/i', $this->return_page) )
				{
					$protocolo = (isset($_SERVER['HTTPS']))?"https":"http";
					header('location:'.$protocolo.'://'.$_SERVER['SERVER_NAME'].'/controle_projetos/'.$this->return_page);
				}
				else
				{
					redirect( $this->return_page );
				}
			}
			else
			{
				redirect( 'home' );
			}
		}
		else
		{
			redirect('login');
		}
	}

	function sair()
	{
		$this->simplelogin->logout();
		redirect('home');
	}
	
	
	function validar_login()
	{
		$u  = $this->input->post("validar_login_usuario", TRUE);  
		$p  = $this->input->post("validar_login_senha");  
		$ir = $this->input->post("validar_login_ir_para", TRUE);  
		$fl = $this->simplelogin->login($u, $p, "", "", TRUE);
		
		if($fl)
		{
			redirect((trim($ir) != "" ? $ir."/".session_id() : "home"));
		}
		else
		{
			echo '	
					<script>
						alert("Erro\n\nSENHA INVÁLIDA\n\n");
						location.href="'.(trim($ir) != "" ? site_url($ir) : site_url("home")).'";
					</script>
				 ';
			exit;
			/*
			Você realizou 2 tentativas consecutivas de logon erradas e se errar novamente bloqueará sua senha da rede.
			*/			
		}
	}
}
?>