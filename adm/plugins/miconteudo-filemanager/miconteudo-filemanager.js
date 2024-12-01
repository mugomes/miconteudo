// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

(function () {
    // Verifique se o GrapesJS estão disponíveis
    if (typeof grapesjs === 'undefined') {
        throw new Error('GrapesJS não está disponível');
    }

    // Registra o plugin no GrapesJS
    grapesjs.plugins.add('miconteudo-filemanager', function (editor, opts = {}) {
        const { assetManagerOptions = {} } = opts;

        // Substitui a configuração padrão do assetManager
        editor.Commands.add('open-assets', {
            run(editor) {
                // Abre a janela do gerenciador de arquivos externo
                window.open('/adm/dashboard/filemanager/list.php?tipo=editor', 'parent', 'width=500,height=500');

                // Configura um listener para receber a mensagem da janela do gerenciador de arquivos
                window.addEventListener('message', function handleMessage(event) {
                    console.log(event.data.fileSelected)
                    if (event.data.fileSelected) {
                        const fileUrl = event.data.fileSelected;

                        // Adiciona o arquivo ao Asset Manager do GrapesJS
                        // editor.AssetManager.add([{
                        //     src: fileUrl,
                        // }]);

                        // Obtém o componente selecionado
                        const selectedComponent = editor.getSelected();
                        if (selectedComponent && selectedComponent.get('type') === 'image') {
                            // Atualiza o atributo 'src' do componente de imagem com a URL selecionada
                            selectedComponent.set({ src: fileUrl });
                        }

                        // Adiciona a imagem ao canvas do editor
                        // editor.addComponents(`<img src="${fileUrl}" />`);

                        // Remove o listener para evitar múltiplas chamadas
                        window.removeEventListener('message', handleMessage);

                        // Fecha o Asset Manager do GrapesJS
                        editor.AssetManager.close();
                    }
                });
            }
        });
    });
})();
