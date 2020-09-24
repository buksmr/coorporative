import Vue from 'vue';
import Router from 'vue-router'
Vue.use(Router);
import Home from '../views/Home.vue'
//import About from '../views/About.vue'
import Finpay from '../views/finpay.vue'
import Invoicetry from '../views/invoicetry.vue'
import Free from '../views/free.vue'
import Trydemo from '../views/tryDemo.vue'

import Login from '../views/Login.vue'
import Clienttable from '../views/Clienttable.vue'
import Clientdetail from '../views/Clientdetail.vue'

import Clientform from '../views/Clientform.vue'
import Categoryform from '../views/Categoryform.vue'
import Categorytable from '../views/Categorytable.vue'
import Productunitsform from '../views/Productunitsform.vue'
import Productunitstable from '../views/Productunitstable.vue'
import Productform from '../views/Productform.vue'
import Producttable from '../views/Producttable.vue'
import Contributions from '../views/Contributions.vue'

import Loan from '../views/Loan.vue'
import Loanform from '../views/Loanform.vue'
import Purchase from '../views/Purchase.vue'
import Purchaseform from '../views/Purchaseform.vue'
import Frontoffice from '../views/Frontoffice.vue'
import Frontofficeform from '../views/Frontofficeform.vue'
import Recurringinvoice from '../views/Recurringinvoice.vue'
import Recurringinvoiceform from '../views/Recurringinvoiceform.vue'
import PurchasePayments from '../views/PurchasePayments.vue'
import LoanPayments from '../views/LoanPayments.vue'

import Expenses from '../views/Expenses.vue'
import Expenseform from '../views/Expenseform.vue'

import Currenciesform from '../views/Currenciesform.vue'
import Currencies from '../views/Currencies.vue'
import Company from '../views/Company.vue'
import Companyform from '../views/Companyform.vue'
import Paymentmethod from '../views/Paymentmethod.vue'
import Paymentmethodform from '../views/Paymentmethodform.vue'
import PaymentsCollected from '../views/PaymentsCollected.vue'
import ClientStatement from '../views/ClientStatement.vue'
import RevenueClient from '../views/RevenueClient.vue'
import Tax from '../views/Tax.vue'
import Taxform from '../views/Taxform.vue'
import Account from '../views/Account.vue'
import Accountform from '../views/Accountform.vue'
import System from '../views/System.vue'


import auth from '../store/auth';
//import auth from './middleware/auth'

export default new Router({
    mode: "history",
    baseurl: "/api/v1",
	base: 'bank',

    routes: [
        {
            path: '/home',
            name: 'home',
            component: Home,
            meta: {

                requiresAuth: true,
            }

        },


        {
            path: '/finpay',
            name: 'finpay',
            component: Finpay,

        },
        {
            path: '/paymentscollected',
            name: 'paymentscollected',
            component: PaymentsCollected,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/clientstatement',
            name: 'clientstatement',
            component: ClientStatement,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/revenueclient',
            name: 'revenueclient',
            component: RevenueClient,
            meta: {
                requiresAuth: true,
            }

        },

        {
            path: '/invoicetry',
            name: 'invoicetry',
            component: Invoicetry,

        },
        {
            path: '/free',
            name: 'free',
            component: Free,

        },

        {
            path: '/TryDemo',
            name: 'tryDemo',
            component: Trydemo,

        },




        {
            path: '/',
            name: 'login',
            component: Login,

        },

        {
            path: '/clientform',
            name: 'clientform',
            component: Clientform,
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/clienttable',
            name: 'clienttable',
            component: Clienttable,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/clientdetail',
            name: 'clientdetail',
            component: Clientdetail,
            meta: {
                requiresAuth: true,
            }

        },

        {
            path: '/categoryform',
            name: 'categoryform',
            component: Categoryform,
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/categorytable',
            name: 'categorytable',
            component: Categorytable,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/productunitsform',
            name: 'productunitsform',
            component: Productunitsform,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/purchase',
            name: 'purchase',
            component: Purchase,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/purchaseform',
            name: 'purchaseform',
            component: Purchaseform,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/frontoffice',
            name: 'frontoffice',
            component: Frontoffice,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/frontofficeform',
            name: 'frontofficeform',
            component: Frontofficeform,
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/productunitstable',
            name: 'productunitstable',
            component: Productunitstable,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/productform',
            name: 'productform',
            component: Productform,
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/producttable',
            name: 'producttable',
            component: Producttable,
            meta: {
                requiresAuth: true,
            }

        },

        {
            path: '/contributions',
            name: 'Contributions',
            component: Contributions,
            meta: {
                requiresAuth: true,
            }

        },

        {
            path: '/loan',
            name: 'loan',
            component: Loan,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/loanform',
            name: 'loanform',
            component: Loanform,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/recurringinvoice',
            name: 'recurringinvoice',
            component: Recurringinvoice,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/recurringinvoiceform',
            name: 'recurringinvoiceform',
            component: Recurringinvoiceform,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/purchasepayments',
            name: 'purchasepayments',
            component: PurchasePayments,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/loanpayments',
            name: 'loanpayments',
            component: LoanPayments,
            meta: {
                requiresAuth: true,
            }

        },
   
        {
            path: '/currenciesform',
            name: 'currenciesform',
            component: Currenciesform,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/currencies',
            name: 'currencies',
            component: Currencies,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/company',
            name: 'company',
            component: Company,
            meta: {
                requiresAuth: true,
            }

        },

        {
            path: '/expenses',
            name: 'expenses',
            component: Expenses,
            meta: {
                requiresAuth: true,
            }

        },

        {
            path: '/expenseform',
            name: 'expenseform',
            component: Expenseform,
            meta: {
                requiresAuth: true,
            }

        },


        

        {
            path: '/companyform',
            name: 'companyform',
            component: Companyform,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/paymentmethod',
            name: 'paymentmethod',
            component: Paymentmethod,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/paymentmethodform',
            name: 'paymentmethodform',
            component: Paymentmethodform,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/tax',
            name: 'tax',
            component: Tax,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/taxform',
            name: 'taxform',
            component: Taxform,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/account',
            name: 'account',
            component: Account,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/accountform',
            name: 'accountform',
            component: Accountform,
            meta: {
                requiresAuth: true,
            }

        },
        {
            path: '/system',
            name: 'system',
            component: System,
            meta: {
                requiresAuth: true,
            }

        },

    ]
})

