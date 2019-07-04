import Vue from 'vue';

import './assets/layout.css'
import './assets/header.css'
import './assets/nav.css'
import './assets/demos.css'
import './assets/form.css'

import { store } from './_store';
import { router } from './_helpers';
import App from './app/App';

new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App)
});