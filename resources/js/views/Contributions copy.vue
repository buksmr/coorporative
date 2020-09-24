<template>
   <section class="hero is-fullheight is-default is-bold">
      <layout-header></layout-header>
      <div class="container breadcrums1">
         <h6 class="form-name is-uppercase is-pulled-left is-size-6">List Contributions</h6>
         <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
            <ul>
               <li class="is-size-7">
                  <a class="has-text-grey" href="#">Pages</a>
               </li>
               <li class="is-size-7">
                  <a class="has-text-grey" href="#">Users</a>
               </li>
               <li class="is-active is-size-7">
                  <a href="#" aria-current="page">Category</a>
               </li>
            </ul>
         </nav>
      </div>
      <section class="container forms-sec has-background-white box is-clearfix">
        
        <div class="field invo1 has-addons is-pulled-right ">
           <div>
            <b-field grouped group-multiline>
            <b-select  v-model="company_name" >
            <option value = "0"> All Company</option>
             <option v-for="(companies,index) in companyquote"  v-bind:value="companies.id" :key="index">
                          {{companies.company_name}}
                             </option>
       
         </b-select>
         </b-field>
         </div>

           <div>
            <b-field grouped group-multiline>
            <b-select  v-model="status" >
            <option value = "0"> All Status</option>
         <option v-for="(quotes_status_master,index) in statuses" v-bind:value="quotes_status_master.quotesstatus" :key="index">
                                       {{ quotes_status_master.quotesstatus }}
                                    </option>
         </b-select>
         </b-field>
         </div>
           <!-- <div>
              <b-field grouped group-multiline>
                 </b-field>
              </div> -->
         
            <div class="control has-icons-left is-hidden-mobile">
               <input type="text" class="input is-info is-small" v-model="search" placeholder="Filter.....">
               <span class="icon is-medium is-left">
                  <i class="fa fa-futbol-o"></i>
               </span>
            </div>
            <div class="control is-hidden-mobile">
               <a class="button is-info is-small has-background-grey-darker" @click="filter">Search</a>
            </div>
            <a @click="isImageModalActive = true" class="has-background-grey-darker has-text-white is-uppercase is-size-7 addform">Add</a>
         </div>

     
         <b-field grouped group-multiline>
            <b-select v-model="perPage" :disabled="!isPaginated">
               <option value="5">5 per page</option>
               <option value="10">10 per page</option>
               <option value="15">15 per page</option>
               <option value="20">20 per page</option>
            </b-select>
            <div class="control is-flex">
               <b-switch v-model="isPaginated" class="is-size-7 is-uppercase">Paginated</b-switch>
            </div>
         </b-field>

        <b-table class="table is-bordered is-striped is-fullwidth search-table inner" 
         :data="filteredCustomers"
         :paginated="isPaginated" 
         :per-page="perPage" 
         :current-page.sync="currentPage" 
         :pagination-simple="isPaginationSimple"
         :default-sort-direction="defaultSortDirection" default-sort="">
            <template slot-scope="props">
               <b-table-column field="id" class="is-size-7-mobile" label="ID" width="40" sortable numeric      >{{props.index + 1 }}
               </b-table-column>

               <b-table-column field="invoice_number" class="is-size-7-mobile" label=" Contribution No" sortable>
                  {{ props.row.payment_no }}
               </b-table-column>
               <b-table-column field="payment_date" class="is-size-7-mobile" label="Date" sortable>
                  {{formatter(props.row.payment_date)  }}
               </b-table-column>
               <b-table-column field="payment_amount" class="is-size-7-mobile" label="Payment" sortable>
                 {{ props.row.currencies_id}}  {{props.row.payment_amount}}
               </b-table-column>
               <b-table-column field="client_name" class="is-size-7-mobile" label="ClientName" sortable>
                  {{ props.row.client_name}}
               </b-table-column>

               
              
               <b-table-column field="paymenttype_id" class="is-size-7-mobile" label="Payment Method" sortable>
   
                            {{props.row.paymenttype_id}}
               </b-table-column>
                 <b-table-column 
                  field="note"
                  class="is-size-7-mobile"
                  label="notes"
                  sortable
                  >{{ props.row.note }}</b-table-column>

               <!-- <b-table-column field="Options" class="is-size-7-mobile" label="Options" sortable> -->
                
                  <!-- <b-dropdown hoverable>
                     <button class="button is-small has-background-grey-darker has-text-white" slot="trigger">
                        <span>Options</span>
                        <i class="fas fa-caret-down drops"></i>
                     </button>
                     <b-dropdown-item >
                   <router-link :to="{name: 'frontofficeform', query: { id : props.row.quotes_id, edit: true }}" class="has-text-dark">
                        <i class="fas fa-edit icon1" ></i> Edit 
                        </router-link>
                     </b-dropdown-item>
                     <b-dropdown-item @click="goToDelete(props.row.quotes_id)">
                        <i class="fas fa-trash-alt icon1"></i>Delete
                     </b-dropdown-item>
                     <b-dropdown-item @click="savefiles(props.row.quotes_id)">
                        <i class="fas fa-download icon1" ></i>Download PDF
                     </b-dropdown-item>
                           <b-dropdown-item v-if="props.row.status != 'Draft' && props.row.status != 'Cancelled'">
                     
                        <a  @click="isImageModalActive1=true;  payment(props.row.quotes_id, props.row.balance_amount)"  class="has-text-dark">
                        <i class="fas fa-credit-card icon1"></i>Payments
                        </a>
                     </b-dropdown-item>
                     
                        <b-dropdown-item v-if="props.row.status != 'Draft' && props.row.status != 'Cancelled'">
                     
                   <router-link :to="{name: 'purchasepayments', query: { id : props.row.quotes_id, edit: false }}" class="has-text-dark">
                        <i class="fas fa-edit icon1" ></i> View Payment 
                        </router-link>
                     </b-dropdown-item>
                     
                  </b-dropdown> -->
            <!-- </b-table-column> -->
            </template>
           
            <template slot="empty">
          <h4>
            <center>{{"No record found"}}</center>
          </h4>
        </template>
         
         </b-table>
            
      </section>


      <template>
         <section>
            <form id="apps" @submit.prevent="handlesubmit" validate>
               <b-modal :active.sync="isImageModalActive " :width="340">
                  <div class="card section sect">
                     <h4 class="has-text-grey-dark	is-uppercase is-size-6 has-text-centered	">Register Contributors</h4>

                     <p class="bd-notification is-info">
                        <label>Client
                        </label>
                        <div class="field">
                           <b-autocomplete name="client_id" v-model="client_id" field="title" :loading="isFetching"
                              @typing="getAsyncData" :class="{ 'is-invalid': submitted && errors.has('client_id') }"
                              :data="filteredDataArray" placeholder="client" icon="magnify" @select="cool">
                              <template slot="empty">No results found</template>
                           </b-autocomplete>

                         
                     </p>
                  </div>

                                                      <p class="bd-notification is-info">
                     <label>Amount Contributed <span class="has-text-danger">*</span>
                     </label>

                   <div class="field">
                        <p class="control has-icons-left has-icons-right">
                           <input class="input" type="text"  placeholder="Amount" id="amount" v-model="contributor.amount" name="amount" v-validate="'required|numeric'"
                            >
                       
                        </p>
                                     <!-- <span v-show="errors.has('amount')" class="invalid-feedback">{{errorAmount}}</span>
                                     <span v-if="balanceError != null" class="invalid-feedback"> {{ balanceError }} </span>
                  -->
                     </div>
                         </p>
               
                  <p class="bd-notification is-info">
                     <label>Date <span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                           <b-datepicker id="paymentdate" name="paymentdate" v-model="contributor.paymentdate" v-validate="'required'"
                            :date-formatter="formatDate"
                          placeholder="enter date..." icon="calendar-today">
                           </b-datepicker>
                        </p>
                     </div>
                  </p>


                     <p class="bd-notification is-info">
                        <label>Payment Method <span class="has-text-danger">*</span> </label>
                     <div class="field has-addons">
                        <div class="control is-expanded">
                           <div class="select is-fullwidth">
                              <select name="paymentmethod" id="paymentmethod" v-model="contributor.paymentmethod"  v-validate="'required'"
                                 >
                                 <option v-for="(paymentmethod) in name" v-bind:value="paymentmethod.id" :key="paymentmethod.id">
                                    {{paymentmethod.name}}
                                 </option>
                              </select>
                              <span
                                        v-show="errors.has('paymentmethod')"
                                        class="invalid-feedback"
                                        >Field is required</span
                                    >
                           </div>
                        </div>
                     </div>
                     </p>

                     <p class="bd-notification is-info">
                        <label>Notes
                        </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                           <textarea placeholder="Notes 
                              " class="textarea is-small is-shadowless" id="paymentnotes" v-model="contributor.paymentnotes" name="paymentnotes"  
                              :class="{ 'is-invalid': submitted && errors.has('paymentnotes') }"></textarea>                             
                        </p>
                     </div>
                     </p>



                
                  <button class="button is-dark is-small is-pulled-right">Submit</button>
             <button type="button" @click="isImageModalActive = false" class="button has-background-light is-small is-pulled-right clearbuton"  >Cancel</button>   
           

                  </div>
                  
               </b-modal>
                  
            </form>
         
         </section>
      </template>
      <layout-footer></layout-footer>
   </section>
