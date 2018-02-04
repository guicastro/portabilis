
/*#######################################################
|														|
| Arquivo com todas as funções javascript utilizadas 	|
| na tela de cadastro de Tabela Dinâmica 				|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/



/*### NOTE: FUNÇÃO QUE GERA O FORMULÁRIO PARA ITENS DA TABELA DINÂMICA ###*/
//LastIdObject = objeto com a última questão criada
//Data = objeto com os valores dos items já criados (quando carregar)
function AddItem(LastIdObject, Data) {

	// console.log('AddItem');
	// console.log(LastIdObject);
	// console.log(Data);

	var LastId = $(LastIdObject).data("id");

	var NextId = parseInt(LastId) + 1;

	// console.log(LastId);
	// console.log(NextId);


	$('<div id="item-'+NextId+'" class="row item" data-id="'+NextId+'">\
			<input type="hidden" name="opt-item-id['+NextId+']" id="opt-item-'+NextId+'-id" value="" >\
			<div class="col-md-12">\
				<div class="form-group">\
					<div class="col-xs-12">\
						<label for="opt-item-codigo-'+NextId+'">Item '+NextId+'</label>\
						<button type="button" id="remover-item-'+NextId+'" data-id="'+NextId+'" class="btn btn-danger btn-circle btn-xs remover-item" title="Remover Item '+NextId+'"><i class="fa fa-minus-circle"></i></button>\
					</div>\
					<div class="col-xs-2"><input name="opt-item-codigo['+NextId+']" id="opt-item-codigo-'+NextId+'" type="text" maxlength="20" class="form-control" placeholder="Código '+NextId+'" /></div>\
					<div class="col-xs-6"><input name="opt-item-descricao['+NextId+']" id="opt-item-descricao-'+NextId+'" type="text" class="form-control" placeholder="Descrição '+NextId+'" /></div>\
				</div>\
			</div>\
			&nbsp;<hr>\
		</div>').insertAfter(LastIdObject);



	if(Data.id) {
		$("#opt-item-"+NextId+'-id').val(Data.id);
	}

	if(Data.codigo) {
		$("#opt-item-codigo-"+NextId).val(Data.codigo);
	}

	if(Data.descricao) {
		$("#opt-item-descricao-"+NextId).val(Data.descricao);
	}


	/*### AÇÕES QUANDO CLICA PARA REMOVER UM ITEM ###*/
	$(".remover-item").on("click", function (e) {

		e.preventDefault();

		RemoveItem($(this).data("id"));

    });
	/*### AÇÕES QUANDO CLICA PARA REMOVER UM ITEM ###*/




	/*### CONFIGURA TODOS OS CAMPOS DOS ITENS PARA MAIÚSCULO ###*/
	$(".item :input").keyup(function(){
	    this.value = this.value.toUpperCase();
	});
	/*### CONFIGURA TODOS OS CAMPOS DOS ITENS PARA MAIÚSCULO ###*/

}
/*### FUNÇÃO QUE GERA O FORMULÁRIO PARA ITENS DA TABELA DINÂMICA ###*/










/*### NOTE: FUNÇÃO QUE APAGA O ITEM DA TABELA DINÂMICA ###*/
//Id = id do elemento do item que será removido
function RemoveItem(Id) {

	$('#item-'+Id).remove();
}
/*### FUNÇÃO QUE APAGA O ITEM DA TABELA DINÂMICA ###*/






