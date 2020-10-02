<template>
  <section class="hero is-fullheight is-default is-bold">
    <layout-header></layout-header>

    <div class="container breadcrums1">
      <h6 class="form-name is-uppercase is-pulled-left is-size-6">List User Account</h6>

      <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
        <ul>
          <li class="is-size-7">
            <a class="has-text-grey" href="#">Pages</a>
          </li>

          <li class="is-active is-size-7">
            <a href="#" aria-current="page">Users Account</a>
          </li>
        </ul>
      </nav>
    </div>

    <section class="container forms-sec has-background-white box is-clearfix">
 <div class="field invo1 has-addons is-pulled-right ">
               
                &nbsp;&nbsp;&nbsp;&nbsp;
                <div  class="control is-hidden-mobile">
                    <a
                        class="button is-info is-samll has-background-grey-darker"
                        >Search</a
                    >
                </div>

                <div class="control has-icons-left is-hidden-mobile">
                    <input
                        type="text"
                        class="input is-info is-small"
                        v-model="search"
                        placeholder="Filter"
                    />
                    <span class="icon is-medium is-left">
                        <i class="fa fa-futbol-o"></i>
                    </span>
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

      <b-table
        class="table is-bordered is-striped is-fullwidth search-table inner"
        :data="filteredCustomers"
        :paginated="isPaginated"
        :per-page="perPage"
        :current-page.sync="currentPage"
        :default-sort-direction="defaultSortDirection"
        :mobile-cards="hasMobileCards"
        default-sort="name"
      >
        <template slot-scope="props">
          <b-table-column
            field="id"
            class="is-size-7-mobile"
            label="ID"
            width="40"
            sortable
            numeric
            >{{ props.index + 1 }}</b-table-column
          
          >



          <b-table-column
            field="Name"
            class="is-size-7-mobile"
            label="  First Name"
            sortable
          >{{ props.row.name }}</b-table-column>

          <b-table-column
            field="email"
            class="is-size-7-mobile"
            label="  Last Name"
            sortable
          >{{ props.row.lastname }}</b-table-column>

          <b-table-column
            field="type"
            class="is-size-7-mobile"
            label="Email"
            sortable
          >{{ props.row.email }}</b-table-column>
                    <b-table-column
            field="type"
            class="is-size-7-mobile"
            label=" User Privilege"
            sortable
          >{{ props.row.user_privilege }}</b-table-column>
                    <b-table-column
            field="type"
            class="is-size-7-mobile"
            label="Status"
            sortable
          >{{ props.row.status }}</b-table-column>

          <b-table-column field="Options" class="is-size-7-mobile" label="Options" sortable>
            <b-dropdown hoverable>
              <button
                class="button is-small has-background-grey-darker has-text-white"
                slot="trigger"
              >
                <span>Options</span>
                <i class="fas fa-caret-down drops"></i>
              </button>

                     <b-dropdown-item @click="editUserValue(props.row.id, props.row.name, props.row.lastname, props.row.email)">
                        <i class="fas fa-trash-alt icon1"></i>Edit
                     </b-dropdown-item>

              <b-dropdown-item  @click="deleteUser(props.row.id)">
                <i class="fas fa-trash-alt icon1"></i>Delete
              </b-dropdown-item>
            </b-dropdown>
          </b-table-column>
        </template>
      </b-table>
    </section>


          <template>
         <section>
            <form id="apps" @submit.prevent="saveuser" validate>
               <b-modal :active.sync="isImageModalActive " :width="340">
                  <div class="card section sect">
                     <h4 class="has-text-grey-dark	is-uppercase is-size-6 has-text-centered	">Create New user</h4>

                     <p class="bd-notification is-info">
                        <label>First Name <span class="has-text-danger">*</span>
                        </label>
                        <div class="field">
                          
                          <input id="firstname" class="input" name="firstname" v-model="user.name" type="text" v-validate="'required'" 
                            >                           
                     </p>

                         
                     </p>
                  </div>
               
                  <p class="bd-notification is-info">
                     <label>Last Name <span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                                 <input id="lastname" class="input" name="lastname" v-model="user.lastname" type="text" v-validate="'required'" 
                            >   

                        </p>
                     </div>
                  </p>

                                    <p class="bd-notification is-info">
                     <label>User Privilege<span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                                 <input id="user_privilege" class="input" name="user_privilege" v-model="user.user_privilege" type="text" v-validate="'required'" disabled
                            >   

                        </p>
                     </div>
                  </p>

                  
                                    <p class="bd-notification is-info">
                     <label>Email<span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                                 <input id="email" class="input" name="email" v-model="user.email" type="text" v-validate="'email'" data-vv-as="email"
                            >   

                        </p>

                         <span v-show="errors.has('email')" class="invalid-feedback">{{ errors.first('email') }}</span>

                     </div>
                  </p>


                  <p class="bd-notification is-info">
                     <label>Password <span class="has-text-danger">*</span></label>
                  
                                          <div class="field">
                     <p class="control has-icons-left has-icons-right">
                           <input v-validate="'required|min:7'" class="input" name="password" type="password" v-model="user.password"  :class="{'is-danger': errors.has('password')}" placeholder="Password" ref="password">
                        
                     </p>
                     <span v-show="errors.has('password')" class="invalid-feedback">{{ errors.first('password') }}</span>

               
                     </div>
                   </p>

                   

                  <p class="bd-notification is-info">
                     <label>Confirm Password <span class="has-text-danger">*</span></label>
                  
                                          <div class="field">
                     <p class="control has-icons-left has-icons-right">

