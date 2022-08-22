<template>
  <section class="content">
        <!-- PDF Generator begins -->
        <html-pdf
            :show-layout="false"
            :float-layout="true"
            :enable-download="true"
            :preview-modal="true"
            :paginate-elements-by-height="14000"
            filename="{{$data['singular_lower']}}_lists"
            :pdf-quality="2"
            :manual-pagination="true"
            pdf-format="a4"
            pdf-orientation="landscape"
            pdf-content-width="100%"
            ref="html2Pdf"
          >
              <section  id = "printPaper" slot="pdf-content" style=" width:100%; background-color: white;  padding: 0% 0.5% 40% 0.5%;">
                <div style = "margin-left: 0; width: 100%; ">
                  <h3 style="text-align:center; text-decoration: underline; padding: 1em; "> {{$data['singular']}} Lists</h3>
                  <table class="table table-bordered" style="width: 100%; ">
                        <thead>
                            <tr>
                              @foreach($data['fields'] as $field)
                                @if( $field['name'] != 'id' && $field['name'] != 'created_at' && $field['name'] != 'updated_at' )
                                  <th>{{ucwords( str_replace("_", " ", $field['name']) )}}</th>
                                @endif
                              @endforeach
                            </tr>   
                        </thead>
                        <tbody >
                            <tr v-for="({{$data['singular_lower']}}, index) in {{$data['plural_lower']}}" :key="{{$data['singular_lower']}}.id">
                                @foreach($data['fields'] as $field)
                                    @if( $field['name'] != 'id' && $field['name'] != 'created_at' && $field['name'] != 'updated_at' )
                                      <?php echo"<td>{{";?> {{$data['singular_lower']}}.{{$field['name']}} <?php echo"}}";?> </td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                  </table>
                </div>
              </section>
        </html-pdf>
        <!-- PDF Generator Ends -->

        <!-- Container Begins -->
        <div class="container-fluid" >
          <!-- Row begins-->
          <div class="row">
            <!-- Card Begins (Only Admin can see this content) -->
              <div class="card w-100" v-if="$gate.isAdmin()" > 
                <!-- card header -->
                <div class="card-header pr-sm-3">
                  <div class="d-flex mb-3">
                    <h3 class="card-title mr-auto ">{{$data['singular']}} List</h3>
                    <button type="button" class="btn btn-sm btn-primary " @click="newModal">
                        <i class="fa fa-plus-square"></i>
                        Add New
                    </button>
                  </div>
                </div> <!-- /.card header -->

                <!-- card-body table container -->
                <div class="card-body table-responsive p-2"> 
                    <!-- VUE GOOD TABLE BEGINS -->  
                    <vue-good-table
                      mode="remote"
                      @on-page-change="onPageChange"
                      @on-sort-change="onSortChange"
                      @on-column-filter="onColumnFilter"
                      @on-per-page-change="onPerPageChange"
                      @on-search="onSearch"
                      @on-selected-rows-change="onSelectionChanged"

                      styleClass="vgt-table  bordered table-hover "
                      :totalRows="totalRecords"
                      :isLoading.sync="isLoading"
                      :select-options="{
                        enabled: true,
                      }"
                      :pagination-options="{
                        enabled: true,
                        perPageDropdown: [5, 10, 20, 50, 75, 100],
                        dropdownAllowAll: false,
                      }"
                      :search-options="{
                        enabled: true,
                        placeholder: 'Search the table',
                      }"
                      :rows="{{$data['plural_lower']}}"
                      :columns="columns">
                          <!-- Vue Good TABLE CONTENTS and ACTIONS slot -->  
                          <div slot="table-actions">
                              <!-- Button Groups for EXPORTING TABLE -->  
                              <div class="mr-auto btn-group my-1" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" class="btn btn-default btn-sm " data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-download"></i> Export
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button href="#" class="dropdown-item" @click.prevent="print">
                                        <i class="fa fa-print mr-1"></i> Print
                                    </button>  
                                      <button href="#" class="dropdown-item">
                                        <!-- JSON_EXCEL Component -->  
                                        <json-excel class="" :data="{{$data['plural_lower']}}" :fields="table_heders" worksheet="{{$data['singular']}} Lits" name="{{$data['singular_lower']}}_lists.xls">
                                            <i class="fa fa-file-excel mr-1"></i> Excel
                                        </json-excel>
                                    </button>
                                    <button href="#" class="dropdown-item" @click.prevent="generatePDF">
                                        <i class="fa fa-file-pdf mr-1"></i> PDF
                                    </button>    
                                  </div>
                                </div>
                              </div>

                              <!-- Button Groups for SHOWING/HIDING Columns -->  
                              <div class="mr-auto btn-group my-1" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" class="btn btn-default btn-sm " data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-eye"></i> Show
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li v-for="(column, index) in columns" :key="index">
                                      <a href="#" class="dropdown-item" tabIndex="-1" @click.prevent="toggleColumn( index, $event )"><input :checked="!column.hidden" type="checkbox"/>&nbsp;&nbsp;@{{column.label}}</a>
                                    </li>
                                  </div>
                                </div>
                              </div>
                          </div><!-- Vue Good Table Action slot and contents ends --> 
                          
                          <div slot="emptystate">
                            No @{{$data['singular_lower']}} records found
                          </div>

                          <!-- Vue Good TABLE ACTION COLUMN options -->  
                          <template slot="table-row" slot-scope="props">
                            <span v-if="props.column.field == 'action'">
                              <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary"  @click="{{$data['singular_lower']}}Detail(props)">Detail</button>
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false">
                                  <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <div class="dropdown-menu">
                                  <!--<a class="dropdown-item" href="#" @click="{{$data['singular_lower']}}Detail('show')"><i class="fa fa-eye"> <span style="margin-left:0.1em"> Details </span> </i></a>
                                  <div class="dropdown-divider"></div>-->
                                  <a class="dropdown-item" href="#" @click="editModal(props.row)"><i class="fa fa-edit">  <span style="margin-left:0.1em"> Edit </span>  </i></a>
                                  <a class="dropdown-item " href="#" @click="delete{{$data['singular']}}(props.row.id)"><i class="fa fa-trash">  <span style="margin-left:0.1em"> Delete </span>  </i></a>
                                </div>
                              </div>
                            </span>
                            <span v-else>
                              @{{props.formattedRow[props.column.field]}}
                            </span>
                          </template> <!-- Vue Good TABLE ACTION Column ends -->  

                          <!-- Vue Good TABLE SELECTED ROW Actions -->  
                          <div slot="selected-row-actions">
                              <div class="dropdown">
                                  <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    Selected {{$data['plural']}}
                                  </button>
                                  <div class="dropdown-menu">
                                    <a class="dropdown-item " href="#" @click="deleteSelected{{$data['plural']}}()"><i class="fa fa-trash">  <span style="margin-left:0.1em"> Delete </span>  </i></a>
                                  </div>
                                </div>
                          </div>
                    </vue-good-table>
                </div> <!-- card-body table container ends -->

                <div class="card-footer">
                    <!--<pagination :data="{{$data['plural_lower']}}" @pagination-change-page="getResults"></pagination>-->
                </div>
              </div> <!-- /.card ends-->
          </div> <!-- /.row ends-->
        </div> <!-- /.container ends-->

        <!-- Containt Not Found! -->
        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header"> <!-- Modal Header -->
                      <h5 class="modal-title" v-show="!editmode">New {{$data['singular']}}</h5>
                      <h5 class="modal-title" v-show="editmode">Update {{$data['singular']}}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <!-- <form @submit.prevent="createModel"> -->
                  <form @submit.prevent="editmode ? update{{$data['singular']}}() : create{{$data['singular']}}()">
                    <div class="modal-body">
                        @foreach($data['fields'] as $field)
                            <div class="form-group">
                              @if($field['name'] == 'id' || $field['name'] == 'updated_at' || $field['name'] == 'created_at' )   
                                  <input type="hidden" v-model="form.{{$field['name']}}"></input>
                              @elseif($field['simplified_type'] == 'text')
                                  <label>{{ ucfirst( str_replace('_',' ', $field['name']) ) }}</label>
                                  <input type="text" v-model="form.{{$field['name']}}" name="{{ $field['name'] }}" class="form-control" :class="{ 'is-invalid': form.errors.has( '{{$field['name']}}' ) }" @if($field['max']) maxlength="{{$field['max']}}" @endif>
                                  @if($field['required'] && $field['name'] !== 'id')
                                      <has-error :form="form" field="{{$field['name']}}"></has-error>
                                  @endif
                              @elseif($field['simplified_type'] == 'textarea')
                                  <label>{{ ucfirst( str_replace('_',' ', $field['name']) ) }}</label>
                                  <textarea v-model="form.{{$field['name']}}" @if($field['max']) maxlength="{{$field['max']}}" @endif :class="{ 'is-invalid': form.errors.has( '{{$field['name']}}' ) }" class="form-control" style="min-height: 100px; max-height: 300px;"></textarea>
                                  @if($field['required'] && $field['name'] !== 'id')
                                      <has-error :form="form" field="{{$field['name']}}"></has-error>
                                  @endif
                              @elseif($field['type'] == 'datetime')
                                  <label>{{ ucfirst( str_replace('_',' ', $field['name']) ) }}</label>
                                  <input type="datetime-local" v-model="form.{{$field['name']}}" class="form-control"  :class="{ 'is-invalid': form.errors.has( '{{$field['name']}}' ) }"></input>
                                  @if($field['required'] && $field['name'] !== 'id')
                                      <has-error :form="form" field="{{$field['name']}}"></has-error>
                                  @endif
                              @elseif($field['type'] == 'date')
                                  <label>{{ ucfirst( str_replace('_',' ', $field['name']) ) }}</label>
                                  <input type="date" v-model="form.{{$field['name']}}" class="form-control" :class="{ 'is-invalid': form.errors.has( '{{$field['name']}}' ) }"></input>
                                  @if($field['required'] && $field['name'] !== 'id')
                                      <has-error :form="form" field="{{$field['name']}}"></has-error>
                                  @endif    
                              @elseif($field['type'] == 'enum')
                                  <label>{{ ucfirst( str_replace('_',' ', $field['name']) ) }}</label>
                                  <select v-model="form.{{$field['name']}}" name="{{ $field['name'] }}" class="form-control" :class="{ 'is-invalid': form.errors.has( '{{$field['name']}}' ) }">
                                      @foreach($field["enumArray"] as $enumVal)
                                          <option> {{$enumVal}} </option>
                                      @endforeach
                                  </select>
                                  @if($field['required'] && $field['name'] !== 'id' && $field['name'] !== 'created_at' && $field['name'] !== 'updated_at')
                                      <has-error :form="form" field="{{$field['name']}}"></has-error>
                                  @endif
                                                        
                              @else
                                  <label>{{ ucfirst( str_replace('_',' ', $field['name']) ) }}</label>
                                  <input type="{{$field['simplified_type'] }}" v-model="form.{{$field['name']}}" class="form-control" :class="{ 'is-invalid': form.errors.has( '{{$field['name']}}' ) }"></input>
                                  @if($field['required'] && $field['name'] !== 'id' && $field['name'] !== 'created_at' && $field['name'] !== 'updated_at')
                                      <has-error :form="form" field="{{$field['name']}}"></has-error>
                                  @endif
                              @endif
                            </div>
                          @endforeach
                    </div><!-- Modal body ends -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form> <!-- Form Ends -->
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="{{$data['singular_lower']}}Detail" tabindex="-1" role="dialog" aria-labelledby="{{$data['singular_lower']}}Detail" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header"> <!-- Modal Header -->
                        <h5 class="modal-title" > {{$data['singular']}} Detail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-body">
                        <table class="table">
                          <tr class="row" v-for="(item, key, index) in clickedRow" v-if="!isSpecialColumn(key)">
                                <th class="text-primary col-4" style="text-align: right; margin-left:0em"> @{{ ucAllWords(key) }} </th><td class="col-8"> @{{ item }} </td>
                          </tr>
                        </table>
                    </div>    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" 
                        data-dismiss="modal" 
                        @click="delete{{$data['singular']}}(clickedRow.id)"><i class="fa fa-trash"></i> Delete </button>
                        <button type="button" class="btn btn-primary" 
                        data-dismiss="modal" 
                        @click="editModal(clickedRow)"><i class="fa fa-edit"></i> Edit</button>
                        <button type="button" class="btn btn-primary" 
                        data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                                                          
                    </div>
                </div>
            </div>
        </div>


  </section>
