<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistema de Chamado de Suporte</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-['Roboto'] min-h-screen flex flex-col items-center p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Sistema de Chamado de Suporte</h1>

    <?php
    $ticketsFile = 'tickets.json';

    // Load existing tickets
    $tickets = [];
    if (file_exists($ticketsFile)) {
        $json = file_get_contents($ticketsFile);
        $tickets = json_decode($json, true) ?? [];
    }

    $errors = [];
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $priority = trim($_POST['priority'] ?? '');
        $attachmentPath = '';

        // Basic validation
        if (!$name) $errors[] = 'O nome é obrigatório.';
        if (!$email) $errors[] = 'O email é obrigatório.';
        if (!$subject) $errors[] = 'O assunto é obrigatório.';
        if (!$title) $errors[] = 'O título do chamado é obrigatório.';
        if (!$description) $errors[] = 'A descrição do problema é obrigatória.';
        if (!$category) $errors[] = 'A categoria é obrigatória.';
        if (!$priority) $errors[] = 'A prioridade é obrigatória.';

        // Handle file upload if exists
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileTmpPath = $_FILES['attachment']['tmp_name'];
            $fileName = basename($_FILES['attachment']['name']);
            $fileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            $destPath = $uploadDir . uniqid() . '_' . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $attachmentPath = $destPath;
            } else {
                $errors[] = 'Erro ao fazer upload do arquivo.';
            }
        }

        if (empty($errors)) {
            $newTicket = [
                'id' => uniqid(),
                'name' => htmlspecialchars($name),
                'email' => htmlspecialchars($email),
                'subject' => htmlspecialchars($subject),
                'title' => htmlspecialchars($title),
                'description' => htmlspecialchars($description),
                'category' => htmlspecialchars($category),
                'priority' => htmlspecialchars($priority),
                'attachment' => $attachmentPath,
                'status' => 'Aberto',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $tickets[] = $newTicket;
            file_put_contents($ticketsFile, json_encode($tickets, JSON_PRETTY_PRINT));
            $success = 'Chamado criado com sucesso!';
            // Clear form fields
            $name = $email = $subject = $title = $description = $category = $priority = '';
        }
    }
    ?>

    <div class="w-full max-w-3xl bg-white rounded shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Abrir Novo Chamado</h2>
        <?php if ($errors): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?= $success ?>
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="name" class="block font-medium mb-1">Nome</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
                <label for="subject" class="block font-medium mb-1">Assunto</label>
                <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
                <label for="title" class="block font-medium mb-1">Título do Chamado</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
            </div>
            <div>
                <label for="description" class="block font-medium mb-1">Descrição do Problema</label>
                <textarea id="description" name="description" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>
            <div>
                <label for="category" class="block font-medium mb-1">Categoria</label>
                <select id="category" name="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>Selecione a categoria</option>
                    <option value="Hardware" <?= (isset($category) && $category === 'Hardware') ? 'selected' : '' ?>>Hardware</option>
                    <option value="Software" <?= (isset($category) && $category === 'Software') ? 'selected' : '' ?>>Software</option>
                    <option value="Rede" <?= (isset($category) && $category === 'Rede') ? 'selected' : '' ?>>Rede</option>
                    <option value="Outro" <?= (isset($category) && $category === 'Outro') ? 'selected' : '' ?>>Outro</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block font-medium mb-1">Prioridade</label>
                <select id="priority" name="priority" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>Selecione a prioridade</option>
                    <option value="Baixa" <?= (isset($priority) && $priority === 'Baixa') ? 'selected' : '' ?>>Baixa</option>
                    <option value="Média" <?= (isset($priority) && $priority === 'Média') ? 'selected' : '' ?>>Média</option>
                    <option value="Alta" <?= (isset($priority) && $priority === 'Alta') ? 'selected' : '' ?>>Alta</option>
                </select>
            </div>
            <div>
                <label for="attachment" class="block font-medium mb-1">Anexar Arquivo</label>
                <input type="file" id="attachment" name="attachment" class="w-full" />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Enviar Chamado</button>
        </form>
    </div>

    <div class="w-full max-w-3xl bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Chamados Abertos</h2>
        <?php if (empty($tickets)): ?>
            <p class="text-gray-600">Nenhum chamado aberto.</p>
        <?php else: ?>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-2 border border-gray-300">ID</th>
                        <th class="p-2 border border-gray-300">Título</th>
                        <th class="p-2 border border-gray-300">Categoria</th>
                        <th class="p-2 border border-gray-300">Prioridade</th>
                        <th class="p-2 border border-gray-300">Status</th>
                        <th class="p-2 border border-gray-300">Criado em</th>
                        <th class="p-2 border border-gray-300">Anexo</th>
                        <th class="p-2 border border-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($tickets) as $ticket): ?>
                        <tr class="border border-gray-300 hover:bg-gray-50">
                            <td class="p-2 border border-gray-300 text-sm"><?= htmlspecialchars($ticket['id']) ?></td>
                            <td class="p-2 border border-gray-300"><?= htmlspecialchars($ticket['title']) ?></td>
                            <td class="p-2 border border-gray-300"><?= htmlspecialchars($ticket['category']) ?></td>
                            <td class="p-2 border border-gray-300"><?= htmlspecialchars($ticket['priority']) ?></td>
                            <td class="p-2 border border-gray-300"><?= htmlspecialchars($ticket['status']) ?></td>
                            <td class="p-2 border border-gray-300 text-sm"><?= htmlspecialchars($ticket['created_at']) ?></td>
                            <td class="p-2 border border-gray-300 text-center">
                                <?php if (!empty($ticket['attachment'])): ?>
                                    <a href="<?= htmlspecialchars($ticket['attachment']) ?>" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-paperclip"></i> Anexo</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border border-gray-300">
                                <a href="ticket.php?id=<?= urlencode($ticket['id']) ?>" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i> Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
