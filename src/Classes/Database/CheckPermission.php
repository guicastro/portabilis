<?php

/*#######################################################
|														|
| Arquivo com a classe que retorna se o usuário possui	|
| permissão para aquele módulo ou operação				|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Database;

/*### CLASSE QUE CHECA SE O USUÁRIO TEM PERMISSÃO NO MÓDULO OU OPERAÇÃO ###*/
class CheckPermission {

	protected $db;
	protected $Module;
	protected $Request;
	protected $Action;
	protected $TokenClass;
	protected $SQLAction;
	protected $CheckPermission;
	protected $SpecialPermissions;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct(\Database\IConnect $db, $container) {

		//CHAMADA PARA MÉTODO CONNECT DA CLASSE COM INTERFACE ICONNECT
		$this->db = $db->connect();

		//CHAMADA PARA CLASSE TOKEN
		$this->TokenClass = $container['TokenClass'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### ARMAZENA O MÓDULO ###*/
	public function setModule($ModuleDefs) {

		if($this->Request["Route"]=="DataTablesGrid") $this->Module = $ModuleDefs["Name"];
		else $this->Module = $ModuleDefs->Name;

		return $this->Module;
	}
	/*### ARMAZENA O MÓDULO ###*/


	/*### ARMAZENA TODA A VARIÁVEL REQUEST ###*/
	public function setRequest ($Request) {

		$this->Request = $Request;
		return $this->Request;
	}
	/*### ARMAZENA TODA A VARIÁVEL REQUEST ###*/


	/*### ARMAZENA A ACTION QUE DETERMINARÁ AS DEMAIS AÇÕES ###*/
	public function setAction ($Action) {

		if($this->Request["Route"]=="DataTablesGrid") $this->Action = "gerenciar";
		else $this->Action = $Action;

		return $this->Action;
	}
	/*### ARMAZENA A ACTION QUE DETERMINARÁ AS DEMAIS AÇÕES ###*/




	/*### ARMAZENA A ACTION QUE DETERMINARÁ AS DEMAIS AÇÕES ###*/
	public function getSpecialPermissions () {

		return $this->SpecialPermissions;
	}
	/*### ARMAZENA A ACTION QUE DETERMINARÁ AS DEMAIS AÇÕES ###*/





	/*### CHECA SE O USUÁRIO TEM PERMISSÃO DO MÓDULO E OPERAÇÃO E RETORNA TRUE OU FALSE ###*/
	public function ReturnPermission() {

		if($this->Action=="selecionar") $ActionPermission = "gerenciar";
		elseif($this->Action=="Grid") $ActionPermission = "gerenciar";
		elseif($this->Action=="alterar_permissoes") $ActionPermission = "alterar";
		elseif($this->Action=="duplicar") $ActionPermission = "alterar";
		elseif($this->Action=="transferir_usuarios") $ActionPermission = "alterar";
		elseif($this->Action=="excluir_selecionados") $ActionPermission = "excluir";
		else $ActionPermission = $this->Action;

		$this->SQLAction = "SELECT
									UsuaPerm_Valor as PERMISSAO
								FROM
									UsuariosPermissoes
								WHERE
									Usuarios_Usua_id = ".$this->TokenClass->getClaim("UserData")->Usua_id."
									AND UsuaPerm_Modulo = '".$this->Module."'
									AND UsuaPerm_Operacao = '".$ActionPermission."'
									AND UsuaPerm_Delete = 0";


		//PERPARA E EXECUTA A QUERY DOS MÓDULOS
		$SQLPermission = $this->db->query($this->SQLAction);

		$Permission = $SQLPermission->fetch(\PDO::FETCH_OBJ);

		if($Permission->permissao==1) $this->CheckPermission = true;
		elseif($this->Request["Route"]=="DynamicTable") $this->CheckPermission = true;
		elseif($this->Request["alterar_perfil"]=="OK") $this->CheckPermission = true;
		elseif($this->Module=="Painel") $this->CheckPermission = true;
		else $this->CheckPermission = false;

		return $this->CheckPermission;
	}
	/*### CHECA SE O USUÁRIO TEM PERMISSÃO DO MÓDULO E OPERAÇÃO E RETORNA TRUE OU FALSE ###*/




	/*### RETORNA O OBJETO DE DADOS CONFORME CADA PERMISSÃO ESPECIAL ###*/
	public function ReturnSpecialPermissions() {

		$this->SQLAction = "SELECT
									UsuaPerm_Operacao as PERMESP
								FROM
									UsuariosPermissoes
								WHERE
									Usuarios_Usua_id = ".$this->TokenClass->getClaim("UserData")->Usua_id."
									AND UsuaPerm_Modulo = 'permissoes-especiais'
									AND UsuaPerm_Delete = 0";


		//PERPARA E EXECUTA A QUERY DOS MÓDULOS
		$ExecuteSQLSpecialPermissions = $this->db->query($this->SQLAction);

		//RETORNA O OBJETO COM TODAS AS PERMISSÕES ESPECIAIS
		$SpecialPermissions = $ExecuteSQLSpecialPermissions->fetchAll(\PDO::FETCH_OBJ);
	}
	/*### RETORNA O OBJETO DE DADOS CONFORME CADA PERMISSÃO ESPECIAL ###*/


}
/*### CLASSE QUE CHECA SE O USUÁRIO TEM PERMISSÃO NO MÓDULO OU OPERAÇÃO ###*/
