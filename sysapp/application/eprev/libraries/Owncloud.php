<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Owncloud{

    public function cria($dir)
	{
		$ci =& get_instance();
		
		$ch = curl_init();
		
		$args = array(
			'token' => '7a2584226d7f72f3a83920be80b2f33e',
			'dir'   => $dir
		);

		curl_setopt($ch, CURLOPT_URL, 'https://www.fcprev.com.br/srvweb/index.php/criar_diretorio_owncloud');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$retorno_json = curl_exec($ch);

		$json = json_decode($retorno_json, true);
		
		return $json;
	}
	
	public function envia($dir, $dir_file, $name_file)
	{
		$ci =& get_instance();
		
		$ch = curl_init();
		
		$args = array(
			'token'     => '7a2584226d7f72f3a83920be80b2f33e',
			'dir'       => $dir,
			'dir_file'  => $dir_file,
			'name_file' => $name_file,
		);

		curl_setopt($ch, CURLOPT_URL, 'https://www.fcprev.com.br/srvweb/index.php/enviar_arquivo_owncloud');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$retorno_json = curl_exec($ch);

		$json = json_decode($retorno_json, true);
		
		return $json;
	}
	
	public function deleta($dir)
	{		
		$ch = curl_init();
		
		$args = array(
			'token' => '7a2584226d7f72f3a83920be80b2f33e',
			'dir'   => $dir
		);

		curl_setopt($ch, CURLOPT_URL, 'https://www.fcprev.com.br/srvweb/index.php/deleta_arquivo_owncloud');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$retorno_json = curl_exec($ch);

		$json = json_decode($retorno_json, true);
		
		return $json;
	}

	public function renomeia($dir, $dir_new)
	{		
		$ch = curl_init();
		
		$args = array(
			'token'   => '7a2584226d7f72f3a83920be80b2f33e',
			'dir'     => $dir,
			'dir_new' => $dir_new
		);

		curl_setopt($ch, CURLOPT_URL, 'https://www.fcprev.com.br/srvweb/index.php/renomeia_owncloud');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$retorno_json = curl_exec($ch);

		$json = json_decode($retorno_json, true);
		
		return $json;
	}


}
?>