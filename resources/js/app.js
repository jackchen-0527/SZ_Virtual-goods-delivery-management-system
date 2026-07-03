import './bootstrap';
import { createApp } from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';

import AdminDashboard from './components/AdminDashboard.vue';

// 创建 Vue 实例
const app = createApp({});

// 注册 Element Plus 所有的图标组件
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component);
}

// 注册 Element Plus 组件库
app.use(ElementPlus);

// 注册你的核心后台单文件组件
app.component('admin-dashboard', AdminDashboard);

// 确保 DOM 树加载完毕后再执行挂载，防止由于未解析导致页面空白
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('app');
    if (el) {
        app.mount(el);
    }
});
