<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chamados em Aberto</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-['Roboto'] min-h-screen p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Chamados em Aberto</h1>

    <?php
    $ticketsFile = 'tickets.json';

    // Load existing tickets
    $tickets = [];
    if (file_exists($ticketsFile)) {
        $json = file_get_contents($ticketsFile);
        $tickets = json_decode($json, true) ?? [];
    }

    // Handle form submission to update ticket fields
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        $newStatus = $_POST['status'] ?? '';
        $newName = $_POST['name'] ?? '';
        $newEmail = $_POST['email'] ?? '';
        $newSubject = $_POST['subject'] ?? '';

        // Validate status
        $validStatuses = ['Em analise', 'Em andamento', 'Concluído'];
        if (in_array($newStatus, $validStatuses)) {
            foreach ($tickets as &$ticket) {
                if ($ticket['id'] === $id && $ticket['status'] !== 'Concluído') {
                    $ticket['status'] = $newStatus;
                    $ticket['name'] = $newName;
                    $ticket['email'] = $newEmail;
                    $ticket['subject'] = $newSubject;
                    break;
                }
            }
            // Save updated tickets
            file_put_contents($ticketsFile, json_encode($tickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        // Redirect to avoid form resubmission
        header('Location: open_tickets.php');
        exit;
    }

    // Map "Aberto" status to "Em analise" for consistency
    foreach ($tickets as &$ticket) {
        if (($ticket['status'] ?? '') === 'Aberto') {
            $ticket['status'] = 'Em analise';
        }
    }
    unset($ticket);

    // Filter tickets with status not "Concluído"
    $openTickets = array_filter($tickets, function ($ticket) {
        return ($ticket['status'] ?? '') !== 'Concluído';
    });
    ?>

    <?php if (empty($openTickets)): ?>
        <p class="text-gray-600">Não há chamados em aberto.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nome</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Assunto</th>
                        <th class="py-3 px-6 text-left">Descrição</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-center">Criado em</th>
                        <th class="py-3 px-6 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <?php foreach ($openTickets as $ticket): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap"><?= htmlspecialchars($ticket['id'] ?? '') ?></td>
                            <td class="py-3 px-6 text-left"><?= htmlspecialchars($ticket['name'] ?? '') ?></td>
                            <td class="py-3 px-6 text-left"><?= htmlspecialchars($ticket['email'] ?? '') ?></td>
                            <td class="py-3 px-6 text-left"><?= htmlspecialchars($ticket['subject'] ?? '') ?></td>
                            <td class="py-3 px-6 text-left max-w-xs break-words"><?= htmlspecialchars($ticket['description'] ?? '') ?></td>
                            <td class="py-3 px-6 text-left">
                                <?php if (($ticket['status'] ?? '') === 'Concluído'): ?>
                                    <?= htmlspecialchars($ticket['status']) ?>
                                <?php else: ?>
                                    <form method="POST" class="inline space-y-2">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>" />
                                        <input type="text" name="name" value="<?= htmlspecialchars($ticket['name'] ?? '') ?>" placeholder="Nome" class="border border-gray-300 rounded px-2 py-1 w-full" required />
                                        <input type="email" name="email" value="<?= htmlspecialchars($ticket['email'] ?? '') ?>" placeholder="Email" class="border border-gray-300 rounded px-2 py-1 w-full" required />
                                        <input type="text" name="subject" value="<?= htmlspecialchars($ticket['subject'] ?? '') ?>" placeholder="Assunto" class="border border-gray-300 rounded px-2 py-1 w-full" required />
                                        <select name="status" class="border border-gray-300 rounded px-2 py-1 w-full" onchange="this.form.submit()">
                                            <?php
                                            $statuses = ['Em analise', 'Em andamento', 'Concluído'];
                                            $currentStatus = $ticket['status'] ?? '';
                                            foreach ($statuses as $statusOption):
                                            ?>
                                                <option value="<?= $statusOption ?>" <?= $currentStatus === $statusOption ? 'selected' : '' ?>>
                                                    <?= $statusOption ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6 text-center"><?= htmlspecialchars($ticket['created_at'] ?? '') ?></td>
                            <td class="py-3 px-6 text-center">
                                <a href="ticket.php?id=<?= urlencode($ticket['id']) ?>" class="text-blue-600 hover:underline">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-6">
        <a href="index.php" class="text-blue-600 hover:underline"><i class="fas fa-arrow-left"></i> Voltar para Home</a>
    </div>
</body>
</html>
