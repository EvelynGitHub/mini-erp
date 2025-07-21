<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Pedidos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            /* Um cinza claro para o fundo */
            color: #333;
        }

        .navbar {
            background-color: #007bff;
            /* Azul primário do Bootstrap */
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
        }

        .card {
            border-radius: 1rem;
            /* Cantos mais arredondados */
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            /* Sombra mais suave */
            border: none;
            /* Remove a borda padrão do card */
        }

        .card-header {
            background-color: #ffffff;
            /* Fundo branco para o cabeçalho do card */
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
            /* Efeito de elevação ao passar o mouse */
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background-color: #117a8b;
            border-color: #117a8b;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #bd2130;
            border-color: #bd2130;
            transform: translateY(-1px);
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .alert {
            border-radius: 0.75rem;
            margin-top: 1.5rem;
            font-weight: 500;
        }

        .table {
            border-radius: 1rem;
            overflow: hidden;
            /* Garante que os cantos arredondados funcionem com a tabela */
        }

        .table thead th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            padding: 1rem;
        }

        .modal-content {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 0.5rem 1.5rem 1.5rem 1.5rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Mini-ERP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="?page=produtos">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=pedidos">Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=carrinho">
                            Carrinho
                            <span class="badge rounded-pill bg-danger">
                                <?= count($_SESSION["carrinho"]) ?>
                            </span>
                        </a>
                    </li>
                    <!-- Adicione outros links de navegação aqui se necessário -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h1><?= $msg ?></h1>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Card para Listagem de Pedidos -->
                <div class="card mt-4">
                    <div class="card-header">
                        Pedidos Existentes
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID Pedido</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Endereço</th>
                                        <th scope="col">Criado Em</th>
                                        <!-- Adicione mais colunas se PedidoModel->listar() retornar mais dados relevantes -->
                                    </tr>
                                </thead>
                                <tbody id="tabelaPedidos">
                                    <?php if (empty($pedidos)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Nenhum pedido cadastrado.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pedidos as $pedido): ?>
                                            <tr>
                                                <td><?= $pedido['id'] ?></td>
                                                <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                                                <td><?= ucfirst($pedido['status']) ?></td>
                                                <td><?= $pedido['endereco'] ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                                                <!-- Renderize mais dados aqui -->
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Não há necessidade de JavaScript complexo para esta tela,
        // já que a listagem é feita via PHP e não há operações AJAX diretas aqui.
        // Se futuras funcionalidades de edição/exclusão de pedidos forem adicionadas,
        // o JavaScript será implementado de forma similar às telas de produtos e variações.
    </script>
</body>

</html>