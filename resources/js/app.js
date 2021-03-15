/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

window.Vue = require('vue');

/**
 * Add Vuetify
 */
import Vuetify from 'vuetify';

Vue.use(Vuetify);

/**
 * Cropper
 */
import VuejsClipper from 'vuejs-clipper'
import VueRx from 'vue-rx'

Vue.use(VueRx);
Vue.use(VuejsClipper);

/**
 * Add VeeValidator
 */
import './veevalidator';

/**
 * Import all components
 */
import BrokerForm from "./components/BrokerForm";
import UserForm from "./components/UserForm";
import LoginForm from "./components/LoginForm";
import GetAnOffer from "./components/GetAnOffer";
import InquiryForm from "./components/InquiryForm";
import InquiryList from "./components/InquiryList";
import OfferForm from "./components/OfferForm";
import OffersList from "./components/OffersList";
import AcceptOffer from "./components/AcceptOffer";
import BrokerList from "./components/BrokerList";
import MakeConnection from "./components/MakeConnection";
import Notifications from "./components/Notifications";
import RateForm from "./components/RateForm";
import Recommended from "./components/Recommended";
import Tabs from "./components/base/Tabs";
import ResetPasswordForm from "./components/ResetPasswordForm";
import ResetForm from "./components/ResetForm";
import MyInquiryList from "./components/MyInquiryList";
import BrokerInquiryList from "./components/BrokerInquiryList";
import BrokerProfileForm from "./components/BrokerProfileForm";
import UserProfileForm from "./components/UserProfileForm";
import BrokerBecomeVisible from "./components/BrokerBecomeVisible";
import CenterBox from "./components/CenterBox";
import ContentList from "./components/ContentList";
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.config.devtools = false;
// Vue.config.debug = false;
// Vue.config.silent = true;

const app = new Vue({
    el: '#app',
    components: {
        Tabs,
        BrokerForm,
        UserForm,
        LoginForm,
        GetAnOffer,
        InquiryForm,
        InquiryList,
        OfferForm,
        OffersList,
        AcceptOffer,
        BrokerList,
        MakeConnection,
        Notifications,
        RateForm,
        Recommended,
        ResetPasswordForm,
        ResetForm,
        MyInquiryList,
        BrokerInquiryList,
        BrokerProfileForm,
        UserProfileForm,
        BrokerBecomeVisible,
        CenterBox,
        ContentList
    },
    vuetify: new Vuetify({
        theme: {
            dark: false,
            themes: {
                dark: {
                    primary: '#21CFF3',
                    accent: '#FF4081',
                    secondary: '#ffe18d',
                    success: '#4CAF50',
                    info: '#2196F3',
                    warning: '#FB8C00',
                    error: '#FF5252'
                },
                light: {
                    primary: '#08B973',
                    accent: '#E91E63',
                    secondary: '#363A7B',
                    success: '#4CAF50',
                    info: '#2196F3',
                    warning: '#FB8C00',
                    error: '#FF5252'
                }
            }
        },
    }),
    data: () => ({
        drawer: false,
        btotop: false,
    }),
    methods: {
        authenticated() {
            if (window.auth) {
                return true;
            }

            return false;
        },

        getPrice() {
          if (window.auth.price) {
              return window.auth.price;
          }

          return false
        },

        getCurrency() {
            if (window.auth.currency) {
                return window.auth.currency;
            }

            return false
        },

        role() {
            return window.auth && window.auth.role ? window.auth.role : false;
        },

        onScroll(e) {
            if (typeof window === 'undefined') return;

            const top = window.pageYOffset || e.target.scrollTop | 0;

            // If scrolled atleast 20 px show b to top
            this.btotop = top > 20;
        },

        toTop() {
            this.$vuetify.goTo(0);
        }
    }
});
console.log(window.OneSignal, "window.OneSignal")
if (window.OneSignal !== undefined || typeof window.OneSignal !== undefined || typeof window.OneSignal !== 'undefined')
{
    window.OneSignal.push(function () {
        window.OneSignal.getUserId(function (userId) {
            if (! userId || userId === 'null' || userId === null) {
                return;
            }

            axios.post(`notifications/onesignal/${userId}`);
        });
    });
}
