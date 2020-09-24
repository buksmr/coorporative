<template>
    <section class="hero is-fullheight is-default is-bold">
        <layout-header></layout-header>

        <div class="container breadcrums1">
            <h6 class="form-name is-uppercase is-pulled-left is-size-6">
                List Expenses
            </h6>

            <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
                <ul>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Pages</a>
                    </li>

                    <li class="is-active is-size-7">
                        <a href="#" aria-current="page">Expense</a>
                    </li>
                </ul>
            </nav>
        </div>

        <section
            class="container forms-sec has-background-white box is-clearfix"
        >
            <div class="field has-addons is-pulled-right">
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
                    <a
                        :disabled="loading"
                        class="button is-info is-samll has-background-grey-darker"
                        >Search</a
                    >
                </div>
                <router-link
                    to="/expenseform"
                    class="has-background-grey-darker has-text-white is-uppercase is-size-7 addform"
                    >Add</router-link
                >
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
                        :disabled="loading"
                        v-model="isPaginated"
                        class="is-size-7 is-uppercase"
                        >Paginated</b-switch
                    >
                </div>
            </b-field>

            <b-table
                class="table is-bordered is-striped is-fullwidth search-table inner"
                :data="filteredCustomers"
                :paginated="isPaginated"
                :per-page="perPage"
                :current-page.sync="currentPage"
                :pagination-simple="isPaginationSimple"
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
                        field="expense_no"
                        class="is-size-7-mobile"
                        label="Expense ID"
                        sortable
                        >{{ props.row.expense_no }}</b-table-column
                    >

                    <b-table-column
                        field="title"
                        class="is-size-7-mobile"
                        label="Title"
                        sortable
                        >{{ props.row.title }}</b-table-column
                    >

                    <b-table-column
                        field="amount"
                        class="is-size-7-mobile"
                        label="Amount"
                        sortable
                        >{{ props.row.amount }}</b-table-column
                    >
                    <b-table-column
                        field="date"
                        class="is-size-7-mobile"
                        label="Date"
                        sortable
                        >{{ formatter(props.row.date) }}</b-table-column
                    >

                    <b-table-column
                        field="note"
                        class="is-size-7-mobile"
                        label="Note"
                        sortable
                        >{{ props.row.note }}</b-table-column
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
                                        name: 'expenseform',
                                        query: { id: props.row.id }
                                    }"
                                    class="has-text-dark"
                                >
                                    <i class="fas fa-edit icon1"></i> Edit
                                </router-link>
                            </b-dropdown-item>

                            <b-dropdown-item @click="goToDelete(props.row.id)">
                                <i class="fas fa-trash-alt icon1"></i>Delete
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
import moment, { defaultFormat } from "moment";
export default {
    components: {
        LayoutHeader,
        LayoutFooter
    },

    data() {
        return {
            loading: true,
            search: "",
            data: [],

            isPaginated: true,
            isPaginationSimple: false,
            defaultSortDirection: "asc",
            currentPage: 1,
            perPage: 5,
            hasMobileCards: false
        };
    },
    computed: {
        ...mapState({
            loading: state => state.isLoading,
            userState: state => state.user
        }),
        filteredCustomers: function() {
            var self = this;
            return this.data.filter(function(cust) {
                return (
                    cust.expense_no
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.title
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.amount.indexOf >= 0 ||
                    cust.date.indexOf >= 0
                );
            });
        }
    },

    created: function() {
        this.fetchdata();
    },

    methods: {
        ...mapActions({
            setLoading: "setLoading"
        }),

        formatter(date) {
            return moment(new Date(date)).format("DD/MM/YYYY");
        },
        fetchdata() {
            this.setLoading(true);
            let uri = "/expenses";
            this.loading = true;

            axios
                .get(uri)
                .then(response => {
                    console.log(response);

                    this.data = response.data.expense;
                })
                .finally(() => {
                    this.loading = false;
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
            let uri = "/Taxrate/";
            this.loding = true;
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
                        //   this.getdata();
                    } else {
                        this.$buefy.toast.open({
                            duration: 4000,
                            message: resp.data.message,
                            position: "is-top-right",
                            type: "is-success"
                        });
                    }
                    //    alert(resp.data.message);

                    this.fetchdata();
                })

                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                    this.setLoading(false);
                });
        }
    }
};
</script>
