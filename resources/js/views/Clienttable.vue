<template>
    <section class="hero is-fullheight is-default is-bold">
        <layout-header></layout-header>

        <div class="container breadcrums1">
            <h6 class="form-name is-uppercase is-pulled-left is-size-6">
                List Members
            </h6>

            <nav class="breadcrumb is-pulled-right" aria-label="breadcrumbs">
                <ul>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Pages</a>
                    </li>
                    <li class="is-size-7">
                        <a class="has-text-grey" href="#">Users</a>
                    </li>

                    <li class="is-active is-size-7">
                        <a href="#" aria-current="page">Members</a>
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
                        :disabled="loadings"
                        class="button is-info is-samll has-background-grey-darker"
                        >Search</a
                    >
                </div>

                <router-link
                    to="/Clientform"
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
                        :disabled="loadings"
                        v-model="isPaginated"
                        class="is-size-7 is-uppercase"
                        >Paginated</b-switch
                    >
                </div>
            </b-field>

            <b-table
                class="table is-bordered is-striped is-fullwidth search-table inner"
                :data="isEmpty ? [] : filteredCustomers"
                :paginated="isPaginated"
                :per-page="perPage"
                :current-page.sync="currentPage"
                :pagination-simple="isPaginationSimple"
                :default-sort-direction="defaultSortDirection"
                :mobile-cards="hasMobileCards"
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
                        field="client_name"
                        class="is-size-7-mobile"
                        label="Full Name"
                        sortable
                        >{{ props.row.client_name }}</b-table-column
                    >

                    <b-table-column
                        field="email"
                        class="is-size-7-mobile"
                        label="email"
                        sortable
                        >{{ props.row.email }}</b-table-column
                    >

                    <b-table-column
                        field="phone_number"
                        class="is-size-7-mobile"
                        label="Phone Number"
                        sortable
                    >
                        {{ props.row.phone_number }}
                    </b-table-column>

                    <!-- <b-table-column field="photo" class="is-size-7-mobile" label="photo" sortable><img v-bind:src="props.row.photo" style="width:40px"></b-table-column> -->
                    <b-table-column
                        field="amount"
                        class="is-size-7-mobile"
                        label="Amount"
                        sortable
                        >{{
                            props.row.currencies_placement == 0
                                ? props.row.currencies_id
                                : ""
                        }}{{ props.row.amount }}
                        {{
                            props.row.currencies_placement == 1
                                ? props.row.currencies_id
                                : ""
                        }}</b-table-column
                    >

                    <b-table-column
                        field="loan"
                        class="is-size-7-mobile"
                        label="Loan status"
                        sortable
                        >{{ props.row.loan == 0 ? "No" : ""
                        }}{{ props.row.loan == 1 ? "Yes" : "" }}</b-table-column
                    >

                    <b-table-column
                        field="status"
                        class="is-size-7-mobile"
                        label="status"
                        sortable
                        >{{ props.row.status }}</b-table-column
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

                            <b-dropdown-item
                                v-if="props.row.status == 'Active'"
                            >
                                <router-link
                                    :to="{
                                        name: 'clientdetail',
                                        query: { id: props.row.id }
                                    }"
                                    class="has-text-dark"
                                >
                                    <i class="fas fa-search icon1"></i> View
                                </router-link>
                            </b-dropdown-item>
                            <b-dropdown-item>
                                <router-link
                                    :to="{
                                        name: 'clientform',
                                        query: { id: props.row.id }
                                    }"
                                    class="has-text-dark"
                                >
                                    <i class="fas fa-edit icon1"></i> Edit
                                </router-link>
                            </b-dropdown-item>
                            <b-dropdown-item
                                v-if="props.row.status == 'Active'"
                            >
                                <router-link
                                    :to="{
                                        name: 'loanform',
                                        query: { name: props.row.client_name }
                                    }"
                                    class="has-text-dark"
                                >
                                    <i class="fas fa-file icon1"></i>Book Loan
                                </router-link>
                            </b-dropdown-item>

                            <b-dropdown-item @click="savefiles(props.row.id)">
                                <i class="fas fa-download icon1"></i>Download
                                PDF
                            </b-dropdown-item>
                        </b-dropdown>
                    </b-table-column>
                </template>
                <template slot="empty">
                    <h4>
                        <center>{{ "No record found" }}</center>
                    </h4>
                </template>
            </b-table>
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
            loadings: true,
            search: "",
            users: [],

            isPaginated: true,
            isPaginationSimple: false,
            defaultSortDirection: "asc",
            currentPage: 1,
            perPage: 5,
            hasMobileCards: false,
            loanstatus: "yes",
            isEmpty: false
        };
    },

    computed: {
        ...mapState({
            loading: state => state.isLoading,
            userState: state => state.user
        }),

        filteredCustomers: function() {
            var self = this;
            return this.users.filter(function(cust) {
                return (
                    cust.client_name
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.email
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.status
                        .toLowerCase()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.amount
                        .toLocaleString()
                        .indexOf(self.search.toLowerCase()) >= 0 ||
                    cust.phone_number
                        .toLocaleString()
                        .indexOf(self.search.toLowerCase()) >= 0
                );
            });
        }
    },
    mounted: function() {
        this.fetchuser();
    },
    methods: {
        ...mapActions({
            setLoading: "setLoading"
        }),
        fetchuser() {
            let url = "/Client";
            this.setLoading(true);
            this.loadings = true;
            axios
                .get(url)
                .then(response => {
                    this.users = response.data.clientDetails;
                    //console.log("mobile_number", response.data.clientDetails);
                })
                .finally(() => {
                    this.loadings = false;
                    this.setLoading(false);
                });
        },

        goToDelete(id) {
            this.$buefy.dialog.confirm({
                message: "Are you sure you want to delete this record?",
                onConfirm: () => this.destroy(id)
            });
        },

        savefiles(id) {
            let currentObj = this;
            this.setLoading(true);

            axios({
                url: "/reportdownload/" + id,
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
                })
                .finally(() => {
                    this.setLoading(false);
                });
        },

        destroy(id) {
            let url = "/Client/";
            this.setLoading(true);
            this.loadings = true;
            axios
                .delete(url + id)
                .then(resp => {
                    if (resp.data.status == 1) {
                        this.$buefy.toast.open({
                            duration: 4000,
                            message: resp.data.message,
                            title: "deleted successfully",
                            type: "is-success",
                            position: "is-bottom-right"
                        });
                        this.fetchuser();
                    } else {
                        this.$buefy.toast.open({
                            duration: 1000,
                            message: resp.data.message,
                            title: "deleted failed",
                            position: "is-top-right",
                            type: "is-danger"
                        });
                    }
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.loadings = false;
                    this.setLoading(false);
                });
        }
    }
};
</script>

<style lang="scss" scoped>
.search-table.table-wrapper {
    overflow: auto !important;
}
</style>