</template>


<script>
    import JsonExcel from "vue-json-excel";
    import VueHtml2pdf from "vue-html2pdf";

    export default {
        data () {
            return {
                editmode: false,
                {{$data['plural_lower']}} : [],
                search : '',

                isLoading: false,
                totalRecords: 0,
                clickedRow: null,
                selectedRows: [],

                serverParams: {
                  columnFilters: {
                  },
                  sort: [
                      @foreach($data['fields'] as $field)
                        @if( $field['name'] != 'id' && $field['name'] != 'created_at' && $field['name'] != 'updated_at' )
                          {"type" : "asc",
                          "field" : "{{$field['name']}}"},
                        @endif
                      @endforeach
                  ],
                  page: 1, 
                  perPage: 5,
                  searchTerm: '',
                },   
                     
                form: new Form({
                  @foreach($data['fields'] as $field)
                      "{{$field['name']}}" : "",
                  @endforeach
                }),
                
                table_heders: {
                  @foreach($data['fields'] as $field)
                    @if( $field['name'] != 'id' && $field['name'] != 'created_at' && $field['name'] != 'updated_at' )
                      "{{ucwords( str_replace("_", " ", $field['name']) )}}" : "{{$field['name']}}",
                    @endif
                  @endforeach
                },

                columns: [ 
                  @foreach($data['fields'] as $field)
                      { label : "{{ucwords( str_replace("_", " ", $field['name']) )}}",
                      field : "{{$field['name']}}",
                      @if( $field['name'] === 'id' || $field['name'] === 'created_at' || $field['name'] === 'updated_at' )
                        hidden : true},
                      @else
                        hidden : false},
                      @endif
                  @endforeach

                  {
                    label: 'Actions',
                    field: 'action',
                    sortable: false,

                  },

                ],

            };
        },


        components: {
          "json-excel":JsonExcel,
          "html-pdf":VueHtml2pdf,  
        },

                               
        methods: {

            {{$data['singular_lower']}}Detail(params){
              this.clickedRow = params.row;
              $('#{{$data['singular_lower']}}Detail').modal('show');
            },

            update{{$data['singular']}}(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/{{$data['singular_lower']}}/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');
                    this.load{{$data['plural']}}();
                })
                .catch(() => {
                    Toast.fire({
                          icon: 'error',
                          title: 'Some error occured!'
                      });
                    this.$Progress.fail();
                });
            },

            editModal({{$data['singular_lower']}}){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill({{$data['singular_lower']}});
            },

            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            delete{{$data['singular']}}(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        // Send request to the server
                         if (result.value) {
                                const theData = [id];
                                this.form.delete('api/{{$data['singular_lower']}}/'+JSON.stringify(theData) ).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'The {{$data['singular_lower']}} was deleted successfully.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.load{{$data['plural']}}();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },

            deleteSelected{{$data['plural']}}(){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete "+this.selectedRows.length+" records? You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        // Send request to the server
                         if (result.value) {
                                let theData = JSON.stringify(this.selectedRows);
                                this.form.delete('api/{{$data['singular_lower']}}/'+theData).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'The {{$data['singular_lower']}} was deleted successfully.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.load{{$data['plural']}}();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },
            

            create{{$data['singular']}}(){
                this.form.post('api/{{$data['singular_lower']}}')
                .then((response)=>{
                    $('#addNew').modal('hide');
                    Toast.fire({
                          icon: 'success',
                          title: response.data.message
                    });
                    this.$Progress.finish();
                    this.load{{$data['plural']}}();
                })
                .catch(()=>{
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured!'
                    });
                })
            },


            generatePDF() {
              this.$Progress.start();
              this.$refs.html2Pdf.generatePdf();
              this.$Progress.finish();
            },

            print () {
              this.$Progress.start();
              this.$htmlToPaper('printPaper');
              this.$Progress.finish();
            },
            

            updateParams(newProps) {
              this.serverParams = Object.assign({}, this.serverParams, newProps);
            },


            onSelectionChanged(params){
              this.selectedRows = [];
              for( var i=0; i< params.selectedRows.length; i++){
                if (params.selectedRows[i].vgtSelected)
                    this.selectedRows[i] = params.selectedRows[i].id;
              }
            },
            
            
            onPageChange(params) {
              this.updateParams({page: params.currentPage});
              this.load{{$data['plural']}}();
            },

            onPerPageChange(params) {
              this.updateParams({perPage: params.currentPerPage});
              this.load{{$data['plural']}}();
            },

            onSortChange(params) {
                var sortType = params[0].type;
                if(sortType != 'asc' && sortType != 'desc')
                  sortType = 'asc';
                this.updateParams({
                    sort: [{
                        type: sortType,
                        field: params[0].field,
                    }],
                });
                this.load{{$data['plural']}}();
            },


            onColumnFilter(params) {
              this.updateParams(params);
              this.load{{$data['plural']}}();
            },
            

            onSearch(params) {
              this.updateParams({searchTerm: params.searchTerm});
              this.load{{$data['plural']}}();
            },    


            toggleColumn( index, event ){
              // Set hidden to inverse of what it currently is
              this.$set( this.columns[ index ], 'hidden', ! this.columns[ index ].hidden );
            },


            // load items is what brings back the rows from server
            load{{$data['plural']}}() {
                this.$Progress.start();
                var parameters = "?perPage="+ this.serverParams.perPage;
                parameters = parameters + "&page="+ this.serverParams.page;
                parameters = parameters + "&sortField="+ this.serverParams.sort[0].field;
                parameters = parameters + "&sortType="+ this.serverParams.sort[0].type;
                parameters = parameters + "&searchTerm="+ this.serverParams.searchTerm;
                var url = "api/{{$data['singular_lower']}}"+parameters;
                //console.log(JSON.stringify( url));
                try{
                    this.form.get( url ).then( {{$data['singular_lower']}}  => {
                        if({{$data['singular_lower']}}.data.data){
                          this.totalRecords = {{$data['singular_lower']}}.data.data.total
                          this.{{$data['plural_lower']}} = {{$data['singular_lower']}}.data.data.data;
                        }
                    });
                } catch(error){
                  console.log(error.message);
                };
                this.$Progress.finish();
            },


            ucAllWords(words) {
              var separateWord = words.toLowerCase().split('_');
              for (var i = 0; i < separateWord.length; i++) {
                  separateWord[i] = separateWord[i].charAt(0).toUpperCase() +
                  separateWord[i].substring(1);
              }
              return separateWord.join(' ');
            },


            isSpecialColumn(field){
              if(field != 'id' && field != 'updated_at' && field != 'created_at' 
                   && field != 'vgt_id' && field != 'vgtSelected' && field != 'originalIndex' ) 
                    return false;
                   else
                    return true;
            }

        },


        mounted() {
            //console.log('{{$data['singular']}} Component mounted.')
            this.$Progress.start();
            this.load{{$data['plural']}}();
            this.$Progress.finish();

        },


        created() {
            this.$Progress.start();
            this.load{{$data['plural']}}();
            this.$Progress.finish();
            
        },


        computed: {},

        
    }


</script>

<style>
@media screen and (min-width: 990px) {
  #printPaper {
    margin-left: -22.2%;
    padding: 0% 1% 3% 1%;
    width: 100%;
  }
}
</style>



