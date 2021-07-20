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
          <i class="fa fa-align-justify"></i> Proveedores
          <button
            type="button"
            class="btn btn-secondary"
            @click="AbrirModal('proveedor', 'registrar')"
          >
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-3" v-model="criterio">
                  <option value="nombre">Nombre</option>
                  <option value="num_documento">Documento</option>
                  <option value="email">Email</option>
                  <option value="telefono">Telefono</option>
                </select>
                <input type="text" class="form-control" @keyup.enter="ListarProveedores(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarProveedores(1, buscar, criterio)" class="btn btn-primary">
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
                <th>Tipo documento</th>
                <th>Documento</th>
                <th>Dirección</th>
                <th>Telefono</th>
                <th>Email</th>
                <th>Contacto</th>
                <th>Telefono contacto</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="datos in arrayProveedores" :key="datos.id">
                <td>
                  <button
                    type="button"
                    class="btn btn-warning btn-sm"
                    @click="AbrirModal('proveedor', 'actualizar', datos)"
                  >
                    <i class="icon-pencil"></i>
                  </button>
                  &nbsp;
                </td>
                <td v-text="datos.nombre"></td>
                <td v-text="datos.tipo_documento"></td>
                <td v-text="datos.num_documento"></td>
                <td v-text="datos.direccion"></td>
                <td v-text="datos.telefono"></td>
                <td v-text="datos.email"></td>
                <td v-text="datos.contacto"></td>
                <td v-text="datos.telefono_contacto"></td>
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
            <form action="" method="post" class="form-horizontal">
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Nombre (*)</label>
                <div class="col-md-9">
                  <input type="text" v-model="nombre" class="form-control" placeholder="Nombre proveedor"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Tipo documento</label>
                <div class="col-md-9">
                  <select v-model="tipo_documento" class="form-control">
                    <option value="CC">Cedula Ciudadania</option>
                    <option value="CE">Cedula Extranjera</option>
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                    <option value="PASS">Pasaporte</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Numero documento</label>
                <div class="col-md-9">
                  <input type="text" v-model="num_documento" class="form-control" placeholder="Numero documento"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Direccion</label>
                <div class="col-md-9">
                  <input type="text" v-model="direccion" class="form-control" placeholder="Direccion"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Telefono</label>
                <div class="col-md-9">
                  <input type="text" v-model="telefono" class="form-control" placeholder="Telefono"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-email">Email</label>
                <div class="col-md-9">
                  <input type="email" v-model="email" class="form-control" placeholder="Correo electronico"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-email">Contacto</label>
                <div class="col-md-9">
                  <input type="text" v-model="contacto" class="form-control" placeholder="Contacto"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-email">Telefono de contacto</label>
                <div class="col-md-9">
                  <input type="number" v-model="telefono_contacto" class="form-control" placeholder="Telefono de contacto"/>
                </div>
              </div>
                <div v-show="errorProveedor" class="form-group row div-error">
                  <div class="text-center text-error">
                    <div v-for="error in errorMostrarMsjProveedor" :key="error" v-text="error"></div>
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
            <button type="button" v-if="tipoAccion==1" @click="RegistrarProveedor()" class="btn btn-primary">Guardar</button>
            <button type="button" v-if="tipoAccion==2" @click="ActualizarProveedor()" class="btn btn-primary">Actualizar</button>
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
      persona_id : 0,
      nombre: '',
      tipo_documento: 'CC',
      num_documento: '',
      direccion: '',
      telefono: '',
      email: '',
      contacto: '',
      telefono_contacto: '',
      arrayProveedores: [],
      modal : 0,
      tituloModal : '',
      tipoAccion : '',
      errorProveedor : 0,
      errorMostrarMsjProveedor : [],
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
    ListarProveedores(page, buscar, criterio) {
      let me = this;
      var url = '/proveedores?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayProveedores = respuesta.proveedores.data;
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
      me.ListarProveedores(page, buscar, criterio);
    },

    ValidarProveedor(){
      this.errorProveedor = 0;
      this.errorMostrarMsjProveedor = [];

      if(!this.nombre) {
        this.errorMostrarMsjProveedor.push("El nombre del proveedor no puede quedar vacio");
      }

      if(this.errorMostrarMsjProveedor.length > 0){
        this.errorProveedor = 1;
      }

      return this.errorProveedor;
    },

    ActualizarProveedor(){
      if (this.ValidarProveedor() == 1) {
        return;
      }
      let me = this;
      axios.put('/proveedores/actualizar',{
        'id' : this.persona_id,
        'nombre' : this.nombre,
        'tipo_documento' : this.tipo_documento,
        'num_documento' : this.num_documento,
        'direccion' : this.direccion,
        'telefono' : this.telefono,
        'email' : this.email,
        'contacto' : this.contacto,
        'telefono_contacto' : this.telefono_contacto,
      }).then(function (response) {
          me.CerrarModal();
          me.ListarProveedores(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    RegistrarProveedor(){
      if(this.ValidarProveedor() == 1){
        return;
      }
      let me = this;
      
      axios.post('/proveedores/registrar',{
        'nombre' : this.nombre,
        'tipo_documento' : this.tipo_documento,
        'num_documento' : this.num_documento,
        'direccion' : this.direccion,
        'telefono' : this.telefono,
        'email' : this.email,
        'contacto' : this.contacto,
        'telefono_contacto' : this.telefono_contacto,
      }).then(function (response) {
          me.CerrarModal();
          me.ListarProveedores(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    CerrarModal(){
      this.errorProveedor = 0;
      this.errorMostrarMsjProveedor = [];
      this.modal = 0;   
    },

    AbrirModal(modelo, accion, data){
      switch (modelo) {
        case "proveedor":
          {
            switch (accion) {
              case "registrar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Registrar proveedor';
                  this.nombre = '',
                  this.tipo_documento = 'CC',
                  this.num_documento = '',
                  this.direccion = '',
                  this.telefono = '',
                  this.email = '',
                  this.email = '',
                  this.contacto = '',
                  this.telefono_contacto = '',
                  this.tipoAccion = 1;
                  /* this.errorCategoria = 0;
                  this.errorMostrarMsjCategoria = []; */
                  break;
                }
              case "actualizar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Actualizar proveedor';
                  this.persona_id = data['id']
                  this.nombre = data['nombre'],
                  this.tipo_documento = data['tipo_documento'],
                  this.num_documento = data['num_documento'],
                  this.direccion = data['direccion'],
                  this.telefono = data['telefono'],
                  this.email = data['email'],
                  this.contacto = data['contacto'],
                  this.telefono_contacto = data['telefono_contacto'],
                  this.tipoAccion = 2
                  break;
                  // console.log(data)
                }
            }
          }      
      }
    },
  },

  mounted() {
    this.ListarProveedores(1, this.buscar, this.criterio);
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
    margin: 1% 0 0 1% !important;
  }
  .text-error{  
    color: red !important;
    font-weight: bold !important;
  }
</style>