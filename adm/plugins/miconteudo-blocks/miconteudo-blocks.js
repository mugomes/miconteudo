// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

function miConteudoBlocos(editor) {
    editor.Components.addType('div', {
        model: {
            defaults: {
                tagName: 'div',
                draggable: true
            }
        },
        isComponent: (el) => el.tagName === 'DIV',
    });

    // Blocos
    editor.Blocks.add('column-1', {
        label: 'Coluna 1',
        content: `
                <div style="display: inline-block; width: 100%; min-height: 30px; position: relative;">
                
                </div>
            `,
        category: 'Blocos',
    });

    editor.Blocks.add('column-2', {
        label: 'Coluna 2',
        content: `
                <div style="display: flex; width: 100%; min-height: 30px; position: relative;">
                    <div style="flex: 1;"></div>
                    <div style="flex: 1;"></div>
                </div>
            `,
        category: 'Blocos',
    });

    editor.Blocks.add('column-3', {
        label: 'Coluna 3',
        content: `
                <div style="display: flex; width: 100%; min-height: 30px; position: relative;">
                    <div style="flex: 1;"></div>
                    <div style="flex: 1;"></div>
                    <div style="flex: 1;"></div>
                </div>
            `,
        category: 'Blocos',
    });

    editor.Blocks.add('block-text', {
        label: 'Texto',
        content: {
            type: 'text',
            content: 'Adicione o texto aqui',
            style: {
                width: '100%',
                display: 'inline-block',
                position: 'relative'
            }
        },
        category: 'Blocos',
    });
    
    // Imagem
    editor.Blocks.add('block-image', {
        label: 'Imagem',
        content: {
            type: 'image',
        },
        category: 'Multimidia',
    });
    editor.Components.addType('image', {
        model: {
            defaults: {
                tagName: 'image',
                attributes: {
                    alt: '',
                    title: ''
                },
                traits: [{
                    label: 'Alt',
                    name: 'alt'
                }, {
                    label: 'Título',
                    name: 'title'
                }]
            }
        }
    });

    //Video
    editor.Blocks.add('block-video', {
        label: 'Vídeo',
        content: {
            type: 'video'
        },
        category: 'Multimidia',
    });

    // Heading
    editor.BlockManager.add('block-h1', {
        label: 'Título 1',
        content: '<h1 style="font-size:32px;">Título 1</h1>',
        category: 'Títulos',
    });

    editor.BlockManager.add('block-h2', {
        label: 'Título 2',
        content: '<h2 style="font-size:28px;">Título 2</h2>',
        category: 'Títulos',
    });

    editor.BlockManager.add('block-h3', {
        label: 'Título 3',
        content: '<h3 style="font-size:24px;">Título 3</h3>',
        category: 'Títulos',
    });

    editor.BlockManager.add('block-h4', {
        label: 'Título 4',
        content: '<h4 style="font-size:18px;">Título 4</h4>',
        category: 'Títulos',
    });

    editor.BlockManager.add('block-h5', {
        label: 'Título 5',
        content: '<h5 style="font-size:16px;">Título 5</h5>',
        category: 'Títulos',
    });

    editor.BlockManager.add('block-h6', {
        label: 'Título 6',
        content: '<h6 style="font-size:14px;">Título 6</h6>',
        category: 'Títulos',
    });

    editor.Blocks.add('plano-duplo', {
        label: 'Plano Duplo',
        content: `
            <div id="plano-duplo" style="width: 100%; display: flex; flex-direction: row; gap: 20px; justify-content: center; align-items: flex-start; padding: 20px; box-sizing: border-box;">
                <div style="flex: 1; max-width: 400px; padding: 20px; background-color: #ffffff; border: 1px solid #dedede; border-radius: 10px; text-align: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <h3 style="margin: 0 0 10px; font-size: 1.8em; color: #007bff;">Plano Básico</h3>
                    <p style="margin: 10px 0; font-size: 2.2em; font-weight: bold; color: #007bff;">R$ 49,99</p>
                    <p style="margin: 10px 0; font-size: 1em; color: #555;">
                        Ideal para quem está começando. Inclui funcionalidades básicas:
                    </p>
                    <ul style="list-style-type: none; padding: 0; margin: 20px 0; text-align: left; color: #333; font-size: 0.9em;">
                        <li>✔ Acesso limitado a recursos</li>
                        <li>✔ Suporte por e-mail</li>
                        <li>✔ 10 GB de armazenamento</li>
                    </ul>
                    <a style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">
                        Assinar Plano Básico
                    </a>
                </div>

                <div style="flex: 1; max-width: 400px; padding: 20px; background-color: #ffffff; border: 1px solid #dedede; border-radius: 10px; text-align: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <h3 style="margin: 0 0 10px; font-size: 1.8em; color: #28a745;">Plano Premium</h3>
                    <p style="margin: 10px 0; font-size: 2.2em; font-weight: bold; color: #28a745;">R$ 99,99</p>
                    <p style="margin: 10px 0; font-size: 1em; color: #555;">
                        Perfeito para usuários avançados. Inclui:
                    </p>
                    <ul style="list-style-type: none; padding: 0; margin: 20px 0; text-align: left; color: #333; font-size: 0.9em;">
                        <li>✔ Acesso total a todos os recursos</li>
                        <li>✔ Suporte 24/7</li>
                        <li>✔ 1 TB de armazenamento</li>
                        <li>✔ Relatórios personalizados</li>
                    </ul>
                    <a style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">
                        Assinar Plano Premium
                    </a>
                </div>
            </div>
            <style>
                @media (max-width: 768px) {
                    #plano-duplo {
                        flex-direction: column; /* Alinha os planos verticalmente em telas menores */
                        gap: 20px;
                    }
                    #plano-duplo > div {
                        width: 100%;
                        max-width: 100%;
                    }
                }
            </style>
        `,
        category: 'Componentes',
    });


}