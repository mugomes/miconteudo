<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

if (!defined('miconteudo')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor</title>
    <link rel="stylesheet" href="/adm/plugins/grapesjs/grapes.min.css">
    <link rel="stylesheet" href="/adm/plugins/grapesjs-component-code-editor/dist/grapesjs-component-code-editor.min.css">
    <link rel="stylesheet" href="/adm/plugins/grapesjs-plugin-filestack/dist/grapesjs-plugin-filestack.css">
    <link rel="stylesheet" href="/adm/plugins/grapesjs-rulers/dist/grapesjs-rulers.min.css">
    <link rel="stylesheet" href="/adm/plugins/grapick/dist/grapick.min.css">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }
    </style>
</head>

<body>
    <div id="gjs"></div>

    <script src="/adm/plugins/grapesjs/grapes.min.js"></script>
    <script src="/adm/plugins/grapesjs-blocks-basic/dist/index.js"></script>
    <script src="/adm/plugins/grapesjs-blocks-flexbox/dist/index.js"></script>
    <script src="/adm/plugins/grapesjs-component-code-editor/dist/grapesjs-component-code-editor.min.js"></script>
    <script src="/adm/plugins/grapesjs-component-countdown/dist/index.js"></script>
    <script src="/adm/plugins/grapesjs-rulers/dist/grapesjs-rulers.min.js"></script>
    <script src="/adm/plugins/grapick/dist/grapick.min.js"></script>
    <script src="/adm/plugins/grapesjs-templates/dist/index.js"></script>
    <script src="/adm/plugins/grapesjs-plugin-forms/dist/index.js"></script>
    <script src="/adm/plugins/tinymce/tinymce.min.js"></script>
    <script src="/adm/plugins/miconteudo-editor/miconteudo-editor.js"></script>
    <script src="/adm/plugins/miconteudo-filemanager/miconteudo-filemanager.js"></script>
    <script src="/adm/plugins/miconteudo-blocks/miconteudo-blocks.js"></script>
    <script>
        function post(url, data, callback) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);

            xhr.onreadystatechange = function() {
                if (this.status == 200) {
                    callback(this.responseText);
                }
            }

            xhr.send(data);
        }

        setTimeout(function() {
            var editor = grapesjs.init({
                container: '#gjs',
                height: "100%",
                fromElement: true,
                storageManager: false,
                plugins: ['miconteudo-editor', 'miconteudo-filemanager', miConteudoBlocos, 'grapesjs-rulers', 'grapesjs-component-code-editor', 'grapesjs-plugin-forms', 'grapesjs-component-countdown'],
                pluginsOpts: {
                    // 'grapesjs-tailwind': {}
                },
                assetManager: {
                    embedAsBase64: false
                },
                canvas: {
                    styles: [
                        // "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
                        "/themes/css/normalize.css",
                        "/themes/css/style.css",
                        "/themes/css/style.responsive.css",
                        "/sites/<?php echo $idsite; ?>/themes/css/style.css",
                        "/sites/<?php echo $idsite; ?>/themes/css/style.responsive.css",
                        "/themes/css/prism.css"
                    ],
                    scripts: [
                        "/themes/js/prism.js"
                        // "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                    ]
                },
                i18n: {
                    locale: 'pt',
                    localeFallback: 'pt',
                    messages: {
                        pt: {
                            traitManager: {
                                label: 'Configurações',
                                traits: {
                                    labels: {
                                        for: 'Para',
                                        text: 'Texto',
                                        method: 'Método',
                                        action: 'Ação'
                                    }
                                }
                            },
                            assetManager: {
                                addButton: 'Adicionar imagem',
                                inputPlh: 'http://caminho/para/a/imagem.jpg',
                                modalTitle: 'Selecionar imagem',
                                uploadTitle: 'Solte os arquivos aqui ou clique para enviar'
                            },
                            // Here just as a reference, GrapesJS core doesn't contain any block,
                            // so this should be omitted from other local files
                            blockManager: {
                                labels: {
                                    // 'block-id': 'Block Label',
                                    column1: '1 Coluna',
                                    column2: '2 Colunas',
                                    column3: '3 Colunas',
                                    'column3-7': '2 Colunas 3/7',
                                    text: 'Texto',
                                    link: 'Link',
                                    image: 'Imagem',
                                    video: 'Vídeo',
                                    map: 'Mapa',
                                    countdown: 'Contador',
                                    tabs: 'Abas'
                                },
                                categories: {
                                    // 'category-id': 'Category Label',
                                    Basic: 'Básico'
                                }
                            },
                            domComponents: {
                                names: {
                                    '': 'Box',
                                    wrapper: 'Corpo',
                                    text: 'Texto',
                                    comment: 'Comentário',
                                    image: 'Imagem',
                                    video: 'Vídeo',
                                    label: 'Label',
                                    link: 'Link',
                                    map: 'Mapa',
                                    tfoot: 'Rodapé da tabela',
                                    tbody: 'Corpo da tabela',
                                    thead: 'Cabeçalho da tabela',
                                    table: 'Tabela',
                                    row: 'Linha da tabela',
                                    cell: 'Célula da tabela',
                                    section: 'Seção',
                                    body: 'Corpo'
                                }
                            },
                            deviceManager: {
                                device: 'Dispositivo',
                                devices: {
                                    desktop: 'Desktop',
                                    tablet: 'Tablet',
                                    mobileLandscape: 'Celular, modo panorâmico',
                                    mobilePortrait: 'Celular, modo retrato'
                                }
                            },
                            panels: {
                                buttons: {
                                    titles: {
                                        preview: 'Pré-visualização',
                                        fullscreen: 'Tela cheia',
                                        'sw-visibility': 'Ver componentes',
                                        'export-template': 'Ver código',
                                        'open-sm': 'Abrir gerenciador de estilos',
                                        'open-tm': 'Configurações',
                                        'open-layers': 'Abrir gerenciador de camadas',
                                        'open-blocks': 'Abrir blocos'
                                    }
                                }
                            },
                            selectorManager: {
                                label: 'Classes',
                                selected: 'Selecionado',
                                emptyState: '- Estado -',
                                states: {
                                    hover: 'Hover',
                                    active: 'Click',
                                    'nth-of-type(2n)': 'Even/Odd'
                                }
                            },
                            styleManager: {
                                empty: 'Selecione um elemento para usar o gerenciador de estilos',
                                layer: 'Camada',
                                fileButton: 'Imagens',
                                sectors: {
                                    general: 'Geral',
                                    layout: 'Disposição',
                                    typography: 'Tipografia',
                                    decorations: 'Decorações',
                                    extra: 'Extra',
                                    flex: 'Flex',
                                    dimension: 'Dimensão'
                                },
                                // The core library generates the name by their `property` name
                                properties: {
                                    float: 'Float',
                                    display: 'Exibição',
                                    position: 'Posição',
                                    top: 'Topo',
                                    right: 'Direita',
                                    left: 'Esquerda',
                                    bottom: 'Inferior',
                                    width: 'Largura',
                                    height: 'Altura',
                                    'max-width': 'Largura Max.',
                                    'max-height': 'Altura Max.',
                                    margin: 'Margem',
                                    'margin-top-sub': 'Superior',
                                    'margin-right-sub': 'Direita',
                                    'margin-left-sub': 'Esquerda',
                                    'margin-bottom-sub': 'Inferior',
                                    padding: 'Padding',
                                    'padding-top-sub': 'Superior',
                                    'padding-left-sub': 'Esquerda',
                                    'padding-right-sub': 'Direita',
                                    'padding-bottom-sub': 'Inferior',
                                    'font-family': 'Tipo de letra',
                                    'font-size': 'Tamanho da fonte',
                                    'font-weight': 'Espessura da fonte',
                                    'letter-spacing': 'Espaço entre letras',
                                    color: 'Cor',
                                    'line-height': 'Altura da linha',
                                    'text-align': 'Alinhamento do texto',
                                    'text-shadow': 'Sombra do texto',
                                    'text-shadow-h': 'Sombra do texto: horizontal',
                                    'text-shadow-v': 'Sombra do texto: vertical',
                                    'text-shadow-blur': 'Desfoque da sombra do texto',
                                    'text-shadow-color': 'Cor da sombra da fonte',
                                    'border-top-left': 'Borda superior a esquerda',
                                    'border-top-right': 'Borda superior a direita',
                                    'border-bottom-left': 'Borda inferior a esquerda',
                                    'border-bottom-right': 'Borda inferior a direita',
                                    'border-top-left-radius-sub': 'Superior esquerda',
                                    'border-top-right-radius-sub': 'Superior direita',
                                    'border-bottom-right-radius-sub': 'Inferior direita',
                                    'border-bottom-left-radius-sub': 'Inferior esquerda',
                                    'border-radius': 'Raio da borda',
                                    border: 'Borda',
                                    'border-width-sub': 'Largura',
                                    'border-style-sub': 'Estilo',
                                    'border-color-sub': 'Cor',
                                    'box-shadow': 'Sombra da box',
                                    'box-shadow-h': 'Sombra da box: horizontal',
                                    'box-shadow-v': 'Sombra da box: vertical',
                                    'box-shadow-blur': 'Desfoque da sombra da box',
                                    'box-shadow-spread': 'Extensão da sombra da box',
                                    'box-shadow-color': 'Cor da sombra da box',
                                    'box-shadow-type': 'Tipo de sombra da box',
                                    background: 'Fundo',
                                    'background-color': 'Cor de fundo',
                                    'background-image': 'Imagem de fundo',
                                    'background-repeat': 'Repetir fundo',
                                    'background-position': 'Posição do fundo',
                                    'background-attachment': 'Plugin de fundo',
                                    'background-size': 'Tamanho do fundo',
                                    opacity: 'Opacidade',
                                    transition: 'Transição',
                                    'transition-property': 'Tipo de transição',
                                    'transition-duration': 'Tempo de transição',
                                    'transition-timing-function': 'Função do tempo da transição',
                                    perspective: 'Perspectiva',
                                    transform: 'Transformação',
                                    'transform-rotate-x': 'Rotacionar horizontalmente',
                                    'transform-rotate-y': 'Rotacionar verticalmente',
                                    'transform-rotate-z': 'Rotacionar profundidade',
                                    'transform-scale-x': 'Escalar horizontalmente',
                                    'transform-scale-y': 'Escalar verticalmente',
                                    'transform-scale-z': 'Escalar profundidade',
                                    'flex-direction': 'Direção Flex',
                                    'flex-wrap': 'Flex wrap',
                                    'justify-content': 'Ajustar conteúdo',
                                    'align-items': 'Alinhar elementos',
                                    'align-content': 'Alinhar conteúdo',
                                    order: 'Ordem',
                                    'flex-basis': 'Base Flex',
                                    'flex-grow': 'Crescimento Flex',
                                    'flex-shrink': 'Contração Flex',
                                    'align-self': 'Alinhar-se'
                                }

                            }
                        }
                    }
                }
            });

            /* Painel para plugin: grapesjs-component-code-editor */
            const pn = editor.Panels;
            const panelViews = pn.addPanel({
                id: "views"
            });
            panelViews.get("buttons").add([{
                attributes: {
                    title: "Editar Código"
                },
                className: "fa fa-file-code-o",
                command: "open-code",
                togglable: false, //do not close when button is clicked again
                id: "open-code"
            }]);

            pn.addButton('options', [{
                attributes: {
                    title: "Salvar"
                },
                className: "fa fa-floppy-o",
                command: "save-db",
                togglable: false,
                id: "save-db"
            }]);

            pn.addButton('options', [{
                attributes: {
                    title: "Sair"
                },
                className: "fa fa-window-close-o",
                command: "fechar",
                togglable: false,
                id: "fechar"
            }]);

            editor.on('load', () => {
                editor.loadProjectData(JSON.parse('<?php echo (empty($projeto)) ? '{\"assets\":[],\"styles\":[{\"selectors\":[\"#i7yl\"],\"style\":{\"width\":\"100%\",\"min-height\":\"30px\",\"display\":\"inline-block\",\"position\":\"relative\"}},{\"selectors\":[\"#ixmi\"],\"style\":{\"width\":\"100%\",\"display\":\"inline-block\",\"position\":\"relative\"}}],\"pages\":[{\"frames\":[{\"component\":{\"type\":\"wrapper\",\"stylable\":[\"background\",\"background-color\",\"background-image\",\"background-repeat\",\"background-attachment\",\"background-position\",\"background-size\"],\"components\":[{\"type\":\"div\",\"attributes\":{\"id\":\"i7yl\"},\"components\":[{\"type\":\"text\",\"content\":\"Adicione o texto aqui\",\"attributes\":{\"id\":\"ixmi\"}}]}],\"head\":{\"type\":\"head\"},\"docEl\":{\"tagName\":\"html\"}},\"id\":\"um7lYnFkKrXHC4ba\"}],\"type\":\"main\",\"id\":\"LN0RWgD0JekREpiW\"}],\"symbols\":[]}' : $projeto; ?>'));

                const blockManager = editor.BlockManager;

                // Remove o bloco de imagem
                //blockManager.remove('image');

                // Remove o bloco de link
                //blockManager.remove('link');

            });

            // Add the command
            editor.Commands.add('save-db', {
                run: function(editor, sender) {
                    sender && sender.set('active', 0); // turn off the button
                    editor.store();

                    var projectData = JSON.stringify(editor.getProjectData());

                    // Compilado
                    var htmldata = editor.getHtml();
                    var cssdata = editor.getCss();

                    let formData = new FormData();
                    formData.append('txtProjeto', projectData);
                    formData.append('txtDescricao', htmldata);
                    formData.append('txtEstilos', cssdata);

                    let saveCount = 1;
                    post('<?php echo $saveURL; ?>', formData, function(retorno) {
                        if (retorno == 'savefile' && saveCount === 3) {
                            alert('Salvo com sucesso!');
                        }

                        saveCount += 1;
                    });
                }
            });

            // Add the command
            editor.Commands.add('fechar', {
                run: function(editor, sender) {
                    if (confirm('Você deseja realmente fechar?\nQualquer alteração não salva será perdida!')) {
                        window.location.assign('<?php echo $closeURL; ?>');
                    }
                }
            });

            /* Painel para plugin: grapesjs-rulers */
            pn.addButton('options', [{
                attributes: {
                    title: "Régua"
                },
                label: `<svg width="18" viewBox="0 0 16 16"><path d="M0 8a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 0 1H.5A.5.5 0 0 1 0 8z"/><path d="M4 3h8a1 1 0 0 1 1 1v2.5h1V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2.5h1V4a1 1 0 0 1 1-1zM3 9.5H2V12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9.5h-1V12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/></svg>`,
                command: "ruler-visibility",
                id: "ruler-visibility"
            }]);

            editor.runCommand("ruler-visibility");
        }, 0)
    </script>
</body>

</html>