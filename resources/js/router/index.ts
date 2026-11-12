import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import HomePage from '../pages/HomePage.vue';
import TablePage from '../pages/TablePage.vue';

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: HomePage,
    },
    {
        path: '/table',
        name: 'table',
        component: TablePage,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
