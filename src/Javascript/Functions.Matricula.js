
/*#######################################################
|														|
| Arquivo com as funções javascript utilizadas na tela	|
| e nos processo de Matrícula 							|
|														|
| Data de criação: 04/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

/*### NOTE: FUNÇÃO QUE LIMPA OS DADOS DO ALUNO NO FORMULÁRIO DE MATRÍCULA ###*/
//IdElementForm = ID do elemento FORM do formulário de matrícula
function LimpaAluno(IdElementForm) {

	$("#"+IdElementForm+" input[name=alun_nome]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=alun_cpf]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=alun_rg]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=alun_dtnascimento]").closest('.form-group').slideUp();

	$("#"+IdElementForm+" input[name=alun_nome]").val('');
	$("#"+IdElementForm+" input[name=alun_id]").val('');
	$("#"+IdElementForm+" input[name=alun_cpf]").val('');
	$("#"+IdElementForm+" input[name=alun_rg]").val('');
	$("#"+IdElementForm+" input[name=alun_dtnascimento]").val('');
}
/*### FUNÇÃO QUE LIMPA OS DADOS DO ALUNO NO FORMULÁRIO DE MATRÍCULA ###*/



/*### NOTE: FUNÇÃO QUE LIMPA OS DADOS DO CURSO NO FORMULÁRIO DE MATRÍCULA ###*/
//IdElementForm = ID do elemento FORM do formulário de matrícula
function LimpaCurso(IdElementForm) {

	$("#"+IdElementForm+" input[name=curs_valor_matricula]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=curs_valor_mensalidade]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=curs_duracao]").closest('.form-group').slideUp();

	$("#"+IdElementForm+" input[name=curs_valor_matricula]").val('');
	$("#"+IdElementForm+" input[name=curs_valor_mensalidade]").val('');
	$("#"+IdElementForm+" input[name=curs_duracao]").val('');
}
/*### FUNÇÃO QUE LIMPA OS DADOS DO CURSO NO FORMULÁRIO DE MATRÍCULA ###*/



/*### FUNÇÃO QUE CALCULA O TROCO NO PAGAMENTO ###*/
function CalculaTroco(valorRecebido) {

	// console.log("keyup");
	var valor = valorRecebido;

	valor = valor.replace(".","");
	valor = valor.replace(",",".");

	var troco = Number(valor) - Number($("#res_total_pagar").val());

	var trocoFormat = troco;

	// console.log("troco");
	// console.log(troco);

	$("#res_autoriza_pgto").val(0);

	if(Number(valor)>0) {

		if(Number(troco)<0) {

			$("#res_troco").html("<p class='bg-danger'>Valor insuficiente para realizar o pagamento</p>");
		}
		else if(Number(troco)==0) {

			$("#res_troco").html("R$ 0 (troco não necessário)");
			$("#res_autoriza_pgto").val(1);
		}
		else {

			// console.log("troco cedulas");

			var cedulasDisponiveis = [100,50,10,5,1,0.50,0.10,0.05,0.01];

			var cedulasTroco = new Array();

			var texto_cedulas = "";

			for (x in cedulasDisponiveis) {

				if (cedulasDisponiveis[x] > troco) continue;

				var quantidadeCedula = parseInt(troco / cedulasDisponiveis[x]);
				cedulasTroco.push([quantidadeCedula, cedulasDisponiveis[x]]);
				troco = troco - (quantidadeCedula * cedulasDisponiveis[x]);

				if(cedulasDisponiveis[x]>1) {

					texto_cedulas += quantidadeCedula + " nota(s) de " + (cedulasDisponiveis[x]).formatMoney(2, ',', '.') + ", ";
				}
				else {

					texto_cedulas += quantidadeCedula + " moeda(s) de " + (cedulasDisponiveis[x]).formatMoney(2, ',', '.') + ", ";
				}
			}

			// console.log(cedulasTroco);
			// console.log(texto_cedulas.slice(0,-2));

			$("#res_troco").html("R$ " + (trocoFormat).formatMoney(2, ',', '.') + "<br>" + texto_cedulas.slice(0,-2));
			$("#res_autoriza_pgto").val(1);
		}
	}
	else {

		$("#res_troco").html("Digite o valor recebido");
	}
}
/*### FUNÇÃO QUE CALCULA O TROCO NO PAGAMENTO ###*/

