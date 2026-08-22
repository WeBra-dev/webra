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
            $('.projeto03').on('click', () => {window.location.href = './exemplos/siteperso/index.html'});
            $('.projeto04').on('click', () => {window.location.href = './exemplos/catalogo-online/catalogo.html'});
        })
    </script>
</head>

<body>

    <header class="header">

        <div class="container">
            <a href="index.php">
                <img src="./assets/img/logo.png" style="width: 80px; height: 60px">
            </a>

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