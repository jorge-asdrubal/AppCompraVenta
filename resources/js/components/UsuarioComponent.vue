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
          <i class="fa fa-align-justify"></i> Usuarios
          <button
            type="button"
            class="btn btn-secondary"
            @click="AbrirModal('usuarios', 'registrar')"
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
                <input type="text" class="form-control" @keyup.enter="ListarUsuarios(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarUsuarios(1, buscar, criterio)" class="btn btn-primary">
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
                <th>Email recuperacion</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Condicion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="datos in arrayUsuarios" :key="datos.id">
                <td>
                  <button
                    type="button"
                    class="btn btn-warning btn-sm"
                    @click="AbrirModal('usuarios', 'actualizar', datos)"
                  >
                    <i class="icon-pencil"></i>
                  </button>
                  &nbsp;
                  <template v-if="datos.condicion==1">
                    <button type="button" @click="DesactivarUsuario(datos.id)" class="btn btn-danger btn-sm">
                      <i class="icon-trash"></i>
                    </button>
                  </template>
                  <template v-else>
                    <button type="button" @click="ActivarUsuario(datos.id)" class="btn btn-success btn-sm">
                      <i class="icon-check"></i>
                    </button>
                  </template>
                </td>
                <td v-text="datos.nombre"></td>
                <td v-text="datos.tipo_documento"></td>
                <td v-text="datos.num_documento"></td>
                <td v-text="datos.direccion"></td>
                <td v-text="datos.telefono"></td>
                <td v-text="datos.email_recuperacion"></td>
                <td v-text="datos.usuario"></td>
                <td v-text="datos.rol"></td>
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
            <form action="" method="post" class="form-horizontal">
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-input">Nombre (*)</label>
                <div class="col-md-9">
                  <input type="text" v-model="nombre" class="form-control" placeholder="Nombre persona"/>
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
                <label class="col-md-3 form-control-label" for="text-email">Usuario (*)</label>
                <div class="col-md-9">
                  <input type="text" v-model="usuario" class="form-control" placeholder="Usuario"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-email">Password (*)</label>
                <div class="col-md-9">
                  <input type="password" v-model="password" class="form-control" placeholder="Password"/>
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
                <label class="col-md-3 form-control-label" for="text-email">Email de recuperacion</label>
                <div class="col-md-9">
                  <input type="email" v-model="email_recuperacion" class="form-control" placeholder="Correo de recuperacion"/>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 form-control-label" for="text-email">Rol</label>
                <div class="col-md-9">
                  <select class="form-control" v-model="id_rol">
                    <option value="0">Seleccione un rol</option>
                    <option v-for="roles in arrayRoles" :key="roles.id" :value="roles.id" v-text="roles.nombre"></option>
                  </select>
                </div>
              </div>
                <div v-show="errorUsuario" class="form-group row div-error">
                  <div class="text-center text-error">
                    <div v-for="error in errorMostrarMsjUsuario" :key="error" v-text="error"></div>
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
            <button type="button" v-if="tipoAccion==1" @click="RegistrarUsuario()" class="btn btn-primary">Guardar</button>
            <button type="button" v-if="tipoAccion==2" @click="ActualizarUsuario()" class="btn btn-primary">Actualizar</button>
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
      email_recuperacion: '',
      usuario: '',
      password: '',
      id_rol: 0,
      arrayUsuarios: [],
      arrayRoles: [],
      modal : 0,
      tituloModal : '',
      tipoAccion : '',
      errorUsuario : 0,
      errorMostrarMsjUsuario : [],
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
    ListarUsuarios(page, buscar, criterio) {
      let me = this;
      var url = '/usuarios?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayUsuarios = respuesta.users.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });
    },

    SelectRol(){
      let me = this;

      axios.get('/roles/selectRol').then(function (response) {
          me.arrayRoles = response.data.roles;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });
    },

    CambiarPagina(page, buscar, criterio){
      let me = this;
      me.pagination.current_page = page;
      me.ListarUsuarios(page, buscar, criterio);
    },

    ValidarUsuario(){
      this.errorUsuario = 0;
      this.errorMostrarMsjUsuario = [];

      if(!this.nombre) {
        this.errorMostrarMsjUsuario.push("El nombre de la persona no puede quedar vacio");
      }else
      if(this.id_rol == 0) {
        this.errorMostrarMsjUsuario.push("Debe seleccionar un rol");
      }else
      if(!this.usuario) {
        this.errorMostrarMsjUsuario.push("El usuario no puede quedar vacio");
      }else
      if(!this.password) {
        this.errorMostrarMsjUsuario.push("Debe ingresar una contraseña");
      }else
      if(!this.email_recuperacion) {
        this.errorMostrarMsjUsuario.push("Debe ingresar un correo de recuperacion");
      }

      if(this.errorMostrarMsjUsuario.length > 0){
        this.errorUsuario = 1;
      }

      return this.errorUsuario;
    },

    ActualizarUsuario(){
      if (this.ValidarUsuario() == 1) {
        return;
      }
      let me = this;
      axios.put('/usuarios/actualizar',{
        'nombre' : this.nombre,
        'tipo_documento' : this.tipo_documento,
        'num_documento' : this.num_documento,
        'direccion' : this.direccion,
        'telefono' : this.telefono,
        'email' : this.email_recuperacion,
        'usuario' : this.usuario,
        'password' : this.password,
        'idRol' : this.id_rol,
        'email_recuperacion' : this.email_recuperacion,
        'id' : this.persona_id
      }).then(function (response) {
          me.CerrarModal();
          me.ListarUsuarios(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    RegistrarUsuario(){
      if(this.ValidarUsuario() == 1){
        return;
      }
      let me = this;
      
      axios.post('/usuarios/registrar',{
        'nombre' : this.nombre,
        'tipo_documento' : this.tipo_documento,
        'num_documento' : this.num_documento,
        'direccion' : this.direccion,
        'telefono' : this.telefono,
        'email' : this.email_recuperacion,
        'usuario' : this.usuario,
        'password' : this.password,
        'idRol' : this.id_rol,
        'email_recuperacion' : this.email_recuperacion
      }).then(function (response) {
          me.CerrarModal();
          me.ListarUsuarios(1, '', 'nombre');
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    CerrarModal(){
      this.errorUsuario = 0;
      this.errorMostrarMsjUsuario = [];
      this.modal = 0;   
    },

    AbrirModal(modelo, accion, data){
      switch (modelo) {
        case "usuarios":
          {
            switch (accion) {
              case "registrar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Registrar usuario';
                  this.nombre = '',
                  this.tipo_documento = 'CC',
                  this.num_documento = '',
                  this.direccion = '',
                  this.telefono = '',
                  this.email = '',
                  this.email_recuperacion = '',
                  this.usuario = '',
                  this.password = '',
                  this.id_rol = 0,
                  this.tipoAccion = 1;
                  /* this.errorCategoria = 0;
                  this.errorMostrarMsjCategoria = []; */
                  break;
                }
              case "actualizar":
                {
                  this.modal = 1;
                  this.tituloModal = 'Actualizar usuario';
                  this.persona_id = data['id']
                  this.nombre = data['nombre'],
                  this.tipo_documento = data['tipo_documento'],
                  this.num_documento = data['num_documento'],
                  this.direccion = data['direccion'],
                  this.telefono = data['telefono'],
                  this.email = data['email'],
                  this.email_recuperacion = data['email_recuperacion'],
                  this.usuario = data['usuario'],
                  this.password = data['password'],
                  this.id_rol = data['idRol'],
                  this.tipoAccion = 2
                  break;
                  // console.log(data)
                }
            }
          }      
      }
      this.SelectRol();
    },

    ActivarUsuario(id){
      Swal.fire({
        title: 'Esta seguro de habilitar este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, habilitar!'
      }).then((result) => {
        if (result.isConfirmed) {
          let me = this;
          axios.put('/usuarios/activar',{
            'id' : id
          }).then(function (response) {
              me.ListarUsuarios(1, '', 'nombre');
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

    DesactivarUsuario(id){
      Swal.fire({
        title: 'Esta seguro de deshabilitar este cliente?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, deshabilitar!'
      }).then((result) => {
        if (result.isConfirmed) {
          let me = this;
          axios.put('/usuarios/desactivar',{
            'id' : id
          }).then(function (response) {
              me.ListarUsuarios(1, '', 'nombre');
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
  },

  mounted() {
    this.ListarUsuarios(1, this.buscar, this.criterio);
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
    margin: 1% 0 0 1% !important;
  }
  .text-error{  
    color: red !important;
    font-weight: bold !important;
  }
</style>