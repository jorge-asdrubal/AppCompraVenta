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
          <i class="fa fa-align-justify"></i> Ingresos
          <button
            type="button"
            class="btn btn-secondary"
            @click="MostrarDetalle()" 
          >
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
        </div>

        <!-- Listado de ingresos -->
        <template v-if="listado == 1">
        <div class="card-body">
          <div class="form-group row">
            <div class="col-md-6">
              <div class="input-group">
                <select class="form-control col-md-4" v-model="criterio">
                  <option value="tipo_comprobante">Tipo comprobante</option>
                  <option value="num_comprobante">Numero comprobante</option>
                  <option value="fecha_hora">Fecha-hora</option>
                </select>
                <input type="text" class="form-control" @keyup.enter="ListarIngresos(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarIngresos(1, buscar, criterio)" class="btn btn-primary">
                  <i class="fa fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
              <thead>
                <tr>
                  <th>Opciones</th>
                  <th>Usuario</th>
                  <th>Proveedor</th>
                  <th>Tipo comprobante</th>
                  <th>Serie comprobante</th>
                  <th>Numero comprobante</th>
                  <th>Fecha hora</th>
                  <th>Total</th>
                  <th>Impuesto</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="datos in arrayIngresos" :key="datos.id">
                  <td>
                    <button
                      type="button"
                      class="btn btn-success btn-sm"
                      @click="VerIngreso(datos.id)"
                    >
                      <i class="icon-eye"></i>
                    </button>
                    &nbsp;
                    <template v-if="datos.estado=='Registrado'">
                      <button type="button" @click="DesactivarIngreso(datos.id)" class="btn btn-danger btn-sm">
                        <i class="icon-trash"></i>
                      </button>
                    </template>
                  </td>
                  <td v-text="datos.usuario"></td>
                  <td v-text="datos.nombre"></td>
                  <td v-text="datos.tipo_comprobante"></td>
                  <td v-text="datos.serie_comprobante"></td>
                  <td v-text="datos.num_comprobante"></td>
                  <td v-text="datos.fecha_hora"></td>
                  <td>$ <span v-text="datos.total"></span></td>
                  <td v-text="datos.impuesto"></td>
                  <td v-text="datos.estado"></td>
                </tr>
              </tbody>
            </table>
          </div>
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
        </template>
        <!-- Fin listado -->
        
        <!-- Registro del detalle -->
        <template v-else-if="listado==0">
        <div class="card-body">
          <div class="form-group row border">
            <div class="col-md-9">
              <div class="form-group">
                <label for="">Proveedor (*)</label>
                <v-select @search="SelectProveedor" label="nombre" :options="arrayProveedores"
                placeholder="Buscar proveedores" @input="getDatosProveedor">
                </v-select>
                <!-- <select class="form-control">
                  <option value="0">Seleccione</option>
                </select> -->
              </div>
            </div>
            <div class="col-md-3">
              <label for="">Impuesto (*)</label>
              <input type="text" class="form-control" v-model="impuesto">
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Tipo comprobante (*)</label>
                <select class="form-control" v-model="tipo_comprobante">
                  <option value="0">Seleccione</option>
                  <option value="Boleta">Boleta</option>
                  <option value="Factura">Factura</option>
                  <option value="Ticket">Ticket</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Serie comprobante</label>
                <input type="text" class="form-control" v-model="serie_comprobante" placeholder="000x">
              </div>
            </div>  
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Numero comprobante (*)</label>
                <input type="text" class="form-control" v-model="num_comprobante" placeholder="000xx">
              </div>
            </div>  
          </div>
          <div class="col-md-12">
            <div v-show="errorIngreso" class="form-group row div-error">
              <div class="text-center text-error">
                <div v-for="error in errorMostrarMsjIngreso" :key="error" v-text="error"></div>
              </div>
            </div>
          </div>
          <div class="form-group row border">
            <div class="col-md-6">
              <div class="form-group">
                <label for="">Articulo <span style="color: red" v-show="idarticulo == 0">(*Seleccione)</span></label>
                <div class="form-inline">
                  <input type="text" class="form-control" v-model="codigo" @keyup.enter="BuscarArticulo()" placeholder="Ingrese al articulo">
                  <button @click="AbrirModal()" class="btn btn-primary">...</button>
                  <input type="text" readonly class="form-control" v-model="articulo">
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="">Precio <span style="color: red" v-show="precio == 0">(*Ingrese)</span></label>
                <input type="number" value="0" step="any" v-model="precio" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="">Cantidad <span style="color: red" v-show="cantidad == 0">(*Ingrese)</span></label>
                <input type="number" value="0" step="any" v-model="cantidad" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <button @click="AgregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
              </div>
            </div>
          </div>
          <div class="form-group row border">
            <div class="table-responsive col-md-12">
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Opciones</th>
                    <th>Articulo</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody v-if="arrayDetalles.length > 0">
                  <tr v-for="detalle in arrayDetalles" :key="detalle.id">
                    <td>
                      <button type="button" @click="EliminarDetalle(detalle)" class="btn btn-danger btn-sm">
                        <i class="icon-close"></i>
                      </button>
                    </td>
                    <td v-text="detalle.articulo">Articulo n</td>
                    <td>
                      <input v-model="detalle.precio" type="number" value="3" class="form-control">
                    </td>
                    <td>
                      <input v-model="detalle.cantidad" type="number" value="2" class="form-control">
                    </td>
                    <td v-text="(detalle.precio*detalle.cantidad)"></td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="4" align="right"><strong>Total parcial:</strong></td>
                    <td>$ {{totalParcial= total - totalImpuesto}}</td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="4" align="right"><strong>Total impuesto:</strong></td>
                    <td>$ {{totalImpuesto=((total*impuesto)/(1+impuesto)).toFixed(2)}}</td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="4" align="right"><strong>Total neto:</strong></td>
                    <td>$ {{total = CalcularTotal}}</td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <td colspan="5">
                      No hay articulos agregados
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <button type="button" class="btn btn-secondary" @click="OcultarDetalle()">Cerrar</button>
              <button type="button" class="btn btn-primary" @click="RegistrarDetalle()">Registrar compra</button>
            </div>
          </div>
        </div>
        </template>
        <!-- Fin del registro del detalle -->

        <!--Ver ingreso-->
        <template v-else-if="listado==2">
        <div class="card-body">
          <div class="form-group row border">
            <div class="col-md-9">
              <div class="form-group">
                <label for="">Proveedor</label>
                <p v-text="proveedor"></p>
              </div>
            </div>
            <div class="col-md-3">
              <label for="">Impuesto</label>
              <p v-text="impuesto"></p>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Tipo comprobante</label>
                <p v-text="tipo_comprobante"></p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Serie comprobante</label>
                <p v-text="serie_comprobante"></p>
              </div>
            </div>  
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Numero comprobante</label>
                <p v-text="num_comprobante"></p>
              </div>
            </div>  
          </div>
          <div class="form-group row border">
            <div class="table-responsive col-md-12">
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Articulo</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody v-if="arrayDetalles.length > 0">
                  <tr v-for="detalle in arrayDetalles" :key="detalle.id">
                    <td v-text="detalle.articulo"></td>
                    <td v-text="detalle.precio"></td>
                    <td v-text="detalle.cantidad"></td>
                    <td v-text="(detalle.precio*detalle.cantidad)"></td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="3" align="right"><strong>Total parcial:</strong></td>
                    <td>$ {{totalParcial= total - totalImpuesto}}</td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="3" align="right"><strong>Total impuesto:</strong></td>
                    <td>$ {{totalImpuesto=(total*impuesto).toFixed(2)}}</td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="3" align="right"><strong>Total neto:</strong></td>
                    <td>$ {{total}}</td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <td colspan="4">
                      No hay articulos agregados
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <button type="button" class="btn btn-secondary" @click="OcultarDetalle()">Cerrar</button>
            </div>
          </div>
        </div>
        </template>
        <!--In ver ingreso-->
      </div>
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
            <div class="form-group row">
              <div class="col-md-6">
                <div class="input-group">
                  <select class="form-control col-md-3" v-model="criterioA">
                    <option value="nombre">Nombre</option>
                    <option value="descripcion">Descripción</option>
                    <option value="codigo">Codigo</option>
                  </select>
                  <input type="text" class="form-control" @keyup.enter="ListarArticulos(buscarA, criterioA)" placeholder="Texto a buscar" v-model="buscarA"/>
                  <button type="button" @click="ListarArticulos(buscarA, criterioA)" class="btn btn-primary">
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>Opciones</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Precio venta</th>
                    <th>Stock</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="datos in arrayArticulos" :key="datos.id">
                    <td>
                      <button type="button" class="btn btn-success btn-sm" @click="AgregarDetalleModal(datos)">
                        <i class="icon-plus"></i>
                      </button>
                    </td>
                    <td v-text="datos.codigo"></td>
                    <td v-text="datos.nombre"></td>
                    <td v-text="datos.nombre_categoria"></td>
                    <td v-text="datos.precio_venta"></td>
                    <td v-text="datos.stock"></td>
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
            </div>
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
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
  data(){
    return {
      ingreso_id : 0,
      idproveedor: 0,
      proveedor: '',
      tipo_comprobante: 'Boleta',
      serie_comprobante: '',
      num_comprobante: '',
      impuesto: 0.19,
      total: 0.0,
      totalImpuesto: 0.0,
      totalParcial: 0.0,
      listado : 1,
      arrayIngresos : [],
      arrayDetalles : [],
      arrayProveedores : [],
      modal : 0,
      tituloModal : '',
      tipoAccion : '',
      errorIngreso : 0,
      errorMostrarMsjIngreso : [],
      pagination : {
        'total' : 0,
        'current_page' : 0,
        'per_page' : 0,
        'last_page' : 0,
        'from' : 0,
        'to' : 0,
      },
      offset : 3,
      criterio : 'num_comprobante',
      buscar : '',
      criterioA : 'codigo',
      buscarA : '',
      arrayArticulos : [],
      idarticulo : 0,
      articulo : '',
      codigo : '',
      precio : 0,
      cantidad : 0
    }
  },

  components : {
    vSelect
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
    },

    CalcularTotal: function(){
      let resultado = 0
      for(let i=0; i<this.arrayDetalles.length; i++){
        resultado += (this.arrayDetalles[i].precio*this.arrayDetalles[i].cantidad)
      }
      return resultado
    }
  },

  methods: {
    ListarIngresos(page, buscar, criterio) {
      let me = this;
      var url = '/ingresos?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayIngresos = respuesta.ingresos.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });
    },

    SelectProveedor(search){
      let me = this;

      var url = '/proveedores/selectProveedor?filtro='+search;
      axios.get(url).then(function (response) {
        me.arrayProveedores = response.data.proveedores;
        q: search
      })
      .catch(function (error) {
        console.log(error);
      })
    },

    getDatosProveedor(val1){
      let me = this;
      me.idproveedor = val1.id;
    },

    BuscarArticulo(){
      let me = this;
      axios.get('/articulos/buscarArticulos?filtro='+me.codigo).then(function (response) {
        me.arrayArticulos = response.data.articulos;

        if(me.arrayArticulos.length > 0){
          me.articulo = me.arrayArticulos[0]['nombre'];
          me.idarticulo = me.arrayArticulos[0]['id'];
        }else{
          me.articulo = 'No existe el articulo';
          me.idarticulo = 0;
        }
      })
      .catch(function (error) {
        console.log(error);
      })
    },

    CambiarPagina(page, buscar, criterio){
      let me = this;
      me.pagination.current_page = page;
      me.ListarIngresos(page, buscar, criterio);
    },

    encuentra(id){
      let sw = false;
      for(let i = 0; i<this.arrayDetalles.length; i++){
        if (this.arrayDetalles[i].idarticulo == id) {
          sw = true;
        }
      }
      return sw;
    },

    EliminarDetalle(detalle){
      let me = this
      me.arrayDetalles.splice(detalle, 1)
    },

    AgregarDetalleModal(datos){
      let me = this;
      if(me.encuentra(datos['id'])){
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'El articulo ya fue agregado al detalle'
        })
      }else{
        me.arrayDetalles.push({
        idarticulo: datos['id'],
        articulo: datos['nombre'],
        cantidad: 1,
        precio: 1
        });   
      }
    },
    

    ListarArticulos(buscar, criterio) {
      let me = this;

      axios.get('/articulos/listarArticulos?buscar='+ buscar +'&criterio='+ criterio).then(function (response) {
          me.arrayArticulos = response.data.articulos.data;
        })
        .catch(function (error) {
          // handle error
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    AgregarDetalle(){
      let me = this;
      if(me.idarticulo == 0 || me.cantidad == 0 || me.precio == 0){

      }else{
        if(me.encuentra(me.idarticulo)){
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'El articulo ya fue agregado al detalle'
          })
        }else{
          me.arrayDetalles.push({
          idarticulo: me.idarticulo,
          articulo: me.articulo,
          cantidad: me.cantidad,
          precio: me.precio
          });   
          me.codigo = ''
          me.articulo = ''
          me.idarticulo = 0
          me.precio = 0
          me.cantidad = 0
        }
        
      }
    },

    ValidarIngreso(){
      this.errorIngreso = 0;
      this.errorMostrarMsjIngreso = [];

      if(this.idproveedor == 0) {
        this.errorMostrarMsjIngreso.push("Seleccione un proveedor");
      }else
      if(this.tipo_comprobante == 0) {
        this.errorMostrarMsjIngreso.push("Seleccione el comprobante");
      }else
      if(this.num_comprobante == "") {
        this.errorMostrarMsjIngreso.push("Ingrese el numero de comprobante");
      }else
      if(!this.impuesto) {
        this.errorMostrarMsjIngreso.push("Ingrese el impuesto de la compra");
      }else
      if(this.arrayDetalles.length <= 0) {
        this.errorMostrarMsjIngreso.push("Ingrese detalles");
      }

      if(this.errorMostrarMsjIngreso.length > 0){
        this.errorIngreso = 1;
      }

      return this.errorIngreso;
    },

    RegistrarDetalle(){
      if(this.ValidarIngreso() == 1){
        return;
      }
      let me = this;
      
      axios.post('/ingresos/registrar',{
        'idproveedor' : this.idproveedor,
        'tipo_comprobante' : this.tipo_comprobante,
        'serie_comprobante' : this.serie_comprobante,
        'num_comprobante' : this.num_comprobante,
        'impuesto' : this.impuesto,
        'total' : this.total,
        'data' : this.arrayDetalles
      }).then(function (response) {
          me.OcultarDetalle()
          me.ListarIngresos(1, '', 'num_comprobante')
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
          // always executed
        });
    },

    MostrarDetalle(){
      let me = this;
      this.listado = 0;
      this.SelectProveedor('');
      me.idproveedor = 0
      me.tipo_comprobante = 'Boleta'
      me.serie_comprobante = ''
      me.num_comprobante = ''
      me.impuesto = 0.19
      me.total = 0.0
      me.idarticulo = 0
      me.articulo = ''
      me.cantidad = 0
      me.precio = 0
      me.arrayDetalles = []
    },

    OcultarDetalle(){
      let me = this;
      this.listado = 1;
      me.idproveedor = 0
      me.tipo_comprobante = 'Boleta'
      me.serie_comprobante = ''
      me.num_comprobante = ''
      me.impuesto = 0.19
      me.total = 0.0
      me.idarticulo = 0
      me.articulo = ''
      me.cantidad = 0
      me.precio = 0
      me.arrayDetalles = []
    },

    CerrarModal(){
      this.modal = 0;   
      this.tituloModal = '';
      this.arrayArticulos = [];
      this.buscarA = '';
    },

    AbrirModal(){
      this.modal = 1;
      this.tituloModal = 'Seleccione uno o varios articulo';
      this.arrayArticulos = [];
      this.buscarA = '';
    },

    VerIngreso(id){
      let me = this
      me.listado = 2
      var arrayIngresoT = []

      //Obtener cabecera (datos del ingreso)
      axios.get('/ingresos/obtenerCabecera?id='+id).then(function (response) {
          arrayIngresoT = response.data.ingreso;
          me.impuesto = arrayIngresoT[0]['impuesto'];
          me.proveedor  = arrayIngresoT[0]['nombre'];
          me.num_comprobante = arrayIngresoT[0]['num_comprobante'];
          me.serie_comprobante = arrayIngresoT[0]['serie_comprobante'];
          me.tipo_comprobante = arrayIngresoT[0]['tipo_comprobante'];
          me.total = arrayIngresoT[0]['total'];
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });

      //Obtener detalles (detalles del ingreso)
      axios.get('/ingresos/obtenerDetalles?id='+id).then(function (response) {
          me.arrayDetalles = response.data.detalles;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });
    },

    DesactivarIngreso(id){
      Swal.fire({
        title: 'Esta seguro de anular este ingreso?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, anular!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          let me = this;
          axios.put('/ingresos/desactivar',{
            'id' : id
          }).then(function (response) {
              Swal.fire(
                'Anulado!',
                'El ingreso se anulo con exito.',
                'success'
              )
              me.ListarIngresos(1, '', 'num_comprobante');
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
    this.ListarIngresos(1, this.buscar, this.criterio);
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
    margin-top: 2% !important;
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
  @media (min-width: 600px){
    .btnagregar{
      margin-top: 2rem;
    }
  }
</style>