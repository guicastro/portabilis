<?php

/*#######################################################
|														|
| Arquivo do Controller da aplicação que define as 		|
| as rotas e ações das chamadas da View 				|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/


header('Content-Type: text/html; charset=utf-8');

//CARREGA O ARQUIVO DE AUTOLOAD DE TODAS AS CLASSES (COMPOSER)
require_once("../../vendor/autoload.php");

//CARREGA O ARQUIVO DE CONFIGURAÇÃO GERAL
require_once("../../config/config.php");

//CARREGA O SERVIÇO DO PIMPLE
require_once("../Service/Service.php");

// echo "<pre>"; print_r($_REQUEST); "</pre>";

// echo "<pre>"; print_r($container['Connect']); "</pre>";


/*#### AÇÕES REALIZADAS EM TODAS AS CHAMADAS DO CONTROLLER, EXCETO O LOGIN ####*/
if($_REQUEST['Route']<>"Login") {

	/*### VALIDA O TOKEN DE ACESSO ###*/
	$Token = $container['TokenClass'];
	$Token->setToken($_REQUEST['Token']);
	$Token->receiveToken();


	$ValidateToken = $Token->ValidateToken();
	if($ValidateToken==true) {

		$TokenUserData = $Token->getClaim('UserData');
	} else {

		header('Content-Type: application/json');
		$response["InvalidToken"] = true;
		$response["error"] = "Invalid-Token";
		echo json_encode($response);
		exit;
	}
	/*### VALIDA O TOKEN DE ACESSO ###*/


	/*### CHECA AS PERMISSÕES ESPECIAIS DO USUÁRIO ###*/
	$CheckSpecialPermission = $container['CheckPermission'];
	$container["SpecialPermissions"] = $CheckSpecialPermission->ReturnSpecialPermissions();
	/*### CHECA AS PERMISSÕES ESPECIAIS DO USUÁRIO ###*/
}
/*#### AÇÕES REALIZADAS EM TODAS AS CHAMADAS DO CONTROLLER, EXCETO O LOGIN ####*/






/*#### AÇÕES REALIZADAS EM TODAS AS CHAMADAS DO CONTROLLER DO GRID E ACTIONFORM ####*/
if(($_REQUEST['Route']=="DataTablesGrid")||($_REQUEST['Route']=="ActionForm")) {

	$ModuleDefs = ($_REQUEST['Route']=="ActionForm") ? json_decode($_REQUEST['ModuleDefs']) : $_REQUEST['ModuleDefs'];

	/*### CHECA AS PERMISSÕES DO USUÁRIO NO MÓDULO E NA OPERAÇÃO ###*/
	$CheckPermission = $container['CheckPermission'];
	$CheckPermission->setRequest($_REQUEST);
	$CheckPermission->setModule($ModuleDefs);
	$CheckPermission->setAction($_REQUEST['action']);
	$Permission = $CheckPermission->ReturnPermission();
	// echo "<pre>"; print_r($CheckPermission); echo "</pre>";
	if($Permission<>true){

		header('Content-Type: application/json');
		$response["data"] = [];
		$response["Permission"] = false;
		$response["error"] = "Permission-Deined";
		$response["ErrorMsg"] = "Usuário não possui permissão para executar esta operação.";
		echo json_encode($response);
		exit;
	}
	/*### CHECA AS PERMISSÕES DO USUÁRIO NO MÓDULO E NA OPERAÇÃO ###*/
}
/*#### AÇÕES REALIZADAS EM TODAS AS CHAMADAS DO CONTROLLER DO GRID E ACTIONFORM ####*/






/*#### ROTA: GRID DE DADOS ####*/
if($_REQUEST['Route']=="DataTablesGrid") {

	$Grid = $container['DataTablesGrid'];
	$Grid->setModuleDefs($_REQUEST['ModuleDefs']);
	$Grid->setRequest($_REQUEST);
	$Grid->setSpecialPermissions($container["SpecialPermissions"]);
	$Grid->ConfigGrid();
	$Grid->setResponse();
	$Grid->BuildSqlGrid();
	$Grid->ReturnAction();

	// echo "<pre>"; print_r($Grid); "</pre>";
}
/*#### ROTA: GRID DE DADOS ####*/



/*#### ROTA: CHAMADA DO ACTIONFORM (CRUD) ####*/
if($_REQUEST['Route']=="ActionForm") {

	$ModuleDefs = json_decode($_REQUEST['ModuleDefs']);

	$ActionForm = $container[$ModuleDefs->Entity];
	$ActionForm->setModuleDefs($_REQUEST['ModuleDefs']);
	$ActionForm->setRequest($_REQUEST);
	$ActionForm->setAction($_REQUEST['action']);
	$ActionForm->setPrimaryKey($_REQUEST['id_reg']);
	$ActionForm->setPrimaryKeyName($_REQUEST['PrimaryKeyName']);
	$ActionForm->setSpecialPermissions($container["SpecialPermissions"]);
	$ActionForm->BeforeSQL();
	$ActionForm->setSQLSelectFields();
	$ActionForm->setSQLSelectFrom();
	$ActionForm->setSQLSelectWhere();
	$ActionForm->setSQLSelectGroup();
	$ActionForm->setSQLSelectAfterGroup();
	$ActionForm->setSQLSelectOrder();
	$ActionForm->setSQLDescribeEntity();
	$ActionForm->ExecuteDescribeEntity();
	$ActionForm->MaskInsertUpdateValues();
	$ActionForm->setSQLInsertInto();
	$ActionForm->setSQLInsertFields();
	$ActionForm->setSQLInsertValues();
	$ActionForm->setSQLUpdateTable();
	$ActionForm->setSQLUpdateSet();
	$ActionForm->setSQLUpdateWhere();
	$ActionForm->setSQLDelete();
	$ActionForm->BuildSqlAction();
	$ActionForm->BeforeExecuteAction();
	$ActionForm->ExecuteAction();
	$ActionForm->AfterExecuteAction();
	$ActionForm->MaskResultSQLAction();
	$ActionForm->ReturnAction();

	// echo "<pre>"; print_r($ActionForm); "</pre>";
}
/*#### ROTA: CHAMADA DO ACTIONFORM (CRUD) ####*/



