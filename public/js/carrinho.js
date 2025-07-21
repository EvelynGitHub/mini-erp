// Aplicar Cupom
$("#btnCupom").on('click', function () {
    const codigo = $("#codigoCupom").val();

    $.post('/index.php?page=aplicar-cupom', { codigo: codigo, subtotal: subtotal }, function (res) {
        const data = res; // Assumindo que 'res' já é o objeto JSON ou será parseado pelo jQuery
        if (data.valido) {
            const desconto = parseFloat(data.desconto);
            const novoTotal = parseFloat(subtotal) + parseFloat(frete) - desconto;
            $("#total").text("Total: R$" + novoTotal.toFixed(2).replace('.', ','));
            $("#msgCupom").css("color", "green").text("Cupom aplicado! Desconto: R$" + desconto.toFixed(2).replace('.', ','));
        } else {
            $("#msgCupom").css("color", "red").text(data.msg);
        }
    }, 'json'); // Adicionado 'json' para garantir que jQuery parseie a resposta como JSON
});

// Consulta CEP com ViaCEP
$("#btnCep").on('click', function () {
    const cep = $("#cep").val().replace(/\D/g, '');
    if (cep.length === 8) {
        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function (data) {
            if (!data.erro) {
                console.log(data);
                $("#endereco").val(`${data.logradouro}, nº , ${data.bairro}, ${data.localidade}-${data.uf}`);
            } else {
                // Substituindo alert() por uma mensagem na UI
                const alertaCep = document.createElement('div');
                alertaCep.className = 'alert alert-warning mt-2';
                alertaCep.textContent = 'CEP não encontrado.';
                document.getElementById('cep').parentNode.insertBefore(alertaCep, document.getElementById('cep').nextSibling);
                setTimeout(() => alertaCep.remove(), 3000); // Remove a mensagem após 3 segundos
            }
        });
    } else {
        // Mensagem para CEP inválido
        const alertaCep = document.createElement('div');
        alertaCep.className = 'alert alert-warning mt-2';
        alertaCep.textContent = 'Por favor, insira um CEP válido com 8 dígitos.';
        document.getElementById('cep').parentNode.insertBefore(alertaCep, document.getElementById('cep').nextSibling);
        setTimeout(() => alertaCep.remove(), 3000); // Remove a mensagem após 3 segundos
    }
});