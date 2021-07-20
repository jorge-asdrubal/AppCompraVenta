<template>
    <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="form-group row">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th class="text-center" colspan="2"><strong>Notificaciones {{notifications.lenght}}</strong></th>
                        </tr>
                    </thead>
                    <div v-if="notifications.lenght > 0">
                        <tbody  v-for="item in notifications" :key="item.id">
                            <tr>
                                <td>
                                    <a class="dropdown-item" href="#">
                                        <i class="fa fa-envelope-o"></i> {{(item.data).datos.ingresos.msj}}
                                        <span class="badge badge-success">{{(item.data).datos.ingresos.numero}}</span>
                                    </a>
                                </td>
                                <td>
                                    <a class="dropdown-item" href="#">
                                        <i class="fa fa-tasks"></i> {{(item.data).datos.ventas.msj}}
                                        <span class="badge badge-danger">{{(item.data).datos.ventas.numero}}</span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </div>
                    <div v-else>
                        <a><span>No existen notificaciones</span></a>
                    </div>
                </table>
            </div>
        </div>
      </div>
    </div>
  </main>
</template>
<script>
export default {
    props : ['notifications'],
    data(){
        return {

        }
    }
    
}
</script>