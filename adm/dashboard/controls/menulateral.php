<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/adm/dashboard/dashboard.php" class="brand-link">
        <span class="brand-text font-weight-light">MiConteudo</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/adm/dashboard/dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/page/list.php" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>
                            Páginas
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/post/list.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                            Postagens
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/categories/list.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                            Categorias
                        </p>
                    </a>
                </li>
                <li class="nav-header">Configurações</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            Layout
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/adm/dashboard/menutopo/list.php" class="nav-link active">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Menu Topo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/adm/dashboard/rodape/editor.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rodapé</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/options.php" class="nav-link">
                        <i class="nav-icon far fa-calendar-alt"></i>
                        <p>
                            Opções
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/configemail.php" class="nav-link">
                        <i class="nav-icon far fa-calendar-alt"></i>
                        <p>
                            SMTP
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/filemanager/list.php" class="nav-link">
                        <i class="nav-icon far fa-image"></i>
                        <p>
                            Gerenciador de Arquivos
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/adm/dashboard/users/list.php" class="nav-link">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Usuários
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>