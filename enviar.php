<?php

// Carrega as configurações
$config = require __DIR__ . '/config.php';

// Função para registrar os envios
function registrarEnvio($destinatario, $status, $resultado) {

    $destinatario = str_replace(["\r", "\n", "|"], '', $destinatario);
    $resultado = str_replace(["\r", "\n", "|"], ' ', $resultado);

    $data = date('Y-m-d H:i:s');

    $linha = "$data | $destinatario | $status | $resultado" . PHP_EOL;

    file_put_contents(
        __DIR__ . '/envios.log',
        $linha,
        FILE_APPEND
    );
}

// Função para enviar e-mail pela API
function enviarEmail($config, $dados) {

    $ch = curl_init($config['api_url']);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,

        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $config['api_key'],
            'Content-Type: application/json'
        ],

        CURLOPT_POSTFIELDS => json_encode($dados)
    ]);

    $resposta = curl_exec($ch);

    $erroCurl = curl_error($ch);

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [
        'status' => $status,
        'resposta' => $resposta,
        'erro' => $erroCurl
    ];
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Acesso inválido.');
}

// Recebe os dados do formulário
$nome = trim($_POST['nome'] ?? '');
$email = filter_input(
    INPUT_POST,
    'email',
    FILTER_VALIDATE_EMAIL
);

$mensagem = trim($_POST['mensagem'] ?? '');

// Valida os campos
if ($nome === '' || !$email || $mensagem === '') {
    http_response_code(422);
    exit('Preencha todos os campos corretamente.');
}

// Protege o conteúdo HTML
$nomeHtml = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
$mensagemHtml = nl2br(
    htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8')
);

// Monta o e-mail de contato
$corpoHtml = '
<table width="600" cellpadding="0" cellspacing="0"
style="font-family: Arial, sans-serif; border-collapse: collapse;">

    <tr>
        <td style="background: #0F2E5C; color: #fff; padding: 20px;">
            <h2 style="margin: 0;">Contato do site</h2>
        </td>
    </tr>

    <tr>
        <td style="padding: 20px; color: #333; line-height: 1.5;">
            <p><strong>Nome:</strong> ' . $nomeHtml . '</p>

            <p><strong>E-mail:</strong> ' . $email . '</p>

            <p><strong>Mensagem:</strong></p>

            <p>' . $mensagemHtml . '</p>
        </td>
    </tr>

    <tr>
        <td style="background: #f4f4f4; padding: 14px;
        font-size: 12px; color: #777;">
            Enviado pelo formulário do site.
        </td>
    </tr>

</table>
';

// Dados do primeiro e-mail
$dados = [
    'from' => $config['remetente'],
    'to' => [$config['destino']],
    'reply_to' => [$email],
    'subject' => 'Contato do site — ' . $nome,
    'html' => $corpoHtml,
    'text' => "Nome: $nome\nE-mail: $email\nMensagem: $mensagem"
];

// Envia o e-mail para você
$resultado = enviarEmail($config, $dados);

$status = $resultado['status'];
$json = json_decode($resultado['resposta'], true);

// Verifica falha de conexão
if ($resultado['erro']) {

    registrarEnvio(
        $config['destino'],
        'REDE',
        $resultado['erro']
    );

    $mensagemResultado = 'Não foi possível conectar ao servidor. Verifique sua internet e tente novamente.';

    $sucesso = false;

} elseif ($status >= 200 && $status < 300) {

    $id = $json['id'] ?? 'ID não informado';

    registrarEnvio(
        $config['destino'],
        $status,
        $id
    );

    $sucesso = true;

} else {

    $erro = $json['message'] ?? 'Erro desconhecido';

    registrarEnvio(
        $config['destino'],
        $status,
        $erro
    );

    // Mensagens específicas para cada erro
    switch ($status) {

        case 401:
            $mensagemResultado = 'Erro de autenticação. Entre em contato com o responsável pelo site.';
            break;

        case 403:
            $mensagemResultado = 'Não foi possível autorizar o envio. Verifique as permissões do remetente.';
            break;

        case 422:
            $mensagemResultado = 'Os dados enviados são inválidos. Confira as informações.';
            break;

        case 429:
            $mensagemResultado = 'Limite de envios atingido. Tente novamente mais tarde.';
            break;

        default:
            $mensagemResultado = 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
    }

    $sucesso = false;
}

// Se o primeiro e-mail foi enviado, manda a confirmação
if ($sucesso) {

    $confirmacaoHtml = '
    <table width="600" cellpadding="0" cellspacing="0"
    style="font-family: Arial, sans-serif; border-collapse: collapse;">

        <tr>
            <td style="background: #0F2E5C; color: #fff; padding: 20px;">
                <h2 style="margin: 0;">Mensagem recebida!</h2>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; color: #333; line-height: 1.5;">
                <p>Olá, ' . $nomeHtml . '!</p>

                <p>Recebemos sua mensagem com sucesso.</p>

                <p>Em breve entraremos em contato com você.</p>
            </td>
        </tr>

        <tr>
            <td style="background: #f4f4f4; padding: 14px;
            font-size: 12px; color: #777;">
                Obrigado pelo contato!
            </td>
        </tr>

    </table>
    ';

    $dadosConfirmacao = [
        'from' => $config['remetente'],
        'to' => [$email],
        'subject' => 'Confirmação de recebimento',
        'html' => $confirmacaoHtml,
        'text' => "Olá, $nome! Recebemos sua mensagem. Em breve entraremos em contato."
    ];

    $confirmacao = enviarEmail($config, $dadosConfirmacao);

    if ($confirmacao['erro']) {

        registrarEnvio(
            $email,
            'REDE',
            $confirmacao['erro']
        );

    } else {

        $statusConfirmacao = $confirmacao['status'];
        $jsonConfirmacao = json_decode(
            $confirmacao['resposta'],
            true
        );

        registrarEnvio(
            $email,
            $statusConfirmacao,
            $jsonConfirmacao['id'] ?? $jsonConfirmacao['message'] ?? 'Resposta sem identificação'
        );
    }

    $mensagemResultado = 'Sua mensagem foi enviada com sucesso!';

}

// Mostra o resultado
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do envio</title>
</head>
<body>

    <h1>
        <?php echo $sucesso ? 'Sucesso!' : 'Ops!'; ?>
    </h1>

    <p>
        <?php echo htmlspecialchars(
            $mensagemResultado,
            ENT_QUOTES,
            'UTF-8'
        ); ?>
    </p>

    <a href="index.html">Voltar ao formulário</a>

</body>
</html>
