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
          <i class="fa fa-align-justify"></i> Articulos
          <button type="button" class="btn btn-secondary" @click="AbrirModal('articulo', 'registrar')">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <button type="button" class="btn btn-info" @click="CargarPDF()">
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
                <input type="text" class="form-control" @keyup.enter="ListarArticulos(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarArticulos(1, buscar, criterio)" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Opciones</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoria</th>
                <th>Precio venta</th>
                <th>Stock</th>
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="datos in arrayArticulos" :key="datos.id">
                <td>
                  <button
                    type="button"
                    class="btn btn-warning btn-sm"
                    @click="AbrirModal('articulo', 'actualizar', datos)"
                  >
                    <i class="icon-pencil"></i>
                  </button>
                  &nbsp;
                  <template v-if="datos.condicion==1">
                    <button type="button" @click="DesactivarArticulo(datos.id)" class="btn btn-danger btn-sm">
                      <i class="icon-trash"></i>
                    </button>
                  </template>
                  <template v-else>
                    <button type="button" @click="ActivarArticulo(datos.id)" class="btn btn-success btn-sm">
                      <i class="icon-check"></i>
                    </button>
                  </template>
                </td>
                <td v-text="datos.codigo"></td>
                <td v-text="datos.nombre"></td>
                <td v-text="datos.nombre_categoria"></td>
                <td v-text="datos.precio_venta"></td>
                <td v-text="datos.stock"></td>
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
    <div class="modal fade" :class="{'mostrar' : modal}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none" aria-hidden="true">
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="tituloModal"></h4>
            <button type="button" class="close" aria-label="Close" @click="CerrarModal()">
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
                  >Categoria</label
                >
                <div class="col-md-9">
                  <select class="form-control" v-model="idcategoria">
                      <option value="0" disabled>Seleccione</option>
                      <option v-for="categorias in arrayCategorias" :key="categorias.id" :value="categorias.id" v-text="categorias.nombre"></option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Codigo</label>
                <div class="col-md-9">
                  <input type="text" v-model="codigo" class="form-control" placeholder="Codigo de barras"/>
                  <barcode :value="codigo" :options="{ format: 'EAN-13'}">
                    Generando codigo de barras
                  </barcode>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                <div class="col-md-9">
                  <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de articulo"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Precio de venta</label>
                <div class="col-md-9">
                  <input type="number" v-model="precio_venta" class="form-control" placeholder="Precio de venta"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Stock</label>
                <div class="col-md-9">
                  <input type="number" v-model="stock" class="form-control" placeholder="Stock"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Descripción</label>
                <div class="col-md-9">
                  <input type="text" v-model="descripcion" class="form-control" placeholder="Ingrese la descripcion"/>
                </div>
                <div v-show="errorArticulo" class="form-group row div-error">
                  <div class="text-center text-error">
                    <div v-for="error in errorMostrarMsjArticulo" :key="error" v-text="error"></div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="CerrarModal()">Cerrar</button>
            <button type="button" v-if="tipoAccion==1" @click="RegistrarArticulo()" class="btn btn-primary">Guardar</button>
            <button type="button" v-if="tipoAccion==2" @click="ActualizarArticulo()" class="btn btn-primary">Actualizar</button>
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
import VueBarcode from 'vue-barcode';
export default {
  data(){
    return {
      articulo_id : 0,
      idcategoria : 0,
      nombre_categoria: '',
      nombre: '',
      codigo: '',
      precio_venta: 0,
      stock: 0,
      descripcion: '',
      arrayArticulos: [],
      modal : 0,
      tituloModal : '',
      tipoAccion : '',
      errorArticulo : 0,
      errorMostrarMsjArticulo : [],
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
      buscar : '',
      arrayCategorias: []
    }
  },

  components: {
    'barcode': VueBarcode
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
    ListarArticulos(page, buscar, criterio) {
      let me = this;
      var url = '/articulos?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayArticulos = respuesta.articulos.data;
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

    SelectCategoria(){
        let me = this;

        axios.get('/categorias/selectCategoria').then(function (response) {
            me.arrayCategorias = response.data.categorias;
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
      me.ListarArticulos(page, buscar, criterio);
    },

    DesactivarArticulo(id){
    Swal.fire({
      title: '¿Esta seguro de desactivar este articulo?',
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, desactivalo!'
    }).then((result) => {
      if (result.isConfirmed) { 
      let me = this;
      axios.put('/articulos/desactivar',{
        'id' : id
      }).then(function (response) {
          me.ListarArticulos(1, '', 'nombre');
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

    CargarPDF(){
      window.open('http://localhost:8000/articulos/listarPDF', 'blank');
    },

    ActivarArticulo(id){
    Swal.fire({
      title: '¿Esta seguro de activar este articulo?',
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, activalá!'
    }).then((result) => {
      if (result.isConfirmed) { 
      let me = this;
      axios.put('/articulos/activar',{
        'id' : id
      }).then(function (response) {
          me.ListarArticulos(1, '', 'nombre');
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

    ValidarArticulo(){
      this.errorArticulo = 0;
      this.errorMostrarMsjArticulo = [];

      if(this.idcategoria==0) this.errorMostrarMsjArticulo.push("Seleccione una categoria para el articulo")
      if(!this.nombre)  this.errorMostrarMsjArticulo.push("El nombre del articulo no puede quedar vacio");
      if(!this.stock)  this.errorMostrarMsjArticulo.push("El stock del articulo debe ser un numero y no puede quedar vacio");
      if(!this.precio_venta)  this.errorMostrarMsjArticulo.push("El precio del articulo debe ser un numero y no puede quedar vacio");

      if(this.errorMostrarMsjArticulo.length > 0) this.errorArticulo = 1;

      return this.errorArticulo;
    },

    ActualizarArticulo(){
      if (this.ValidarArticulo() == 1) {
        return;
      }
      let me = this;
      axios.put('/articulos/actualizar',{
        'id' : this.articulo_id,
        'idcategoria' : this.idcategoria,
        'codigo' : this.codigo,
        'nombre' : this.nombre,
        'stock' : this.stock,
        'precio_venta' : this.precio_venta,
        'descripcion' : this.descripcion
      }).then(function (response) {
          me.CerrarModal();
          me.ListarArticulos(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    RegistrarArticulo(){
      if(this.ValidarArticulo() == 1){
        return;
      }
      let me = this;
      
      axios.post('/articulos/registrar',{
        'idcategoria' : this.idcategoria,
        'codigo' : this.codigo,
        'nombre' : this.nombre,
        'stock' : this.stock,
        'precio_venta' : this.precio_venta,
        'descripcion' : this.descripcion
      }).then(function (response) {
          me.CerrarModal();
          me.ListarArticulos(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    CerrarModal(){
      this.errorArticulo = 0;
      this.errorMostrarMsjArticulo = [];
      this.modal = 0;
      this.idcategoria = 0;
      this.codigo = '';
      this.nombre = '';
      this.precio_venta = 0;
      this.stock = 0;
      this.descripcion = '';
    },

    AbrirModal(modelo, accion, data){
      switch (modelo) {
        case "articulo":
          {
            switch (accion) {
              case "registrar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Registrar articulo';
                  this.idcategoria = 0;
                  this.nombre_categoria = '';
                  this.codigo = '';
                  this.nombre = '';
                  this.precio_venta = 0;
                  this.stock = 0;
                  this.descripcion = '';
                  this.tipoAccion = 1;
                  break;
                }
              case "actualizar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Actualizar articulos';
                  this.articulo_id = data['id'];
                  this.idcategoria = data['idcategoria'];
                  this.codigo = data['codigo'];
                  this.nombre = data['nombre'];
                  this.stock = data['stock'];
                  this.precio_venta = data['precio_venta'];
                  this.descripcion = data['descripcion'];
                  this.tipoAccion = 2;
                  break;
                  // console.log(data)
                }
            }
          }      
      }
      this.SelectCategoria();
    },
  },

  mounted() {
    this.ListarArticulos(1, this.buscar, this.criterio);
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
    top: -45px !important;
  }
  .div-error{
    display: flex !important;
    justify-content: center !important;
    margin: 1% 0 0 28% !important;
  }
  .text-error{  
    color: red !important;
    font-weight: bold !important;
  }
</style>