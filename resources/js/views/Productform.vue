<template>
   <section class="hero is-fullheight is-default is-bold">
      <layout-header></layout-header>
      <form id="app" @submit.prevent="handleSubmit">
         <div class="container breadcrums1">
            <h6 class="form-name is-uppercase is-pulled-left is-size-6">Add Products</h6>
            <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
               <ul>
                  <li class="is-size-7">
                     <a class="has-text-grey" href="#">Pages</a>
                  </li>
                  <li class="is-size-7">
                     <a class="has-text-grey" href="#">Products</a>
                  </li>
                  <li class="is-size-7 is-active">
                     <a class="" href="#" aria-current="page">Product</a>
                  </li>
               </ul>
            </nav>
         </div>
         <section class="container forms-sec has-background-white	box">
            <div class="columns is-multiline is-mobile">
               <div class="column is-4 is-12-mobile">
                  <p class="bd-notification is-info">
                     <label>Category <span class="has-text-danger	">*</span></label>
                  <div class="field has-addons">
                     <div class="control is-expanded">
                        <div class="select is-fullwidth">
                           <select id="category_id" name="category_id"  v-model="product.category_id" v-validate="'required'"
                              :class="{ 'is-invalid': submitted && errors.has('category_id') }">
                              <option v-for="cat in category" :key="cat.id"  v-bind:value="cat.id">
                                 {{cat.name}} 
                              </option>
                           </select>
                           <span v-show="errors.has('category_id')" class="invalid-feedback">{{category_idError}}</span>
                        </div>
                     </div>
                  </div>
                  </p>
               </div>
               <div class="column is-4 is-12-mobile">
                  <p class="bd-notification is-info">
                     <label>Product Name <span class="has-text-danger">*</span>
                     </label>
                  <div class="field">
                     <p class="control has-icons-left has-icons-right">
                        <input id="product_name" class="input" name="product_name" v-model="product.product_name" type="text" placeholder=" Product Name "  v-validate="'required'"
                           :class="{ 'is-invalid': submitted && errors.has('product_name') }" >
                     </p>
                     <span v-show="errors.has('product_name')" class="invalid-feedback">{{product_nameError}}</span>
                  </div>
                  </p>
               </div>
               

               <div class="column is-4 is-12-mobile">
                  <p class="bd-notification is-info">
                     <label>Short Description
                     <span class="has-text-danger	">*</span>
                     </label>
                  <div class="field">
                     <p class="control has-icons-left has-icons-right">
                        <textarea id="description" placeholder="Description" name="description" class="textarea is-small is-shadowless" v-model="product.description"  v-validate="'required'"
                           :class="{ 'is-invalid': submitted && errors.has('description') }" ></textarea>                              
                     </p>
                     <span v-show="errors.has('description')" class="invalid-feedback">{{descriptionError}}</span>
                  </div>
                  </p>

               </div>
               <div class="column is-3 is-12-mobile">
                  <p class="bd-notification is-info">
                     <label>Status <span class="has-text-danger">*</span>
                     </label>
                  <div class="field has-addons">
                     <div class="control is-expanded">
                        <div class="select is-fullwidth">
                           <select id="status" name="status"  v-model="product.status" :default="0" v-validate="'required'"
                              :class="{ 'is-invalid': submitted && errors.has(' status') }">
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                           </select>
                           <span v-show="errors.has('status')" class="invalid-feedback">{{statusError}}</span>
                        </div>
                     </div>
                  </div>
                  </p>
      
               </div>
            </div>


              



            <button type="submit" class="button is-dark is-pulled-right is-small"  
               >Submit</button>
            <button type="button" class="button has-background-light is-small is-pulled-right clearbuton" @click="resetform">Clear</button>
         </section>
         <layout-footer></layout-footer>
      </form>
   </section>
</template>

