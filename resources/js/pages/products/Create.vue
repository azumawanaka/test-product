<script setup>
    import axios from 'axios';
    import { ref } from 'vue';
    import { Form, Field } from 'vee-validate';

    import * as yup from 'yup';
    import { useToastr } from '../../toastr';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';

    const toastr = useToastr();
    const loading = ref(false);
    const isValid = ref(true);
    const currentStep = ref(1);
    const categories = ref([
        {
            code: '',
            name: 'Select Category',
        },
        {
            code: 'category_a',
            name: 'Category A',
        },
        {
            code: 'category_b',
            name: 'Category B',
        },
        {
            code: 'category_c',
            name: 'Category C',
        },
    ]);
    const filePreviews = ref([]);

    const formData = ref({
        name: '',
        category: '',
        descriptions: '',
        images: [],
        date_added: ''
    });

    // Define Yup schema for validation
    const schema = yup.object().shape({
        name: yup.string().required('Name is required'),
        category: yup.string().required('Category is required'),
        descriptions: yup.string().required('Description is required'),
    });

    // Function to validate form data
    const validateForm = async () => {
        try {
            await schema.validate(formData.value, { abortEarly: false });
            return true;
        } catch (error) {
            toastr.error('An error occurred. Please try again!');
            return false;
        }
    };

    const nextStep = async () => {
        isValid.value = false;
        if (currentStep.value === 1) {
            isValid.value = await validateForm();
        } else if (currentStep.value === 2) {
            if (formData.value.images.length > 0) {
                isValid.value = true;
            } else {
                toastr.error('Please add some images!');
            }
        }

        if (isValid.value) {
            currentStep.value++;
        }
    };

    const prevStep = () => {
        currentStep.value--;
    };

    const handleChange = (event) => {
        const selectedFiles = event.target.files;
        formData.value.images = selectedFiles;

        filePreviews.value = [];
        for (let i = 0; i < selectedFiles.length; i++) {
            const reader = new FileReader();

            reader.onload = () => {
                filePreviews.value.push(reader.result);
            };

            if (selectedFiles[i]) {
                reader.readAsDataURL(selectedFiles[i]);
            }
        }
    };

    const deleteFile = (index) => {
        filePreviews.value.splice(index, 1);
    };

    const checkDateTime = () => {
        if (formData.value.date_added === '') {
            toastr.error('Date and time is required.');
        }
    };

    const submitForm = async () => {
        const isValid = await validateForm();
        const dateAdded = formData.value.date_added;

        if (isValid && dateAdded !== '') {
            const formDataToSend = new FormData();
            formDataToSend.append('name', formData.value.name);
            formDataToSend.append('category', formData.value.category);
            formDataToSend.append('descriptions', formData.value.descriptions);
            formDataToSend.append('date_added', new Date(formData.value.date_added).toISOString());

            for (let i = 0; i < formData.value.images.length; i++) {
                formDataToSend.append('images[]', formData.value.images[i]);
            }

            axios.post('/api/products', formDataToSend)
                .then((response) => {
                    loading.value = false;

                    console.log(response);

                    Swal.fire({
                        title: "Success!",
                        text: "Product was successfully added!",
                        icon: "success",
                        timer: 3000,
                        willClose: () => {
                            window.location.href = '/admin/products';
                        }
                    });
                }).catch((error) => {
                    toastr.error('An error occurred while saving.');
                    console.error(error);
                });
        }
    };
</script>

<template>
     <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Product</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <Form @submit="submitForm"
                        :validation-schema="schema" v-slot="{ errors }"
                        :class="loading ? 'loading' : ''"
                        enctype="multipart/form-data">

                        <div class="form-validation mb-5">
                            <div v-show="currentStep === 1">
                                <div class="input-group mb-3">
                                    <Field type="text" id="name" name="name" v-model="formData.name" placeholder="Enter Product Name" class="form-control" :class="{ 'is-invalid': errors.name }" />
                                    <span class="invalid-feedback">{{ errors.name }}</span>
                                </div>
                                <div class="input-group mb-3">
                                    <Field name="category" as="select" v-model="formData.category" class="form-control" :class="{ 'is-invalid': errors.category }">
                                        <option v-for="(category, index) in categories" :key="index" :value="category.code">{{ category.name }}</option>
                                    </Field>
                                    <span class="invalid-feedback">{{ errors.category }}</span>
                                </div>
                                <div class="input-group mb-3">
                                    <Field as="textarea" name="descriptions" id="descriptions" v-model="formData.descriptions" placeholder="Add descriptions..." cols="30" rows="5" class="form-control" :class="{ 'is-invalid': errors.descriptions }" />
                                    <span class="invalid-feedback">{{ errors.descriptions }}</span>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary btn-md" @click="nextStep">Next</button>
                                </div>
                            </div>

                            <div v-show="currentStep === 2">
                                <h2>Step 2</h2>
                                <hr />
                                <div class="row">
                                    <div v-for="(prev, index) in filePreviews" :key="index" class="d-flex flex-column justify-content-between col-md-1 mb-3 border py-2">
                                        <img :src="prev" alt="Product Image Preview" class="img-fluid">
                                        <button type="button" class="btn btn-block btn-xs btn-danger py-0" @click="deleteFile(index)">
                                            Delete
                                        </button>
                                    </div>

                                    <div class="col-md-1 mb-3 ml-2 border py-2 h3 d-flex align-items-center justify-content-center text-info hover">
                                        <i class="fa fa-plus-square"></i>

                                        <Field name="images" v-slot="{ value, errorMessage  }" rules="required">
                                            <input type="file" id="fileBtn" @change="handleChange($event)" accept="image/*" multiple />
                                            <span v-if="errorMessage" class="text-danger">{{ errorMessage }}</span>
                                        </Field>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-warning btn-md mr-2" @click="prevStep">Previous</button>
                                    <button class="btn btn-primary btn-md" @click="nextStep">Next</button>
                                </div>
                            </div>

                            <div v-show="currentStep === 3">
                                <h2>Step 3</h2>
                                <div class="input-group mb-3">
                                    <label>Date and Time</label>
                                    <VueDatePicker v-model="formData.date_added" />
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-warning btn-md mr-2" @click="prevStep">Previous</button>
                                    <button class="btn btn-primary btn-md" type="submit" @click="checkDateTime">Submit</button>
                                </div>
                            </div>

                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
input#fileBtn {
    position: absolute;
    font-size: 12px;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9;
    opacity: 0;
}

.hover:hover {
    background-color: #f0f0f0;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    transition: linear .3s;
}
</style>
