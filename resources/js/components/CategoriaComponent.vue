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
          <i class="fa fa-align-justify"></i> Categorías
          <button type="button" class="btn btn-secondary"  @click="AbrirModal('categoria', 'registrar')">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <button type="button" class="btn btn-info"  @click="CargarPDF()">
            <i class="icon-doc"></i>&nbsp;Reporte
          </button>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-3" v-model="criterio">
                  <option value="nombre">Nombre</option>
                  <option value="descripcion">Descripción</option>
                </select>
                <input type="text" class="form-control" @keyup.enter="ListarCategorias(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarCategorias(1, buscar, criterio)" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="datos in arrayCategorias" :key="datos.id">
                <td>
                  <button
                    type="button"
                    class="btn btn-warning btn-sm"
                    @click="AbrirModal('categoria', 'actualizar', datos)"
                  >
                    <i class="icon-pencil"></i>
                  </button>
                  &nbsp;
                  <template v-if="datos.condicion==1">
                    <button type="button" @click="DesactivarCategoria(datos.id)" class="btn btn-danger btn-sm">
                      <i class="icon-trash"></i>
                    </button>
                  </template>
                  <template v-else>
                    <button type="button" @click="ActivarCategoria(datos.id)" class="btn btn-success btn-sm">
                      <i class="icon-check"></i>
                    </button>
                  </template>
                </td>
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
    <!--Inicio del modal agregar/actualizar-->
    <div
      class="modal fade"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none"
      aria-hidden="true"
      :class="{'mostrar' : modal}"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="tituloModal"></h4>
            <button
              type="button"
              class="close"
              aria-label="Close"
              @click="CerrarModal()"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <form
              action=""
              method="post"
              enctype="multipart/form-data"
              class="form-horizontal"
            >
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input"
                  >Nombre</label
                >
                <div class="col-md-9">
                  <input
                    type="text"
                    v-model="nombre"
                    class="form-control"
                    placeholder="Nombre de categoría"
                  />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input"
                  >Descripción</label
                >
                <div class="col-md-9">
                  <input
                    type="text"
                    v-model="descripcion"
                    class="form-control"
                    placeholder="Ingrese la descripcion"
                  />
                </div>
                <div v-show="errorCategoria" class="form-group row div-error">
                  <div class="text-center text-error">
                    <div v-for="error in errorMostrarMsjCategoria" :key="error" v-text="error"></div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="CerrarModal()"
            >
              Cerrar
            </button>
            <button type="button" v-if="tipoAccion==1" @click="RegistrarCategoria()" class="btn btn-primary">Guardar</button>
            <button type="button" v-if="tipoAccion==2" @click="ActualizarCategoria()" class="btn btn-primary">Actualizar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
  </main>
</template>

<script>
export default {
  data(){
    return {
      categoria_id : 0,
      nombre: '',
      descripcion: '',
      arrayCategorias: [],
      modal : 0,
      tituloModal : '',
      tipoAccion : '',
      errorCategoria : 0,
      errorMostrarMsjCategoria : [],
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
    ListarCategorias(page, buscar, criterio) {
      let me = this;
      var url = '/categorias?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayCategorias = respuesta.categorias.data;
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

    CargarPDF(){
      window.open('http://localhost:8000/categorias/listarPDF', 'blank');
    },

    CambiarPagina(page, buscar, criterio){
      let me = this;
      me.pagination.current_page = page;
      me.ListarCategorias(page, buscar, criterio);
    },

    DesactivarCategoria(id){
    Swal.fire({
      title: '¿Esta seguro de desactivar esta categoria?',
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, desactivalo!'
    }).then((result) => {
      if (result.isConfirmed) { 
      let me = this;
      axios.put('/categorias/desactivar',{
        'id' : id
      }).then(function (response) {
          me.ListarCategorias(1, '', 'nombre');
          /* Swal.fire(
            'Desactivado',
            'Se desactivo con exito.',
            'success'
          ) */
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
      }
    })
    },

    ActivarCategoria(id){
    Swal.fire({
      title: '¿Esta seguro de activar esta categoria?',
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, activalá!'
    }).then((result) => {
      if (result.isConfirmed) { 
      let me = this;
      axios.put('/categorias/activar',{
        'id' : id
      }).then(function (response) {
          me.ListarCategorias(1, '', 'nombre');
          /* Swal.fire(
            'Activado.',
            'Se activo con exito.',
            'success'
          ) */
          })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
      }
    })
    },

    ValidarCategoria(){
      this.errorCategoria = 0;
      this.errorMostrarMsjCategoria = [];

      if(this.nombre.trim() == "") {
        this.errorMostrarMsjCategoria.push("El nombre de la categoria no puede quedar vacio");
      }

      if(this.errorMostrarMsjCategoria.length > 0){
        this.errorCategoria = 1;
      }

      return this.errorCategoria;
    },
    ActualizarCategoria(){
      if (this.ValidarCategoria() == 1) {
        return;
      }
      let me = this;
      axios.put('/categorias/actualizar',{
        'id' : this.categoria_id,
        'nombre' : this.nombre,
        'descripcion' : this.descripcion
      }).then(function (response) {
          me.CerrarModal();
          me.ListarCategorias(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    RegistrarCategoria(){
      if(this.ValidarCategoria() == 1){
        return;
      }
      let me = this;
      
      axios.post('/categorias/registrar',{
        'nombre' : this.nombre,
        'descripcion' : this.descripcion
      }).then(function (response) {
          me.CerrarModal();
          me.ListarCategorias(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    CerrarModal(){
      this.errorCategoria = 0;
      this.errorMostrarMsjCategoria = [];
      this.modal = 0;   
    },

    AbrirModal(modelo, accion, data){
      switch (modelo) {
        case "categoria":
          {
            switch (accion) {
              case "registrar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Registrar categoria';
                  this.nombre = '';
                  this.descripcion = '';
                  this.tipoAccion = 1;
                  /* this.errorCategoria = 0;
                  this.errorMostrarMsjCategoria = []; */
                  break;
                }
              case "actualizar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Actualizar categoria';
                  this.categoria_id = data['id']
                  this.nombre = data['nombre'];
                  this.descripcion = data['descripcion'];
                  this.tipoAccion = 2;
                  break;
                  // console.log(data)
                }
            }
          }      
      }
    },
  },

  mounted() {
    this.ListarCategorias(1, this.buscar, this.criterio);
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