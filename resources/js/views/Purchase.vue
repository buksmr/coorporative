<template>
    <section class="hero is-fullheight is-default is-bold">
        <layout-header></layout-header>
        <div class="container breadcrums1">
            <h6 class="form-name is-uppercase is-pulled-left is-size-6">
                Inventory List
            </h6>
            <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
                <ul>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Pages</a>
                    </li>
                    <li class="is-active is-size-7">
                        <a href="#" aria-current="page">Invoice</a>
                    </li>
                </ul>
            </nav>
        </div>

        <section
            class="container forms-sec has-background-white box is-clearfix"
        >
            <div class="field invo1 has-addons is-pulled-right ">
                <div>
                    <b-field grouped group-multiline>
                        <b-select v-model="company_name">
                            <option value="0"> All Company</option>
                        </b-select>
                    </b-field>
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;

                <div>
                    <b-field grouped group-multiline>
                        <b-select v-model="status">
                            <option value="0"> All Status</option>
                        </b-select>
                    </b-field>
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <div @click="filter" class="control is-hidden-mobile">
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
                <router-link
                    :to="{
                        name: 'purchaseform',
                        query: {
                            edit: false
                        }
                    }"
                    class="has-background-grey-darker has-text-white is-uppercase is-size-7 addform"
                    >Add
                </router-link>
            </div>
            <b-field grouped group-multiline>
                <b-select v-model="perPage" :disabled="!isPaginated">
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
            <b-table
                class="table is-bordered is-striped is-fullwidth search-table inner"
                :data="filteredDataArray"
                :paginated="isPaginated"
                :per-page="perPage"
                :current-page.sync="currentPage"
                :pagination-simple="isPaginationSimple"
                :default-sort-direction="defaultSortDirection"
                default-sort=""
            >
                <template slot-scope="props">
                    <b-table-column
                        field="id"
                        class="is-size-7-mobile"
                        label="ID"
                        width="40"
                        sortable
                        numeric
                        :mobile-cards="hasMobileCards"
                        >{{ props.index + 1 }}</b-table-column
                    >
                    <b-table-column
                        field="invoice_number"
                        class="is-size-7-mobile"
                        label=" Purchase ID"
                        sortable
                        >{{ props.row.invoice_number }}</b-table-column
                    >
                    <b-table-column
                        field="date"
                        class="is-size-7-mobile"
                        label="Date"
                        sortable
                        >{{ formatter(props.row.date) }}</b-table-column
                    >

                    <b-table-column
                        field="total_paid_amount"
                        class="is-size-7-mobile"
                        label="Purchase Price"
                        sortable
                        >{{
                            props.row.currencies_placement == 0
                                ? props.row.currencies_id
                                : ""
                        }}
                        {{ props.row.total_paid_amount }}</b-table-column
                    >
                    <b-table-column
                        field="total"
                        class="is-size-7-mobile"
                        label="Selling Price"
                        sortable
                        >{{
                            props.row.currencies_placement == 0
                                ? props.row.currencies_id
                                : ""
                        }}
                        {{ props.row.total }}</b-table-column
                    >
                    <b-table-column
                        field="balance"
                        class="is-size-7-mobile"
                        label="Inventory officer"
                        sortable
                    >
                        {{ props.row.username }}</b-table-column
                    >
                    <b-table-column
                        field="status"
                        class="is-size-7-mobile"
                        label="status"
                        sortable
                        >Approve</b-table-column
                    >
                    <b-table-column
                        field="Options"
                        class="is-size-7-mobile"
                        label="Options"
                        sortable
                    >
                        <b-dropdown hoverable>
                            <button
                                class="button is-small has-background-grey-darker has-text-white"
                                slot="trigger"
                            >
                                <span>Options</span>
                                <i class="fas fa-caret-down drops"></i>
                            </button>
                            <b-dropdown-item>
                                <router-link
                                    :to="{
                                        name: 'purchaseform',
                                        query: {
                                            id: props.row.invoice_id,
                                            currency: props.row.currency_id,
                                            edit: true
                                        }
                                    }"
                                    class="has-text-dark"
                                >
                                    <i class="fas fa-edit icon1"></i> Edit
                                </router-link>
                            </b-dropdown-item>
                            <b-dropdown-item
                                @click="goToDelete(props.row.invoice_id)"
                            >
                                <i class="fas fa-trash-alt icon1"></i>Delete
                            </b-dropdown-item>
                            <b-dropdown-item
                                @click="savefile(props.row.invoice_id)"
                            >
                                <i class="fas fa-download icon1"></i>Download
                                PDF
                            </b-dropdown-item>
                            <b-dropdown-item
                                v-if="
                                    props.row.status != 'Draft' &&
                                        props.row.status != 'Cancelled'
                                "
                            >
                                <a
                                    @click="
                                        isImageModalActive1 = true;
                                        payment(
                                            props.row.invoice_id,
                                            props.row.balance,
                                            props.row.currencies_id,
                                            props.row.currencies_placement,
                                            props.row.currencydecimal,
                                            props.row.currencythousands
                                        );
                                    "
                                    class="has-text-dark"
                                >
                                    <i class="fas fa-credit-card icon1"></i
                                    >Payments
                                </a>
                            </b-dropdown-item>
                        </b-dropdown>
                    </b-table-column>
                </template>
                <template slot="empty">{{ "No Record Found" }}</template>
            </b-table>
        </section>

        <layout-footer></layout-footer>
    </section>
