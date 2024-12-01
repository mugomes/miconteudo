// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

let menuToggle = document.querySelector(".menuToggle");
let header = document.querySelector("nav ul");

menuToggle.onclick = function () {
    header.classList.toggle("active")
};

document.querySelector("header nav li a").addEventListener('click', function (a) {
    if (document.querySelector("header nav li a").getAttribute("href") === "#") {
        a.preventDefault()
    }
});

let submenuItems = document.querySelectorAll("nav ul li > a");

submenuItems.forEach(item => {
  item.addEventListener('click', function (e) {
    const nextSibling = item.nextElementSibling;
    if (nextSibling && nextSibling.classList.contains('submenu')) {
      e.preventDefault();  // Evita redirecionamento ao clicar
      const parent = item.parentElement;
      parent.classList.toggle('active');  // Exibe submenu ao clicar
    }
  });
});