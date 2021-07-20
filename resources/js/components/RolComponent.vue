<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>
    <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Roles
        </div>
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-3" v-model="criterio">
                  <option value="nombre">Nombre</option>
                  <option value="descripcion">Descripción</option>
                </select>
                <input type="text" class="form-control" @keyup.enter="ListarRoles(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarRoles(1, buscar, criterio)" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="datos in arrayRoles" :key="datos.id">
                <td v-text="datos.nombre"></td>
                <td v-text="datos.descripcion"></td>
                <td>
                  <div v-if="datos.condicion==1">
                    <span class="badge badge-success">Activado</span>
                  </div>
                  <div v-else>
                    <span class="badge badge-danger">Desactivado</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <nav>
            <ul class="pagination">
              <li class="page-item" v-if="pagination.current_page > 1">
                <a class="page-link" href="#" @click.prevent="CambiarPagina(pagination.current_page - 1, buscar, criterio)">Ant</a>
              </li>
              <li class="page-item" v-for="pages in pagesNumber" :key="pages" :class="[pages == isActived ? 'active' : '']">
                <a class="page-link" href="#" @click="CambiarPagina(pages, buscar, criterio)" v-text="pages"></a>
              <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                <a class="page-link" href="#" @click.prevent="CambiarPagina(pagination.current_page + 1, buscar, criterio)">Sig</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
  </main>
</template>

<script>
export default {
  data(){
    return {
      rol_id : 0,
      nombre: '',
      descripcion: '',
      arrayRoles: [],
      pagination : {
        'total' : 0,
        'current_page' : 0,
        'per_page' : 0,
        'last_page' : 0,
        'from' : 0,
        'to' : 0,
      },
      offset : 3,
      criterio : 'nombre',
      buscar : ''
    }
  },

  computed:{
    isActived: function(){
      return this.pagination.current_page;
    },

    pagesNumber: function(){
      if(!this.pagination.to){
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if(from < 1){
        from = 1;
      }

      var to = from + (this.offset * 2);
      if(to >= this.pagination.last_page){
          to = this.pagination.last_page;
      }

      var pagesArray = [];
      while(from <= to){
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    }
  },

  methods: {
    ListarRoles(page, buscar, criterio) {
      let me = this;
      var url = '/roles?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayRoles = respuesta.roles.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function (error) {
          // handle error
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    CambiarPagina(page, buscar, criterio){
      let me = this;
      me.pagination.current_page = page;
      me.ListarRoles(page, buscar, criterio);
    },
  },

  mounted() {
    this.ListarRoles(1, this.buscar, this.criterio);
    // console.log("Component mounted.");
  },
};
</script>
<style>
  .mostrar{
    display: list-item !important;
    opacity: 1 !important;
    position: absolute !important;
    background-color: #3c29297a !important;
  }

  .modal-content{
    width: 100% !important;
    position: absolute !important;
  }
  .div-error{
    display: flex !important;
    justify-content: center !important;
    /* margin-left: 28% !important; */
    margin: 1% 0 0 28% !important;
  }
  .text-error{  
    color: red !important;
    font-weight: bold !important;
  }
</style>