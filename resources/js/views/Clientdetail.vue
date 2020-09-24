<template>
    <section class="hero is-fullheight is-default is-bold">
        <layout-header></layout-header>

        <div class="container breadcrums1">
            <h6 class="form-name is-uppercase is-pulled-left is-size-6">
                Client Details
            </h6>


            <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
                <ul>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Pages</a>
                    </li>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Users</a>
                    </li>
                    <li class="is-size-7 is-active">
                        <a class href="#" aria-current="page">Offers</a>
                    </li>
                </ul>
            </nav>
        </div>

        <section
            class="container forms-sec has-background-white box detsection2"
        >
            <b-tabs>
                <b-tab-item label="Details" class="det-list">
                    <h3>Details</h3>
                    <div class="columns">
                        <div class="column">
                            <ul>
                                <li>Client Name <span>: {{client.client_name}}</span></li>
                                <li>Email <span>: {{client.email}}</span></li>
                                <li>City <span>: {{client.city}}</span></li>
                                <li>State <span>: {{client.state}}</span></li>
                                <li>Postal Code <span>: {{client.postal_code}}</span></li>
                                <li>Country <span>: {{client.country}}</span></li>
                                <li>Phone Number <span> : {{client.phone_number}}</span></li>
                            </ul>
                        </div>
                        <div class="column">
                            <ul>
                                <li>Fax Number <span>: {{client.fax_number}}</span></li>
                                <li>Office Units <span>: {{client.office_unit}}</span></li>
                                <li>
                                    Amount Contributed
                                    <span>: {{client.amount}}</span>
                                </li>
                                <!-- <li>Default Currency <span>: {{client.default_currency}}</span></li> -->
                                <li>Tax Number <span> : {{client.tax_code}}</span></li>
                                <li>
                                    Address
                                    <span>: {{client.address}}</span>
                                </li>
                                <!-- <li>Status <span>: {{client.status}}</span></li> -->
                            </ul>
                        </div>
                    </div>
                </b-tab-item>

                <b-tab-item label="Contributions">
                    <section class=" is-clearfix">
                        <div class="field has-addons is-pulled-right ">
                            <div
                                class="control has-icons-left is-hidden-mobile"
                            >
                                <input
                                    type="text"
                                    class="input is-info is-small"
                                    v-model="search"
                                    placeholder="Filter..."
                                />
                                <span class="icon is-medium is-left">
                                    <i class="fa fa-futbol-o"></i>
                                </span>
                            </div>
                            <div class="control is-hidden-mobile">
                                <a
                                    class="button is-info is-samll has-background-grey-darker"
                                    >Search</a
                                >
                            </div>
                            <!-- <a
                                @click="isImageModalActive = true"
                                class="has-background-grey-darker has-text-white is-uppercase is-size-7 addform"
                                >Add</a
                            > -->
                        </div>
                        <b-field grouped group-multiline>
                            <b-select
                                v-model="perPage"
                                :disabled="!isPaginated"
                            >
                                <option value="5">5 per page</option>
                                <option value="10">10 per page</option>
                                <option value="15">15 per page</option>
                                <option value="20">20 per page</option>
                            </b-select>
                            <div class="control is-flex">
                                <b-switch
                                    v-model="isPaginated"
                                    class="is-size-7 is-uppercase"
                                    >Paginated</b-switch
                                >
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

            </template>
           
            <template slot="empty">
          <h4>
            <center>{{"No record found"}}</center>
          </h4>
        </template>
         
         </b-table>
                    </section>
                </b-tab-item>

                <b-tab-item label="Loans">
                    <section class="is-clearfix">
                        <div class="field has-addons is-pulled-right ">
                            <div
                                class="control has-icons-left is-hidden-mobile"
                            >
                                <input
                                    type="text"
                                    class="input is-info is-small"
                                    v-model="search"
                                    placeholder="Filter....."
                                />
                                <span class="icon is-medium is-left">
                                    <i class="fa fa-futbol-o"></i>
                                </span>
                            </div>
                            <div class="control is-hidden-mobile">
                                <a
                                    class="button is-info is-samll has-background-grey-darker"
                                    >Search</a
                                >
                            </div>
                            <!-- <a
                                @click="isImageModalActive = true"
                                class="has-background-grey-darker has-text-white is-uppercase is-size-7 addform"
                                >Add</a
                            > -->
                        </div>
                        <b-field grouped group-multiline>
                            <b-select
                                v-model="perPage"
                                :disabled="!isPaginated"
                            >
                                <option value="5">5 per page</option>
                                <option value="10">10 per page</option>
                                <option value="15">15 per page</option>
                                <option value="20">20 per page</option>
                            </b-select>
                            <div class="control is-flex">
                                <b-switch
                                    v-model="isPaginated"
                                    class="is-size-7 is-uppercase"
                                    >Paginated</b-switch
                                >
                            </div>
                        </b-field>
        <b-table class="table is-bordered is-striped is-fullwidth search-table inner" 
         :data="filteredLoan"
         :paginated="isPaginated" 
         :per-page="perPage" 
         :current-page.sync="currentPage" 
         :pagination-simple="isPaginationSimple"
         :default-sort-direction="defaultSortDirection" default-sort="">
            <template slot-scope="props">
               <b-table-column field="id" class="is-size-7-mobile" label="ID" width="40" sortable numeric      >{{props.index + 1 }}
               </b-table-column>

               <b-table-column field="loan_no" class="is-size-7-mobile" label=" Loan No" sortable>
                  {{ props.row.loan_no }}
               </b-table-column>
               <b-table-column field="date" class="is-size-7-mobile" label="Booking Date" sortable>
                  {{formatter(props.row.date)  }}
               </b-table-column>
               <b-table-column field="amount" class="is-size-7-mobile" label="Loan Amount" sortable>
                 {{props.row.currencies_placement==0 ? props.row.currencies_id : ''}}  {{props.row.amount}}
               </b-table-column>
                  <b-table-column field="paid_amount" class="is-size-7-mobile" label="Paid Amount" sortable>
                 {{props.row.currencies_placement==0 ? props.row.currencies_id : ''}}  {{props.row.paid_amount}}
               </b-table-column>
               <b-table-column field="client_name" class="is-size-7-mobile" label="ClientName" sortable>
                  {{ props.row.client_name}}
               </b-table-column>

                  <b-table-column field="expiredate" class="is-size-7-mobile" label="DueDate" sortable>
                  {{ formatter(props.row.expiredate)}}
               </b-table-column>

               

               
              
               <b-table-column field="phone_number" class="is-size-7-mobile" label="Phone Number" sortable>
   

                  {{props.row.phone_number}}


               </b-table-column>
                 <b-table-column 
                  field="quotesstatus"
                  class="is-size-7-mobile"
                  label="status"
                  sortable
                  >{{ props.row.status }}</b-table-column>

               <!-- <b-table-column field="Options" class="is-size-7-mobile" label="Options" sortable>
                
                  <b-dropdown hoverable>
                     <button class="button is-small has-background-grey-darker has-text-white" slot="trigger">
                        <span>Options</span>
                        <i class="fas fa-caret-down drops"></i>
                     </button>
                     <b-dropdown-item >
                   <router-link :to="{name: 'frontofficeform', query: { id : props.row.loan_id, edit: true }}" class="has-text-dark">
                        <i class="fas fa-edit icon1" ></i> Edit 
                        </router-link>
                     </b-dropdown-item>
                     <b-dropdown-item @click="goToDelete(props.row.loan_id)">
                        <i class="fas fa-trash-alt icon1"></i>Delete
                     </b-dropdown-item>
                     <b-dropdown-item @click="savefiles(props.row.loan_id)">
                        <i class="fas fa-download icon1" ></i>Download PDF
                     </b-dropdown-item>
                           <b-dropdown-item v-if="props.row.status != 'Draft' && props.row.status != 'Cancelled'">
                     
                        <a  @click="isImageModalActive1=true;  payment(props.row.loan_id, props.row.balance_amount)"  class="has-text-dark">
                        <i class="fas fa-credit-card icon1"></i>Payments
                        </a>
                     </b-dropdown-item>
                     
                        <b-dropdown-item v-if="props.row.status != 'Draft' && props.row.status != 'Cancelled'">
                     
                   <router-link :to="{name: 'loanpayments', query: { id : props.row.loan_id, edit: false }}" class="has-text-dark">
                        <i class="fas fa-edit icon1" ></i> View Payment 
                        </router-link>
                     </b-dropdown-item>
                     
                  </b-dropdown>
               </b-table-column> -->
            </template>
           
            <template slot="empty">
          <h4>
            <center>{{"No record found"}}</center>
          </h4>
        </template>
         
         </b-table>
                    </section>
                </b-tab-item>

                <b-tab-item label="Purchases">
                    <section class="is-clearfix">
      <div class="field has-addons is-pulled-right ">
        <div class="control has-icons-left is-hidden-mobile">
          <input
            type="text"
            class="input is-info is-small"
            v-model="search"
            placeholder="Filter....."
          />
          <span class="icon is-medium is-left">
            <i class="fa fa-futbol-o"></i>
          </span>
        </div>
        <div class="control is-hidden-mobile">
          <a class="button is-info is-samll has-background-grey-darker"
            >Search</a
          >
        </div>
      </div>

      <b-field grouped group-multiline>
        <b-select v-model="perPage" :disabled="!isPaginated">
          <option value="5">5 per page</option>
          <option value="10">10 per page</option>
          <option value="15">15 per page</option>
          <option value="20">20 per page</option>
        </b-select>

         <div class="control is-flex">
          <b-switch v-model="isPaginated" class="is-size-7 is-uppercase"
            >Paginated</b-switch
          >
        </div>
      </b-field> 

           <b-table class="table is-bordered is-striped is-fullwidth search-table inner" 
         :data="filteredpurchase"
         :paginated="isPaginated" 
         :per-page="perPage" 
         :current-page.sync="currentPage" 
         :pagination-simple="isPaginationSimple"
         :default-sort-direction="defaultSortDirection" default-sort="">
            <template slot-scope="props">
               <b-table-column field="id" class="is-size-7-mobile" label="ID" width="40" sortable numeric      >{{props.index + 1 }}
               </b-table-column>

               <b-table-column field="quote_no" class="is-size-7-mobile" label=" Quote No" sortable>
                  {{ props.row.quote_no }}
               </b-table-column>
               <b-table-column field="date" class="is-size-7-mobile" label="Date" sortable>
                  {{formatter(props.row.date)  }}
               </b-table-column>
               <b-table-column field="payed_amount_sum" class="is-size-7-mobile" label="Payment" sortable>
                 {{props.row.currencies_placement==0 ? props.row.currencies_id : ''}}  {{props.row.payed_amount_sum}}
               </b-table-column>
               <b-table-column field="client_name" class="is-size-7-mobile" label="ClientName" sortable>
                  {{ props.row.client_name}}
               </b-table-column>

                  <b-table-column field="due_date" class="is-size-7-mobile" label="DueDate" sortable>
                  {{ formatter(props.row.due_date)}}
               </b-table-column>

               
              
               <b-table-column field="total_amount" class="is-size-7-mobile" label="Total Amount" sortable>
   
              <!-- {{props.row.currencies_placement==0 ? props.row.currencies_id : ''}}  {{(props.row.totalamount).toFixed(props.row.currencies_decimal).toString().replace(/\B(?=(\d{3})+(?!\d))/g, props.row.currencies_thousands)}} {{props.row.currencies_placement==1 ? props.row.currencies_id : ''}}
    -->
    {{props.row.currencies_placement==0 ? props.row.currencies_id : ''}}  {{(props.row.totalamount)}}
               </b-table-column>
                 <b-table-column 
                  field="quotesstatus"
                  class="is-size-7-mobile"
                  label="status"
                  sortable
                  >{{ props.row.quotesstatus }}</b-table-column>

               <!-- <b-table-column field="Options" class="is-size-7-mobile" label="Options" sortable>
                
                  <b-dropdown hoverable>
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
                     
                  </b-dropdown>
               </b-table-column> -->
            </template>
           
            <template slot="empty">
          <h4>
            <center>{{"No record found"}}</center>
          </h4>
        </template>
         
         </b-table>
    </section> 
                </b-tab-item>
            </b-tabs>
        </section>
         <template>
         <section>
            <form id="app" @submit.prevent="handlesubmit" validate>
               <b-modal :active.sync="isImageModalActive2" :width="340">
                  <div class="card section sect">
                     <h4 class="has-text-grey-dark	is-uppercase is-size-6 has-text-centered	">Enter Payment</h4>
                     <p class="bd-notification is-info">
                        <!-- quick -->
                        <label>Amount Balance:  {{paymentBalance }} 
                        </label>
                           </p>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                           <input class="input" type="text"  placeholder="Amount" id="amount" v-model="invoice.amount" name="amount" v-validate="'required'"
                              :class="{ 'is-invalid': submitted && errors.has('amount') }">
                        </p>
                     </div>
                  
                     <p class="bd-notification is-info">
                        <label>Payment Date <span class="has-text-danger">*</span>
                        </label>   </p>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                           <b-datepicker
                              placeholder="06/12/2019"
                              icon="calendar-today"  
                              id="paymentdate" 
                              v-model="invoice.paymentdate"
                              :date-formatter="formatDate" 
                              name="paymentdate" 
                              v-validate="'required'"
                              :class="{ 'is-invalid': submitted && errors.has('paymentdate') }">
                           </b-datepicker>
                        </p>
                        <span v-show="errors.has('paymentdate')" class="invalid-feedback">Field is required</span>
                     </div>
                   
                     <p class="bd-notification is-info">
                        <label>Payment Method <span class="has-text-danger">*</span> </label>
                     <div class="field has-addons">
                        <div class="control is-expanded">
                           <div class="select is-fullwidth">
                              <select name="paymentmethod" id="paymentmethod" v-model="invoice.paymentmethod"  v-validate="'required'"
                                 :class="{ 'is-invalid': submitted && errors.has('paymentmethod') }">
                                 <option v-for="paymentmethod in name"  v-bind:value="paymentmethod.id" :key="paymentmethod.id">
                                    {{paymentmethod.name}} 
                                 </option>
                              </select>
                              <span
                                 v-show="errors.has('paymentmethod')"
                                 class="invalid-feedback"
                                 >Paymentmethod is required</span>
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
                              " class="textarea is-small is-shadowless" id="paymentnotes" v-model="invoice.paymentnotes" name="paymentnotes" 
                              :class="{ 'is-invalid': submitted && errors.has('paymentnotes') }"></textarea>                             
                        </p>
                     </div>
                     </p>
                     <span v-if="balanceError != null"> {{ balanceError }} </span>
                     <button class="button is-dark is-small is-pulled-right"
                        >Submit</button>
                     <button2 class="button has-background-light is-small is-pulled-right clearbuton" @click="isImageModalActive2 = false" >Cancel</button2>
                  </div>
               </b-modal>
            </form>
         </section>
      </template>

        <layout-footer> </layout-footer>
       
    </section>
