<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Klinik</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        /* Navbar Custom */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(13, 110, 253, 0.95) !important;
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05);
            z-index: 1040;
        }

        /* Sidebar Modern */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            position: fixed;
            left: 0;
            top: 56px;
            box-shadow: 1px 0 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1030;
            border-right: 1px solid #e2e8f0;
        }

        .sidebar a {
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            margin: 4px 12px;
            border-radius: 8px;
        }

        .sidebar a i {
            font-size: 1.2rem;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #eff6ff;
            color: #2563eb;
        }

        /* Content & Footer Area */
        .content, footer {
            margin-left: 260px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content {
            margin-top: 56px;
            padding: 32px;
        }

        footer {
            padding: 24px;
            text-align: center;
            color: #94a3b8;
            font-size: 0.875rem;
        }

        /* Card Dashboard Modern */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        /* Hamburger Logic State (Toggled) */
        body.sidebar-toggled .sidebar {
            transform: translateX(-260px);
        }

        body.sidebar-toggled .content,
        body.sidebar-toggled footer {
            margin-left: 0;
        }

        /* Responsive Breakpoint untuk Mobile */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-260px);
            }
            .content, footer {
                margin-left: 0;
            }
            body.sidebar-toggled .sidebar {
                transform: translateX(0);
            }
            body.sidebar-toggled .content,
            body.sidebar-toggled footer {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>