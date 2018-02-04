<?php

use Pimple\Container;

$container = new Container();

$container["ServerName"] = php_uname('n');

############################## CAMINHO ABSOLUTO E RELATIVO ##############################
if(is_dir($_SERVER['DOCUMENT_ROOT']."/portabilis"))
	{
		$container['AbsolutePath'] = $_SERVER['DOCUMENT_ROOT']."/portabilis"; //sem barra no final
		$container['RelativePath'] = "/portabilis"; //sem barra no final
	}
else
	{
		$container['AbsolutePath'] = $_SERVER['DOCUMENT_ROOT']; //sem barra no final
		$container['RelativePath'] = "/"; //sem barra no final
	}
############################## CAMINHO ABSOLUTO E RELATIVO ##############################


############################## HTTP ABSOLUTO E RELATIVO ##############################
if(is_dir($_SERVER['DOCUMENT_ROOT']."/portabilis"))
	{
		$container['HttpReferer'] = $_SERVER['HTTP_HOST']."/portabilis"; //sem barra no final
	}
else
	{
		$container['HttpReferer'] = $_SERVER['HTTP_HOST']; //sem barra no final
	}
############################## HTTP ABSOLUTO E RELATIVO ##############################


############################## BANCO DE DADOS ##############################
if($container['HttpReferer']=="portabilis.testesite.com.br")
	{
		$container['DatabasePDODriver'] = "pgsql";
		$container['DatabaseHost'] = "portabilishml.pgsql.dbaas.com.br";
		$container['DatabaseCharset'] = "utf8";
		$container['DatabaseName'] = "portabilis";
		$container['DatabaseUser'] = "portabilis";
		$container['DatabasePass'] = "Long673-Eye-";
	}
else
	{
		$container['DatabasePDODriver'] = "pgsql";
		$container['DatabaseHost'] = "localhost";
		$container['DatabaseCharset'] = "utf8";
		$container['DatabaseName'] = "portabilis";
		$container['DatabaseUser'] = "portabilis";
		$container['DatabasePass'] = "Show7?wore??";
	}
############################## BANCO DE DADOS ##############################

############################## TIMEZONE ##############################
$timezone = "America/Cuiaba";
date_default_timezone_set($timezone); //Define o TIMEZONE
############################## TIMEZONE ##############################




############################## ARRAYS PARA RETORNAR NOMES DOS DIAS DA SEMANA E MESES EM PORTUGÊS ##############################


//ARRAY PARA MOSTRAR O DIA DA SEMANA EM PORTUGUES
$container["DaysOfWeek"] = array('0' => 'Domingo',
										'1' => 'Segunda-Feira',
										'2' => 'Terça-Feira',
										'3' => 'Quarta-Feira',
										'4' => 'Quinta-Feira',
										'5' => 'Sexta-Feira',
										'6' => 'Sábado');


//ARRAY PARA MOSTRAR O DIA DO MES EM PORTUGUES
$container["Months"] = array('01' => 'Janeiro',
										'02' => 'Fevereiro',
										'03' => 'Março',
										'04' => 'Abril',
										'05' => 'Maio',
										'06' => 'Junho',
										'07' => 'Julho',
										'08' => 'Agosto',
										'09' => 'Setembro',
										'10' => 'Outubro',
										'11' => 'Novembro',
										'12' => 'Dezembro');

//ARRAY PARA MOSTRAR O DIA DO MES EM PORTUGUES (MÊS FORMATO n)
$container["MonthsN"] = array('1' => 'Janeiro',
										'2' => 'Fevereiro',
										'3' => 'Março',
										'4' => 'Abril',
										'5' => 'Maio',
										'6' => 'Junho',
										'7' => 'Julho',
										'8' => 'Agosto',
										'9' => 'Setembro',
										'10' => 'Outubro',
										'11' => 'Novembro',
										'12' => 'Dezembro');
############################## ARRAYS PARA RETORNAR NOMES DOS DIAS DA SEMANA E MESES EM PORTUGÊS ##############################




############################## VARIÁVEIS PARA CONTROLE DA DATA ATUAL ##############################
$container["DateNow"] = array("NowDay" => date("d"), //dia atual
							"NowMonth" => date("m"), //mes atual
							"NowMonthN" => date("n"), //mes atual sem zero
							"NowYear" => date("Y"), //ano atual
							"NowHour" => date("H"), //hora atual
							"NowMinute" => date("i"), //minuto atual
							"NowSecond" => date("s"), //segundo atual
							"NowWeek" => date("w"), //dia da semana atual (formato numerico)
							);

$container["Date"] = array("NowMktime" => mktime($container["DateNow"]["NowHour"],$container["DateNow"]["NowMinute"],$container["DateNow"]["NowSecond"],$container["DateNow"]["NowMonth"],$container["DateNow"]["NowDay"],$container["DateNow"]["NowYear"]), //data atual no foramto MK
							"NowUS" => $container["DateNow"]["NowYear"]."-".$container["DateNow"]["NowMonth"]."-".$container["DateNow"]["NowDay"]." ".$container["DateNow"]["NowHour"].":".$container["DateNow"]["NowMinute"].":".$container["DateNow"]["NowSecond"], //data atual no foramto US (0000-00-00 00:00:00)
							"NowBR" => $container["DateNow"]["NowDay"]."/".$container["DateNow"]["NowMonth"]."/".$container["DateNow"]["NowYear"]." ".$container["DateNow"]["NowHour"]."h".$container["DateNow"]["NowMinute"], //data atual no foramto BR (00/00/000 00h00)
							"NowTime" => $container["DateNow"]["NowHour"].":".$container["DateNow"]["NowMinute"].":".$container["DateNow"]["NowSecond"], //horário atual no foramto (00:00:00)
							"NowBR_Data_Ext" => $container["DateNow"]["NowDay"]." de ".$container["MonthsN"][$container["DateNow"]["NowMonthN"]]." de ".$container["DateNow"]["NowYear"], //data atual por exenteso em portugues no foramto (dia do mês de ano)
							);
############################## VARIÁVEIS PARA CONTROLE DA DATA ATUAL ##############################



############################## VARIÁVEIS PARA CÁLCULO DE INTERVALO DE DATAS MK ##############################
$container["DateMK"] = array(
		"DayMK" => 86400, //valor numerico do MK para 1 dia
		"MonthMK" => 86400*30, //valor numerico do MK para 1 mês de 30 dias
		"HourMK" => 86400/24, //valor numerico do MK para 1 hora
		"MinuteMK" => 86400/60, //valor numerico do MK para 1 minuto
	);
############################## VARIÁVEIS PARA CÁLCULO DE INTERVALO DE DATAS MK ##############################




//CHAVE DE CRIPTOGRAFIA
$container["DefuseKey"] = "def000008089d1f7eb4efbaac287fc0698af581db8924fa46f4be4597d7c9519282361a0999c22e96b9994c2870863b85baddca482389490c4ff7e58a4b84e84ec0f65ab";


