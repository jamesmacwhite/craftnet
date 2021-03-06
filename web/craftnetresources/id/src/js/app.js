// import './../sass/app.scss';

import Vue from 'vue';
import store from './store'
import {currency} from './filters/currency';
import {formatCmsLicense, formatPluginLicense} from './filters/licenses';
import {capitalize} from './filters/capitalize';
import App from './App.vue';
import './plugins/craftui'

Vue.filter('currency', currency);
Vue.filter('formatCmsLicense', formatCmsLicense);
Vue.filter('formatPluginLicense', formatPluginLicense);
Vue.filter('capitalize', capitalize);
Vue.use(require('vue-moment'));

import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

window.craftIdApp = new Vue({

    store,
    render: h => h(App),

    data() {
        return {
        }
    },

    methods: {

        /**
         * Connect app callback.
         *
         * @param apps
         */
        connectAppCallback(apps) {
            this.$store.dispatch('account/connectAppCallback', apps);

            this.$store.dispatch('app/displayNotice', 'App connected.');
        },

    },

}).$mount('#app')
