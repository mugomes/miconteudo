<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

header("Content-Type: text/plain; charset=UTF-8", true);

$sLinks = '';

$txt = 'User-Agent: *
Disallow: /themes/css/
Disallow: /themes/js/
Disallow: /sites/' . $idsite . '/themes/css/
Disallow: /sites/' . $idsite . '/themes/js/

Sitemap: ' . servername() . '/sitemapindex.xml';

echo $txt;