/*#### ROTA: LOGIN ####*/
if($_REQUEST['Route']=="Login") {

	$Login = $container['Usuario'];
	$Login->setRequest($_REQUEST);
	$Login->Login();
	$Login->ReturnAction();

	// echo "<pre>"; print_r($Login); "</pre>";
}
/*#### ROTA: LOGIN ####*/



/*#### ROTA: LOGOUT ####*/
if($_REQUEST['Route']=="Logout") {

	$Logout = $container['Usuario'];
	$Logout->Logout();

	// echo "<pre>"; print_r($Logout); "</pre>";
}
/*#### ROTA: LOGOUT ####*/



/*#### ROTA: CHAMADA DAS TABELAS DINÂMICAS ####*/
if($_REQUEST['Route']=="DynamicTable") {

	$DynamicTable = $container['DynamicTable'];
	$DynamicTable->setDynamicTable($_REQUEST['DynamicTable']);
	$DynamicTable->setOtherTable($_REQUEST['OtherTable']);
	$DynamicTable->setRequest($_REQUEST);
	$DynamicTable->setSpecialPermissions($container["SpecialPermissions"]);
	$DynamicTable->setSQLSelectFields();
	$DynamicTable->setSQLSelectFrom();
	$DynamicTable->setSQLSelectWhere();
	$DynamicTable->setSQLSelectOrder();
	$DynamicTable->BuildSqlAction();
	$DynamicTable->ExecuteAction();
	$DynamicTable->MaskResultSQLAction();
	$DynamicTable->ReturnAction();

	// echo "<pre>"; print_r($DynamicTable); "</pre>";
}
/*#### ROTA: CHAMADA DAS TABELAS DINÂMICAS ####*/




/*#### ROTA: MODULESPERMISSIONS ####*/
if($_REQUEST['Route']=="ModulesPermissions") {

	$ModulesPermissions = $container['ModulesPermissions'];
	$ModulesPermissions->setRequest($_REQUEST);
	$ModulesPermissions->ExecuteAction();
	$ModulesPermissions->ReturnAction();

	// echo "<pre>"; print_r($ModulesPermissions); "</pre>";
}
/*#### ROTA: MODULESPERMISSIONS ####*/






/*#### ROTA: EXECUÇÃO DE FUNÇÕES COMPLEMENTARES DO PHP ####*/
if($_REQUEST['Route']=="ExecutePHPFunction") {

	/*#### RETORNA A BRANCH, TAG E DATA DA VERSÃO ATUAL ####*/
	if($_REQUEST['FunctionName']=="GitBranchTagDate") {

		$GitBranchTagDate = $container['GitBranchTagDate'];
		// echo "<pre>"; print_r($GitBranchTagDate); "</pre>";

		$result["GitBranchTagDate"] = $GitBranchTagDate->Branch()."/".$GitBranchTagDate->Tag()." - ".strftime("%d/%m/%Y",$GitBranchTagDate->Date());
		$response = json_encode($result);
	}
	/*#### RETORNA A BRANCH, TAG E DATA DA VERSÃO ATUAL ####*/



	/*#### RETORNA O AMBIENTE DA APLICAÇÃO ####*/
	else if($_REQUEST['FunctionName']=="AppEnvironment") {

		$AppEnvironment = $container['AppEnvironment'];
		// echo "<pre>"; print_r($AppEnvironment); "</pre>";

		$result["AppEnvironment"] = $AppEnvironment->Environment();
		$response = json_encode($result);
	}
	/*#### RETORNA O AMBIENTE DA APLICAÇÃO ####*/



	/*#### RETORNA OS DADOS DO SERVIDOR (PHPINFO) ####*/
	else if($_REQUEST['FunctionName']=="InfoServer") {

		$response = phpinfo();
	}
	/*#### RETORNA OS DADOS DO SERVIDOR (PHPINFO) ####*/



	/*#### RETORNA OS DADOS DO SERVIDOR (PHPINFO) ####*/
	else if($_REQUEST['FunctionName']=="TokenUserData") {

		$response = json_encode($TokenUserData);
	}
	/*#### RETORNA OS DADOS DO SERVIDOR (PHPINFO) ####*/



	/*#### RETORNA OS DADOS DO SERVIDOR (PHPINFO) ####*/
	else if($_REQUEST['FunctionName']=="ServerDate") {

		$Date = $container["Date"];
		$result["NowUS"] = $Date["NowUS"];
		$result["NowBR"] = $Date["NowBR"];
		$result["NowTime"] = $Date["NowTime"];
		$response = json_encode($result);
	}
	/*#### RETORNA OS DADOS DO SERVIDOR (PHPINFO) ####*/

	//EXIBE A RESPOSTA
	echo $response;
}
/*#### ROTA: EXECUÇÃO DE FUNÇÕES COMPLEMENTARES DO PHP ####*/



















