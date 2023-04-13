import './styles/app.scss';
import './bootstrap';

const $ = require('jquery');

import 'bootstrap'

import AOS from 'aos';

import 'slick-carousel';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';

import 'jquery-ui/ui/widgets/datepicker.js';
import 'jquery-ui/themes/base/all.css';

import 'vanilla-cookieconsent/dist/cookieconsent.css';
import 'vanilla-cookieconsent/dist/cookieconsent.js';

import 'sweetalert2/src/sweetalert2.scss';

import {
    startStimulusApp
} from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

import {
    Chart
} from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';

Chart.register(zoomPlugin);

$(document).ready(function () {
    AOS.init();
    console.log("chart");
    $("input[type='password']").each(function () {
        let eye = $('<i class="fa fa-eye" aria-hidden="true"></i>');
        eye.css({
            position: "absolute",
            right: "10px",
            top: "50%",
            transform: "translate(-50%,-50%)",
            cursor: "pointer",
        });
        $(this).after(eye);
        $(this).next().click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
            $(this).prev().attr("type", $(this).prev().attr("type") === "password" ? "text" : "password");
        });
    });
    $(".datepicker").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100:-18",
        maxDate: 0
    });

    $(".datepicker").on("keyup", function () {
        let input = $(this);
        let inputVal = input.val();
        let inputLength = inputVal.length;
        if (inputLength === 2 || inputLength === 5) {
            input.val(inputVal + "/");
        }
        if (inputLength > 10) {
            input.val(inputVal.slice(0, -1));
        }
        if (isNaN(inputVal.slice(-1))) {
            input.val(inputVal.slice(0, -1));
        }
    });

    // obtain plugin
    var cc = initCookieConsent();

    // run plugin with your configuration
    cc.run({
        current_lang: 'en',
        autoclear_cookies: true, // default: false
        page_scripts: true, // default: false
        onFirstAction: function (user_preferences, cookie) {
            // callback triggered only once
        },

        onAccept: function (cookie) {
            // ...
        },

        onChange: function (cookie, changed_preferences) {
            // ...
        },

        languages: {
            'en': {
                consent_modal: {
                    title: 'On utilise des cookies 🍪',
                    description: 'Bonjour, ce site utilise des cookies essentiels pour assurer son bon fonctionnement et des cookies de suivi pour comprendre comment vous interagissez avec lui. Ces derniers ne seront définis qu\'après votre consentement. <button type="button" data-cc="c-settings" class="cc-link">Laissez-moi choisir</button>',
                    primary_btn: {
                        text: 'Accepter tout',
                        role: 'accept_all'
                    },
                    secondary_btn: {
                        text: 'Refuser tout',
                        role: 'accept_necessary'
                    }
                },
                settings_modal: {
                    title: 'Préférences en matière de cookies',
                    save_settings_btn: 'Save settings',
                    save_settings_btn : 'Enregistrer les paramètres',
                    accept_all_btn: 'Accepter tout',
                    reject_all_btn: 'Refuser tout',
                    close_btn_label: 'Fermer',
                    cookie_table_headers: [{
                            col1: 'Name'
                        },
                        {
                            col2: 'Domain'
                        },
                        {
                            col3: 'Expiration'
                        },
                        {
                            col4: 'Description'
                        }
                    ],
                    blocks: [{
                        title_fr: 'Utilisation des cookies 📢',
                        description: 'J\'utilise des cookies pour assurer les fonctionnalités de base du site et pour améliorer votre expérience en ligne. Vous pouvez choisir pour chaque catégorie d\'opter pour l\'entrée / sortie à tout moment. Pour plus de détails relatifs aux cookies et autres données sensibles, veuillez lire la <a href="#" class="cc-link">politique de confidentialité</a> complète.',
                    }, {
                        title: 'Cookies strictement nécessaires 🍪',
                        description: 'Ces cookies sont essentiels au bon fonctionnement de mon site Web. Sans ces cookies, le site Web ne fonctionnerait pas correctement',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    }, {
                        title: 'Cookies de performance et d\'analyse 📈',
                        description: 'Ces cookies permettent au site Web de se souvenir des choix que vous avez faits dans le passé',
                        toggle: {
                            value: 'analytics',
                            enabled: false,
                            readonly: false
                        },
                        cookie_table: [
                            {
                                col1: '^_ga',
                                col2: 'google.com',
                                col3: '1 an',
                                col4: 'description ...',
                                is_regex: true
                            },
                            {
                                col1: '_gid',
                                col2: 'google.com',
                                col3: '1 jour',
                                col4: 'description ...',
                            }
                        ]
                    }, {
                        title: 'Cookies publicitaires et de ciblage',
                        description: 'Ces cookies collectent des informations sur la façon dont vous utilisez le site Web, les pages que vous avez visitées et les liens sur lesquels vous avez cliqué. Toutes les données sont anonymisées et ne peuvent pas être utilisées pour vous identifier',
                        toggle: {
                            value: 'targeting',
                            enabled: false,
                            readonly: false
                        }
                    }, {
                        title: 'Plus d\'informations',
                        description: 'Pour toute question relative à notre politique en matière de cookies et vos choix, veuillez <a class="cc-link" href="#yourcontactpage">nous contacter</a>.',
                    }]
                }
            }
        }
    });
});