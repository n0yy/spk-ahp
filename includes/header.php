<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AHP Decision Support System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.025em;
        }
        
        .nav-link {
            color: var(--secondary-color) !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .btn {
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            font-weight: 600;
            padding: 1.5rem;
        }
        
        .main-container {
            padding: 2rem 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/spk-ahp/dashboard.php">
            <i class="bi bi-diagram-3 me-2" style="font-size: 1.5rem;"></i>
            AHP System
        </a>

        <div class="navbar-nav ms-auto d-flex align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="d-none d-lg-inline text-muted me-3 fw-medium">
                    <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a class="btn btn-sm btn-outline-primary" href="/spk-ahp/logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            <?php else: ?>
                <a class="nav-link me-2" href="login.php">Login</a>
                <a class="btn btn-sm btn-primary" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="main-container">
    <div class="container">
        <div class="row justify-content-center">