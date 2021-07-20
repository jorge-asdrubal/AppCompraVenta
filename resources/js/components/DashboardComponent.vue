<template>
    <main class="main">
        <!-- <ol class="breadcump">
            <li class="breadcump-item"><a href="/">Escritorio</a></li>
        </ol> -->
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Ingresos</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ingresos"></canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Compras de los ultimos meses.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-chart">
                                <div class="card-header">
                                    <h4>Ventas</h4>
                                </div>
                                <div class="card-content">
                                    <div class="ct-chart">
                                        <canvas id="ventas"></canvas>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p>Ventas de los ultimos meses.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
export default {
    data(){
        return {
            varIngreso : null,
            charIngreso : null,
            ingresos : [],
            varTotalIngreso : [],
            varMesIngreso : [],

            varVenta : null,
            charVenta : null,
            ventas : [],
            varTotalVenta : [],
            varMesVenta : []
        }
    },
    
    methods : {
        getIngresos(){
            let me = this;
            axios.get('/dashboard').then(function(response){
                me.ingresos = response.data.ingresos

                //Cargamos los datos del char
                me.loadIngresos()
            }).catch(function(error){
                console.log(error)
            })
        },

        loadIngresos(){
            let me = this
            me.ingresos.map(function(x){
                me.varMesIngreso.push(x.mes)
                me.varTotalIngreso.push(x.total)
            })
            me.varIngreso = document.getElementById('ingresos').getContext('2d')
            me.charIngreso = new Chart(me.varIngreso, {
            type: 'bar',
            data: {
                labels: me.varMesIngreso,
                datasets: [{
                    label: 'Ingresos',
                    data: me.varTotalIngreso,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        },

        getVentas(){
            let me = this;
            axios.get('/dashboard').then(function(response){
                me.ventas = response.data.ventas

                //Cargamos los datos del char
                me.loadVentas()
            }).catch(function(error){
                console.log(error)
            })
        },

        loadVentas(){
            let me = this
            me.ventas.map(function(x){
                me.varMesVenta.push(x.mes)
                me.varTotalVenta.push(x.total)
            })
            me.varVenta = document.getElementById('ventas').getContext('2d')
            me.charVenta = new Chart(me.varVenta, {
            type: 'bar',
            data: {
                labels: me.varMesVenta,
                datasets: [{
                    label: 'Ventas',
                    data: me.varTotalVenta,
                    backgroundColor: 'rgba(54, 162, 25, 0.2)',
                    borderColor: 'rgba(54, 162, 25, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        },

        montar(){
            let me = this;
            me.getIngresos();
            me.getVentas();
        }
    },

    mounted(){
        this.montar();
    }
}
</script>