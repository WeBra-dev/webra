<?php

header("Content-Type: application/json");

require_once("../config.php");

$webhook = "https://discord.com/api/webhooks/" . $id . "/" . $token;

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$mensagem = $_POST['mensagem'] ?? '';

$dados = [
    "embeds" => [
        [
            "title" => "Novo contato pelo site",
            "color" => 2067271,
            "fields" => [
                [
                    "name" => "Nome",
                    "value" => $nome,
                    "inline" => false
                ],
                [
                    "name" => "E-mail",
                    "value" => $email,
                    "inline" => false
                ],
                [
                    "name" => "Mensagem",
                    "value" => $mensagem,
                    "inline" => false
                ]
            ]
        ]
    ]
];

$ch = curl_init($webhook);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($dados),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$resposta = curl_exec($ch);

if ($resposta === false) {
    echo json_encode([
        "status" => false,
        "erro" => curl_error($ch)
    ]);

    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode([
        "status" => true
    ]);
} else {
    echo json_encode([
        "status" => false,
        "erro" => "Discord retornou HTTP " . $httpCode,
        "resposta" => $resposta
    ]);
}