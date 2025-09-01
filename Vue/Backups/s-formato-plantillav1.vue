<!--#SDK-->
<s-template>
















 
	<span>
    <v-dialog v-model="show" persistent fullscreen hide-overlay transition="dialog-bottom-transition" scrollable>
      <v-card>
        <v-card-title class = "headline primary" >
        <s-toolbar-modal 
          titulo = "Formatos de plantilla"
          :id = "id"
          :url = "baseURL"
          :esNuevo = "isNew" 
          :estatus = "registroActual.estatus"
          :permiso = "permiso"
          modelo = "formato_plantilla"
          @cerrar = "onClose()"
          @guardar = "Guardar()"
          @eliminar = "Eliminar()"
          @boton1 = "showModelos = true"
        />
        </v-card-title>
        
        <v-card-text>
          <v-form ref="form" dense> 
            <v-row dense class="pt-4">
                <v-col cols="9" xs="12" sm="12" md="6" lg="6" xl="6">
                  <v-text-field
                  dense
                  hide-details
                  label = "Nombre"
                  v-model = "registroActual.nombre"
              		:rules = "inputRules"
                />
                </v-col>
              	<v-col cols="12" xs="13" sm="3" md="3" lg="3" xl="3">
                  <v-autocomplete  
                    dense
                    clearable
                    persistent-hint
                    label = "Modelo"
                    :items = "modelos"
                    v-model = "registroActual.tabla"
                    item-text = "nombre"
                    item-value = "modelo"
                    :rules = "inputRules"
                  />
              		</v-col>
               		<v-col cols="12" xs="12" sm="3" md="3" lg="3" xl="3">
                  <v-text-field
                  	dense
                    hide-details
                  	label = "Agrupador"
                  	:rules = "inputRules"
                  	v-model = "registroActual.agrupador"
               	 />
              </v-col>
            </v-row>
            
            <v-row dense class="pt-1">
              <v-col cols="12" xs="12" sm="6" md="6" lg="6" xl="6">
                <v-text-field
                	dense
                  hide-details
                	label = "Nombre universal"
                	v-model="registroActual.uuid"
               />
              </v-col>
             		
               <v-col cols="12" xs="12" sm="3" md="3" lg="3" xl="3">
                  <v-autocomplete  
                  dense
                  clearable
                  label="Tipo"
                  hide-details		
                  persistent-hint
                  :items = "tipos_notificacion"
                  v-model = "registroActual.tipo"
                  item-text = "nombre"
                  item-value = "id"
                  :rules = "inputRules"
                />
              </v-col>
              
              
              
             
              
              <v-col cols="12" xs="12" sm="12" md="3" lg="3" xl="3">
                <v-btn text light color = "primary" @click = "showImportar = true"><v-icon >mdi mdi-swap-vertical-bold</v-icon> IMPORTAR/EXPORTAR</v-btn>
              </v-col>
            </v-row>
            <v-row dense class="pt-3">
              <v-col cols="12" xs="12" sm="9" md="9" lg="9" xl="9">
                <v-text-field
                	dense
                  hide-details
                	label = "Asunto"
                	v-model="registroActual.asunto"
               />
              </v-col>
            </v-row>
          </v-form>
          
         <v-row class="pt-4">
            <v-col cols="12">
           <v-tabs v-model="tab3" background-color="primary"  color="red" dark show-arrows>
                  <v-tabs-slider color="red"></v-tabs-slider>
                  <v-tab key="1">PLANTILLA</v-tab>
                  <v-tab key="2">PARAMETROS</v-tab>
             			<!--Icono de gpt-->
             			<v-spacer></v-spacer>
	               	<v-btn icon @click="abrirDialog()" style="padding:24px">
                      <v-icon color="yellow">
                        mdi-creation
                      </v-icon>
    	            </v-btn>
             
                </v-tabs>
                <v-tabs-items v-model="tab3" >
                  <v-tab-item key="1">
										<div id="app">
                      <vue-editor v-model="contenido" :editorToolbar="customToolbar"></vue-editor>
                    </div>
                  </v-tab-item>
                  <v-tab-item key="2">
                    <template>
                    <!-- tabla -->
                      <v-responsive>
                        <v-data-table 
                          class="elevation-1"
                          :headers="headerParametros"
                          :items="parametros"
                          ref="dataTable">
                           <template v-slot:item.nombre_campo="{ item }">
                            <v-text-field 
                              dense
                              v-model = "item.nombre_campo"
                              :rules="inputRules"
                            />
                          </template>
                           <template v-slot:item.valor="{ item }">
                            <v-text-field 
                              dense
                              v-model = "item.valor" 
                              :rules="inputRules"
                            />
                          </template> 
                           <template  v-slot:item.opcion = "{ item}">
                           <v-icon @click = "deleteParametro(item)">mdi-delete</v-icon>
                          </template>
                          <template v-slot:body.append>
                            <v-btn text @click = "addParametro()">
                              <v-icon title = "Nuevo parametro">mdi-playlist-plus</v-icon>
                            </v-btn>
                          </template>
                        </v-data-table>
                      </v-responsive>
                    <!-- fin tabla -->
                    </template>
                  </v-tab-item>
                </v-tabs-items>
              </v-col>
           </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    
    <!--Dialog AI-->
    <v-dialog v-model= "dialogAI" persistent max-width="760px" transition="dialog-bottom-transition" scrollable>
      	<v-card>
          <v-card-title class="headline primary">
     				<s-toolbar-modal
            	titulo= "Generador de Plantillas"
              modelo= "consula_ia"
              id= idDialogAI
            	@cerrar = "cerrarDialogAI()"      
            />        
          </v-card-title>
          
          <v-card-text>
             <v-list>
              <!--v-if="item.rol=='developer'||item.rol=='assistant'"-->	
               <v-list-item
                 
      					 v-for="item in filtrarRegistros"
                 :key="item.id"
                 @click="selecionarItem(item)"
								 class="hoverable"

               >
               <v-list-item-content>
                  <v-list-item-title>{{ item.nombre }}</v-list-item-title>
               </v-list-item-content>


               </v-list-item>
            </v-list>	
          </v-card-text> 
        </v-card>
		</v-dialog>
    
    
    
    
    
		<v-dialog v-model = "showImportar" width = "700" persistent>
    	<v-card>
        <v-card-title class = "headline primary">
          <s-toolbar-modal  
          titulo = "Importar / Exportar "
          @cerrar = "OpenCloseExport()"   
          /> 
        </v-card-title>
      
        <v-card-text>
          <v-container>
          <span v-if = "!isNew">
            <v-subheader class = "text-h4">Exportar</v-subheader>

            Se creara un Archivo en formato *.json de la configuracion Actual
            <v-card-actions>
              <v-btn color = "primary" @click = "descargarJson()">Exportar</v-btn>
            </v-card-actions>
            <v-divider class = "mt-2"></v-divider>
          </span>

          <v-subheader class = "text-h4">Importar</v-subheader>

            Podras seleccionar un archivo *.json para impportar un reporte y sus configuraciones.
            <v-row dense>

            </v-row>
            <v-row dense>
              <v-file-input
                accept = ".json"
                show-size
                truncate-length = "20"
                placeholder = "selecciona el archivo JSON correspondiente"
                preped-icon = "mdi-code-json"
                label = "JSON"
                ref = "fileInput"
                v-model = "archivoSeleccionado"
                @change = "seleccionarArchivo"
              >
              </v-file-input>
            </v-row>
            <v-row dense>
              <v-btn color = "primary" @click = "subirJson()">Importar</v-btn>
            </v-row>
            </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    
   
  </span>



























