<template>
  <section class="hero is-fullheight is-default is-bold">
    <layout-header></layout-header>
    <div class="container breadcrums1">
      <h6 class="form-name is-uppercase is-pulled-left is-size-6">Loan Form</h6>
      <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
        <ul>
          <li class="is-size-7">
            <a class="has-text-grey" href="#">Pages</a>
          </li>
          <li class="is-size-7">
            <a class="has-text-grey" href="#">Users</a>
          </li>
          <li class="is-size-7 is-active">
            <a class="" href="#" aria-current="page">Client</a>
          </li>
        </ul>
      </nav>
    </div>

    <section class="container forms-sec has-background-white	box">
      <router-link to="/clienttable" class=" has-text-grey-dark     backsection"><i class="fas fa-arrow-left"></i>Back</router-link>
      <form id="app" @submit.prevent="handleSubmit" validate>
        <div class="columns is-mobile is-multiline ">
          <div class="column is-4 is-12-mobile">
            <p class="bd-notification is-info">
              <label>Client Name <span class="has-text-danger	">*</span>
              </label>
              <div class="field">
             
                                    <b-autocomplete name="client_name" v-model="loan.client_name" v-validate="'required'" field="title" :loading="isFetching"
                              @typing="getAsyncData" :class="{ 'is-invalid': submitted && errors.has('client_name') }"
                              :data="filteredDataArray" placeholder="Client Name" icon="magnify" @select="cool">
                              <template slot="empty">No results found</template>
                           </b-autocomplete>
              
                <span v-show="errors.has('client_name')" class="invalid-feedback">Field is Required</span> 
               
              </div>
            </p>
          </div>
             <div class="column is-4 is-12-mobile">
            <p class="bd-notification is-info">
              <label>Loan Amount <span class="has-text-danger	">*</span>
              </label>
              <div class="field">
                <p class="control has-icons-left has-icons-right">
                  <input class="input" type="text" id="amount" name="amount" maxlength="100" v-validate="'required|numeric'"
                    v-model="loan.amount" placeholder="Loan Amount">
                </p>
                <span v-show="errors.has('amount')" class="invalid-feedback">{{errorAmount}}</span>
              </div>
            </p>

             </div>
           
          
          <div class="column is-4 is-12-mobile">

            <p class="bd-notification is-info">
              <label>Loan ID 
              </label>
              <div class="field">
                <p class="control has-icons-left has-icons-right">
                  <input class="input" type="text" id="loan_no" name="loan_no" max-length="20"
                    v-model="loan.loan_no" placeholder="loan ID" disabled>
                </p>
        
            
              </div>
            </p>
</div>
 <div class="column is-4 is-12-mobile">

            <p class="bd-notification is-info">
              <label>Payment Plan <span class="has-text-danger	">*</span>
              </label>
              <div class="field">
                <p class="control has-icons-left has-icons-right">
                  <input class="input" type="text" id="payment_plan" name="payment_plan" v-validate="'required|numeric'"
                    v-model="loan.payment_plan" placeholder="In month">
                </p>
             
                <span v-show="errors.has('payment_plan')" class="invalid-feedback">{{payment_error}}</span>
              </div>
            </p>
 </div>
  <div class="column is-4 is-12-mobile">

            <p class="bd-notification is-info">
              <label>Disburse Account <span class="has-text-danger	">*</span>
              </label>
              <div class="field">
                <p class="control has-icons-left has-icons-right">
                  <input class="input" type="text" id="disburse_account" name="disburse_account"  v-validate="'required|numeric'"
                    v-model="loan.disburse_account" placeholder="Disburse Account">
                </p>
             
                <span v-show="errors.has('disburse_account')" class="invalid-feedback">{{errorDisburseCode}}</span>
              </div>
            </p>
  </div>
   <div class="column is-4 is-12-mobile">

            <p class="bd-notification is-info">
              <label>Disburse Bank <span class="has-text-danger	">*</span>
              </label>
              <div class="field">
                <p class="control has-icons-left has-icons-right">
                  <input class="input" type="text" id="disburse_bank" name="disburse_bank" maxlength="25" v-validate="'required'"
                    v-model="loan.disburse_bank" placeholder="Disburse Bank">
                </p>
                
                <span v-show="errors.has('disburse_bank')" class="invalid-feedback">Field is Required</span>
              </div>
            </p>
          </div>

                        <div class="column is-4 is-12-mobile">

            <p class="bd-notification is-info">
              <label>Interest Rate</label>
              <div class="field has-addons">
                <div class="control is-expanded">
                  <div class="select is-fullwidth">
                  <select id="interest_rate" name="interest_rate" v-model="loan.interest_rate" v-validate="'required'"  class="input">
                     <option   v-for="(tax_rate,index) in interestRate" v-bind:value="  tax_rate.percentage"    :key="index " >  
                       {{   tax_rate.percentage }}     % </option> </select>
                </div>
                </div>
              </div>
            </p>

        </div>
         

               <div class="column is-4 is-12-mobile">
            <p class="bd-notification is-info">
              <label>Note
              </label>
              <div class="field">
                <p class="control has-icons-left has-icons-right">
                  <textarea placeholder="Address" class="textarea is-small is-shadowless" id="note" name="note"
                    max_length="200" v-model="loan.note"></textarea>
                </p>
                <!-- <div v-if="submitted && errors.has('address')" class="invalid-feedback">{{address}}</div> -->
               
              </div>
            </p>
              </div>
                        <div class="column is-4 is-12-mobile">

            <p class="bd-notification is-info">
              <label>Status</label>
              <div class="field has-addons">
                <div class="control is-expanded">
                  <div class="select is-fullwidth">
                    <select name="status" id="status" v-model="loan.status">
                      <option value="Approve">Approve</option>
                      
                    </select>
                  </div>
                </div>
              </div>
            </p>

        </div>
        </div>
        <button class="button is-dark is-pulled-right is-small" >Submit</button>
        <button4 class="button has-background-light is-small is-pulled-right clearbuton" @click="clear"> Clear </button4>
      </form>
    </section>
    <layout-footer></layout-footer>
  </section>
