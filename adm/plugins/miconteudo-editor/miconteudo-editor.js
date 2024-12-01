// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

(function () {
    // Verifique se o GrapesJS e o TinyMCE estão disponíveis
    if (typeof grapesjs === 'undefined') {
        throw new Error('GrapesJS não está disponível');
    }
    if (typeof tinymce === 'undefined') {
        throw new Error('TinyMCE não está disponível');
    }

    function fileBrowser(callback, value, meta) {
        tinymce.activeEditor.windowManager.openUrl({
            title: 'Gerenciador de Arquivos',
            url: '/adm/dashboard/filemanager/list.php?tipo=editortext',
            onMessage: function (api, data) {
                if (data.mceAction === 'customAction') {
                    callback(data.url);
                    api.close();
                }
            }
        });
    }

    grapesjs.plugins.add('miconteudo-editor', function (editor, opts = {}) {
        var defaults = {
            // Opções do TinyMCE
            options: {
                language: "pt_BR",
                plugins: 'searchreplace code image link media codesample table pagebreak anchor insertdatetime advlist lists wordcount charmap emoticons',
                toolbar: 'fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | pagebreak anchor codesample | code | sourcecode',
                menubar: false,
                height: '500px',
                statusbar: false,
                branding: false,
                license_key: 'gpl',
                relative_urls: false,
                remove_script_host: true,
                entity_encoding: "raw",
                image_uploadtab: false,
                image_advtab: true,
                image_caption: true,
                promotion: false,
                file_picker_callback: fileBrowser,
                style_formats: [{
                    title: '-',
                    selector: 'a',
                    classes: ''
                }, {
                    title: 'Primary',
                    selector: 'a',
                    classes: 'btn btn-primary'
                }, {
                    title: 'Secondary',
                    selector: 'a',
                    classes: 'btn btn-secondary'
                }, {
                    title: 'Success',
                    selector: 'a',
                    classes: 'btn btn-success'
                }, {
                    title: 'Danger',
                    selector: 'a',
                    classes: 'btn btn-danger'
                }, {
                    title: 'Warning',
                    selector: 'a',
                    classes: 'btn btn-warning'
                }, {
                    title: 'Info',
                    selector: 'a',
                    classes: 'btn btn-info'
                }, {
                    title: 'Light',
                    selector: 'a',
                    classes: 'btn btn-light'
                }, {
                    title: 'dark',
                    selector: 'a',
                    classes: 'btn btn-dark'
                }, {
                    title: 'link',
                    selector: 'a',
                    classes: 'btn btn-link'
                }],
                link_class_list: [{
                    title: '-',
                    value: ''
                }, {
                    title: 'Primary',
                    value: 'btn btn-primary'
                }, {
                    title: 'Secondary',
                    value: "btn btn-secondary"
                }, {
                    title: 'Success',
                    value: "btn btn-sucess"
                }, {
                    title: 'Danger',
                    value: "btn btn-danger"
                }, {
                    title: 'Warning',
                    value: "btn btn-warning"
                }, {
                    title: 'Info',
                    value: "btn btn-info"
                }, {
                    title: 'Light',
                    value: "btn btn-light"
                }, {
                    title: 'Dark',
                    value: "btn btn-dark"
                }, {
                    title: 'Link',
                    value: "btn btn-link"
                }],
                link_rel_list: [{
                    title: '-',
                    value: ''
                }, {
                    title: 'No Follow',
                    value: 'nofollow'
                }, {
                    title: 'No Opener',
                    value: 'noopener nofollow'
                }],
                codesample_languages: [
                    {
                        text: 'PHP',
                        value: 'php'
                    },
                    {
                        text: 'Bash/SH',
                        value: 'bash'
                    },
                    {
                        text: 'HTML/XML',
                        value: 'markup'
                    },
                    {
                        text: 'CSS',
                        value: 'css'
                    },
                    {
                        text: 'JavaScript',
                        value: 'javascript'
                    },
                    {
                        text: 'Python',
                        value: 'python'
                    },
                ]
            },

            // Em qual lado do elemento posicionar a barra de ferramentas
            // Opções disponíveis: 'left|center|right'
            position: 'left'
        };

        var c = Object.assign({}, defaults, opts);

        editor.setCustomRte({
            enable: function (el, rte) {
                // Se já existe, apenas foca nele
                el.contentEditable = true;

                // Oculta a barra de ferramentas padrão, se presente
                var rteToolbar = editor.RichTextEditor.getToolbarEl();
                Array.from(rteToolbar.children).forEach(function (child) {
                    child.style.display = 'none';
                });

                var modal = editor.Modal;

                modal.onceOpen(function () {
                    tinymce.init(Object.assign({
                        selector: `#${el.id}tinymce`,
                        setup: function (editorInstance) {
                            editorInstance.on('init', function () {
                                editorInstance.setContent(el.innerHTML);
                            });
                            editorInstance.on('change', function () {
                                // Salva o conteúdo ao mudar
                                editorInstance.save();
                            });
                        }
                    }, c.options));
                });

                modal.onceClose(function () {
                    var tinymceEditor = tinymce.get(`${el.id}tinymce`);
                    if (tinymceEditor) {
                        var tinymceEditor = tinymce.get(`${el.id}tinymce`);
                        if (tinymceEditor) {
                            let elHTML = tinymceEditor.getContent();
                            if (editor.getSelected().getEl().innerHTML !== elHTML) {
                                editor.getSelected().set('content', elHTML);
                            }
                        }
                        tinymceEditor.remove();
                    }
                });

                modal.open({
                    title: 'Editor',
                    content: `<style>.modal-tinymce .tox.tox-tinymce {min-height:400px;}.modal-tinymce .gjs-btn-prim { margin: 10px 5px 5px 0; }</style><div id="${el.id}tinymce"></div>`, //<input type="button" class="gjs-btn-prim" id="BtnSave" value="Save" title="save"><input class="gjs-btn-prim" id="BtnClose" type="button" value="Close" title="close">
                    attributes: {
                        class: `modal-tinymce`,
                        id: `mtinymce${el.id}`
                    }
                });
            },
            focus: function (el, rte) {
                el.contentEditable = true;
            },
            disable: function (el, rte) {
                el.contentEditable = false;
            }
        });
    });
})();
