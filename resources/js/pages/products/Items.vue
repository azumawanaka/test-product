<script setup>
    import { ref } from 'vue';
    import { formatDate } from '../../helper.js';
    import ConfirmDelete from '../../components/modals/products/ConfirmDelete.vue';

    const props = defineProps({
        product: Object,
        index: Number,
    });

    const emit = defineEmits(['productDeleted', 'editProduct', 'toggleSelection']);
    const productId = ref(null);

    const confirmProductDeletion = (product) => {
        productId.value = product.id;
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteProduct(product.id).then((response) => {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Product has been deleted.",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    emit('productDeleted', productId.value);
                }).catch((error) => {
                    Swal.fire({
                        title: "Error!",
                        text: "An error occured. Please try again later!",
                        icon: "error"
                    });
                });
            }
        });
    };

    const deleteProduct = (productId) => {
        return axios.delete(`/api/products/${productId}`);
    };

    const toggleSelection = (v) => {
        emit('toggleSelection', v)
    }

</script>

<template>

    <tr>
        <td><input type="checkbox" class="product_checkbox" @change="toggleSelection(product)"></td>
        <td>{{ product.id }}</td>
        <td>{{ product.name }}</td>
        <td>{{ product.descriptions.length > 50 ? product.descriptions.slice(0, 50) + '...' : product.descriptions }}</td>
        <td>{{ product.category }}</td>
        <td>{{ formatDate(product.date_added) }}</td>
        <td>
            <a :href="`/admin/products/${product.id}/edit`"
                class="text-info"><i class="fa fa-edit"></i> </a>

            <a href="#"
                class="text-danger ml-2"
                @click="confirmProductDeletion(product)"><i class="fa fa-trash"></i> </a>
        </td>
    </tr>

    <ConfirmDelete :product_id="productId" @toggle-delete="deleteProduct" />

</template>
