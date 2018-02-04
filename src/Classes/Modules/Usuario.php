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


	/*### ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO (ALTERAR/EXCLUIR) ###*/
	public function setPrimaryKey ($PrimaryKey) {


		if((($this->Action=="selecionar")||($this->Action=="alterar")||($this->Action=="excluir"))
			&&($this->ModuleDefs->Name=="ParceiroUsuario"))
			{
				$SQLUsuario = "SELECT Usuarios_Usua_id FROM ParceirosUsuarios WHERE ParcUsua_id = ".$PrimaryKey." AND ParcUsua_Delete = 0";

				$ExecuteSQLUsuario = $this->db->query($SQLUsuario);
				$ResultSQLUsuario = $ExecuteSQLUsuario->fetch(\PDO::FETCH_OBJ);
				$this->PrimaryKey = $ResultSQLUsuario->usuarios_usua_id;
			}
		else
			{
				$this->PrimaryKey = $PrimaryKey;
			}

		return $this->PrimaryKey;
	}
	/*### ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO (ALTERAR/EXCLUIR) ###*/




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

					/*### RETORNA TODOS OS PARCEIROS DO USUÁRIO, SE EXISTIR ###*/
					$SQLParceiros = "SELECT Parceiros_Parc_id FROM ParceirosUsuarios WHERE Usuarios_Usua_id = ".$this->PrimaryKey." AND ParcUsua_Delete = 0";
					$ExecuteSQLParceiros = $this->db->query($SQLParceiros);
					$ResultSQLParceiros = $ExecuteSQLParceiros->fetchAll(\PDO::FETCH_OBJ);
					if(count($ResultSQLParceiros)>0)
						{
							foreach ($ResultSQLParceiros as $id => $ParcData) {

								$Parceiros[] = $ParcData->parceiros_parc_id;
							}
						}
					$ResultSQLAction[0]->parceiros = $Parceiros;
					/*### RETORNA TODOS OS PARCEIROS DO USUÁRIO, SE EXISTIR ###*/


					/*### AÇÕES QUANDO A CHAMADA VIR DO MÓDULO ParceiroUsuario ###*/
					if($this->ModuleDefs->Name=="ParceiroUsuario")
						{
							$SQLParceiro = "SELECT Parceiros_Parc_id,
													USUA_CREATED.Usua_Nome AS ParcUsua_RecCreatedbyName,
													ParcUsua_RecCreatedon,
													USUA_MODIFIED.Usua_Nome,
													ParcUsua_RecModifiedon AS ParcUsua_RecModifiedbyName
												FROM
													ParceirosUsuarios
												INNER JOIN
													Usuarios USUA_CREATED ON USUA_CREATED.Usua_id = ParceirosUsuarios.ParcUsua_RecCreatedby
												LEFT JOIN
													Usuarios USUA_MODIFIED ON USUA_MODIFIED.Usua_id = ParceirosUsuarios.ParcUsua_RecModifiedby
												WHERE
													ParcUsua_id = ".$this->Request["id_reg"]." AND ParcUsua_Delete = 0";

							$ExecuteSQLParceiro = $this->db->query($SQLParceiro);
							$ResultSQLParceiro = $ExecuteSQLParceiro->fetch(\PDO::FETCH_OBJ);
							$ResultSQLAction[0]->parceiros_parc_id = $ResultSQLParceiro->Parceiros_Parc_id;
							$ResultSQLAction[0]->parcusua_reccreatedon = $this->MaskValue->Data($ResultSQLParceiro->ParcUsua_RecCreatedon,'US2BR_TIME');
							$ResultSQLAction[0]->parcusua_reccreatedbyname = $ResultSQLParceiro->ParcUsua_RecCreatedbyName;
							$ResultSQLAction[0]->parcusua_recmodifiedon = $this->MaskValue->Data($ResultSQLParceiro->ParcUsua_RecModifiedon,'US2BR_TIME');
							$ResultSQLAction[0]->parcusua_recmodifiedbyname = $ResultSQLParceiro->ParcUsua_RecModifiedbyName;
						}
					/*### AÇÕES QUANDO A CHAMADA VIR DO MÓDULO ParceiroUsuario ###*/
				}


				//ARMAZENA O RESULTADO NA VARIÁVEL ResultSQLAction
				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/





	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

		$Request = $this->Request;

		if((($this->Action=="inserir")||($this->Action=="alterar"))&&($Request["Usua_Senha"]<>""))
			{
				$Request["Usua_Senha"] = $this->CryptDecrypt->CryptMD5($Request["Usua_Senha"]);

			}
		else
			{
				unset($Request["Usua_Senha"]);
			}

		if($this->ModuleDefs->Name=="ParceiroUsuario")
			{
				$Request["Perfis_Perf_id"] = $this->IDPerfilParceiro;
				$Request["perfil_antigo"] = $this->IDPerfilParceiro;

			}

		$this->setRequest($Request);
	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/




	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {


		/*### SE A ACTION FOR ALTERAR PERMISSÕES OU HOUVE MUDANÇA DE PERFIL ###*/
		if(($this->Action=="alterar_permissoes")||(($this->Action=="alterar")&&($this->Request["perfil_antigo"]<>$this->Request["Perfis_Perf_id"])))
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
		elseif($this->Request["login_antigo"]<>$this->Request["Usua_Login"])
			{
				//BUSCA SE JÁ EXISTE UM LOGIN IGUAL
				$SQLLogin = $this->db->query("SELECT COUNT(*) AS LOGIN FROM Usuarios WHERE Usua_Login = '".$this->Request["Usua_Login"]."' AND Usua_delete = 0");

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





		/*### SE A ACTION FOR ALTERAR PARCEIROS ###*/
		if($this->Action=="alterar_parceiros")
			{

				//PERPARA E EXECUTA A QUERY QUE EXCLUI TODOS OS PARCEIROS DO USUÁRIO
				$this->SQLAction = "UPDATE
											ParceirosUsuarios
										SET
											ParcUsua_Delete = 1,
											ParcUsua_RecDeletedon = '".$this->Date["NowUS"]."',
											ParcUsua_RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
										WHERE
											ParcUsua_Delete = 0
											AND Usuarios_Usua_id = ".$this->PrimaryKey;

				//LIBERA A EXECUÇÃO DA QUERY
				$this->BeforeExecuteAction = true;
			}
		/*### SE A ACTION FOR ALTERAR PARCEIROS ###*/





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
						if(($this->Action=="inserir")||($this->Request["perfil_antigo"]<>$this->Request["Perfis_Perf_id"]))
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
																						AND Perfis_Perf_id = ".$this->Request["Perfis_Perf_id"].")");
							}
						/*### SE A ACTION FOR INSERIR HOUVE MUDANÇA DE PERFIL ###*/
					}
				/*### SE A ACTION FOR INSERIR OU ALTERAR ###*/





				/*### SE A ACTION FOR ALTERAR OS PARCEIROS ###*/
				if($this->Action=="alterar_parceiros")
					{

						/*### EXECUTA AS AÇÕES SE FOI ESCOLHIDO PELO MENOS UM PARCEIRO ###*/
						if(count($this->Request["parceiros"])>0) {

							/*### GERA O SQL DE INSERT DOS PARCEIROS DO USUÁRIO ###*/
							foreach($this->Request["parceiros"] as $key => $idParceiro) {

									$SQLValueParceiros .= "(".$this->PrimaryKey.", '".$idParceiro."', '".$this->Date["NowUS"]."', ".$this->TokenClass->getClaim("UserData")->Usua_id."), ";
							}
							/*### GERA O SQL DE INSERT DOS PARCEIROS DO USUÁRIO ###*/


							/*### PERPARA E EXECUTA A QUERY QUE INCLUI OS PARCEIROS DO USUÁRIO ###*/
							$SQLParceiros = "INSERT INTO ParceirosUsuarios (Usuarios_Usua_id, Parceiros_Parc_id, ParcUsua_RecCreatedon, ParcUsua_RecCreatedby) VALUES ".substr($SQLValueParceiros,0,-2);
							$ExecuteSQLParceiros = $this->db->query($SQLParceiros);
							/*### PERPARA E EXECUTA A QUERY QUE INCLUI OS PARCEIROS DO USUÁRIO ###*/
						}
						/*### EXECUTA AS AÇÕES SE FOI ESCOLHIDO PELO MENOS UM PARCEIRO ###*/

					}
				/*### SE A ACTION FOR ALTERAR OS PARCEIROS ###*/



				/*### AÇÕES QUANDO FOR UMA CHAMADO DO MÓDULO ParceiroUsuario ###*/
				if($this->ModuleDefs->Name=="ParceiroUsuario")
					{
						if($this->Action=="inserir")
							{
								$SQLInsertParcUsua = "INSERT INTO ParceirosUsuarios (Usuarios_Usua_id,
																					Parceiros_Parc_id,
																					ParcUsua_RecCreatedby,
																					ParcUsua_RecCreatedon)
																				VALUES
																					(".$this->PrimaryKey.",
																					".$this->Request["Parceiros_Parc_id"].",
																					".$this->TokenClass->getClaim("UserData")->Usua_id.",
																					'".$this->Date["NowUS"]."')";

								$ExecuteSQLInsertParcUsua = $this->db->query($SQLInsertParcUsua);
							}

						if($this->Action=="alterar")
							{
								$SQLUpdateParcUsua = "UPDATE ParceirosUsuarios SET Parceiros_Parc_id = ".$this->Request["Parceiros_Parc_id"].",
																					ParcUsua_RecModifiedby = ".$this->TokenClass->getClaim("UserData")->Usua_id.",
																					ParcUsua_RecModifiedon = '".$this->Date["NowUS"]."'
																				WHERE
																					ParcUsua_id = ".$this->Request["id_reg"];

								$ExecuteSQLUpdateParcUsua = $this->db->query($SQLUpdateParcUsua);
							}

						if($this->Action=="excluir")
							{
								$SQLDeleteParcUsua = "UPDATE ParceirosUsuarios SET ParcUsua_Delete = 1,
																					ParcUsua_RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id.",
																					ParcUsua_RecDeletedon = '".$this->Date["NowUS"]."'
																				WHERE
																					ParcUsua_id = ".$this->Request["id_reg"];
								$ExecuteSQLDeleteParcUsua = $this->db->query($SQLDeleteParcUsua);
							}
					}
				/*### AÇÕES QUANDO FOR UMA CHAMADO DO MÓDULO ParceiroUsuario ###*/


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