<input v-validate="'required|confirmed:password'" class="input" v-model="user.confirm_password" name="password_confirmation" type="password" :class="{'is-danger': errors.has('password_confirmation')}" placeholder="Password, Again" data-vv-as="password">                         
                     </p>

               <span v-show="errors.has('password_confirmation')" class="invalid-feedback">{{ errors.first('password_confirmation') }}</span>
                     </div>
                   </p>

                
                  <button class="button is-dark is-small is-pulled-right">Submit</button>
             <button type="button" @click="isImageModalActive = false" class="button has-background-light is-small is-pulled-right clearbuton"  >Cancel</button>   
           

                  </div>
                  
               </b-modal>
                  
            </form>
         
         </section>

               
      </template>

         <section>
            <form id="apps" @submit.prevent="edituser" validate>
               <b-modal :active.sync="isImageModalActive1 " :width="340">
                  <div class="card section sect">
                     <h4 class="has-text-grey-dark	is-uppercase is-size-6 has-text-centered	">Modify user</h4>

                     <p class="bd-notification is-info">
                        <label>First Name <span class="has-text-danger">*</span>
                        </label>
                        <div class="field">
                          
                          <input id="firstname" class="input" name="firstname" v-model="user.name" type="text" v-validate="'required'" 
                            >                           
                     </p>

                         
                     </p>
                  </div>
               
                  <p class="bd-notification is-info">
                     <label>Last Name <span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                                 <input id="lastname" class="input" name="lastname" v-model="user.lastname" type="text" v-validate="'required'" 
                            >   

                        </p>
                     </div>
                  </p>

                                    <p class="bd-notification is-info">
                     <label>User Privilege<span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                                 <input id="user_privilege" class="input" name="user_privilege" v-model="user.user_privilege" type="text" v-validate="'required'" disabled
                            >   

                        </p>
                     </div>
                  </p>

                  
                                    <p class="bd-notification is-info">
                     <label>Email<span class="has-text-danger">*</span>
                     </label>
                     <div class="field">
                        <p class="control has-icons-left has-icons-right">
                                 <input id="email" class="input" name="email" v-model="user.email" type="text" v-validate="'email'" data-vv-as="email"
                            >   

                        </p>

                         <span v-show="errors.has('email')" class="invalid-feedback">{{ errors.first('email') }}</span>

                     </div>
                  </p>


   

                
                  <button class="button is-dark is-small is-pulled-right">Submit</button>
             <button type="button" @click="isImageModalActive1 = false" class="button has-background-light is-small is-pulled-right clearbuton"  >Cancel</button>   
           

                  </div>
                  
               </b-modal>
                  
            </form>
         
         </section>
    

    <layout-footer></layout-footer>
  </section>
</template>

<script>
   import { mapState, mapActions, mapGetters } from "vuex";
