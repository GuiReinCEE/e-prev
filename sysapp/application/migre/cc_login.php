<?php
	include_once('inc/sessao.php');
	
	#### REDIRECIONA PARA NOVA TELA (23/12/2015) ####
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."index.php/servico/contracheque".'">';
	exit;
include_once('inc/conexao.php');
include_once('inc/ldap.php');
include_once('inc/class.TemplatePower.inc.php');
include_once('inc/ePrev.DAL.DBConnection.php');
include_once('inc/class.pcrypt.php');

class eprev_cc_login
{
	private $usuario;
	private $senha;
	private $_isPost;
	private $dal;
	
	function eprev_cc_login( $_usuario, $_db )
	{
		$this->dal = new DBConnection();
		$this->dal->loadConnection( $_db );
		$this->usuario = $_usuario;
		$this->requestParams();
	}
	
	function __destruct() 
	{
		// do nothing
    }
	
	function requestParams()
	{
		if (isset($_POST["senha"])) {
			$this->senha = $_POST["senha"];
			//$crypt = new pcrypt(MODE_ECB, "BLOWFISH", "secretkey");
			//$this->senha = $crypt->encrypt( $this->senha );
			$this->_isPost = true;
		}
	}
	
	public function isPost()
	{
		return $this->_isPost;
	}
	
	public function isLoginValid()
	{
		$ret = false;
		$this->dal->createQuery("
								SELECT fl_ldap_autenticar,
								       usuario
								  FROM projetos.usuarios_controledi
								 WHERE codigo='{codigo}'
								");
        $this->dal->setAttribute( "{codigo}", $this->usuario );		
		$result = $this->dal->getResultset();
        if ($result) 
		{
			$ar_reg = pg_fetch_array($result);

			if($ar_reg['fl_ldap_autenticar'] == 'S')
			{
				if (valida_ldap($ar_reg['usuario'],$this->senha)) 
				{
					$qr_login = "
							    SELECT COUNT(*) AS usuario_valido
							      FROM projetos.usuarios_controledi 
							     WHERE codigo = '{codigo}'
								   AND tipo   <> 'X'										
							    ";				
				}
				else
				{
					$ret = false;
				}
			}
			else
			{
				$qr_login = "
							SELECT COUNT(*) AS usuario_valido
							  FROM projetos.usuarios_controledi 
							 WHERE codigo    = '{codigo}'
							   AND senha_md5 = MD5('{senha}')	
							   AND tipo      <> 'X'										
							";				
			}			
		}
		else 
		{
			$ret = false;
		}		
		
		$this->dal->createQuery($qr_login);
        $this->dal->setAttribute( "{codigo}", $this->usuario );
        $this->dal->setAttribute( "{senha}",  $this->senha );
        
        $result = $this->dal->getResultset();
        
        if ( $result ) 
		{
			$reg = pg_fetch_array( $result );
			$ret = ( $reg["usuario_valido"]>0 );
		}
		else 
		{
			$ret = false;
		}

		
		/*
		$this->dal->createQuery("

	         SELECT COUNT(*) AS usuario_valido
	           FROM projetos.usuarios_controledi 
	          WHERE codigo = '{codigo}'
	            AND senha_md5 = MD5('{senha}')

        ");
        $this->dal->setAttribute( "{codigo}", $this->usuario );
        $this->dal->setAttribute( "{senha}",   $this->senha );
        
        $result = $this->dal->getResultset();
        
        if ( $result ) {
			$reg = pg_fetch_array( $result );
			$ret = ( $reg["usuario_valido"]>0 );
		}
		else {
			$ret = false;
		}
		*/
		return $ret;
	}
}

/******************************************
 * START PAGE PROCESS
 */
session_start();
if($_SESSION['nr_tentativa'] == "")
{
	$_SESSION['nr_tentativa'] = 0;
}

$_this = new eprev_cc_login( $Z, $db );

if ( $_this->isPost() ) 
{
	
	if ( $_this->isLoginValid() ) {
		$_SESSION["CCLogin"] = "VALID";
		$_SESSION['nr_tentativa'] = 0;
		$redirect = "location: cc.php";
	}
	else {
		$_SESSION["CCLogin"] = "INVALID";
		$_SESSION['nr_tentativa']  = $_SESSION['nr_tentativa'] + 1;
		$msg = "Senha inválida";
		if($_SESSION['nr_tentativa'] == 2)
		{
			$msg = "Você realizou 2 tentativas consecutivas de logon erradas e se errar novamente bloqueará sua senha da rede.";
		}		
	}

}

if ( $redirect=="" )
{
	
	// Template
	$tpl = new TemplatePower('tpl/tpl_cc_login.html');
	$tpl->prepare();
	
	$tpl->assign( "msg", $msg );
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	// Renderização do html
	$tpl->printToScreen();
	
}
else {
	
	header( $redirect );
	
}

?>