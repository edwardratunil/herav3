import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PayrollGrid from './components/PayrollGrid.vue';
import EmployeeSelfService from './components/EmployeeSelfService.vue';
import AdminSettingsView from './components/AdminSettingsView.vue';

document.addEventListener('DOMContentLoaded', () => {
    const adminSettingsMount = document.getElementById('admin-settings-mount');
    if (adminSettingsMount) {
        const adminApp = createApp(AdminSettingsView);
        adminApp.use(createPinia());
        adminApp.mount(adminSettingsMount);
    }

    const payrollMount = document.getElementById('payroll-grid-mount');
    if (payrollMount) {
        const payrollApp = createApp(PayrollGrid);
        payrollApp.use(createPinia());
        payrollApp.mount(payrollMount);
    }

    const essMount = document.getElementById('ess-mount');
    if (essMount) {
        const essApp = createApp(EmployeeSelfService);
        essApp.use(createPinia());
        essApp.mount(essMount);
    }
});
