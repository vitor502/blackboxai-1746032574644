<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalhes do Chamado</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-['Roboto'] min-h-screen flex flex-col items-center p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Detalhes do Chamado</h1>

    <?php
    $ticketsFile = 'tickets.json';

    // Load existing tickets
    $tickets = [];
    if (file_exists($ticketsFile)) {
        $json = file_get_contents($ticketsFile);
        $tickets = json_decode($json, true) ?? [];
    }

    $ticket = null;
    $id = $_GET['id'] ?? '';

    foreach ($tickets as $t) {
        if ($t['id'] === $id) {
            $ticket = $t;
            break;
        }
    }
    ?>

    <div class="w-full max-w-3xl bg-white rounded shadow p-6">
        <?php if (!$ticket): ?>
            <p class="text-red-600 font-semibold">Chamado não encontrado.</p>
            <a href="index.php" class="text-blue-600 hover:underline mt-4 inline-block"><i class="fas fa-arrow-left"></i> Voltar</a>
        <?php else: ?>
            <div class="mb-4">
                <strong>ID:</strong> <?= htmlspecialchars($ticket['id']) ?>
            </div>
            <div class="mb-4">
                <strong>Nome:</strong> <?= htmlspecialchars($ticket['name']) ?>
            </div>
            <div class="mb-4">
                <strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?>
            </div>
            <div class="mb-4">
                <strong>Assunto:</strong> <?= htmlspecialchars($ticket['subject']) ?>
            </div>
            <div class="mb-4">
                <strong>Descrição:</strong>
                <p class="whitespace-pre-wrap border border-gray-300 rounded p-3 bg-gray-50"><?= htmlspecialchars($ticket['description']) ?></p>
            </div>
            <div class="mb-4">
                <strong>Status:</strong> <?= htmlspecialchars($ticket['status']) ?>
            </div>
            <div class="mb-4">
                <strong>Criado em:</strong> <?= htmlspecialchars($ticket['created_at']) ?>
            </div>
            <a href="index.php" class="text-blue-600 hover:underline mt-4 inline-block"><i class="fas fa-arrow-left"></i> Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html>
