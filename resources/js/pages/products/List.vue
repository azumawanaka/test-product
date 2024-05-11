<script setup>
    import axios from 'axios';
    import { ref, onMounted, watch } from 'vue';
    import { Bootstrap4Pagination } from 'laravel-vue-pagination';

    import { useToastr } from '../../toastr';
    const toastr = useToastr();

    import Items from './Items.vue';
    import Search from '../../components/searchbars/products/Search.vue';

    const products = ref([]);
    const formValues = ref();
    const form = ref(null);
    const loading = ref(false);
    const selectedProducts = ref([]);
    const parentChecked = ref(false);
    const searchParam = ref({
        page: 1,
        query: '',
        limit: 2,
    });

    const getProducts = (param) => {
        searchParam.value = param;
        axios.get(`/api/products?page=${searchParam.value.page}`, {
            params: {
                query: searchParam.value.query,
                limit: searchParam.value.limit,
            }
        })
        .then((response) => {
            products.value = response.data;
        })
    };

    const changePage = (num) => {
        searchParam.value.page = num;
        getProducts(searchParam.value);
    };

    const productDeleted = (productId) => {
        // products.value.data = products.value.data.filter(product => product.id !== product);
        getProducts(searchParam.value);
    };

    const checkAllChildren = (el) => {
        const productIds = products.value.data.map(product => product.id);

        $('.product_checkbox').prop('checked', el.srcElement.checked).change();

        if (el.srcElement.checked) {
            selectedProducts.value = productIds;
        } else {
            selectedProducts.value = [];
        }
    };

    const toggleSelection = (product) => {
        const index = selectedProducts.value.indexOf(product.id);
        if (index === -1) {
            selectedProducts.value.push(product.id);
        } else {
            selectedProducts.value.splice(index, 1);
        }
    };

    const bulkDelete = () => {
        axios.delete(`/api/products`, {
            data: {
                ids: selectedProducts.value
            }
        })
        .then((response) => {
            toastr.success(response.data.message);

            selectedProducts.value = {};

            $('.bulk-delete').prop('checked', false).change();

            getProducts(searchParam.value);
        });
    }

    watch(formValues, (newValues) => {
        if (form.value) {
            form.value.setValues(newValues);
        }
    });

    onMounted(() => {
        getProducts(searchParam.value);
    });

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="mb-3">
                    <a href="/admin/products/create" class="btn btn-primary btn-sm">Create</a>

                    <button v-if="selectedProducts.length > 0" type="button" class="btn btn-danger btn-sm ml-2" @click="bulkDelete">
                        Delete Selected
                    </button>
                </div>

                <div class="col-md-3">
                    <Search :page="searchParam.page" :limit="searchParam.limit" @toggle-search="getProducts" />
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hovered">
                            <thead>
                                <tr>
                                    <th style="width: 10px"><input type="checkbox" class="bulk-delete" v-model="parentChecked" @change="checkAllChildren"></th>
                                    <th style="width: 50px">ID</th>
                                    <th>Name</th>
                                    <th>Descriptions</th>
                                    <th>Category</th>
                                    <th>Date Added</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                            <tbody v-if="products.data && products.data.length > 0">
                                <Items v-for="(product, index) in products.data" :key="product.id"
                                    :product=product
                                    :index=index
                                    @product-deleted="productDeleted"
                                    @toggle-selection="toggleSelection"  />
                            </tbody>

                            <tfoot v-else>
                                <tr>
                                    <td colspan="6">Empty result</td>
                                </tr>
                            </tfoot>
                        </table>

                        <Bootstrap4Pagination :data="products" @pagination-change-page="changePage"/>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .loading button {
        pointer-events: none; /* Prevent form submission while loading */
        opacity: 0.7; /* Optionally decrease opacity to indicate loading state */
        /* Add additional styling for indicating loading state */
    }
</style>
