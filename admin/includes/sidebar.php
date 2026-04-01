<aside class="sidebar">
    <div class="logo">
        <h1>Wire Stack</h1>
        <p>Painel Administrativo</p>
    </div>
    
    <ul class="menu">
        <li class="menu-item <?= ($current_page == 'dashboard') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/dashboard.php">
                <span class="menu-icon">📊</span>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="menu-item <?= ($current_module == 'home') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">🏠</span>
                <span>Página Inicial</span>
            </a>
            <ul class="submenu">
                <li><a href="<?= BASE_URL ?>/admin/home/editar.php" <?= ($current_page == 'home-editar') ? 'class="active"' : '' ?>>Gerenciar Conteúdos</a></li>
            </ul>
        </li>

        <li class="menu-item <?= ($current_module == 'usuarios') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">👥</span>
                <span>Usuários</span>
            </a>
            <ul class="submenu">
                <li><a href="<?= BASE_URL ?>/admin/usuarios/index.php" <?= ($current_page == 'usuarios-lista') ? 'class="active"' : '' ?>>Lista de Usuários</a></li>
            </ul>
        </li>

        <li class="menu-item <?= ($current_module == 'personalizacao') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">🎨</span>
                <span>Personalização</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="<?= BASE_URL ?>/admin/personalizacao/header/index.php"
                       <?= ($current_page == 'config-header') ? 'class="active"' : '' ?>>
                       Menu Superior
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/personalizacao/icones_flutuantes/index.php"
                       <?= ($current_page == 'icones-flutuantes') ? 'class="active"' : '' ?>>
                       Ícones Flutuantes
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/personalizacao/css_personalizado/index.php"
                       <?= ($current_page == 'css-personalizado') ? 'class="active"' : '' ?>>
                       CSS Personalizado
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="menu-item <?= ($current_module == 'menus') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/menus/index.php">
                <span class="menu-icon">📋</span>
                <span>Menus</span>
            </a>
        </li>

        <li class="menu-item <?= ($current_module == 'produtos') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">🛍️</span>
                <span>Produtos</span>
            </a>
            <ul class="submenu">
                <li><a href="<?= BASE_URL ?>/admin/produtos/index.php" <?= ($current_page == 'produtos-lista') ? 'class="active"' : '' ?>>Lista de Produtos</a></li>
                <li><a href="<?= BASE_URL ?>/admin/categorias_produtos/index.php" <?= ($current_page == 'cat-produtos-lista') ? 'class="active"' : '' ?>>Categorias de Produtos</a></li>
            </ul>
        </li>
        
        <li class="menu-item <?= ($current_module == 'blog') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">📝</span>
                <span>Blog</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="<?= BASE_URL ?>/admin/blog/posts/index.php"
                       <?= ($current_page == 'blog-posts') ? 'class="active"' : '' ?>>
                       Posts
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/blog/categorias/index.php"
                       <?= ($current_page == 'blog-categorias') ? 'class="active"' : '' ?>>
                       Categorias
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/blog/config.php"
                       <?= ($current_page == 'blog-config') ? 'class="active"' : '' ?>>
                       Configurações
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item <?= ($current_module == 'paginas') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">📄</span>
                <span>Páginas</span>
            </a>
            <ul class="submenu">
                <li><a href="<?= BASE_URL ?>/admin/paginas/index.php">Lista de Páginas</a></li>
            </ul>
        </li>

        <li class="menu-item <?= ($current_module == 'rodape') ? 'active' : '' ?>">
            <a href="#" onclick="return false;">
                <span class="menu-icon">🦶</span>
                <span>Rodapé</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="<?= BASE_URL ?>/admin/rodape/index.php"
                       <?= ($current_page == 'rodape-index') ? 'class="active"' : '' ?>>
                       Configurações
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item <?= ($current_module == 'configuracoes') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/configuracoes/index.php">
                <span class="menu-icon">⚙️</span>
                <span>Configurações</span>
            </a>
        </li>
        
    </ul>
</aside>

<!-- HEADER -->
<header class="header">
    <div class="header-left">
        <h2><?= $page_title ?? 'Painel' ?></h2>
    </div>
    
    <div class="header-right">
        <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn-view-site" title="Ver Site">
            🌐 Ver Site
        </a>
        
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
                <span class="user-role"><?= ucfirst($user_role) ?></span>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="btn-logout">Sair</a>
    </div>
</header>