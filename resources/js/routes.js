import Login from './pages/auth/Login.vue';
import Register from './pages/auth/Register.vue';
import Dashboard from './components/Dashboard.vue';
import UserList from './pages/users/UserList.vue';
import ProductList from './pages/products/List.vue';
import ProductCreate from './pages/products/Create.vue';
import ProductEdit from './pages/products/Edit.vue';

export default [
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
    },
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UserList,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/products',
        name: 'admin.products',
        component: ProductList,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/products/create',
        name: 'admin.products.create',
        component: ProductCreate,
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/products/:id/edit',
        name: 'admin.products.edit',
        component: ProductEdit,
        meta: { requiresAuth: true },
    },
]
