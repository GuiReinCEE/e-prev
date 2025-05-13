	<div style="width:100%; text-align:left;">
	<span class="links2">&raquo; Total de registros: <?php echo $quantos; ?></span>
	</div>	
	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
    	<thead>
		<tr>
			<td>Dt inscrição</td>					
			<td>Nome</td>
			<td>E-mail</td>					
			<td>Cargo</td>
			<td>Empresa</td>
			<td>Endereço</td>
			<td>CEP</td>
			<td>UF</td>
			<td>Cidade</td>
			<td>Telefone</td>
			<td>RE</td>
			<td>Presente</td>
			<td colspan="2">Certificado</td>
			<td></td>
		</tr>
    	</thead>
		<tbody>
			<? foreach( $collection as $item ): ?>
				<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td><?php echo $item['dt_inscricao']; ?></td>
					<td><?php echo $item['nome']; ?></td>
					<td><?php echo $item['email']; ?></td>
					<td><?php echo $item['cargo']; ?></td>
					<td><?php echo $item['empresa']; ?></td>
					<td><?php echo $item['endereco'] . ', ' . $item['numero'] . ', ' . $item['complemento']; ?></td>
					<td><?php echo $item['cep']; ?></td>
					<td><?php echo $item['uf']; ?></td>
					<td><?php echo $item['cidade']; ?></td>
					<td><?php echo $item['telefone']; ?></td>
					<td><?php echo $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
					<td>
						<select name="fl_presente" id="fl_presente" onchange="setPresente(this.value,'<?php echo $item['cd_seminario_seguridade_md5']; ?>');">	
							<option value="" <?php echo ($item['fl_presente'] == "" ? "selected" : ""); ?>></option>
							<option value="S" <?php echo ($item['fl_presente'] == "S" ? "selected" : ""); ?>>Sim</option>
							<option value="N" <?php echo ($item['fl_presente'] == "N" ? "selected" : ""); ?>>Não</option>
						</select>
					</td>
					<td align="center">

					</td>
					<td align="center">
						<!--
						<input type="button" value="Envia Email" onclick="enviaCertificado('<?php echo $item['cd_seminario_seguridade_md5']; ?>');" class="botao" <?php echo ($item['fl_presente'] != "S" ? "style='display:none;'" : ""); ?>>
						-->
					</td>
					<td><a href="seminario_seguridade/excluir/<?php echo $item['cd_seminario_seguridade']; ?>">[Excluir]<a/></td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
