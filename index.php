<?php 
session_start();
// LIMPA AS SESSIONS
unset($_SESSION["play"], $_SESSION["mascara"], $_SESSION["contador"], $_SESSION["som"], $_SESSION["jogador"], $_SESSION["simbolo"]);
?>
<body background="./imagens/caderno31.jpg">
	<table align="center" border="0">
		<tr>
			<td colspan="2" align="center"><img src="./imagens/banner.png"></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><img src="./imagens/logo.png"></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><img src="./imagens/selecione.png"></td>
		</tr>
		<tr>
			<td align="right"><a href="core.php?player=o"><img width="42" height="45" src="./imagens/o.png" style="border: none"></a></td>
			<td align="left"><a href="core.php?player=x"><img width="42" height="45" src="./imagens/x.png" style="border: none"></a></td>
		</tr>
	</table>
</body>
