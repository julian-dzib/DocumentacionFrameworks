<!--#SDK-->
<s-template>






<!--Inicio de mi span superior-->
<span>  

	<!--DIALOG PRINCIPAL-->
  <v-dialog v-model="showPrincipal" persistent fullscreen hide-overlay transition="dialog-bottom-transition" scrollable>
  	<v-card>
      <v-card-title class="headline primary">
        <s-toolbar-modal
        	titulo = "Consulta IA"
        	modelo = "consultas_ia"
          :url = "baseURL"
        	:id = "idPrincipal"
          @cerrar = "closePrincipal()" 
        />       
			</v-card-title> 
      
      <span></span>
      
      <!--Tabla Lista Registro-->
      <v-card-text class="pa-4">
				<v-row class="mt-3">
					<v-col cols="12"> 
						 <v-row>
								<v-col cols="12" md="6" lg="6">
									<v-text-field
                    dense 
                    label="Buscar" 
                    v-model="search"
                  	clearable        
										:loading="bLoading"              
 		
								/>
								</v-col>
								
               <!--Abrir el diglog Agregar-->
                <v-col cols="6" md="3" lg="3" class="d-flex align-center">
                  <v-btn class="secondary" large @click="show=true">
                    <v-icon left>mdi-plus</v-icon>
                    AGREGAR
                  </v-btn>
        				</v-col>
               
               <!--Swicth para mostrar los que estan desactivados-->
                <v-col cols="6" md="3" lg="3" class="d-flex align-center">
                  <v-switch class="light"
                    v-model="swicthEstatus"
                    :label="(swicthEstatus === 'activos' ? 'Activos' : 'Desactivos')"
                    true-value="activos"
                    false-value="desactivos"
                    @change="filtrarPorEstatus"
                  > </v-switch>
                </v-col>
        
  
            </v-row>
                  
            
            <!--Tablas Listado-->
            <v-data-table 
								:headers="headersConsular"
								:items="filtrarRegistros"
								item-key="id"
								:items-per-page="20"  
								class="elevation-1"
								:loading="bLoading"
  							style="border: 1px solid #ccc; border-radius: 1px; margin-top:16px;"
								
								>
              	<template v-slot:item.rol= "{item}">
                  <span> {{ rols[item.rol]}}</span>
              </template>
               
								<template v-slot:item.estatus="{ item }">
                  <span>{{ item.estatus?'Activo':'Baja' }}</span>
                </template>
								<template v-slot:item.actions="{ item }">
									<v-icon class="mr-2" @click="actualizarRegistro(item)">mdi-launch</v-icon>
									<v-icon class="mr-2" @click="eliminarRegistro(item)">mdi-delete</v-icon>
        				</template>
						</v-data-table>
        
      	</v-col>
    	</v-row>
    </v-card-text>
				
      
		</v-card>    
  </v-dialog>
  
	
  <!--DIALOG AGREGAR-->
	<v-dialog v-model="show" persistent max-width="760px" transition="dialog-bottom-transition" scrollable>
  	<v-card>
      <v-card-title class="headline primary">
        <s-toolbar-modal
          titulo = "Registro"
          :id = "id"
          :esNuevo = "isReadonly == true ? undefined : isNew"

          @cerrar = "closeAgregar()"
          @guardar = "Guardar()" 
           /> 
			</v-card-title>
  
      <v-card-text>
      	<!--Definir mis filas y columnas-->
      	<v-form  ref="form" v-model="valid" lazy-validation dense class="mt-4">
         <v-row dense class="mt-4">
          <!-- Nombre -->
           <v-col cols="12" xs="4" sm="4" md="4" lg="4" xl="4">
              <v-text-field
                 dense
                 required
                 :rules="[v=> !!v || 'Por favor, ingresa el nombre']"
                 label="Nombre"        
                 v-model="registroActual.nombre" 
              />
          </v-col>
    			<!-- Tabla_id -->
          <v-col cols="12" xs="4" sm="4" md="4" lg="4" xl="4">
             <v-autocomplete
                 dense
                 required
                 :rules="[v=> !!v || 'Por favor, seleciona un rol']"      
                 
                 item-text="text"           
                 item-value="value"
                 
                 :items="[
                        { value: 'system', text: 'Sistema' },
                        { value: 'assistant', text: 'Asistente' },
                        { value: 'user', text: 'Usuario' },
                        { value: 'developer', text: 'Desarrollador' }
                  ]"
                 label="Rol"
                 v-model="registroActual.rol"           
              ></v-autocomplete>
          </v-col>
    			<!-- Rol -->             
          <v-col cols="12" xs="4" sm="4" md="4" lg="4" xl="4">
             <v-autocomplete
                 dense
                 required
                 :rules="[v=> !!v || 'Por favor, selecciona el Modulo']"
                 label="Módulo Soferp"  
                           
                 item-text = "nombre"
                 item-value = "modulo_id"
                 :items= "modulos"
                 v-model="registroActual.tabla_id"                
              />
              
          </v-col>  
    		</v-row>
        <!-- Contexto -->
        <v-row dense class="mt-4"> 
           <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12">
              <v-textarea
                 dense
                 required
                 :rules="[v=> !!v || 'Por favor, ingresa el contexto']"
                 label="Contexto" 
                 v-model= "registroActual.contexto" 
                 rows = "8"
              />
          </v-col> 
    		</v-row>
          
          
    
          
      	<!-- IMG
        <v-row dense class="mt-4" v-if="capturarImg.img">
          <v-col cols="12">
            <v-img :src="capturarImg.img" max-width="100" contain></v-img>
          </v-col>
        </v-row>-->
	
          
          
        <!--Lugar para cargar la img
        <v-row dense class="mt-4"
        	v-if="switchOp==='si'">
        		<v-file-input accept = "image/*" v-model = "imgSeleccionada" show-size dense label = "Foto" @change = "seleccionarImagen" />
        </v-row>-->
          
          
        <!--Elegir el tipo de plantilla-->
          
                
    
        
        <!--Tipo de plantilla
      	<v-switch
        	v-model="registroActual.tipoPlantilla"
          :label= "(registroActual.tipoPlantilla==='consulta'?'Plantilla de Consulta':'Plantilla de Correo')"
        	true-value="consulta"
          false-value="correo"
          hide-detail
        >
        </v-switch>-->
                   
        
        <!--Establecer un swicth
  			<v-switch class="light"
          	  	v-model="switchOp"
								:label="'Agregar Imágen: ' + (switchOp === 'si' ? 'Si' : 'No')"
                true-value="si"
                false-value="no"
                hide-detail                
        >
        </v-switch>-->      
        </v-form>
      </v-card-text>
    </v-card>
  <!--Cierre de mi dialog superior del inicio-->
  </v-dialog>     


  
  <!--Cierre de mi span superior-->
  </span> 





