import LayoutHeader from "./layouts/Header.vue";
import LayoutFooter from "./layouts/Footer.vue";
export default {
  components: {
    LayoutHeader,
    LayoutFooter
  },

  data() {
  
    return {
        data: [],

      isImageModalActive : false,
      isImageModalActive1: false,

            isPaginated: true,
            isPaginationSimple: false,
            defaultSortDirection: "asc",
            currentPage: 1,
            perPage: 5,
            hasMobileCards: false,
             search: "",
             submitted : false,
             data: [],
             editmode: false,

            user :{
              id: '',
              name: '',
              lastname: '',
              password: '',
              confirm_password: '',
              email: '',
              user_privilege: 'Admin'



            }
     
  }
  },
  computed: {
    filteredCustomers: function() {
    
      var self = this;
      return this.data.filter(function(users) {
        return (
          
          users.name.toLowerCase().indexOf(self.search.toLowerCase()) >= 0 ||
           users.lastname.toLowerCase().indexOf(self.search.toLowerCase()) >= 0 ||
             users.email.toLowerCase().indexOf(self.search.toLowerCase()) >= 0 ||
               users.status.toLowerCase().indexOf(self.search.toLowerCase()) >= 0 ||
                     users.user_privilege.toLowerCase().indexOf(self.search.toLowerCase()) >= 0

        );

      });
    },

            ...mapState({
      loading: state => state.isLoading,
      userState: state => state.user
    }),


  },

  mounted: function(){

    this.getUsers()


    




  },

methods: {
       ...mapActions({
      setLoading: "setLoading"
    }),


    editUserValue(id, name, lastname, email){
      this.editmode = true

      this.isImageModalActive1 = true;

      this.user.id = id
      this.user.name = name
      this.user.lastname = lastname
      this.user.email = email






    },


               saveuser() {
            let context = this;
            context.submitted = true;
         

          
                  context.setLoading(true);
               
                context.$validator.validate().then(valid => {
                    if (valid) {
                         this.isImageModalActive = false;
                      
                     
                      
                        let url = '/user';
                        let config = {
                            headers: {
                                Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                                'content-type': 'multipart/form-data'
                            }
                        }
                        let formdata = new FormData();


                        
                        formdata.append('name', this.user.name);
                        formdata.append('lastname', context.user.lastname);
                               formdata.append('password', context.user.password);
                     
                          formdata.append('email', context.user.email);
                          formdata.append('user_privilege', context.user.user_privilege);
              
                          axios.post(url, formdata, config).then(response => {
                            if (response.data.status == 1) {
                               
              
                                        
                                   context.$buefy.toast.open({
                                    duration: 4000,
                                     message: response.data.message,
                                      type: "is-success",
                                       position: "is-top-right"
                                });
                                
                                  this.getUsers();
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
           

},

edituser(){

              let context = this;
            context.submitted = true;
         

          
                  context.setLoading(true);
               
                context.$validator.validate().then(valid => {
               
                    if (valid) {
                         this.isImageModalActive1 = false;




  let url = '/user/' + this.user.id;
                        let config = {
                            headers: {
                                Authorization: 'Bearer ' + sessionStorage.getItem('token'),
                                'content-type': 'multipart/form-data'
                            }
                        }

                                                  axios.put(url, this.user).then(response => {
                            if (response.data.status == 1) {
                               
              
                                        
                                   context.$buefy.toast.open({
                                    duration: 4000,
                                     message: response.data.message,
                                      type: "is-success",
                                       position: "is-top-right"
                                });
                                
                                  this.getUsers();
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
},




 getUsers() {
            this.setLoading(true);
            let url = "/user";

            //  console.log(url);
            axios
                .get(url)
                .then(response => {
                    this.data = response.data.user;
                     
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },


        deleteUser(id){

     this.$buefy.dialog.confirm({
            message: 'Are you sure you want to delete this record?',
            onConfirm: () => this.destroy(id)
          })


},


    destroy(id){
      this.setLoading(true);
      let url = '/user/' ;
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
         this.getUsers();
        })
        .catch(error => {
           
          })
           .finally(() => {
                this.setLoading(false);
              });
      
    },

},




}
</script>