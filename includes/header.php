<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Desenvolvemos sites modernos, rápidos e personalizados para empresas e negócios.">
    <script src="https://code.jquery.com/jquery-4.0.0.js"></script>

    <title>Webra</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
    <script>
        $(document).ready(function(){
            $('.projeto01').on('click', () => {window.location.href = './exemplos/institucional/index.html'});
            $('.projeto02').on('click', () => {window.location.href = './exemplos/landingpage/index.html'});
            $('.projeto03').on('click', () => {window.location.href = './exemplos/siteperso/index.html'});
            $('.projeto04').on('click', () => {window.location.href = './exemplos/catalogo-online/catalogo.html'});


            $('#nome').on('input', () => {$('#nome').css('border-color', '')});
            $('#email').on('input', () => {$('#email').css('border-color', '')});
            
            $('.enviarMensagem').on('click', function(e){
                e.preventDefault();
                let nome = $('#nome');
                let email = $('#email');
                let mensagem = $('#mensagem');

                   
                    if(!nome.val()){
                        $('.alerta').show();
                        $('#aviso').text('Preencha o campo nome.').css('color', 'red');

                        nome.focus();
                        nome.css('border-color', '#f00');
                        return false;

                    } else if(!email.val()) {
                        $('.alerta').show();
                        $('#aviso').text('Preencha o campo email.').css('color', 'red');
                        email.focus();
                        email.css('border-color', '#f00');
                        return false;
                    } 


                $.ajax({
                    url: './ajax/ajax_contato.php',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        nome: nome.val(),
                        email: email.val(),
                        mensagem: mensagem.val()
                    },
                    success: function(retorno) {
                        $('.alerta').show();

                        if(retorno.status) {
                            $('#aviso').text("Agradecemos pelo contato! Assim que possivel, entraremos em contato.").css('color', 'white');

                             nome.val('');
                             email.val('');
                             mensagem.val('');
                             
                        } else {
                            $('#aviso').text("Houve um problema de comunicação com a API. Lamentamos muito por isso!");
                        }
                    },
                    error: function(err) {
                        $('.alerta').show();
                        $('#aviso').text("Tivemos um problema com o seu envio. Lamentamos muito por isso!");
                        console.log("Erro: ", err.status, err.statusText)
                    }
                });

            });

        })
    </script>
</head>

<body>

    <header class="header">

        <div class="container">
                <a href="#" class="logo-webra"><strong>We<span style="color:var(--primary)">B</span><span style="color:var(--secondary)">r</span><span style="color:var(--accent)">a</span></strong></a>

            <nav class="navbar">
                <a href="#inicio">Início</a>
                <a href="#servicos">Serviços</a>
                <a href="#sobre">Sobre nós</a>
                <a href="#portfolio">Portfólio</a>
                <a href="#contato">Contato</a>
            </nav>

            <a href="#contato" class="header-button">
                Solicitar orçamento
            </a>

        </div>

    </header>