<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

header("Content-Type: text/xml; charset=UTF-8", true);

if (file_exists(documentroot() . '/sites/' . $idsite . '/sitemap/sitemapindex.xml')) {
    echo file_get_contents(documentroot() . '/sites/' . $idsite . '/sitemap/sitemapindex.xml');
}