</template>

<script>
  import { mapState, mapActions, mapGetters } from "vuex";
  import LayoutHeader from "./layouts/Header.vue";
  import LayoutFooter from "./layouts/Footer.vue";
  import { Validator } from "vee-validate";
  export default {
    components: {
      LayoutHeader,
      LayoutFooter
    },
    name: 'app',
    data() {
      return {
        defaultcurrency:[],
        loan:
        {
          client_name: '',
          amount: '',
          loan_no: '',
          disburse_account: '',
          disburse_bank: '',
          payment_plan: '',
          note: '',
          status:'',
          return_id: '',
          interest_rate : ''
          

        },

        interestRate : [],

        loanclient:[],
        id:[],


        submitted: false,
        editMode: false,
           isFetching: false,

      
        validalphaspaces: "Only alphabet value accepted",
        numberAllowed: "Only numeric value accepted",
         validname: "Client Name is required",
        validEmail: "Email is required",
        validaddress: "Address is required",

        validstate: "State is required",
        validPaymentPlan: "Payment Plan is required",
        validcountry: "Country is required",
        ValidDisburseAccount: "Disburse Account is required",
        validdefault_currency: "Default Currency is required",
        validamount: "Amount is required",
        
        
      

      }
    },
    computed:{

        ...mapState({
      loading: state => state.isLoading,
      userState: state => state.user
    }),
      nameError(){
        if(this.loan.return_id == "")
          return this.validname

      },
            errorAmount(){
       if(this.loan.amount == "")
         return this.validamount
          else
         return this.numberAllowed
    },
      errorDisburseCode(){
        if(this.loan.disburse_account == "")
        return this.ValidDisburseAccount
        else
        return this.numberAllowed
      },

     payment_error(){
        if(this.loan.payment_plan == "")
         return this.validPaymentPlan
       else
         return this.numberAllowed
     },



           filteredDataArray() {


          
        return this.loanclient.filter((option) => {
               return option
                  .toString()
                  .toLowerCase()
                  .indexOf(this.loan.client_name.toLowerCase()) >= 0
                  
            })
          },



      },


  
    //page reference call mounted
    mounted: function () {
      this.loanid();
       this.gettax();

    },
   
    methods: {
       ...mapActions({
      setLoading: "setLoading"
    }),

          clear() {
        this.loan = {
          client_name: '',
          amount: '',
          payment_plan: '',
          disburse_account: '',
          disburse_bank: '',
          note: '',
        }
      },

              gettax() { 
            this.setLoading(true);
            let uri = '/listTaxRate';
              axios.get(uri).then((response) => {
               this.interestRate= response.data.TaxRateDetails;
            })
             .finally(() => {
                this.setLoading(false);
              });
        },
      
     
      handleSubmit(e) {
        // alert(1);
         let context = this;
        context.submitted = true;
        if (context.$route.query.id != '' && context.$route.query.id != null) {
          // alert(2);
          context.editMode = true;
          context.submitted = true;
          context.setLoading(true);
           context.$validator.validate().then(valid => {
            //  alert(3);
            //    alert('SUCCESS!! :-)\n\n' + JSON.stringify(context.user));
            if (valid) {
          
          let url = '/Client/' + context.$route.query.id;
          axios.put(url, context.user).then((response) => {
            if (response.data.status == 1) {
              context.$buefy.toast.open({
                duration: 4000,
                message: response.data.message,
                title: "client details updated successfully",
                type: "is-success",
                position: "is-top-right",
              });
                context.$router.push('/Clienttable');
           }
            else {
              context.$buefy.toast.open({
                duration: 1000,
                message: response.data.message,
                title: "updated failed",
                position: "is-right-right",
                type: "is-danger"
              });

            }
            context.$router.push('/Clienttable');
          
          })
           .catch( error => { console.log(error);
            })
            .finally(() => {
                context.setLoading(false);
              });
            }
            else{
                     context.setLoading(false);
                    }
           });
             

        } else {
          context.submitted = true;
          context.setLoading(true);
          context.$validator.validate().then(valid => {
            if (valid) {
              let url = '/Loan';
              // console.log(url);
              let config = {
                headers: {
                  Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                    //  'content-type': 'multipart/form-data',
                      'content-type': 'application/json'
                }
              }
              let formdata = new FormData();
              var interest_amount =  context.loan.amount * context.loan.interest_rate/100
              var amount_disbursed = context.loan.amount - interest_amount
              var monthly_payment = context.loan.amount/context.loan.payment_plan
              formdata.append('client_name', context.loan.return_id);
              formdata.append('loan_no', context.loan.loan_no);
              formdata.append('amount', context.loan.amount);
              formdata.append('payment_plan', context.loan.payment_plan);
              formdata.append('disburse_account', context.loan.disburse_account);
              formdata.append('disburse_bank', context.loan.disburse_bank);
              formdata.append('interest_rate', context.loan.interest_rate);
              formdata.append('amount_disbursed', amount_disbursed);
              formdata.append('monthly_payment', monthly_payment.toFixed(1));
              formdata.append('note', context.loan.note);
              formdata.append('status', context.loan.status);
              axios.post(url, formdata, config).then(response => {
               // console.log(response.data.status)
                if (response.data.status == 1) {
                  context.$buefy.toast.open({
                    duration: 4000,
                    message: response.data.message,
                    title: "client details submitted successfully",
                    type: "is-success",
                    position: "is-top-right"
                  });
                  context.$router.push('/loan');
                }
                else {
                  context.$buefy.toast.open({
                    duration: 4000,
                    message: response.data.message,
                    title: "submitted failed",
                    position: "is-top-right",
                    type: "is-danger",
                  });
                  // context.$router.push('/clienttable');
                }
              })
               .catch( error => { console.log(error); })
               .finally(() => {
                context.setLoading(false);
              });
            }else{
                     context.setLoading(false);
                    }
          });
        }
      },

           loanid() {

        

        

      this.loan.loan_no = 'LD'+Date.now().toString().substr(5, 7);


         },

                     getAsyncData() {
                   
         
         
            if (Object.keys(this.loan.client_name).length > 1) {
                 
               this.isFetching = true
               let url = '/autoComplete'
               
               axios.get(url,{query: {client_name: this.loan.client_name}}).then(response => {
                 
              
                 this.loanclient = response.data.autocomplete;
                  this.id = response.data.id;



                 
             
               })
                  .catch((error) => {
                     this.loanclient = []
                     throw error
                  })
                 .finally(() => {
                    this.isFetching = false
            
              });
            }

         },

         cool(selected){

console.log(selected)
       var  productIndex = -1
var client = this.client_id
                       this.loanclient.forEach(function (value, index) {
                  if (value === selected) {
                     productIndex = index;
                       

                    
                  }
               });
  this.loan.return_id = this.id[productIndex]

 
                  


         },

      editgetUser() {
         this.setLoading(true);
        // this.editMode = true,
        let url = '/Client/' + this.$route.query.id;
        axios.get(url).then((response) => {
          this.user = response.data.clientDetails;
        //  console.log(this.user);
        })
        .finally(() => {
                this.setLoading(false);
              });
      },

        getdefaultcurrency() {
            let url = '/Currencies';
             axios.get(url).then((response) => {
               this.defaultcurrency = response.data.currenciesDetails;
        
            });
         },
    }
  };
</script>
<style>
  input.input {
    font-size: 13px !important;
    padding: 16px !important;
    border-right-color: #ddd !important;

    border-radius: 0px;
  }

  label {
    font-size: 14px;
    font-weight: 400;
    line-height: 2.5;
    color: #646367;
  }

  select {
    color: #ddd !important;
    font-size: 14px !important;
    font-weight: 400;
    height: 35px !important;
  }

  .select:not(.is-multiple):not(.is-loading)::after {


    top: 58% !important;


    width: 0 !important;
    border-color: #888 transparent transparent transparent !important;
    border-style: solid !important;
    border-width: 5px 4px 0 4px !important;
    height: 0 !important;
    transform: unset;
    border-radius: 0;

  }

  .forms-sec {



    box-shadow: 0 1px 15px 1px rgba(208, 201, 243, .5);
    padding: 35px;
  }

  select {
    color: #000 !important;
  }

  .invalid-feedback {
    color: red;
  }
</style>