</template>

<script>
import { mapState, mapActions, mapGetters } from "vuex";
import LayoutHeader from "./layouts/Header.vue";
import LayoutFooter from "./layouts/Footer.vue";
import { Validator } from "vee-validate";
import moment from "moment";
export default {
    components: {
        LayoutHeader,
        LayoutFooter,
        moment
    },

    data() {
        return {
            invoice_status: [],
            indexColor: "",
            logCreditLimit: "",
            submitted: false,
            paymentSymbol: "",
            balanceError: null,
            paymentBalance: 0,
            isImageModalActive: false,
            isImageModalActive1: false,
            isCardModalActive: false,
            search: "",
            data: [],
            invoiceData: [],
            system: "",
            systemValue: "",
            currency: "",
            company: "",
            placement: "",
            decimals: "",
            thousands: "",
            invoice_id: "",
            client_id: "",
            paymentmethod: "",
            invoice: {
                client_id: "",
                date: new Date(),
                company: "",
                invoice_template: "SystimaNX balde.pdf",
                amount: "",
                paymentdate: new Date(),
                paymentmethod: "",
                paymentnotes: ""
            },
            hasMobileCards: false,
            invoiceform: {
                invoice_number: ""
            },
            currencysymbol: "",
            currencyplace: "",
            currencythousands: "",
            currencydecimal: "",
            submitted: false,
            editMode: false,

            isPaginated: true,
            isPaginationSimple: false,
            defaultSortDirection: "asc",
            currentPage: 1,
            perPage: 5,
            company_name: "0",
            status: "0",
            isFetching: false,
            dateoptions: {
                year: "numeric",
                month: "numeric",
                day: "numeric"
            }
        };
    },

    async created() {
        let self = this;

        if (
            this.$route.query.status != null &&
            this.$route.query.status != ""
        ) {
            this.status = this.$route.query.status;

            this.filter();
        }
    },

    mounted: function() {
        this.isImageModalActive = this.$route.query.popup ? true : false;
        this.client_id = this.$route.query.name ? this.$route.query.name : "";

        this.paymentMethod();

        this.customFormatter();
        this.fetchdata();
    },

    computed: {
        ...mapState({
            loading: state => state.isLoading,
            userState: state => state.user
        }),

        filteredDataArray: function() {
            var self = this;

            return this.invoiceData.filter(function(cust) {
                return (
                    cust.invoice_number
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.date
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.expires_on
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.total_amount.indexOf >= 0 ||
                    cust.balance.indexOf >= 0 ||
                    cust.status
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0
                );
            });
        },
        filteredCustomerArray: function() {
            var self = this;
            return this.data.filter(option => {
                return (
                    option
                        .toString()
                        .toLowerCase()
                        .indexOf(self.client_id.toLowerCase()) >= 0
                );
            });
        }
    },

    methods: {
        ...mapActions({
            setLoading: "setLoading"
        }),

        explorerecipeStyle(index) {
            //   if (this.data == null) {
            //         return "background:#fff;";
            //       } else {
            //         return "background:#eef2f7;";
            //       }
        },

        formatter(date) {
            return moment(new Date(date)).format(this.systemValue);
        },

        customFormatter() {
            this.system = "DD/MM/YYYY";
            this.systemValue = "DD/MM/YYYY";
        },

        formatDate(date) {
            return moment(new Date(date)).format(this.systemValue);
        },

        callFunction() {
            var currentDateWithFormat = new Date()
                .toJSON()
                .slice(0, 10)
                .replace(/-/g, "/");
            console.log("cureenctdate", currentDateWithFormat);
        },

        filter() {
            this.setLoading(true);
            const input = {
                Company: this.company_name,
                Status: this.status
            };
            let uri = "/filter";
            axios
                .post(uri, input)
                .then(response => {
                    this.invoiceData = response.data.invoiceview;
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },

        paymentMethod() {
            this.setLoading(true);
            let uri = "/PaymentMethod";
            axios
                .get(uri)
                .then(response => {
                    this.name = response.data.payment_methodDetails;
                    this.invoice.paymentmethod = this.name[0].id;
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },
        savefile(id) {
            this.invoice_id = id;
            let currentObj = this;
            this.setLoading(true);
            axios({
                url: "/pdfview/" + this.invoice_id,
                method: "get",
                responseType: "blob" // important for pdf download
            })
                // axios.post(url, formdata)
                .then(response => {
                    var fileURL = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    var fileLink = document.createElement("a");
                    fileLink.href = fileURL;
                    fileLink.setAttribute("download", "file.pdf");
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    this.fetchdata();
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },

        resetform() {
            this.invoice = {
                client_id: "",
                date: "",
                invoice_template: ""
            };
        },

        fetchdata() {
            this.setLoading(true);
            let uri = "/invoice";
            axios
                .get(uri)
                .then(response => {
                    this.invoiceData = response.data.invoiceview;

                    this.company = response.data.company;
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },

        goToDelete(id) {
            this.$buefy.dialog.confirm({
                message: "Are you sure you want to delete this record?",
                onConfirm: () => this.destroy(id)
            });
        },

        destroy(id) {
            this.setLoading(true);
            let uri = "/invoice/";
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
                    } else {
                        this.$buefy.toast.open({
                            duration: 4000,
                            message: resp.data.message,
                            position: "is-top-right",
                            type: "is-success"
                        });
                    }
                    this.fetchdata();
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },

        getData() {
            if (Object.keys(this.client_id).length > 1) {
                this.isFetching = true;
                let url = "/autoComplete";
                axios
                    .get(url, { params: { client_name: this.client_id } })
                    .then(response => {
                        this.data = response.data.autocomplete;
                    })
                    .catch(error => {
                        this.data = [];
                        throw error;
                    })
                    .finally(() => {
                        this.isFetching = false;
                    });
            }
        },

        saveInvoice() {
            let context = this;
            context.submitted = true;
            context.setLoading(true);
            context.$validator.validate().then(valid => {
                if (valid) {
                    let url = "/invoice";
                    let config = {
                        headers: {
                            Authorization:
                                "Bearer " + sessionStorage.getItem("token"),
                            "content-type": "multipart/form-data",
                            "content-type": "application/json"
                        }
                    };
                    let formdata = new FormData();
                    formdata.append("client_id", context.client_id);
                    formdata.append("company", context.invoice.company);
                    formdata.append(
                        "invoice_template",
                        context.invoice.invoice_template
                    );
                    if (context.invoice.date != "") {
                        var dateObj = new Date(context.invoice.date);
                        var momentObj = moment(dateObj);
                        var momentString = momentObj.format("YYYY-MM-DD");
                        context.invoice.date = momentString;
                        formdata.append("date", context.invoice.date);
                    }
                    axios
                        .post(url, formdata, config)
                        .then(response => {
                            context.invoice_id = response.data.id;
                            if (response.data.status == 1) {
                                context.$buefy.toast.open({
                                    duration: 4000,
                                    message: response.data.message,
                                    type: "is-success",
                                    position: "is-top-right"
                                });
                                context.$router.push({
                                    name: "invoiceform",
                                    query: {
                                        id: response.data.id,
                                        client_id: response.data.client_id,
                                        edit: false
                                    }
                                });
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
                }
                context.setLoading(false);
            });
        },

        payment(
            id,
            balance,
            symbol,
            currencies_placement,
            currencydecimal,
            currencythousands
        ) {
            this.setLoading(true);
            this.invoice_id = id;
            this.paymentBalance = Number(balance);
            this.paymentSymbol = symbol;
            this.placement = currencies_placement;
            this.decimal = currencydecimal;
            this.thousands = currencythousands;
            let uri = "/invoice";
            axios
                .get(uri)
                .then(response => {
                    this.invoice.amount = Number(this.paymentBalance);
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },

        handlesubmit() {
            let context = this;
            context.submitted = true;
            context.balanceError = null;
            if (
                Number(context.paymentBalance) <
                    Number(context.invoice.amount) &&
                context.paymentBalance == "0"
            ) {
                context.balanceError = "Amount is larger";
                return;
            }
            if (context.balanceError == null) {
                context.setLoading(true);
                context.$validator.validate().then(valid => {
                    if (valid) {
                        let url = "/paymentinvoice/" + context.invoice_id;
                        let config = {
                            headers: {
                                Authorization:
                                    "Bearer " + sessionStorage.getItem("token"),
                                "content-type": "multipart/form-data"
                            }
                        };
                        let formdata = new FormData();
                        formdata.append("amount", context.invoice.amount);
                        if (context.invoice.paymentdate != "") {
                            var dateObj = new Date(context.invoice.paymentdate);
                            var momentObj = moment(dateObj);
                            var momentString = momentObj.format("YYYY-MM-DD");
                            context.invoice.paymentdate = momentString;
                            formdata.append(
                                "paymentdate",
                                context.invoice.paymentdate
                            );
                        }
                        formdata.append(
                            "paymentmethod",
                            context.invoice.paymentmethod
                        );
                        formdata.append(
                            "paymentnotes",
                            context.invoice.paymentnotes
                        );
                        axios
                            .post(url, formdata, config)
                            .then(response => {
                                if (response.data.status == 1) {
                                    context.invoice = [];
                                    context.invoice.date = new Date();
                                    context.invoice.company = [];
                                    context.invoice.invoice_template =
                                        "SystimaNX balde.pdf";
                                    context.invoice.paymentdate = new Date();
                                    context.invoice.amount =
                                        context.paymentBalance;

                                    context.$buefy.toast.open({
                                        duration: 4000,
                                        message: response.data.message,
                                        type: "is-success",
                                        position: "is-top-right"
                                    });

                                    this.fetchdata();
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
                    } else {
                        context.setLoading(false);
                    }
                });
            }
        },

        getinvoice() {
            this.setLoading(true);
            let uri = "/invoice";
            axios
                .get(uri)
                .then(response => {
                    this.invoice_id = response.data.id;
                })
                .finally(() => {
                    this.setLoading(false);
                });
        }
    }
};
</script>
<style>
.sect {
    padding: 3rem 1.5rem;
    padding-top: 2rem !important;
}
</style>
