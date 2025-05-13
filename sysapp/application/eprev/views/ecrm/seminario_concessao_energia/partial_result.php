	<div style="width:100%; text-align:left;">
	<span class="links2">&raquo; Total de registros: <?php echo $quantos; ?></span>
	</div>	
	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
    	<thead>
		<tr>
			<td>Data de inscrição</td>					
			<td>Nome</td>
			<td>E-mail</td>					
			<td>Cargo</td>
			<td>Empresa</td>
			<td>Endereço</td>
			<td>CEP</td>
			<td>UF</td>
			<td>Cidade</td>
			<td>Telefone-Ramal/Celular</td>
			<td>RE-d</td>
			<td>Excluir</td>
			<td>Status Email</td>
			<td>Presente</td>
		</tr>
    	</thead>
		<tbody>
			<? foreach( $collection as $item ): ?>
				<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td><?php echo $item['dt_inscricao']; ?></td>
					<td style="white-space: nowrap"><?php echo $item['nome']; ?></td>
					<td><?php echo $item['email']; ?></td>
					<td><?php echo $item['cargo']; ?></td>
					<td><?php echo $item['empresa']; ?></td>
					<td><?php echo $item['endereco'] . ', ' . $item['numero'] . ', ' . $item['complemento']; ?></td>
					<td><?php echo $item['cep']; ?></td>
					<td><?php echo $item['uf']; ?></td>
					<td><?php echo $item['cidade']; ?></td>
					<td><?php echo '('.$item['telefone_ddd'].')'.$item['telefone'].' - '.$item['telefone_ramal']; ?></td>
					<td><?php echo $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
					<td>
						<input type="button" value="Excluir" onclick="excluirInscricao('<?php echo $item['cd_seminario_concessao_energia_md5']; ?>');" class="botao">
					</td>					
					<td><?php echo ($item['fl_retorno'] == "S" ? "<b style='color:red'>Retornou</b>" : "<b style='color:green'>Normal</b>"); ?></td>
					<td>
						<select name="fl_presente" id="fl_presente" onchange="setPresente(this.value,'<?php echo $item['cd_seminario_concessao_energia_md5']; ?>');">	
							<option value="" <?php echo ($item['fl_presente'] == "" ? "selected" : ""); ?>></option>
							<option value="S" <?php echo ($item['fl_presente'] == "S" ? "selected" : ""); ?>>Sim</option>
							<option value="N" <?php echo ($item['fl_presente'] == "N" ? "selected" : ""); ?>>Não</option>
						</select>
					</td>					
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