</template>
<script>
   import { mapState, mapActions, mapGetters } from "vuex";
   import LayoutHeader from "./layouts/Header.vue";
   import LayoutFooter from "./layouts/Footer.vue";
   import moment,{ defaultFormat } from 'moment';
     import { Validator } from "vee-validate";

    const formatte = new Intl.NumberFormat('en-US', {

  minimumFractionDigits: 2
})

   export default {
      components: {
         LayoutHeader,
         LayoutFooter,
         moment
      },
      

      data() {
         return {
            system:"",
            systemValue:"",
            systemquantities : "",
            isImageModalActive: false,
            isCardModalActive: false,
             isImageModalActive1: false,
            search: "",
            paymentSymbol: '',
            balanceError: null,
            paymentBalance: 0,
            paymentmethod: "",
            // status:"",
            quotes_id: "",
            client_id: "",
            currencysymbol: "",
            currencyplace:"",
            expDate:"",
            myDate: '',
            newDay: '',
            expires_on: '',
            company_name:"0",
            status:"0",
            decimal:"",
            thousands:"",
            placement:"",
            // data:[],
            statuses:[],
            client:[],
            quoteclient:[],
            companyquote: [],
            quotedata:[],
            id:[],
            ggg: -1,
            return_id: '',
             return_amount: '',
            // quotesData:[],
                        item_payment: {
                client_id: "",
                date: new Date(),
                company:"",
                invoice_template: "SystimaNX balde.pdf",
                amount: "",
                paymentdate: new Date(),
                paymentmethod: "",
                paymentnotes: "", 
                payment_no: "",
            },
            
            contributor: 
               {
                  amount: "",
                  paymentdate: new Date(),
                  client_id: "",
                  paymentnotes: "",
                   paymentmethod: "",
               },
            
             quoteform: 
            {
               quote_no: "",
               date: "",
               expires_on: "",
               client_name: "",
               totalamount: "",
               quotesstatus: "",
            },
         name:[],

         filterAmount:[],
           
            submitted: false,
            editMode: false,


            required: "date required",
            requiredclient: "select client",
            requireddate: "select date",

           // data: [],

            isPaginated: true,
            isPaginationSimple: false,
            defaultSortDirection: "asc",
            currentPage: 1,
            perPage: 5,
            isFetching: false,
            dateoptions : { year: 'numeric', month: 'numeric', day: 'numeric' },

            validamount: "Amount is required",
         numberAllowed: "Only numeric value accepted",
      
         };
      },



      computed: {

           ...mapState({
      loading: state => state.isLoading,
      userState: state => state.user
    }),

            errorAmount(){
               this.balanceError = null;
       if(this.item_payment.amount == ""){

 return this.validamount
       }
        
          if(isNaN(this.item_payment.amount)){

 return this.numberAllowed
          }
        
          if (Number(this.item_payment.amount) > Number(this.paymentBalance)) {
               return this.balanceError = "Amount is larger"
                
          }
    },

      filteredDataArray() {


          
        return this.quoteclient.filter((option) => {
               return option
                  .toString()
                  .toLowerCase()
                  .indexOf(this.client_id.toLowerCase()) >= 0
                  
            })
          },


      

   
      filteredCustomers: function() {
        
     var self = this;
      // if(self.search != null && self.search != undefined && self.search != ''){
         return this.quotedata.filter(function(cust) {
            
         return (
            cust.payment_no.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0 ||
            cust.payment_date.toLocaleString().indexOf(self.search.toLowerCase()) >=
               0 ||
                cust.client_name.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0 ||
              cust.paymenttype_id.toLowerCase().indexOf(self.search.toLowerCase()) >= 0 ||
                cust.quotesstatus.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0
         );
         });
     }
      },     
     
      mounted: function () {


        this.isImageModalActive = this.$route.query.popup ? true : false;
        console.log("popupstatus",this.$route.query);
        this.client_id = this.$route.query.name ? this.$route.query.name : '';
   
        this.fetchquotes();
  
   
         this.paymentMethod();
      
       
      
      //  this.dateDisplay();
      },

      

      methods: {

          ...mapActions({
      setLoading: "setLoading"
    }),
         

    
     filter(){
          this.setLoading(true);
      const input={
         Company:this.company_name,
         Status:this.status
      }
         let url = '/filterquotes'; 
            axios.post(url,input)
                .then((response) => {
                   this.quotedata = response.data.quotesviews;
                           this.setLoading(false);
                 
                });
         
     },
     

      paymentMethod() {
           this.setLoading(true);
            let uri = '/PaymentMethod';
              axios.get(uri).then((response) => {
               this.name = response.data.payment_methodDetails;
               this.contributor.paymentmethod = this.name[0].id;
            })
             .finally(() => {
                this.setLoading(false);
              });
        },
     


     
      getstatus(){
      //   alert(1);
        this.setLoading(true);
         let url = '/listStatus';
            axios.get(url).then(response => {
             this.statuses = response.data.statusDetails;
          
            })
            .finally(() => {
                this.setLoading(false);
              });
     },


     
     
         formatter(date){
           return moment(new Date(date)).format(this.systemValue);
         },


    
         formatDate(date){
          //  console.log("dataeformat",dt.toLocaleDateString(this.system));
          //  return dt.toLocaleDateString(this.system);
              return moment(new Date(date)).format(this.systemValue);
              
         },
       
        clear() {
            this.isImageModalActive = false;

        },

        cancelmodal(){

           
            this.isImageModalActive1 = false;
this.setLoading(false);
        },
      

       fetchquotes() {
         //  alert(1);
          this.setLoading(true);
            let url = '/Contributors';
            axios.get(url).then(response => {
               console.log(response);
            this.quotedata = response.data.payment_Details;
            console.log( this.quotedata,"quotedata");
          })
          .finally(() => {
                this.setLoading(false);
              });
      },





        


           handlesubmit() {
            let context = this;
            context.submitted = true;

 
            if (context.balanceError == null) {

              


              
               
                context.$validator.validate().then(valid => {
                    if (valid) {
                      
                     this.isImageModalActive = false;
                      
                        let url = '/Contributors';
                        let config = {
                            headers: {
                                Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                                'content-type': 'multipart/form-data'
                            }
                        }
                        let formdata = new FormData();

                        if (context.contributor.paymentdate != '') {
                            var dateObj = new Date(context.item_payment.paymentdate);
                             var momentObj = moment(dateObj);
                              var momentString = momentObj.format('YYYY-MM-DD');
                      
                                formdata.append('paymentdate', momentString);
                        }
                        formdata.append('client_id', this.return_id);
                        formdata.append('paymentmethod', context.contributor.paymentmethod);
                               formdata.append('payment_amount', context.contributor.amount);
                     
                          formdata.append('paymentnotes', context.contributor.paymentnotes);
                         formdata.append('payment_no', 'CB'+Date.now().toString().substr(4, 9));
                          axios.post(url, formdata, config).then(response => {
                            if (response.data.status == 1) {
                               
              
                                        
                                   context.$buefy.toast.open({
                                    duration: 4000,
                                     message: response.data.message,
                                      type: "is-success",
                                       position: "is-top-right"
                                });
                                
                                  this.fetchquotes();
                            } else {
                                context.$buefy.toast.open({
                                 message: response.data.message,
                                  position: "is-top-right",
                                   type: "is-danger"
                                });
                               
                            }
                            
                            
                        })

                        .finally(() => {
                                context.setLoading(false);
                                 
                       });
                              
                    }else{
                   context.setLoading(false);
                    }
                });
            }
        },


        

         

         getAsyncData() {
           // alert(0);
            this.setLoading(true);
            if (Object.keys(this.client_id).length > 1) {
               this.isFetching = true
               let url = '/autoComplete'
               
               axios.get(url,{query: {client_name: this.client_id}}).then(response => {
                 
              
                 this.quoteclient = response.data.autocomplete;
                  this.id = response.data.id;
                  this.filterAmount = response.data.amount;

             


                 
             
               })
                  .catch((error) => {
                     this.quoteclient = []
                     throw error
                  })
                 .finally(() => {
                this.setLoading(false);
              });
            }

         },

         cool(selected){

console.log(selected)
       var  productIndex = -1
var client = this.client_id
                       this.quoteclient.forEach(function (value, index) {
                  if (value === selected) {
                     productIndex = index;
                       

                    
                  }
               });
  this.return_id = this.id[productIndex]
    this.contributor.amount = this.filterAmount[productIndex]


                  


         },

           goToDelete(id) {
          this.$buefy.dialog.confirm({
            message: 'Are you sure you want to delete this record?',
            onConfirm: () => this.destroy(id)
          })
        },

        view_payment(id){




        },
    
    destroy(id){
        this.setLoading(true);
      let url = '/Quotes/' ;
  //    console.log("URL",url);
      
        axios.delete(url+id)
        .then(resp =>{
          if (resp.data.status == 1) {
                      this.$buefy.toast.open({
                      duration: 4000,
                      message: resp.data.message,
                      title: "deleted successfully",
                      type: 'is-success',
                      position: "is-top-right",
                    });
                   // this.fetchquotes();
                    // alert('success');
                    // this.user = '';
                      window.location.href = "";
                  }
                  else {
                      this.$buefy.toast.open({
                      duration: 1000,
                      message: resp.data.message,
                      title: "deleted failed",
                      position: "is-top-right",
                      type: 'is-danger'
                    });
                    // alert('failed');
                  }
          //  alert(resp.data.message);
          // this.fetchuser();
        })
        .catch(error => {
            console.log(error);
          })
          .finally(() => {
                this.setLoading(false);
              });
      
    },
   
         savequote(e) {
            // alert(124);

                   this.submitted = true;
                    this.setLoading(true);
                   this.$validator.validate().then(valid => {
                  if (valid) {

                     this.$router.push({ name: 'frontofficeform', query: { client_id: this.return_id, edit:false } });

                  }
               });
            }

            
         },

         

       
                 
                  

    }

    
      
  

</script>
<style>
   .sect {
      padding: 3rem 1.5rem;
      padding-top: 2rem !important;
   }
</style>