</s-template>
<!--#SDK-->
<script>




























props:[
  'show',
  'id',
  'isNew',
  'permiso'
],

data: () => ({
  //url base
  baseURL:'/formatos_plantillas',
   contenido: "",
    tab3:0,
    parametros:[],
    headerParametros:[
    {text: 'Nombre del campo ', align: 'start', value: 'nombre_campo', sortable: false,class: "primary white--text" },
    {text: 'Valor', align: 'start', value: 'valor', sortable: false,class: "primary white--text" },
    
    {text: 'Opciones', align: 'center', value: 'opcion', width:150,sortable: false,class: "primary white--text"}
  ],
	inputRules: [
    v => !!v || "Este campo es requerido",
  ],
  registroActual:{},
  showImportar: false,
  archivoSeleccionado: null,
  tipos_notificacion: [
      { id:'sms', nombre: 'SMS'},
      { id:'waba', nombre: 'WABA'},
      { id:'twilio', nombre: 'TWILIO'},
      { id:'email', nombre: 'EMAIL'},
      { id:'push', nombre: 'PUSH'},
      { id:'chat', nombre: 'CHAT'},
      { id:'ultramsg', nombre: 'ULTRAMSG'},
    	{id:'prompt', nombre: 'PROMPT'}
    ],
  customToolbar: [
        ["bold", "italic", "underline","strike"], // Solo estas tres herramientas,
    		["link", "image"]
      ],
  
  
  showModelos:false,
  modelos:[],
  
  //Variables para el dialog AI
  baseUrlAI: '/consultas_ia',

  dialogAI: false,
  filtrarRegistros: [],

  
  
}),
mounted() {
  this.getModelos();
},
  
