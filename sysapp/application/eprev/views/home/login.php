<?php
$this->load->view('header_without_login');
?>
<center>
<div id="login">
<br>
  	<table border="0" width="100%">

  	<tr>

  		<td align="center" valign="middle"><img src="<?= base_url(); ?>/skins/skin001/img/logofceee.png" border="0" /></td>

  		<td align="center"><br />

  			<b>Área restrita</b><br><br>

  			<table border="0">
	  		<tr>
	  			<td><label for="user_text">Usuário</label></td>
	  			<td><input name="user_text" id="user_text" style="width:220px;"></td>
	  		</tr>
	  		<tr>
	  			<td><label for="pass_text">Senha</label></td>
	  			<td><input type="password" name="pass_text" id="pass_text" style="width:220px;" onkeypress="if(event.keyCode==13)realizar_login();" ></td>
	  		</tr>
	  		<tr><td></td><td><input type="button" value="Entrar" onclick="realizar_login();" /></td></tr>
	  		</table>

	  		<!-- <br /> redirecionamento: <?= $return_page; ?> -->

	  	</td>

  	</tr>

  	</table>
  	<br><br>
  	<hr width='90%' />
  	Esqueceu sua senha? Clique <a href="http://www.e-prev.com.br/controle_projetos/esqueci_senha.html"><b>aqui</b></a>.
  	<br><br>

</div>
</center>
<script>
	document.getElementById('user_text').focus();
</script>
<?php
$this->load->view('footer_without_login');
?>