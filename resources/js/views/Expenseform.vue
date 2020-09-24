<template>
    <section class="hero is-fullheight is-default is-bold">
        <layout-header></layout-header>

        <div class="container breadcrums1">
            <h6 class="form-name is-uppercase is-pulled-left is-size-6">
                Add Expenses
            </h6>

            <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
                <ul>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Pages</a>
                    </li>

                    <li class="is-size-7 is-active">
                        <a class href="#" aria-current="page">Expenditure</a>
                    </li>
                </ul>
            </nav>
        </div>

        <section class="container forms-sec has-background-white box">
            <a href="/expenses" class="has-text-grey-dark backsection">
                <i class="fas fa-arrow-left"></i>Back
            </a>
            <form id="app" @submit.prevent="handleSubmit" validate>
                <div class="columns is-multiline is-mobile">
                    <div class="column is-4 is-12-mobile">
                        <p class="bd-notification is-info">
                            <label>
                                Expense Title
                                <span class="has-text-danger">*</span>
                            </label>
                        </p>
                        <div class="field">
                            <p class="control has-icons-left has-icons-right">
                                <input
                                    id="title"
                                    class="input"
                                    type="text"
                                    v-model="expense.title"
                                    name="title"
                                    placeholder="Expense Title"
                                    v-validate="'required'"
                                    :class="{
                                        'is-invalid':
                                            submitted && errors.has('title')
                                    }"
                                />
                            </p>
                            <span
                                v-show="errors.has('title')"
                                class="invalid-feedback"
                                >{{ titleError }}</span
                            >
                        </div>
                    </div>
                    <div class="column is-4 is-12-mobile">
                        <p class="bd-notification is-info">
                            <label>
                                Amount
                                <span class="has-text-danger">*</span>
                            </label>
                        </p>
                        <div class="field">
                            <input
                                id="amount"
                                class="input"
                                type="text"
                                v-model="expense.amount"
                                name="amount"
                                placeholder="Amount"
                                v-validate="'required'"
                                :class="{
                                    'is-invalid':
                                        submitted && errors.has('title')
                                }"
                            />

                            <span
                                v-show="errors.has('amount')"
                                class="invalid-feedback"
                                >{{ amountError }}</span
                            >
                        </div>
                    </div>
                    <div class="column is-4 is-12-mobile">
                        <p class="bd-notification is-info">
                            <label>
                                Expense ID
                                <span class="has-text-danger">*</span>
                            </label>
                        </p>
                        <div class="field">
                            <p class="control has-icons-left has-icons-right">
                                <input
                                    id="id"
                                    class="input"
                                    type="text"
                                    v-model="expense.id"
                                    name="id"
                                    disabled
                                    placeholder="Expense Title"
                                    v-validate="'required'"
                                />
                            </p>
                        </div>
                    </div>
                    <div class="column is-4 is-12-mobile">
                        <p class="bd-notification is-info">
                            <label>
                                Date
                                <span class="has-text-danger">*</span>
                            </label>
                        </p>
                        <div class="field">
                            <b-datepicker
                                id="date"
                                name="date"
                                v-model="expense.date"
                                placeholder="enter date..."
                                icon="calendar-today"
                            >
                            </b-datepicker>
                        </div>
                    </div>

                    <div class="column is-4 is-12-mobile">
                        <p class="bd-notification is-info">
                            <label>
                                Note
                                <span class="has-text-danger"></span>
                            </label>
                        </p>
                        <div class="field">
                            <textarea
                                placeholder="note"
                                class="textarea is-small is-shadowless"
                                id="note"
                                name="note"
                                max_length="200"
                                v-model="expense.note"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <button class="button is-dark is-pulled-right is-small">
                    Submit
                </button>
                <button1
                    class="button has-background-light is-small is-pulled-right clearbuton"
                    @click="resetform"
                    >Clear</button1
                >
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
    data() {
        return {
            loading: true,
            submitted: false,
            tax: {
                name: "",
                compound: "",
                percentage: "",
                calculate_as_vat_gst: "0"
            },

            expense: {
                title: "",
                amount: "",
                id: "",
                date: new Date(),
                note: ""
            },

            submitted: false,
            validname: "Field is required ",

            numberAllowed: "only numbers allowed"

            //validfield: " please enter the name",

            // validoption3: "select the option"
        };
    },
    computed: {
        ...mapState({
            loading: state => state.isLoading,
            userState: state => state.user
        }),
        amountError() {
            if (this.expense.amount == "") return this.validname;
        },

        titleError() {
            if (this.expense.title == "") return this.validname;
        }
    },

    created: function() {
        this.expenceId();
        if (this.$route.query.id != "" && this.$route.query.id != null)
            this.getExpense();
    },

    methods: {
        ...mapActions({
            setLoading: "setLoading"
        }),

        // doSomething() {
        //   if (this.submitted) {
        //     return;
        //   }
        //   this.submitted = true;

        //   this.submitted = false;

        //   setTimeout(
        //     function() {
        //       this.submitted = false;
        //     }.bind(this),
        //     1000
        //   );
        // },

        resetform() {
            this.tax = {
                name: "",
                compound: "",
                percentage: "",
                calculate_as_vat_gst: ""
            };
        },

        handleSubmit(e) {
            let context = this;
            if (
                context.$route.query.id != "" &&
                context.$route.query.id != null
            ) {
                context.editMode = true;
                context.setLoading(true);
                context.$validator.validate().then(valid => {
                    if (valid) {
                        let uri = "/updateExpense/" + context.$route.query.id;
                        //console.log(context.item.cost)
                        axios
                            .put(uri, context.expense)
                            .then(response => {
                                if (response.data.status == 1) {
                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        title: " updated successfully",
                                        position: "is-top-right",
                                        type: "is-success"
                                    });

                                    context.$router.push("/Expenses");
                                } else {
                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        title: " updated successfully",
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
                    }
                    context.setLoading(false);
                });
            } else {
                context.submitted = true;
                context.setLoading(true);
                context.$validator.validate().then(valid => {
                    if (valid) {
                        let url = "/saveExpense";
                        let config = {
                            headers: {
                                Authorization:
                                    "Bearer " + sessionStorage.getItem("token"),
                                "content-type": "multipart/form-data",
                                "content-type": "application/json"
                            }
                        };

                        let formdata = new FormData();
                        formdata.append("id", context.expense.id);
                        formdata.append("title", context.expense.title);
                        formdata.append("amount", context.expense.amount);
                        formdata.append("note", context.expense.note);

                        axios
                            .post(url, formdata, config)
                            .then(response => {
                                // console.log(response);
                                //alert(11111);
                                if (response.data.status == 1) {
                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        position: "is-top-right",
                                        type: "is-success"
                                    });
                                    context.$router.push("/Expenses");
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
                    }
                    context.setLoading(false);
                });
            }
        },

        expenceId() {
            this.expense.id =
                "EX" +
                Date.now()
                    .toString()
                    .substr(5, 7);
        },

        getExpense() {
            this.setLoading(true);
            let uri = "/showexpense/" + this.$route.query.id;
            axios
                .get(uri)
                .then(response => {
                    this.expense = response.data.ExpenseArray;
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },
        updatetax() {
            this.editMode = true;
            this.setLoading(true);
            let uri = "/Taxrate/" + this.$route.query.id;
            //console.log(this.item.cost)
            axios
                .put(uri, this.tax)
                .then(response => {
                    this.$router.push({
                        company_name: this.tax.name,
                        address: this.tax.percentage,
                        city: this.tax.calculate_as_vat_gst,
                        state: this.tax.compound
                    });
                    // alert(response.data.message)
                })
                .finally(() => {
                    this.setLoading(false);
                });
        }
    },
    components: {
        LayoutHeader,
        LayoutFooter
    }
};
</script>
