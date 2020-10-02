<template>
  <section class="hero is-fullheight is-default is-bold">
    <layout-header></layout-header>

    <div class="container breadcrums1">
      <h6 class="form-name is-uppercase is-pulled-left is-size-6">Payments Collected</h6>

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

   <section class="container forms-sec has-background-white box">
         <form id="app" @submit.prevent="downloadpdf" validate>
        <div class="columns">
          <div class="column">
                  <p class="bd-notification is-info">
                     <label>Company Name
                     <span class="has-text-danger	">*</span>
                     </label>
                  <div class="field">
                     <p class="control has-icons-left has-icons-right">
                        <input id="product_name" class="input" name="product_name" v-model="paymentcollected.company_profile" type="text" disabled
                            >                           
                     </p>

                  </div>
                  </p>
        </div>
        
 
          <div class="column">
            <p class="bd-notification is-info">
              <label>
                From Date <span class="has-text-danger">*</span>
              </label>
            <div class="field"> 
               <p class="control has-icons-left has-icons-right">
                           <b-datepicker id="from_date" name="from_date" v-model="paymentcollected.from_date" v-validate="'required'"
                          placeholder="enter date.." icon="calendar-today">
                           </b-datepicker>
                        </p> 
             </div>
              <div v-show="errors.has('from_date')" class="invalid-feedback">{{fromdateError}}</div>
            </p>
          </div> 
            <div class="column">
            <p class="bd-notification is-info">
              <label>
                To Date <span class="has-text-danger">*</span>
              </label>
            <div class="field"> 
               <p class="control has-icons-left has-icons-right">
                           <b-datepicker  id="to_date" name="to_date" v-model="paymentcollected.to_date" v-validate="'required'"
                          placeholder="enter date.." icon="calendar-today">
                           </b-datepicker>
                        </p> 
                  <div v-show="errors.has('to_date')" class="invalid-feedback">{{todateError}}</div>        
             </div>
            </p>
          </div>

        </div>

         
        <button type="button" class="button is-dark is-pulled-right is-small" @click="downloadpdf" >Run report</button>
       
       <button type="button" class="button has-background-light is-small is-pulled-right clearbuton" @click="topdf" >Download pdf</button>
       <div v-if ="resultpaymentcollected !=null">

      

    <h1 style="margin-bottom: 0;text-align: left;">{{this.fromdate}} - {{this.todate}}</h1>
       <H3>Loans</H3> 
    <br>
    <table class="table table-bordered">
        <thead>
       <tr>
            <th class="amount">Month</th>
            <th class="amount">Amount Collected</th>
            <th class="amount">Repayed Amount</th>
            <th class="amount">Balance</th>
          
            
       </tr>
        </thead>
        <tbody>
        <tr v-for ="(paymentcollected,i) in resultpaymentcollected" :key="i">
          <td class="amount" style="font-weight: bold;">{{paymentcollected.month_name}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.amount}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.paid_amount}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.amount - paymentcollected.paid_amount}}</td>


           
        </tr>
        </tbody>
    </table>
 <h1 class="is-pulled-right" style="font-weight: bold;">Total Amount Disbursed: {{loan_calculation.loan_amount_array_sum}}</h1> <br>
  <h1 class="is-pulled-right" style="font-weight: bold;" > Total Repayment: {{loan_calculation.loan_paid_amount_array_sum}}</h1> <br>
    <h1 class="is-pulled-right" style="font-weight: bold;">Balance:  {{loan_calculation.loan_amount_array_sum - loan_calculation.loan_paid_amount_array_sum}}</h1> 

         </div>

          <br>
       

           <div v-if ="purchases !=null">
             <H3>Purchases</H3>
      <!-- <div  ref="content" v-if ="resultpaymentcollected !=null"> -->
  
    
    <br>
    <table class="table table-bordered">
        <thead>
       <tr>
            <th class="amount">Month</th> 
            <th class="amount">Item Amount Collected</th>
            <th class="amount">Repayed Amount</th>
            <th class="amount">Balance</th>
        
            
       </tr>
        </thead>
        <tbody>
        <tr v-for ="(paymentcollected,i) in purchases" :key="i">
            <td class="amount" style="font-weight: bold;">{{paymentcollected.month_name}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.total_amount}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.total_paid_amount}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.balance}}</td>


           
        </tr>
        </tbody>
    </table>
 <h1 class="is-pulled-right" style="font-weight: bold;">Total Amount Purchased: {{purchase_calculation.total_amount_array_sum}}</h1> <br>
  <h1 class="is-pulled-right" style="font-weight: bold;" > Total Payment: {{purchase_calculation.paid_amount_array_sum}}</h1> <br>
    <h1 class="is-pulled-right" style="font-weight: bold;">Balance:  {{purchase_calculation.balance_amount_array_sum}}</h1> 

         </div>

          <br>
           <br>

             <div v-if ="contribute_sum !=null">
          <H3>Contributions</H3>
<br>

<table class="table table-bordered">
        <thead>
       <tr>
            <th class="amount">Month</th> 
            <th class="amount">Contributions</th>

        
            
       </tr>
        </thead>
        <tbody>
        <tr v-for ="(paymentcollected,i) in contributors" :key="i">
            <td class="amount" style="font-weight: bold;">{{paymentcollected.month_name}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.payment_amount}}</td>



           
        </tr>
        </tbody>
    </table>

  <h1 class="is-pulled-right" style="font-weight: bold;" > Total Contributions: {{contribute_sum}}</h1> <br>


          </div>

