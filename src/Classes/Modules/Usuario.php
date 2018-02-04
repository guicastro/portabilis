<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Usuários			|
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

namespace Modules;

/*### CLASSE DO CADASTRO DE USUÁRIO (HERDA CRUD) ###*/
class Usuario extends \Database\Crud {

	protected $DynamicTable;
	protected $CryptDecrypt;
	protected $IDPerfilParceiro;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];

		//CHAMADA PARA CLASSE CRYPTDECRYPT
		$this->CryptDecrypt = $container['CryptDecrypt'];

	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### ARMAZENA AS DEFINIÇÕES DO MÓDULO ###*/
	public function setModuleDefs ($ModuleDefs) {

		$ModuleDefs = json_decode($ModuleDefs);

		if($ModuleDefs->Name=="ParceiroUsuario") {

			$ModuleDefs->Table = "Usuarios";
			$ModuleDefs->Prefix = "Usua_";
		}


		$this->ModuleDefs = $ModuleDefs;
		return $this->ModuleDefs;
	}
	/*### ARMAZENA AS DEFINIÇÕES DO MÓDULO ###*/





	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{
				$ResultSQLAction = $this->ResultSQLAction;

				if(count($ResultSQLAction[0])>0) {

					$ResultSQLAction[0]->usua_reccreatedon = $this->MaskValue->Data($ResultSQLAction[0]->usua_reccreatedon,'US2BR_TIME');
					$ResultSQLAction[0]->usua_recmodifiedon = $this->MaskValue->Data($ResultSQLAction[0]->usua_recmodifiedon,'US2BR_TIME');
					$ResultSQLAction[0]->perfil_antigo = $ResultSQLAction[0]->perfis_perf_id;
					$ResultSQLAction[0]->login_antigo = $ResultSQLAction[0]->usua_login;

					//SELECIONA TODAS AS PERMISSÕES DO USUÁRIO
					$SQLPermissions = $this->db->query("SELECT * FROM UsuariosPermissoes WHERE UsuaPerm_Delete = 0 AND Usuarios_Usua_id = ".$this->PrimaryKey);

					//RETORNA O OBJETO DO PDO COM AS PERMISSÕES DO USUÁRIO
					$Permissions = $SQLPermissions->fetchAll(\PDO::FETCH_OBJ);

					/*### GERA O OBJETO JSON COM AS PERMISSÕES ###*/
					foreach($Permissions as $key => $PermissionData) {

						$ResultSQLAction[0]->permissoes[$PermissionData->usuaperm_modulo][$PermissionData->usuaperm_operacao] = $PermissionData->usuaperm_valor;
					}
					/*### GERA O OBJETO JSON COM AS PERMISSÕES ###*/
				}


				//ARMAZENA O RESULTADO NA VARIÁVEL ResultSQLAction
				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/





	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

		$Request = $this->Request;

		if((($this->Action=="inserir")||($this->Action=="alterar"))&&($Request["usua_senha"]<>""))
			{
				$Request["usua_senha"] = $this->CryptDecrypt->CryptMD5($Request["usua_senha"]);

			}
		else
			{
				unset($Request["usua_senha"]);
			}

		$this->setRequest($Request);
	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/




	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {


		/*### SE A ACTION FOR ALTERAR PERMISSÕES OU HOUVE MUDANÇA DE PERFIL ###*/
		if(($this->Action=="alterar_permissoes")||(($this->Action=="alterar")&&($this->Request["perfil_antigo"]<>$this->Request["perfis_perf_id"])))
			{

				//PERPARA E EXECUTA A QUERY QUE EXCLUI TODAS AS PERMISSÕES DO USUÁRIO ATUAL
				$SQLDeletePermissions = $this->db->query("UPDATE
																UsuariosPermissoes
															SET
																UsuaPerm_Delete = 1,
																UsuaPerm_RecDeletedon = '".$this->Date["NowUS"]."',
																UsuaPerm_RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
															WHERE
																UsuaPerm_Delete = 0
																AND Usuarios_Usua_id = ".$this->PrimaryKey);

				/*### SE A ACTION FOR ALTERAR PERMISSÕES ###*/
				if($this->Action=="alterar_permissoes")
					{
						/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES ###*/
						foreach($this->Request["permissoes"] as $modulo => $array_operacao) {

							foreach($array_operacao as $operacao => $valor) {

								$UserPermissions .= ($valor==1)  ? "(".$this->PrimaryKey.", '".$modulo."', '".$operacao."', '".$valor."', '".$this->Date["NowUS"]."', ".$this->TokenClass->getClaim("UserData")->Usua_id."), " : "";
							}
						}
						/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES ###*/

						//PERPARA E EXECUTA A QUERY QUE INCLUI AS NOVAS PERMISSÕES
						$this->SQLAction = "INSERT INTO UsuariosPermissoes (Usuarios_Usua_id, UsuaPerm_Modulo, UsuaPerm_Operacao, UsuaPerm_Valor, UsuaPerm_RecCreatedon, UsuaPerm_RecCreatedby) VALUES ".substr($UserPermissions,0,-2);
					}
				/*### SE A ACTION FOR ALTERAR PERMISSÕES ###*/


				//LIBERA A EXECUÇÃO DA QUERY
				$this->BeforeExecuteAction = true;
			}
		/*### SE A ACTION FOR ALTERAR PERMISSÕES OU HOUVE MUDANÇA DE PERFIL ###*/




		/*### SE HOUVE MUDANÇA DE LOGIN ###*/
		elseif($this->Request["login_antigo"]<>$this->Request["usua_login"])
			{
				//BUSCA SE JÁ EXISTE UM LOGIN IGUAL
				$SQLLogin = $this->db->query("SELECT COUNT(*) AS LOGIN FROM Usuarios WHERE Usua_Login = '".$this->Request["usua_login"]."' AND Usua_delete = 0");

				//RETORNA O OBJETO DO PDO COM O TOTAL DE LOGINS EXISTENTES
				$Login = $SQLLogin->fetch(\PDO::FETCH_OBJ);

				/*### SE JÁ EXISTE UM LOGIN, RETORNA ERRO ###*/
				if($Login->LOGIN>0) {

					$this->BeforeExecuteAction = false;
					$this->ErrorBeforeExecuteAction = "O login digitado já existe, por favor, tente outro.";
				}
				else {

					$this->BeforeExecuteAction = true;
				}
				/*### SE JÁ EXISTE UM LOGIN, RETORNA ERRO ###*/

			}
		/*### SE HOUVE MUDANÇA DE LOGIN ###*/








		/*### SENÃO LIBERA A EXECUÇÃO ###*/
		else
			{
				//LIBERA A EXECUÇÃO DA QUERY
				$this->BeforeExecuteAction = true;
			}
		/*### SENÃO LIBERA A EXECUÇÃO ###*/
	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/



	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

		if($this->ExecuteAction==true)
			{
				/*### SE A ACTION FOR INSERIR OU ALTERAR ###*/
				if(($this->Action=="inserir")||($this->Action=="alterar"))
					{
						//ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO, SE FOR OPERAÇÃO DE INSERIR
						if($this->Action=="inserir") $this->PrimaryKey = $this->db->lastInsertId();

						/*### SE A ACTION FOR INSERIR HOUVE MUDANÇA DE PERFIL ###*/
						if(($this->Action=="inserir")||($this->Request["perfil_antigo"]<>$this->Request["perfis_perf_id"]))
							{

								//PERPARA E EXECUTA A QUERY QUE INSERE AS PERMISSÕES DO PERFIL ESCOLHIDO
								$SQLInsertPermissions = $this->db->query("INSERT INTO
																				UsuariosPermissoes
																					(Usuarios_Usua_id,
																					UsuaPerm_Modulo,
																					UsuaPerm_Operacao,
																					UsuaPerm_Valor,
																					UsuaPerm_RecCreatedon,
																					UsuaPerm_RecCreatedby)
																				(SELECT
																						".$this->PrimaryKey.",
																						PerfPerm_Modulo,
																						PerfPerm_Operacao,
																						PerfPerm_Valor,
																						'".$this->Date["NowUS"]."',
																						".$this->TokenClass->getClaim("UserData")->Usua_id."
																					FROM
																						PerfisPermissoes
																					WHERE
																						PerfPerm_Delete = 0
																						AND Perfis_Perf_id = ".$this->Request["perfis_perf_id"].")");
							}
						/*### SE A ACTION FOR INSERIR HOUVE MUDANÇA DE PERFIL ###*/
					}
				/*### SE A ACTION FOR INSERIR OU ALTERAR ###*/


			}


	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/




	/*### EXECUTA O LOGIN DO USUÁRIO NO PRIMEIRO ACESSO ###*/
	public function Login() {

		//DEFINE ACTION COMO LOGIN
		$this->setAction("login");

		//CONSTRÓI SQL PARA BUSCAR O USUÁRIO
		$SQLSelectUser = "SELECT * FROM Usuarios WHERE Usua_Login = '".$this->AntiInjection->Prepare($this->Request["login"])."' AND Usua_Delete = 0";

		//ARMAZNEA A ACTION DE BUSCA DO USUÁRIO
		$this->setSQLAction($SQLSelectUser);

		//EXECUTA A BUSCA DO USUÁRIO
		$ResultSQLSelectUser = $this->ExecuteAction();

		/*### SE ENCONTRAR O USUÁRIO, FAZ AS VALIDAÇÕES E CRIA O TOKEN DE ACESSO, SENÃO INDICA USUÁRIO INVÁLIDO ###*/
		if($this->ResultSQLAction==true) {

			/*### SE O USÁRIO ESTIVER ATIVO, CONTINUA, SENÃO RETORNA ERRO ###*/
			if($this->ResultSQLAction[0]->usua_status==1) {

				/*### SE A SENHA DIGITADA FOR IGUAL AO QUE ESTÁ NO BANCO, CONTINUA, SENÃO RETORNA ERRO ###*/
				if($this->CryptDecrypt->CryptMD5($this->Request['senha'])==$this->ResultSQLAction[0]->usua_senha) {

					//ARMAZENA O ID DO USÁRIO NO OBJETO DO TOKEN
					$UserData["Usua_id"] = $this->ResultSQLAction[0]->usua_id;

					//ARMAZENA O LOGIN NO OBJETO DO TOKEN
					$UserData["Usua_Login"] = $this->ResultSQLAction[0]->usua_login;

					//ARMAZENA O NOME DO USUÁRIO NO OBJETO DO TOKEN
					$UserData["Usua_Nome"] = $this->ResultSQLAction[0]->usua_nome;

					/*### RETORNA O PRIMEIRO E ÚLTIMO NOME DO USUÁRIO ###*/
					$SeparaNome = explode(" ",$this->ResultSQLAction[0]->usua_nome);
					$CountNome = count($SeparaNome);
					$PrimeiroNome = $SeparaNome[0];
					$UltimoNome = ($CountNome>1) ? " ".$SeparaNome[$CountNome-1] : "";
					$UserData["Usua_PrimeiroUltimoNome"] = $PrimeiroNome.$UltimoNome;
					/*### RETORNA O PRIMEIRO E ÚLTIMO NOME DO USUÁRIO ###*/


					/*### ARMAZENA CONFIGURAÇÕES PADRÃO, UTILIZADAS EM PERMISSÕES ###*/
					$SQLConfig = "SELECT
										Conf_Categoria,
										Conf_Titulo,
										Conf_Valor
									FROM
										Configuracoes
									WHERE
										Conf_Categoria = 'TabelasRestricaoParceiros'
										AND Conf_Delete = 0
									ORDER BY
										Conf_Categoria,
										Conf_Valor";
					$ExecuteSQLConfig = $this->db->query($SQLConfig);
					$ResultSQLConfig = $ExecuteSQLConfig->fetchAll(\PDO::FETCH_OBJ);
					foreach ($ResultSQLConfig as $key => $DefaultConfigObject) {

						$Data["DefaultConfig"][$DefaultConfigObject->Conf_Categoria][] = array($DefaultConfigObject->Conf_Valor => $DefaultConfigObject->Conf_Titulo);
					}
					/*### ARMAZENA CONFIGURAÇÕES PADRÃO, UTILIZADAS EM PERMISSÕES ###*/


					//CRIA O TOKEN DE ACESSO
					$this->TokenClass->CreateToken($UserData, $Data);

					//RETORNA O TOKEN DE ACESSO PARA ENVIO NAS DEMAIS REQUISIÇÕES
					$response["token"] = $this->TokenClass->sendToken();

					//RETORNA ACTION DE LOGIN COM OK
					$response["login"] = "OK";
				}
				else {

					$response["login"] = "NOK";
					$this->ErrorAfterExecuteAction = "<p>Usuário ou senha incorretos.</p>";
				}
				/*### SE A SENHA DIGITADA FOR IGUAL AO QUE ESTÁ NO BANCO, CONTINUA, SENÃO RETORNA ERRO ###*/
			}
			else {

				$response["login"] = "NOK";
				$this->ErrorAfterExecuteAction = "<p>Usuário desativado, por favor, entre em contato com o Administrador do sistema.</p>";
			}
			/*### SE O USÁRIO ESTIVER ATIVO, CONTINUA, SENÃO RETORNA ERRO ###*/

			//RETORNA A RESPOSTA DA ACTION
			$this->setResponseAction($response);
		}
		else {

			$this->ErrorAfterExecuteAction = "<p>Usuário não localizado, por favor, verifique os dados digitados</p>";
		}
		/*### SE ENCONTRAR O USUÁRIO, FAZ AS VALIDAÇÕES E CRIA O TOKEN DE ACESSO, SENÃO INDICA USUÁRIO INVÁLIDO ###*/
	}
	/*### EXECUTA O LOGIN DO USUÁRIO NO PRIMEIRO ACESSO ###*/




	/*### EXECUTA O LOGOUT DO USUÁRIO ###*/
	public function Logout() {

		//APAGA O TOKEN DE ACESSO
		$this->TokenClass->setToken('');

		//APAGA O OBJETO DO TOKEN DE ACESSO
		$this->TokenClass->setTokenObject('');

		//RETORNA RESPOSTA DE LOGOUT OK
		$response["logout"] = "OK";

		//CODIFICA EM JSON
		echo json_encode($response);
	}
	/*### EXECUTA O LOGOUT DO USUÁRIO ###*/


}
/*### CLASSE DO CADASTRO DE USUÁRIO (HERDA CRUD) ###*/