</template>

<script>
import { mapState, mapActions, mapGetters } from "vuex";
import LayoutHeader from "./layouts/Header.vue";
import LayoutFooter from "./layouts/Footer.vue";
import moment from 'moment'
export default {
    components: {
        LayoutHeader,
        LayoutFooter,
        moment
    },
    data() {
        return {
            system:"",
            systemDate:"",
            isImageModalActive: false,
            isImageModalActive2: false,
            isImageModalActive3: false,
            isImageModalActive4: false,
            submitted : false,
            viewpurchasedetails : [],

            paymentSymbol: '',
            balanceError: null,
            paymentBalance: 0,

            isCardModalActive: false,
             
           systemquantities: [],
           systemtaxrounding:[],
            bind_file_name: [],
            copy: '',
            subtotal: 0,
            discount: 0,
            taxtot: 0,
            wholetotal:0,
            index: 0,
           
          
            currency: [],
            note: [],
            bindfile: [],
            search: "",
            data: [],

          paymentdetails:{
          client_name: "",
          payment_date:"",
          invoice_number:"",
          date:"",
          payment_amount:"",
          paymenttype_id:"",
        },
       
      client:
        {
          client_name: '',
          email: '',
          address: '',
          city: '',
          state: '',
          postal_code: '',
          country: '',
          phone_number: '',
          office_unit: '',
          fax_number: '',
          amount: '',
          default_currency: '',
          tax_code: '',
          status: '',

        },

        loandetails:[],
        loanpaymentdetail : [],
          
             quotesdetails: 
            {
               quote_no: "",
               date: "",
               expires_on: "",
               client_name: "",
               total_amount: "",
               quotesstatus: "",
            },
            invoicedetails: {
                invoice_number: "",
                total_amount: "",
                paid: "",
                date: "",
                expires_on: "",
                currency_id: "",
                invoicestatus: "",
            },
          quoteform:
            {
               file_name: [],
               quote_no: "",
               date: "",
               expires_on: "",
               currencies_id: "",
               discountpercentage: 0,
               download_path: "",
               terms: "",
               footer: "",
               quotes_status_id: "",
            },
        company:
            {
               company_name:'',
               address:'',
               phone:'',
               city: '',
               state: '',
               country: '',
               phone: '',
               zipcode:'',
               website: '',
            },
             invoice: {
                client_id: "",
                date: new Date(),
                company:"",
                amount: "",
                invoice_template: "SystimaNX balde.pdf",
                paymentdate: new Date(),
                paymentmethod: "",
                paymentnotes: "", 
            },

            
           
            details: [],
            statuses: [],
            selectedproduct: [],
            invoicedetail:[],
            contribution_details:[],
            paymentdetail:[],
            isPaginated: true,
            name:[],
            isPaginationSimple: false,
            defaultSortDirection: "asc",
            currentPage: 1,
            perPage: 5
        };
    },

     mounted: function () {
       this.customFormatter();
      if (this.$route.query.id != '' && this.$route.query.id != null){ 
      this.editgetUser();
      this.fetchContrbution();
      this.fetchloan();
      this.fetchpurchases();


      
     
      }
       this.paymentMethod();
        this.formatter();
     
       
           
    },
    methods:{
       ...mapActions({
      setLoading: "setLoading"
    }),
      customFormatter() {
         this.setLoading(true);
            let url = '/systemdate';
            axios.get(url).then(response => {
                this.system = response.data.systemDate;
                this.systemValue = response.data.systemValue;
                console.log(this.systemValue,'ddd')

            })
            .finally(() => {
                this.setLoading(false);
              });
        },
       getpayment(id) {
      this.setLoading(true);
  this.payment_id= id;
      let uri = '/Payment/' + id;
      console.log(uri);
      axios.get(uri).then((response) => {
	 
        this.invoice = response.data.paymentdetails;
		
		var dateMomentObj = response.data.paymentdetails.paymentdate;
		console.log(dateMomentObj );
          var DateObj = moment(dateMomentObj);
		 
          this.invoice.paymentdate = new Date(DateObj);
		
        
      })
      .finally(() => {
                this.setLoading(false);
              });
    },
        
        formatter(date) {
         
            return moment(new Date(date)).format(this.systemValue);
        },

              formatDate(date){

              return moment(new Date(date)).format(this.systemValue);
              
         },
        paymentMethod() {
            this.setLoading(true);
            let uri = '/PaymentMethod';
            console.log(uri);
            axios.get(uri).then((response) => {
                this.name = response.data.payment_methodDetails;

            })
              .finally(() => {
                this.setLoading(false);
              });
        },
         payment(id, balance, symbol) {
             this.setLoading(true);
            this.invoice_id = id;
            this.paymentBalance = Number(balance);
            this.paymentSymbol = symbol;
            // console.log(this.invoice_id);
            let uri = '/invoice';
            axios.get(uri)
                .then((response) => {
                      this.invoice.amount = Number(this.paymentBalance);
                  
                })
                .finally(() => {
                this.setLoading(false);
              });
                    
            // console.log(this.invoice.amount, 'invoice');

        },

               fetchloan() {
      
          this.setLoading(true);
            let url = '/Loan/'+ this.$route.query.id;
            axios.get(url).then(response => {
             
            this.loandetails = response.data.loandetails;
          
          })
          .finally(() => {
                this.setLoading(false);
              });
      },

             paymentSubmit( ) {
     this.setLoading(true);
   this.submitted = true;
        if (this.payment_id != '' && this.payment_id != null) {
      this.$validator.validate().then(valid => {
        if (valid) {
          let url = '/Payment/' +this.payment_id;
          let config = {
            headers: {
              'content-type': 'application/json'
            }
          }



          let formdata = new FormData();
          formdata.append('amount', this.invoice.amount);
          if (this.invoice.paymentdate != '') {
              var dateObj = new Date(this.invoice.paymentdate);
              var momentObj = moment(dateObj);
              var momentString = momentObj.format('YYYY-MM-DD');
              this.invoice.paymentdate = momentString;
              formdata.append('date', this.invoice.paymentdate);
            }
          formdata.append('paymentmethod', this.invoice.paymentmethod);
          formdata.append('paymentnotes', this.invoice.paymentnotes);
         
          axios.put(url, this.invoice, config).then(response => {
            //console.log(response);
            //alert(11111);
           if (response.data.status == 1) {
            
          this.$buefy.toast.open({
            duration: 4000,
            message: response.data.message,
            type: "is-danger",
            position: "is-top-right"
          });
      //    this.getdata();
	  
        } else {
          this.invoice = [];
           this.fetchquotes();
          this.$buefy.toast.open({
            duration: 4000,
            message: response.data.message,
            position: "is-top-right",
            type: "is-success"
          });
        }
      })
       .finally(() => {
                this.setLoading(false);
              });
        }
      });
      }
             
           this.isImageModalActive3 = false;


    },

      



         handlesubmit() {
            this.setLoading(true);
            this.submitted = true;
            this.balanceError = null;
            if (Number(this.paymentBalance) < Number(this.invoice.amount)) {
                this.balanceError = "Amount is larger then Balance"
                return

            }
            if (this.balanceError == null) {
                this.$validator.validate().then(valid => {
                    if (valid) {
                        let url = '/paymentinvoice/' + this.invoice_id;
                        let config = {

                            headers: {

                                Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                                'content-type': 'multipart/form-data'
                            }
                        }



                        let formdata = new FormData();

                        formdata.append('amount', this.invoice.amount);
                        if (this.invoice.paymentdate != '') {
                            var dateObj = new Date(this.invoice.paymentdate);
                            var momentObj = moment(dateObj);
                            var momentString = momentObj.format('YYYY-MM-DD');
                            this.invoice.paymentdate = momentString;
                            formdata.append('paymentdate', this.invoice.paymentdate);
                        }
                        formdata.append('paymentmethod', this.invoice.paymentmethod);
                        formdata.append('paymentnotes', this.invoice.paymentnotes);




                        axios.post(url, formdata, config).then(response => {

                            console.log(this.invoice_id);


                            if (response.data.status == 1) {

                                this.isImageModalActive2 = false;
                                // this.fetchdata();
                                 window.location.href = "";
                                this.invoice = [];
                                this.invoice.date = new Date();
                                this.invoice.company = [];
                                this.invoice.invoice_template = "SystimaNX balde.pdf";
                                this.invoice.paymentdate = new Date();


                                this.$buefy.toast.open({
                                    duration: 4000,
                                    message: response.data.message,
                                    type: "is-success",
                                    position: "is-top-right"
                                });




                            } else {
                                this.$buefy.toast.open({
                                    message: response.data.message,
                                    position: "is-top-right",
                                    type: "is-danger"
                                });


                            }
                        })
                        .finally(() => {
                this.setLoading(false);
              });
                    }
                });
               
            this.isImageModalActive1=false
            }
        },
                 editgetUser() {
                       this.setLoading(true);
        // this.editMode = true,
        let url = '/Client/' + this.$route.query.id;
        axios.get(url).then((response) => {
          this.clientdetails = response.data.clientDetails;
          this.client.client_name = response.data.clientDetails.client_name;
               this.client.email = response.data.clientDetails.email;
               this.client.address = response.data.clientDetails.address;
               this.client.phone_number = response.data.clientDetails.phone_number;
               this.client.state = response.data.clientDetails.state;
               this.client.city = response.data.clientDetails.city;
               this.client.postal_code = response.data.clientDetails.postal_code;
               this.client.country = response.data.clientDetails.country;
               this.client.default_currency = response.data.clientDetails.default_currency;
               this.client.status = response.data.clientDetails.status;
               this.client.fax_number = response.data.clientDetails.fax_number;
               this.client.tax_code = response.data.clientDetails.tax_code;
               this.client.amount = response.data.clientDetails.amount;
               this.client.office_unit = response.data.clientDetails.office_unit;
        })
         .finally(() => {
                this.setLoading(false);
              });
      },
      fetchContrbution(){
         this.setLoading(true);
            let url = '/Contributors/'+ this.$route.query.id;
            axios.get(url).then(response => {
             console.log(response);
            this.contribution_details = response.data.contributions;


          })
           .finally(() => {
                this.setLoading(false);
              });
      },
  

          goToDelete(id) {
             
          this.$buefy.dialog.confirm({
            message: 'Are you sure you want to delete this record?',
            onConfirm: () => this.destroy(id)
          })
        },
    
    destroy(id){
      this.setLoading(true);
      let url = '/Quotes/' ;
      // console.log("URL",url);
      
        axios
        .delete(url+id)
        .then(resp =>{
          if (resp.data.status == 1) {
                      this.$buefy.toast.open({
                      duration: 4000,
                      message: resp.data.message,
                      title: "deleted successfully",
                      type: 'is-success',
                      position: "is-top-right",
                    });
                 
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
         this.formatter();
        })
        .catch(error => {
            console.log(error);
          })
           .finally(() => {
                this.setLoading(false);
              });
      
    },
      goInvoiceDelete(id) {
              alert(1);
          this.$buefy.dialog.confirm({
            message: 'Are you sure you want to delete this record?',
            onConfirm: () => this.invoicedestroy(id)
          })
        },
    
    invoicedestroy(id){
       this.setLoading(true);
      let url = '/invoice/' ;
      // console.log("URL",url);
      
        axios
        .delete(url+id)
        .then(resp =>{
          if (resp.data.status == 1) {
                      this.$buefy.toast.open({
                      duration: 4000,
                      message: resp.data.message,
                      title: "deleted successfully",
                      type: 'is-success',
                      position: "is-top-right",
                    });
                  
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
        this.formatter();
        })
        .catch(error => {
            console.log(error);
          })
           .finally(() => {
                this.setLoading(false);
              });
      
    },

       fetchpurchases() {
         //  alert(1);
          this.setLoading(true);
            let url = '/viewid/'+ this.$route.query.id;
            axios.get(url).then(response => {
               console.log(response);
            this.viewpurchasedetails = response.data.quotedetails;
            console.log( this.quotedata,"quotedata");
          })
          .finally(() => {
                this.setLoading(false);
              });
      },
  
       goPaymentDelete(id){ 
	  
     this.$buefy.dialog.confirm({
        message: 'Are you sure you want to delete this record?',
        onConfirm: () => this.paymentdestroy(id)
      })  
    },

 
 paymentdestroy(id) {  
    this.setLoading(true);
        let uri = "/Payment/";
        
          axios
            .delete(uri + id)
            .then(resp => { 
			 if (resp.data.status == 1) {
          this.$buefy.toast.open({
            duration: 4000,
            message: resp.data.message,
            type: "is-danger",
            position: "is-top-right"
          });
        window.location.href = "";
        } else {
          this.$buefy.toast.open({
            duration: 4000,
            message: resp.data.message,
            position: "is-top-right",
            type: "is-success"
          });
        }
          this.formatter();
			  
            })

            .catch(error => {
              console.log(error);
            })
             .finally(() => {
                this.setLoading(false);
              });
        }
      },

   computed: {
      ...mapState({
      loading: state => state.isLoading,
      userState: state => state.user
    }),
        filteredCustomers: function() {
            var self = this;
            console.log(this.contribution_details)
            return this.contribution_details.filter(function(cust) {
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
        },
         filteredLoan: function() {
      var self = this;
     
         return this.loandetails.filter(function(cust) {
         return (
                  cust.client_name.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0 ||
            cust.loan_no.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0 ||
            cust.date.toLocaleString().indexOf(self.search.toLowerCase()) >= 
            0 ||
            cust.expiredate.toLocaleString().indexOf(self.search.toLowerCase()) >=
               0 ||
           
              cust.amount.toLocaleString().indexOf(self.search.toLowerCase()) >= 
              0 ||
                cust.status.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0
         );
         });
      
     
   },
   filteredpurchase: function(){
       var self = this;
      return this.viewpurchasedetails.filter(function(cust){
        return (
            cust.quote_no.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0 ||
            cust.date.toLocaleString().indexOf(self.search.toLowerCase()) >= 0 ||
            cust.due_date.toLocaleString().indexOf(self.search.toLowerCase()) >=
               0 ||
                cust.client_name.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0 ||
              cust.totalamount.toLocaleString().indexOf(self.search.toLowerCase()) >= 0 ||
                cust.quotesstatus.toLowerCase().indexOf(self.search.toLowerCase()) >=
               0
         );
        });
   }
    }
};
</script>