<br>
<br>


           <div v-if ="expenses !=null">
             <H3>Expenses</H3>
      <!-- <div  ref="content" v-if ="resultpaymentcollected !=null"> -->
  
    
    <br>
    <table class="table table-bordered">
        <thead>
       <tr>
            <th class="amount">Month</th> 
            <th class="amount">Amount</th>

            
       </tr>
        </thead>
        <tbody>
        <tr v-for ="(paymentcollected,i) in expenses" :key="i">
            <td class="amount" style="font-weight: bold;">{{paymentcollected.month_name}}</td>
            <td class="amount" style="font-weight: bold;">{{paymentcollected.amount}}</td>



           
        </tr>
        </tbody>
    </table>
 <h1 class="is-pulled-right" style="font-weight: bold;">Total Expenses: {{expenses_sum}}</h1> <br>


         </div>
          
          </form>
      </section>
    <layout-footer></layout-footer>
  </section>
</template>

<script>
  import { mapState, mapActions, mapGetters } from "vuex";
  import {
    Validator
} from "vee-validate";
  import LayoutHeader from "./layouts/Header.vue";
  import LayoutFooter from "./layouts/Footer.vue";
  import moment from 'moment';
 

  
  export default {
    components: {
      LayoutHeader,
      LayoutFooter
    },
    name: 'app',
   data() {
      return {
        companyprofile:'',


        resultpaymentcollected: null,
        contributors:null,
       
        purchases : null,
        expenses : null,

        paymentcollected:
        {
         company_profile: "",
         from_date: null,
         to_date: null,

        },

       loan_calculation : [],
       purchase_calculation : [],
       contribute_sum : null,
       expenses_sum : '',

        submitted: false,
        editMode: false,
      
      
        validalphaspaces: "Only alphabet value accepted",
        numberAllowed: "Only numeric value accepted",
        validcompanyname: "Company Name is required",
        validfromdate: "From date is required",
        validtodate: "To date is required",

     }
     },
     computed:{
        ...mapState({
      loading: state => state.isLoading,
      userState: state => state.user
    }),
        companyError(){
       if(this.paymentcollected.company_profile == "" || this.paymentcollected.company_profile == null )
          return this.validcompanyname
          else
           return this.validalphaspaces
       },
        fromdateError(){
        if(this.paymentcollected.from_date == ""  || this.paymentcollected.to_date == null )
          return this.validfromdate
        else
          return this.numberAllowed  
       
       },
       todateError(){
           if(this.paymentcollected.to_date == ""  || this.paymentcollected.to_date == null)
          return this.validtodate
        else
          return this.numberAllowed  
       }
     },
    methods: {
      ...mapActions({
      setLoading: "setLoading"
    }),
          getcompany() {
            this.setLoading(true);
            let url = '/Company';
             axios.get(url).then((response) => {
               this.paymentcollected.company_profile = response.data.CompanyDetails;
                console.log("companyname",response.data.CompanyDetails);
            })
             .finally(() => {
                this.setLoading(false);
              });
         },


              
    downloadpdf() {


       
         this.submitted =true;
         this.fromdate = moment(this.paymentcollected.from_date).format("YYYY-MM-DD");
         this.todate = moment(this.paymentcollected.to_date).format("YYYY-MM-DD");

       this.api(this.fromdate, this.todate,"view");
    },
    
        api(fromdate, todate,type) {

          

    this.$validator.validate().then(valid => {
            if (valid) {

                 this.setLoading(true);
     
       
        let formData = new FormData();

        formData.append("company_profile", company_profile);
        formData.append("fromdate",fromdate);
        formData.append("todate",todate);
  switch(type){
    
            case "view":
              let url = "/statementTest";
              axios.post(url,formData).then(response => {
                this.resultpaymentcollected = response.data.loanarray;
                this.loan_calculation = response.data.loan_calculation;
                this.purchases = response.data.purchases;
                this.purchase_calculation = response.data.purchase_calculation;
                this.contribute_sum = response.data.contribute_sum;
                this.contributors = response.data.contributors;
                this.expenses = response.data.expenses
                this.expenses_sum = response.data.expenses_sum
                console.log("Response",response.data.paymentcollectedpreview);
              })

                             .finally(() => {
                this.setLoading(false);
              });

              
    
            break;
            case "download":
              
              axios({
                  url:"/statementdownload" ,
                  data: formData,
                  method: "post",
                  responseType: "blob" // important for pdf download
              })
                 .then(response => {
                    var fileURL = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    var fileLink = document.createElement("a");
                    fileLink.href = fileURL;
                    fileLink.setAttribute("download", "file.pdf");
                    document.body.appendChild(fileLink);
                    fileLink.click();
                  
                })
                 .finally(() => {
                this.setLoading(false);
              });
             break;

     }
       }
   
    });
     
       },

   topdf(){

     this.setLoading(true);
   
         this.fromdate = moment(this.paymentcollected.from_date).format("YYYY-MM-DD");
         this.todate = moment(this.paymentcollected.to_date).format("YYYY-MM-DD");
        this.api(this.fromdate, this.todate,"download"); 

     
   }
   },     
     mounted: function() {
      this.getcompany();
  },
 };
</script>