<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema de Arquitetura' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <style>
        /* Sobrescrevendo estilos inline para garantir o tema escuro */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg) !important;
            border-right: 1px solid var(--border-dark);
        }
        .sidebar .nav-link {
            color: var(--text-muted-light) !important;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: var(--text-light) !important;
            background: var(--hover-bg) !important;
        }
        .sidebar .nav-link.active {
            background: var(--primary-color) !important;
        }
        .content-wrapper {
            min-height: 100vh;
            background-color: var(--bg-dark) !important;
        }
        .card {
            border: 1px solid var(--border-dark) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;
            border-radius: 12px;
            background-color: var(--card-bg) !important;
        }
        .btn {
            border-radius: 8px;
        }
        .navbar-brand {
            font-weight: bold;
            color: var(--text-light) !important;
        }
        .sidebar h4 {
            color: var(--text-light) !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .sidebar .bi-building {
            color: var(--primary-color) !important;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <?php if (isset($showSidebar) && $showSidebar): ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-light">
                            <i class="bi bi-building text-primary"></i>
                            Sistema Arquitetura
                        </h4>
                    </div>
                    
                    <ul class="nav flex-column px-2">
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/projects">
                                <i class="bi bi-folder"></i>
                                Projetos
                            </a>
                        </li>
                        <?php if (($user_type ?? '') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin">
                                <i class="bi bi-gear"></i>
                                Administração
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile">
                                <i class="bi bi-person"></i>
                                Perfil
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="/logout">
                                <i class="bi bi-box-arrow-right"></i>
                                Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper bg-dark">
    <?php else: ?>
    <div class="container-fluid content-wrapper bg-dark">
    <?php endif; ?>
    
    <!-- Alerts -->
    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check-circle"></i>
            <?= htmlspecialchars($_SESSION['flash']['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <?= htmlspecialchars($_SESSION['flash']['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']['error']); ?>
    <?php endif; ?>
