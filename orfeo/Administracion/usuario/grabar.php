<?php 
session_start();

	$ruta_raiz = "../..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/*********************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hollmanlp@gmail.com              Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
    
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];

$sqlFechaHoy=$db->conn->DBTimeStamp(time());
$db->conn->debug = true;
?>
<html>
<head>
<title>Untitled Document</title>
<link rel="stylesheet" href="../../estilos/orfeo.css">
</head>
<body>
<?php
//$db->conn->debug = true;
if ($usModo ==2)
{
	
$isql = "SELECT USUA_DOC, USUA_NOMB, DEPE_CODI, USUA_LOGIN, USUA_NACIM, USUA_AT, USUA_PISO, USUA_EXT,
			USUA_EMAIL, USUA_CODI FROM USUARIO WHERE USUARIO.USUA_LOGIN = '" .$usuLogin ."'";
			$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->query($isql);
	$isqlRadic = "SELECT RADI_NUME_RADI FROM RADICADO WHERE RADI_DEPE_ACTU = " . $rs->fields["DEPE_CODI"] .
				" AND RADI_USUA_ACTU = " . $rs->fields["USUA_CODI"];
	$rsRadic = $db->query($isqlRadic);
	$radicado = $rsRadic->fields["RADI_NUME_RADI"];
	if(!trim($radicado)){
	if($perfilOrig != $perfil &&  $rs->fields["DEPE_CODI"] == $dep_sel)
	{	if($perfilOrig == "Jefe" && $perfil == "Normal")
		{	$isqlCod = "SELECT MAX(USUA_CODI) AS NUMERO FROM USUARIO WHERE DEPE_CODI = ".$dep_sel;
			$rs7= $db->query($isqlCod);
			//$nusua_codi = $rs7->fields["NUMERO"] + 1;
			$nusua_codi = $rs7->fields[0] + 1;
                        if($nusua_codi==1)  $nusua_codi++;
		}
		if($perfilOrig == "Normal" && $perfil == "Jefe" )
		{
		  $nusua_codi = 1;	
		}
	  
		$isql1 = $isql1." DEPE_CODI = ".$dep_sel.", ";
		$isql1 = $isql1." USUA_CODI = ".$nusua_codi.", ";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', '".$rs->fields["USUA_CODI"]."', $dep_sel, '".$cedula."', 50, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	}else{
	 ?> 		<table align="center" border="2" bordercolor="#000000">
		<form name="frmAbortar" action="../formAdministracion.php" method="post">
			<tr bordercolor="#FFFFFF"> <td width="211" height="30" colspan="2" class="listado2"><p><span class=etexto>
				<center><B>El usuario <?=$usuLogin?> tiene radicados a su cargo, NO PUEDE CAMBIAR DE DEPENDENCIA</B></center>
				</span></p> </td> </tr>
				<tr bordercolor="#FFFFFF">	<td height="30" colspan="2" class="listado2">
				<center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
				<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
				<input name="krd" type="hidden" value='<?=$krd?>'>
				</td>
			</tr>
		</form>
		</table>
	    <?
	  return;
	}
	if($rs->fields["USUA_DOC"] <> $cedula)
	{	$isql1 = "USUA_DOC = ".$cedula.", ";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."', 4, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	if($rs->fields["USUA_NOMB"] <> $nombre)
	{	$isql1 = $isql1." USUA_NOMB = '".$nombre."', ";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."', 5, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	if($rs->fields["DEPE_CODI"] <> $dep_sel)
	{	if (!$radicado)
		{	$isqlCod = "SELECT MAX(USUA_CODI) AS NUMERO FROM USUARIO WHERE DEPE_CODI = ".$dep_sel;
			$rs7= $db->query($isqlCod);
	      		$nusua_codi = $rs7->fields["NUMERO"] + 1;
			//$nusua_codi = $rs7->fields[0] + 1;
			if($nusua_codi==1 && $perfil == "Normal")  $nusua_codi++;
			$isql1 = $isql1." DEPE_CODI = ".$dep_sel.", ";
			$isql1 = $isql1." USUA_CODI = ".$nusua_codi.", ";
			$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."', 3, ".$sqlFechaHoy.", '".$usuLogin."')";
			$db->conn->Execute($isql);
		}
		else
		{
?>
		<table align="center" border="2" bordercolor="#000000">
		<form name="frmAbortar" action="../formAdministracion.php" method="post">
			<tr bordercolor="#FFFFFF"> <td width="211" height="30" colspan="2" class="listado2"><p><span class=etexto>
				<center><B>El usuario <?=$usuLogin?> tiene radicados a su cargo, NO PUEDE CAMBIAR DE DEPENDENCIA</B></center>
				</span></p> </td> </tr>
				<tr bordercolor="#FFFFFF">	<td height="30" colspan="2" class="listado2">
				<center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
				<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
				<input name="krd" type="hidden" value='<?=$krd?>'>
				</td>
			</tr>
		</form>
		</table>
<?php
		return;
		}
	}
	if($rs->fields["USUA_AT"] <> $ubicacion)
	{	$isql1 = $isql1." USUA_AT = '".$ubicacion."', ";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."', 7, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	if($rs->fields["USUA_EXT"] <> $extension)
	{	$isql1 = $isql1." USUA_EXT = ".$extension.", ";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."', 39, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	if($rs->fields["USUA_PISO"] <> $piso)
	{	$isql1 = $isql1." USUA_PISO = ".$piso.", ";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."',8, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	if($rs->fields["USUA_EMAIL"] <> $email)
	{	$isql1 = $isql1." USUA_EMAIL = '".$email."'";
		$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO, SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '".$usua_doc."', ".$rs->fields["USUA_CODI"].", $dep_sel, '".$cedula."', 40, ".$sqlFechaHoy.", '".$usuLogin."')";
		$db->conn->Execute($isql);
	}
	else
		$isql1 = substr ($isql1, 0, strlen($isql1)-2);

	if ($isql1 != "")
	{	$isql1 = "UPDATE USUARIO SET " .$isql1. " WHERE USUA_LOGIN = '".$usuLogin."'";
		$db->conn->Execute($isql1);
	}

	include "acepPermisosModif.php";

	// VALIDACION E INSERCION DE DEPENDENCIAS SELECCIONADAS VISIBLES
	if (is_array($_POST['dep_vis']))
	{	$db->conn->Execute("DELETE FROM DEPENDENCIA_VISIBILIDAD WHERE DEPENDENCIA_VISIBLE=$dep_sel");
		$rs_sec_dep_vis = $db->conn->Execute("SELECT MAX(CODIGO_VISIBILIDAD) AS IDMAX FROM DEPENDENCIA_VISIBILIDAD");
		$id_CodVis = $rs_sec_dep_vis->Fields('IDMAX');
		while(list($key, $val) = each($_POST['dep_vis']))
		{	$id_CodVis += 1;
			$db->conn->Execute("INSERT INTO DEPENDENCIA_VISIBILIDAD VALUES ($id_CodVis,$dep_sel,$val)");
		}
		unset($id_CodVis);
		$rs_sec_dep_vis->Close();
		unset($rs_sec_dep_vis);
	}

	$isql = "select * from USUARIO WHERE USUA_LOGIN = '".$usuLogin."'";
	$rs=$db->query($isql);
	if (!$swConRadicado)
	{
?>
		<table align="center" border="2" bordercolor="#000000">
		<form name="frmConfirmaCreacion" action="../formAdministracion.php" method="post">
			<tr bordercolor="#FFFFFF"> <td width="211" height="30" colspan="2" class="listado2"><p><span class=etexto>
			<center><B>El usuario <?=$usuLogin?> ha sido Modificado con Exito</B></center>
			</span></p> </td> </tr>
			<tr bordercolor="#FFFFFF">	<td height="30" colspan="2" class="listado2">
			<center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
			<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
			<input name="krd" type="hidden" value='<?=$krd?>'>
			</td> </tr>
		</form>
		</table>
<?php
	} else return;
}
else
{
  echo  "". $perfil;	
  if($perfil=="Normal")
	{	$isql = "SELECT MAX(USUA_CODI) AS NUMERO FROM USUARIO WHERE DEPE_CODI = ".$dep_sel;
		$rs7= $db->query($isql);
		if($rs7->fields["NUMERO"]) $nusua_codi = $rs7->fields["NUMERO"] + 1;
  			else $nusua_codi = $rs7->fields[0] + 1;
		echo "->".$nusua_codi;
	}elseif ($perfil=="Jefe") $nusua_codi = 1;
	$isql_inicial = "INSERT INTO USUARIO (USUA_CODI, DEPE_CODI,USUA_LOGIN,USUA_FECH_CREA,USUA_NOMB, USUA_DOC, USUA_NACIM, ";
	$isql_final = " VALUES ($nusua_codi, $dep_sel, '".strtoupper($usuLogin)."', $sqlFechaHoy, '".$nombre."', $cedula, ";
	if (($dia == "") && ($mes == "") && ($ano == ""))
		//Modificado idrd	
		$isql_final = $isql_final . "null, ";
	else
	{	
		//Modificado carlos barrero - hospital fontibon
		$fenac = " TO_DATE('" .$ano."-".$mes."-".$dia. ",  01:01:01 AM','RRRR-MM-DD, HH:MI:SS AM') ,";
		//$fenac = " CAST(CONVERT(VARCHAR,'" .$mes."/".$dia."/".$ano. "',101) AS DATETIME) ,";
		$fNac = "$ano-$mes-$dia";
		$fenac = $db->conn->DBDate($fNac).",";
		$isql_final = $isql_final.$fenac;
	}
	if ($piso <> "")
	{	$isql_inicial = $isql_inicial . " USUA_PISO, ";
		$isql_final = $isql_final .$piso.", ";
	}
	if ($ubicacion)
	{	$isql_inicial = $isql_inicial . " USUA_AT, ";
		$isql_final = $isql_final."'".$ubicacion."', ";
	}
	if ($email)
	{	$isql_inicial = $isql_inicial . " USUA_EMAIL, ";
		$isql_final = $isql_final."'".$email."', ";
	}
	if ($extension)
	{	$isql_inicial = $isql_inicial . " USUA_EXT, ";
		$isql_final = $isql_final.$extension.", ";
	}
	$isql_inicial = $isql_inicial . " USUA_PASW, PERM_RADI_SAL, ";
	$isql_final = $isql_final."123, 2,";
	include "acepPermisosNuevo.php";
	$isql = $isql_inicial.$isql_final;

	//INICIALIZAMOS LA INSERCION EN LAS DIFERENTES TABLAS.....
	$db->conn->Execute($isql);	//Tabla USUARIOS
	// VALIDACION E INSERCION DE DEPENDENCIAS SELECCIONADAS VISIBLES
	if (is_array($_POST['dep_vis']))
	{	$db->conn->Execute("DELETE FROM DEPENDENCIA_VISIBILIDAD WHERE DEPENDENCIA_VISIBLE=$dep_sel");
		$rs_sec_dep_vis = $db->conn->Execute("SELECT MAX(CODIGO_VISIBILIDAD) AS IDMAX FROM DEPENDENCIA_VISIBILIDAD");
		$id_CodVis = $rs_sec_dep_vis->Fields('IDMAX');
		$ok = true;
		while((list($key, $val) = each($_POST['dep_vis'])) && $ok)
		{	$id_CodVis += 1;
			$ok = $db->conn->Execute("INSERT INTO DEPENDENCIA_VISIBILIDAD VALUES ($id_CodVis,$dep_sel,$val)");	//Tabla Dependencia_Visibilidad
		}
		unset($id_CodVis);
		$rs_sec_dep_vis->Close();
		unset($rs_sec_dep_vis);
	}

	$isql = "select USUA_CODI from USUARIO WHERE USUA_LOGIN = '".strtoupper($usuLogin)."'";
	$rs = $db->query($isql);
	if ($masiva)
	{	$isql = "INSERT INTO CARPETA_PER (USUA_CODI, DEPE_CODI, NOMB_CARP, DESC_CARP, CODI_CARP) VALUES (" . $rs->fields["USUA_CODI"] . ", " . $dep_sel . ", 'Masiva', 'Radicacion Masiva', 5 )";
		$db->conn->Execute($isql);
	}
	$isql = "INSERT INTO SGD_USH_USUHISTORICO (SGD_USH_ADMCOD, SGD_USH_ADMDEP, SGD_USH_ADMDOC, SGD_USH_USUCOD, SGD_USH_USUDEP, SGD_USH_USUDOC, SGD_USH_MODCOD, SGD_USH_FECHEVENTO,SGD_USH_USULOGIN) VALUES ($codusuario, $dependencia, '". $usua_doc."', ".$rs->fields["USUA_CODI"].", ".$dep_sel.", '".$cedula."', 1 , ".$sqlFechaHoy.", '".$usuLogin."')";
	$db->conn->Execute($isql);
	$isql = "select USUA_ESTA, USUA_PRAD_TP2, USUA_PERM_ENVIOS, USUA_ADMIN, USUA_ADMIN_ARCHIVO, USUA_NUEVO, CODI_NIVEL, USUA_PRAD_TP1, USUA_MASIVA, USUA_PERM_DEV, SGD_PANU_CODI, USUA_PRAD_TP3, * from USUARIO WHERE USUA_LOGIN = '".strtoupper($usuLogin)."'";
	$rs = $db->query($isql);

	//Confirmamos las inserciones de datos
	//$ok = $db->conn->CompleteTrans();
	if (!$ok)
	{
	?>
		<form name="frmConfirmaCreacion" action="../formAdministracion.php" method="post">
		<table align="center" border="2" bordercolor="#000000">
		<tr bordercolor="#FFFFFF">
			<td width="211" height="30" colspan="2" class="listado2">
				<p><span class=etexto>
				<center><B>El usuario <?=$usuLogin?> ha sido creado con &Eacute;xito</B></center>
				</span></p>
			</td>
		</tr>
		<tr bordercolor="#FFFFFF">
			<td height="30" colspan="2" class="listado2">
				<center><input class="botones" type="submit" name="Submit" value="Aceptar"></center>
		      	<input name="PHPSESSID" type="hidden" value='<?=session_id()?>'>
		      	<input name="krd" type="hidden" value='<?=$krd?>'>
		    </td>
		</tr>
		</table>
		</form>
	<?php
	}
	else
	{
		echo "Existe un error en los datos diligenciados";
}	}
?>
</body>
</html>
