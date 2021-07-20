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
          <i class="fa fa-align-justify"></i> Consulta ventas
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
                <input type="text" class="form-control" @keyup.enter="ListarVentas(1, buscar, criterio)" placeholder="Texto a buscar" v-model="buscar"/>
                <button type="button" @click="ListarVentas(1, buscar, criterio)" class="btn btn-primary">
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
                  <th>Cliente</th>
                  <th>Tipo comprobante</th>
                  <th>Serie comprobante</th>
                  <th>Numero comprobante</th>
                  <th>Fecha hora</th>
                  <th>Total</th>
                  <th>Impuesto</th>
                  <!-- <th>Descuento</th> -->
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="datos in arrayVentas" :key="datos.id">
                  <td>
                    <button type="button" class="btn btn-success btn-sm" @click="VerVenta(datos.id)">
                      <i class="icon-eye"></i>
                    </button>&nbsp;
                    <button type="button" class="btn btn-info btn-sm" @click="CargarPDF(datos.id)">
                      <i class="icon-doc"></i>
                    </button>
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

        <!--Ver ingreso-->
        <template v-else-if="listado==2">
        <div class="card-body">
          <div class="form-group row border">
            <div class="col-md-9">
              <div class="form-group">
                <label for="">Cliente</label>
                <p v-text="cliente"></p>
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
                    <th>Descuento</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody v-if="arrayDetalles.length > 0">
                  <tr v-for="detalle in arrayDetalles" :key="detalle.id">
                    <td v-text="detalle.articulo"></td>
                    <td v-text="detalle.precio"></td>
                    <td v-text="detalle.cantidad"></td>
                    <td v-text="detalle.descuento"></td>
                    <td v-text="((detalle.precio*detalle.cantidad)-detalle.descuento)"></td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="4" align="right"><strong>Total parcial:</strong></td>
                    <td>$ {{totalParcial= total - totalImpuesto}}</td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="4" align="right"><strong>Total impuesto:</strong></td>
                    <td>$ {{totalImpuesto=(total*impuesto).toFixed(2)}}</td>
                  </tr>
                  <tr style="background-color: #CEECF5;">
                    <td colspan="4" align="right"><strong>Total neto:</strong></td>
                    <td>$ {{total}}</td>
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
            </div>
          </div>
        </div>
        </template>
        <!--In ver ingreso-->
      </div>
    </div>
  </main>
</template>

<script>
export default {
  data(){
    return {
      venta_id : 0,
      idcliente: 0,
      cliente: '',
      tipo_comprobante: 'Boleta',
      serie_comprobante: '',
      num_comprobante: '',
      impuesto: 0.19,
      total: 0.0,
      descuento: 0.0,
      totalImpuesto: 0.0,
      totalParcial: 0.0,
      listado : 1,
      arrayVentas : [],
      arrayDetalles : [],
      arrayClientes : [],
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
      arrayArticulos : [],
      idarticulo : 0,
      articulo : '',
      codigo : '',
      precio : 0,
      cantidad : 0,
      stock : 0
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
    },

    CalcularTotal: function(){
      let resultado = 0
      for(let i=0; i<this.arrayDetalles.length; i++){
        resultado += ((this.arrayDetalles[i].precio*this.arrayDetalles[i].cantidad)-this.arrayDetalles[i].descuento)
      }
      return resultado
    }
  },

  methods: {
    ListarVentas(page, buscar, criterio) {
      let me = this;
      var url = '/ventas?page='+ page +'&buscar='+ buscar +'&criterio='+ criterio;

      axios.get(url).then(function (response) {
          var respuesta = response.data;
          me.arrayVentas = respuesta.ventas.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });
    },

    BuscarArticulo(){
      let me = this;
      axios.get('/articulos/buscarArticulosVenta?filtro='+me.codigo).then(function (response) {
        me.arrayArticulos = response.data.articulos;

        if(me.arrayArticulos.length > 0){
          me.articulo = me.arrayArticulos[0]['nombre'];
          me.idarticulo = me.arrayArticulos[0]['id'];
          me.precio = me.arrayArticulos[0]['precio_venta'];
          me.stock = me.arrayArticulos[0]['stock'];
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
      me.ListarVentas(page, buscar, criterio);
    },

    MostrarDetalle(){
      let me = this;
      me.listado = 0;
      me.SelectCliente('');
      me.idcliente = 0
      me.tipo_comprobante = 'Boleta'
      me.serie_comprobante = ''
      me.num_comprobante = ''
      me.impuesto = 0.19
      me.total = 0.0
      me.idarticulo = 0
      me.articulo = ''
      me.cantidad = 0
      me.precio = 0
      me.stock = 0
      me.descuento = 0
      me.codigo = ''
      me.arrayDetalles = []
    },

    OcultarDetalle(){
      let me = this;
      me.listado = 1;
      me.idcliente = 0
      me.tipo_comprobante = 'Boleta'
      me.serie_comprobante = ''
      me.num_comprobante = ''
      me.impuesto = 0.19
      me.total = 0.0
      me.idarticulo = 0
      me.articulo = ''
      me.cantidad = 0
      me.precio = 0
      me.stock = 0
      me.descuento = 0
      me.codigo = ''
      me.arrayDetalles = []
    },

    VerVenta(id){
      let me = this
      me.listado = 2
      var arrayVentaT = []

      //Obtener cabecera (datos de la venta)
      axios.get('/ventas/obtenerCabecera?id='+id).then(function (response) {
          arrayVentaT = response.data.venta;
          me.impuesto = arrayVentaT[0]['impuesto'];
          me.cliente  = arrayVentaT[0]['nombre'];
          me.num_comprobante = arrayVentaT[0]['num_comprobante'];
          me.serie_comprobante = arrayVentaT[0]['serie_comprobante'];
          me.tipo_comprobante = arrayVentaT[0]['tipo_comprobante'];
          me.total = arrayVentaT[0]['total'];
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });

      //Obtener detalles (detalles del ingreso)
      axios.get('/ventas/obtenerDetalles?id='+id).then(function (response) {
          me.arrayDetalles = response.data.detalles;
        })
        .catch(function (error) {
          console.log(error);
        })
        .then(function () {
        });
    },

    CargarPDF(id){
      window.open('http://localhost:8000/ventas/pdf/'+id, 'blank');
    },
  },

  mounted() {
    this.ListarVentas(1, this.buscar, this.criterio);
  },
};
</script>