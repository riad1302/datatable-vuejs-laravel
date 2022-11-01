<template>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Simple Vue.js Pagination Example with Laravel</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody
                                    v-if="
                                        customers &&
                                        customers.data &&
                                        customers.data.length > 0
                                    "
                                >
                                    <tr
                                        v-for="(
                                            customer, index
                                        ) in customers.data"
                                        :key="index"
                                    >
                                        <td>{{ customer.id }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.phone }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td align="center" colspan="3">
                                            No record found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <pagination
                                align="center"
                                :data="customers"
                                @pagination-change-page="getCustomers"
                            ></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import pagination from "laravel-vue-pagination";
export default {
    components: {
        pagination,
    },
    data() {
        return {
            customers: {},
        };
    },
    mounted() {
        console.log("Component mounted.");
        this.getCustomers();
    },
    methods: {
        getCustomers(page = 1) {
            axios
                .get("/api/customer?page=" + page)
                .then((response) => {
                    this.customers = response.data;
                })
                .catch((error) => {});
        },
    },
};
</script>