methods: {
   getModelos(){
      axios
        .get(this.URL('/modelos', '&estatus=true'))
        .then(response => {
          this.modelos = response.data;
        }).catch(this.MSGBOX);
    },
   subirJson() {
    if (!this.archivoSeleccionado) return; // No se ha seleccionado ningún archivo.

    const lector = new FileReader();

    lector.onload = (evento) => {
      try {
        const contenidoJson = JSON.parse(evento.target.result);
            console.log(contenidoJson);

        this.registroActual = contenidoJson;

        if(!this.registroActual.contenido){
          return
        }

        this.contenido = this.registroActual.contenido;
        
       
        //this.$refs.editorFormula=this.ver_formula;
        if(this.registroActual.parametros == null){
          this.parametros = [];
        } else {
          
          
          this.parametros = JSON.parse(this.registroActual.parametros);
        }
        
        this.registroActual.database_id = sessionStorage.getItem("DataBaseId");

        //this.ref.editorFormula.forceupdate();
        //this.$forceUpdate();
        //this.$nextTick(function () {
        //this.LoadCodeMirror();
        //});
        //this.$set(this.ver_formula, this.registroActual.contenido);

      } catch (error) {
        console.error('Error al analizar el archivo JSON:', error);
      }

    };

    lector.readAsText(this.archivoSeleccionado);
    this.showImportar = false;
  },
    
  seleccionarArchivo(f) {
    this.archivoSeleccionado = null;
    if(f) this.archivoSeleccionado = f;
    return;
  },
    
  descargarJson(){
    let today = new Date();
    let date = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate();
    let link = document.createElement("a");
    let file = new Blob([JSON.stringify(this.registroActual)], { type: 'application/json' });
    link.href = URL.createObjectURL(file);
    let filename = this.registroActual.nombre + '_' + date + '.json';
    link.download = filename;
    link.click();
    this.showImportar = false;
  },
    
  OpenCloseExport() {
    this.showImportar = !this.showImportar;
  },
  addParametro(){
    let data = {
      nombre_campo:null,
      valor:null,
    };
    this.parametros.push(data);

  },
    
  deleteParametro(item){
    let ix = this.parametros.indexOf(item);
    this.parametros.splice(ix,1);
  },
    
  getRegistro(){
    axios
    .get(this.URL(this.baseURL + '/' + this.id)) 
    .then(response => {
      this.registroActual = response.data;
      
      this.contenido = this.registroActual.contenido;
      this.parametros = JSON.parse(this.registroActual.parametros);
      
    })
    .catch(this.MSGBOX);
  },
    
  async Guardar() {
    if (!this.$refs.form.validate()) return false;
    this.registroActual.contenido = this.contenido;
		this.registroActual.parametros = JSON.stringify(this.parametros);
    
    
    //this.registroActual.configuracion = '';
    if(this.isNew){
        axios
        .post(this.URL(this.baseURL), this.registroActual)
        .then(response => {
          this.contenido = ''; //para el textarea
          
          this.$emit('new',response.data.id);
        })
        .catch(this.MSGBOX);
      } else {
        axios
        .put(this.URL(this.baseURL + '/' + this.id),this.registroActual)
        .then(response => {
          //this.ver_formula = null; //para el textarea se comneto para dar ctrl z al actualizar
          //this.objEditor.toTextArea();//para reiniciar el textarea del code mirror se comento para dar ctrl z al actualizar
          this.$emit('update', response.data.id);
        })
        .catch(this.MSGBOX);
      }
    },
      
 
    
  onClose(){
   this.contenido='';
     this.parametros=[];
    this.$emit('close');
  },
    
  Eliminar(){
    this.$emit('delete');
  },
    
  //Abrir y cerrar dialogAI
   abrirDialog(){
    this.dialogAI=true,
    this.getPlantillas();
  },
  cerrarDialogAI(){
    this.dialogAI=false;
  },
   
  
  //Metodo para traer los registro de IA por 
  //Modulos y estatus true
  getPlantillas(){
    //let filtro = sessionStorage.getItem("modulo_id");
    //let estatus = true;
    //let filtros = `&tabla_id=${sessionStorage.getItem("modulo_id")}&estatus=1`;
    axios
      .get(`${this.URL(this.baseUrlAI)}?api_token=${sessionStorage.getItem("token")}&database_id=${sessionStorage.getItem("DataBaseId")}&tabla_id=${sessionStorage.getItem("modulo_id")}&estatus=1`)
 		  //.get(`this.URL(this.baseUrlAI)?filtro`)
      //.get(this.URL(this.baseURL))
    	//.get(this.URL(this.baseUrlAI)+filtros)
      .then(response=>{
          this.filtrarRegistros = response.data;
    }).catch(this.MSGBOX)
	},
  //Metodo para consumir el servicio GPT
	getTextoGPT(item){
    axios
    .get(this.URL(this.baseUrlAI+"/"+"gpt_texto"+"/"+item.id))
    .then(response=>{
      this.contenido= response.data.respuestaGPT;
      //console.log(this.texto);
    }).catch(this.MSGBOX)
  },
    
  //Seleccionar registro plantilla
	selecionarItem(item) {
    this.getTextoGPT(item);
    this.dialogAI=false;
    //console.log('Selecting item:', item.id);
  },
  
    
    
    
    
  
},
  
watch: {
  show: function(){
    if(!this.show){
     
      return;
    }
    
    this.registroActual = {};
    this.contenido = '';
    
    if(!this.isNew){
      this.getRegistro();
      return;
    }
  },
  
}



























</script>
<!--#SDK-->
<style>





















































</style><!--#SDK-->