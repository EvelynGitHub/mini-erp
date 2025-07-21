<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $PAGE_TITLE ?? 'Mini ERP'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Customizado -->
    <?php echo $PAGE_CSS ?? ''; ?>
</head>

<body class="">
    <!-- Menu de Navegação -->
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
                        <a class="nav-link" aria-current="page" href="?page=variacao">Variações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=pedidos">Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=carrinho">
                            Carrinho
                            <span class="badge rounded-pill bg-danger">
                                <?= count($_SESSION["carrinho"] ?? []) ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php echo $PAGE_CONTENT ?? ''; // Conteúdo específico da página será injetado aqui ?>
    </div>


    <!-- Bootstrap JS e Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript Customizado -->
    <?php echo $PAGE_SCRIPTS ?? ''; ?>
</body>

</html>