</s-template>
<!--#SDK-->
<script>






















props:[ 

    'id', 
    
  
  	//'principal',//model
    'idPrincipal', //id
  

  	
    'readonly',
  	'isReadonly',
],

data: () => ({
  
  isNew:true,
  
  //Variables para mi modal principal
  search:null,
  bLoading: false,

  //Variables Globales
  baseURL:"/consultas_ia",
  valid:true,
  registroActual: {},
  
 
  //Estados de mis modales
  show: false,
  showPrincipal: true,
  showDialogAcciones: false,
	showIA:false,
  modulos:[], 
  swicthEstatus: "activos",
  
  //Para mostrar mis registros 
  consultar: [],
  
  inputRules: [
      v => !!v || "Este campo es requerido",
  ],
  headersConsular: [
			{ text: 'Nombre', align: 'start', value: 'nombre'},
			{ text: 'Rol', value: 'rol' }, 
    	{ text: 'Estatus', value: 'estatus' }, 
			{ text: 'Acciones', value: 'actions', sortable: false },
		],
 
  rols:{
    system: 'Sistema',
    assistant: 'Asistente',
    user: 'Usuario',
    developer: 'Desarrollador',

  },


	//Tipo de plantilla
  //opcion:"no",
  //Imagen
  /*imgSeleccionada:null, 
  imagenActual:null,
  capturarImg:{
    img:''
  },
  fileSeleccionado: null,
  */
}),
mounted(){
	this.onMobile = this.MOBILE();   
  //Llamar al metodo modulos
  this.getModulos();
  //this.getRegistros();
  this.filtrarPorEstatus();
},

