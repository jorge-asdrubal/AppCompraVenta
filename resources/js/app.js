/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

const { default: axios } = require('axios');
const { default: Echo } = require('laravel-echo');

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('categoria-component', require('./components/CategoriaComponent.vue').default);
Vue.component('categoria-component', require('./components/CategoriaComponent.vue').default);
Vue.component('articulo-component', require('./components/ArticuloComponent.vue').default);
Vue.component('cliente-component', require('./components/ClienteComponent.vue').default);
Vue.component('proveedor-component', require('./components/ProveedorComponent.vue').default);
Vue.component('rol-component', require('./components/RolComponent.vue').default);
Vue.component('usuario-component', require('./components/UsuarioComponent.vue').default);
Vue.component('ingreso-component', require('./components/IngresoComponent.vue').default);
Vue.component('venta-component', require('./components/VentaComponent.vue').default);
Vue.component('dashboard-component', require('./components/DashboardComponent.vue').default);
Vue.component('reporte_ingreso-component', require('./components/ReporteIngresoComponent.vue').default);
Vue.component('reporte_venta-component', require('./components/ReporteVentaComponent.vue').default);
Vue.component('notificacion-component', require('./components/NotificacionComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data : {
        menu:  0, //Valor defecto a cargar
        notificaciones : []
    },
    created(){
        let me = this;
        axios.post('/notification/get').then(function(response){
            me.notificaciones = response.data
        }).catch(function (error) {  
            console.log(error)
        })

        var userId = $('meta[name="userId"]').attr('content');
        
        Echo.private('App.Models.User.'+userId)
            .notification((notification) => {
                // me.notificaciones.unshift(notification);
                console.log(notification.type)
        });
    }   
});
