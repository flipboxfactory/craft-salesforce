module.exports = {
    title: 'Salesforce for Craft CMS',
    description: 'Salesforce Plugin for Craft CMS',
    base: '/',
    themeConfig: {
        logo: '/icon.svg',
        docsRepo: 'flipboxfactory/craft-salesforce',
        docsDir: 'docs',
        docsBranch: 'master',
        editLinks: true,
        search: true,
        searchMaxSuggestions: 10,
        codeLanguages: {
            twig: 'Twig',
            php: 'PHP',
            json: 'JSON',
            // any other languages you want to include in code toggles...
        },
        nav: [
            {text: 'Changelog', link: 'https://github.com/flipboxfactory/craft-salesforce/blob/master/CHANGELOG.md'},
            {text: 'Repo', link: 'https://github.com/flipboxfactory/craft-salesforce'}
        ],
        sidebar: {
            '/': [
                {
                    title: 'Getting Started',
                    collapsable: false,
                    children: [
                        ['/', 'Introduction'],
                        ['/requirements', 'Requirements'],
                        ['/installation', 'Installation / Upgrading'],
                        ['/support', 'Support'],
                    ]
                }
            ]
        }
    },
    markdown: {
        anchor: { level: [2, 3, 4] },
        toc: { includeLevel: [3] },
        config(md) {
            md.use(require('vuepress-theme-flipbox/markup'))
        }
    }
}