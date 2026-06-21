const { createApp } = Vue;
const { createRouter, createWebHashHistory } = VueRouter;

const apiUrl = 'http://localhost:8080';

// ==========================================
// 🛠️ AXIOS INTERCEPTORS (Materi Praktikum 14)
// ==========================================
axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('userToken');
        if (token) {
            // Otomatis suntik parameter Authorization Bearer ke semua request Axios
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor untuk menangani respon error 401 global
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            alert('Sesi Anda habis atau token tidak valid. Silakan login kembali.');
            localStorage.clear();
            window.location.href = '#/login';
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

// Rute SPA (Praktikum 12 & 13)
const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/about', component: About, meta: { requiresAuth: true } },
    { path: '/artikel', component: Artikel, meta: { requiresAuth: true } }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

// Navigation Guards
router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('isLoggedIn') === 'true';
    if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
        alert('Akses Ditolak! Anda harus login terlebih dahulu.');
        next('/login'); 
    } else {
        next();
    }
});

const app = createApp({
    data() {
        return {
            isLoggedIn: false
        }
    },
    mounted() {
        this.isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    },
    methods: {
        logout() {
            if (confirm('Apakah Anda yakin ingin keluar aplikasi?')) {
                localStorage.clear(); // Bersihkan token dan status login
                this.isLoggedIn = false;
                this.$router.push('/');
            }
        }
    }
});

app.use(router);
app.mount('#app');