methods:{
  
  //CERRAR EL DIALOG PRINCIPAL - SHOWPRINCIPAL
  closePrincipal(){
    this.showPrincipal=false;
  },
	//CERAR EL DIALOG AGREGAR -SHOW
  closeAgregar(){
    this.registroActual = {};
    this.$refs.form.resetValidation(); 
    this.show=false;
    //Cargar los registros agregados
    this.getRegistros();
    //console.log(consultar);
  },
  filtrarPorEstatus(){
    if (this.swicthEstatus === "activos") {
      axios
      .get(this.URL(this.baseURL))
        //.get(this.URL(this.baseURL))
        .then(response=>{
          this.consultar= response.data;
      }).catch(this.MSGBOX)
      
    }else{
      axios
        .get(`${this.URL(this.baseURL)}&estatus=0`)
        //.get(this.URL(this.baseURL))
        .then(response=>{
          this.consultar= response.data;
        }).catch(this.MSGBOX)
    }
  },

  //SERVICIOS API REST
  //GET - DEVOLVER LOS MODULOS
  getModulos(){
    axios
    .get(this.URL('/modulos'))
    .then(response=>{
      this.modulos = response.data;
    })
    .catch(this.MSGBOX);
  },

//POST Y UPDATE - CREAR Y ACTUALIZAR UN REGISTRO CON EL MISMO MODAL
 Guardar(){
  if (!this.$refs.form.validate()) return false;
    //Crear el objeto consulta IA
   	//this.registroActual = sessionStorage.getItem("DataBaseId");
		//VERIFICAR QUE EL REGISTRO SEA NUEVO
		if (this.isNew) {
    	axios
        .post(this.URL(this.baseURL), this.registroActual)
        .then(response => {
        swal.fire({
          icon: "success",
          title: "Registro agregado con éxito",
          timer: 1000
        }),
        this.$emit('new', response.data); 
        this.$refs.form.reset();
        //console.log(this.registroActual.tipoPlantilla);
    	}).catch(this.MSGBOX);
      
   	}else{
      axios
       //CONSTRUIR LA URL
      .put(this.URL(this.baseURL+'/'+this.registroActual.id),this.registroActual)
      .then(response=>{
         swal.fire({
           icon:"success",
           title: "Registro Actualizado con éxito",
           timer: 1000
         })
      	this.isNew=true;	
        this.$emit('update',this.id);
        //console.log(this.registroActual.tipoPlantilla);

        //this.$refs.form.reset();
      }).catch(this.MSGBOX)
      
    }
  },
  //Consumir el servicio getAll
  getRegistros(){
      //let filtro= `?api_token=${sessionStorage.getItem("token")}&database_id=${sessionStorage.getItem("DatabaseId")}$estatus=true`; 
      /*axios
      	.get(this.URL(this.baseURL))
      	.then(response=>{
        	this.consultar= response.data;
      }).catch(this.MSGBOX)
    */
    
  },
  //Consumir el servicio, pasar el objeto para actualizar o eliminar y cambiar el estado del modal 
  actualizarRegistro(item) {
    this.registroActual = item;
    this.show = true;
   	this.isNew=false;
    //console.log(this.capturarImg.img);
  },
    
	eliminarRegistro(item) {
  swal.fire({
    title: 'Eliminar',
    text: '¿Esta seguro de eliminar el registro?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Confirmar'
  }).then((result) => {
    if (result.isConfirmed) {
			console.log("ID del elemento:", item.id);
      axios
      .delete(this.URL(this.baseURL+'/'+item.id))
			//axios.delete(`${this.URL(this.baseURL)}/${item.id}`, {
        //params: {
          //api_token: sessionStorage.getItem("token"),
          //database_id: sessionStorage.getItem("DataBaseId")
        //}
      //})
      .then((response) => {
        swal.fire({
          icon: "success",
          title: "Registro eliminado con éxito",
          timer: 1000,  
          showConfirmButton: false
        });
        //Volver a cargar los registros
        this.getRegistros(); 
      })
      
      .catch(this.MSGBOX); 
    }

  });
},
  
  
  
  
  

	/*seleccionarImagen(e){
      if(!e) return;
      this.fileSeleccionado = e;
      let render = new FileReader();
      
      render.onload = (r) => {
        this.imagenSeleccionada = r.target.result;
        this.capturarImg.img= r.target.result;
        console.log(this.registroActual.img);
      };
      
      render.readAsDataURL(e);
  },*/
   
},
computed:{
   
   filtrarRegistros(){
     if(!this.search) return this.consultar
     return this.consultar.filter(
     	item=>
      	item.nombre.toLowerCase().includes(this.search.toLowerCase())
    );
   },

},








</script>
<!--#SDK-->
<style>























	null























</style><!--#SDK-->