<script>
import { mapState, mapActions, mapGetters } from "vuex";
import LayoutHeader from "./layouts/Header.vue";
import LayoutFooter from "./layouts/Footer.vue";
import {
    Validator
} from "vee-validate";
var productIndex = -1;
export default {
    components: {
        LayoutHeader,
        LayoutFooter
    },

    data() {
        return {
            selectedunit: [],
            image: '',
            index: 0,
            submitted: false,
            product: {
                category_id: "",
                product_name: "",
                display_name: "",
                description: "",
                slug: "",
                tax_rate_id: "",
                status: 1,
            },
            arr: [{
                price: '',
                weight: '',
                unit: '',
                image: ''
            }],

              
            category: [],
            events: [],
            tax: [],
            categoryId: '',
            editMode: false,
            validalphaspaces: "Only charecters allowed",
            validname: "Field is required",
            numbervalid: "Only numbers allowed",
            data: [],
        }
    },

    computed: {
          ...mapState({
      loading: state => state.isLoading,
      userState: state => state.user
    }),

        product_nameError() {

            if (this.product.product_name == "")
                return this.validname
        },

        category_idError() {
            if (this.product.category_id == "")
                return this.validname
            else
                return this.validalphaspaces
        },

        descriptionError() {
            if (this.product.description == "")
                return this.validname

        },

        tax_rate_idError() {
            if (this.product.tax_rate_id == "")
                return this.validname
            else
                return this.validalphaspaces
        },

        statusError() {
            if (this.product.status == "")
                return this.validname
            else
                return this.validalphaspaces
        },

        priceError() {
            if (this.arr.price == "")
                return this.numbervalid
            else
                return this.validname
        },

        weightError() {
            if (this.arr.weight == "")
                return this.validname
            else
                return this.validname
        },

        unitError() {
            if (this.arr.unit == "")
                return this.validname
            else
                return this.validname
        },

        filteredDataArray() {
            if (this.data != null && this.data != "") {
                let temp_data = '';
                temp_data = this.data.filter((option) => {
                    return option
                        .toString()
                        .toLowerCase()
                        .indexOf(this.arr[this.index].unit.toLowerCase()) >= 0
                })
                let selected_item = this.selectedunit
                temp_data = temp_data.filter(function(option) {
                    return !selected_item.includes(option);
                })
                this.products_store = temp_data;
                return (this.products_store);
            }
        }
    },

    mounted: function() {
        
        if (this.$route.query.id != '' && this.$route.query.id != null) {

            this.getproduct();
            this.editMode = true;
        }
        this.getCategory();
    },

    methods: {
 ...mapActions({
      setLoading: "setLoading"
    }),
        resetform() {
            this.product = {
                category_id: "",
                product_name: "",
                display_name: "",
                description: "",
                tax_rate_id: "",
                status: "",
            }
            this.arr[0] = {
                price: "",
                weight: "",
                unit: "",
                image: "",
            }
         
        },

        handleSubmit() {
            let context = this;
              context.setLoading(true);
            
            if (context.$route.query.id != '' && context.$route.query.id != null) {
               
                context.editMode = true;
                context.$validator.validate().then(valid => {
                    if (valid) {
                        let uri = '/productupdate/' + context.$route.query.id;
                        let config = {
                            headers: {
                                Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                                'content-type': 'multipart/form-data',
                                'content-type': 'application/json'
                            }
                        }
                        let formdata = new FormData();
                         if (context.editMode === true) {
                            context.product.id = context.$route.query.id;
     
                        }
                       

                        axios.put(uri, this.product).then(response => {
                               
                                if (response.data.status == 1) {
                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        position: "is-top-right",
                                        type: "is-success"
                                    });
                                    context.$router.push('/Producttable');
                                } else {
                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        type: "is-danger",
                                        position: "is-top-right"
                                    });
                                }
                            })
                            .catch(error => {
                                console.log(error);
                            })
                             .finally(() => {
                                context.setLoading(false);
                       });
                    }else{
                     context.setLoading(false);
                    }
                });
            } else {
                 context.setLoading(true);
                context.submitted = true;
                context.$validator.validate().then(valid => {
                    if (valid) {
                 
                        let url = '/Product';
                        let config = {
                            headers: {
                                Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                                'content-type': 'multipart/form-data',
                                'content-type': 'application/json'
                            }
                        }
                        let formdata = new FormData();
        
                        formdata.append('category_id', context.product.category_id);
                        formdata.append('product_name', context.product.product_name);
               
                        formdata.append('description', context.product.description);
              
                        formdata.append('tax_rate_id', context.product.tax_rate_id);
                        formdata.append('status', context.product.status);

                        axios.post(url, formdata, config).then(response => {
                                if (response.data.status == 1) {

                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        position: "is-top-right",
                                        type: "is-success"
                                    });
                                    context.$router.push('/Producttable');
                                } else {
                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        type: "is-danger",
                                        position: "is-top-right"
                                    });
                                }
                            })
                            .catch(error => {
                                console.log(error);
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





        getCategory() { 
             this.setLoading(true);
            let uri = '/category';
            axios.get(uri).then((response) => {
                this.category = response.data.CategoryMethodDetails;

            })
            .finally(() => {
                       this.setLoading(false);
                    });
        },

        getproduct(){

                this.setLoading(true);
            let uri = '/Product/' + this.$route.query.id;;
            axios.get(uri).then((response) => {
                this.product = response.data.ProductDetails;

            })
            .finally(() => {
                       this.setLoading(false);
                    });


        },




        deleteFiles(index) {
            this.arr.splice(index, 1);
        },


    },
}
</script>