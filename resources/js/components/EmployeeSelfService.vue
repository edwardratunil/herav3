<template>
  <div class="w-full max-w-4xl mx-auto space-y-6">
    <!-- Profile & Quick Info Card -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-900 text-white rounded-2xl p-5 sm:p-6 shadow-md">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <span class="inline-block text-xs uppercase tracking-wider text-emerald-200 font-semibold bg-emerald-700/50 px-2.5 py-0.5 rounded-full mb-1">
            Faculty Self-Service
          </span>
          <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Prof. Antonio P. Luna</h1>
          <p class="text-xs sm:text-sm text-emerald-100">
            Associate Professor • Dept of Physical Sciences • ID: FAC-2026-01
          </p>
        </div>
        <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 border-emerald-700/60 pt-3 sm:pt-0">
          <span class="text-xs text-emerald-200">Current Cutoff Net Pay</span>
          <span class="text-2xl font-extrabold text-white">₱20,325.90</span>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs (Touch targets >= 44px) -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 overflow-x-auto gap-2">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        type="button"
        class="min-h-[44px] px-4 py-2.5 text-sm font-semibold whitespace-nowrap transition-colors border-b-2"
        :class="activeTab === tab.id
          ? 'border-emerald-600 text-emerald-700 dark:text-emerald-400'
          : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
        @click="activeTab = tab.id"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Tab 1: Payslip View -->
    <div v-if="activeTab === 'payslips'" class="space-y-4">
      <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-slate-900 dark:text-white text-base">
            Latest Payslip (Oct 1 - Oct 15, 2026)
          </h3>
          <button
            type="button"
            class="min-h-[44px] px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white dark:bg-emerald-600 dark:hover:bg-emerald-700 text-xs font-semibold rounded-lg shadow-sm transition active:scale-95"
            @click="downloadPdf"
          >
            Download Encrypted PDF
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <!-- Earnings Breakdown -->
          <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800 space-y-2">
            <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] pb-1 border-b border-slate-200 dark:border-slate-700">
              Earnings
            </h4>
            <div class="flex justify-between">
              <span class="text-slate-500">Basic Pay (Semi-monthly):</span>
              <span class="font-mono font-medium">₱20,000.00</span>
            </div>
            <div class="flex justify-between text-indigo-600 dark:text-indigo-400">
              <span>Teaching Overload (3.0 units @ ₱850):</span>
              <span class="font-mono font-medium">₱2,550.00</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">De Minimis Allowance:</span>
              <span class="font-mono font-medium">₱1,000.00</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
              <span>Gross Compensation:</span>
              <span class="font-mono text-emerald-600 dark:text-emerald-400">₱23,550.00</span>
            </div>
          </div>

          <!-- Deductions Breakdown -->
          <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800 space-y-2">
            <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] pb-1 border-b border-slate-200 dark:border-slate-700">
              Deductions
            </h4>
            <div class="flex justify-between">
              <span class="text-slate-500">SSS EE Share:</span>
              <span class="font-mono font-medium">-₱787.50</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">PhilHealth EE Share (2.5%):</span>
              <span class="font-mono font-medium">-₱500.00</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Pag-IBIG EE Share:</span>
              <span class="font-mono font-medium">-₱100.00</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">BIR Withholding Tax:</span>
              <span class="font-mono font-medium">-₱1,836.60</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-700 font-bold text-rose-600 dark:text-rose-400">
              <span>Total Deductions:</span>
              <span class="font-mono">-₱3,224.10</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 2: Daily Time Record (DTR) -->
    <div v-if="activeTab === 'dtr'" class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-900 dark:text-white text-base">Biometric DTR Logs (Current Cutoff)</h3>
          <p class="text-xs text-slate-500">Synced from Campus Biometric Terminals • Grace Period: 15 mins</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
          Verified Logs
        </span>
      </div>

      <!-- DTR Records Table -->
      <div class="overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-lg">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold uppercase tracking-wider">
            <tr>
              <th scope="col" class="px-4 py-2.5">Date</th>
              <th scope="col" class="px-3 py-2.5">Time In</th>
              <th scope="col" class="px-3 py-2.5">Time Out</th>
              <th scope="col" class="px-3 py-2.5">Tardiness</th>
              <th scope="col" class="px-3 py-2.5">Undertime</th>
              <th scope="col" class="px-3 py-2.5">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-mono">
            <tr v-for="log in dtrLogs" :key="log.date" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
              <td class="px-4 py-2.5 font-sans font-medium text-slate-900 dark:text-white">{{ log.date }}</td>
              <td class="px-3 py-2.5 text-slate-700 dark:text-slate-300">{{ log.timeIn }}</td>
              <td class="px-3 py-2.5 text-slate-700 dark:text-slate-300">{{ log.timeOut }}</td>
              <td class="px-3 py-2.5" :class="log.tardinessMin > 0 ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-slate-400'">
                {{ log.tardinessMin > 0 ? `${log.tardinessMin} mins` : '—' }}
              </td>
              <td class="px-3 py-2.5 text-slate-400">{{ log.undertimeMin > 0 ? `${log.undertimeMin} mins` : '—' }}</td>
              <td class="px-3 py-2.5 font-sans">
                <span
                  class="px-2 py-0.5 rounded text-[10px] font-semibold"
                  :class="log.status === 'present' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'"
                >
                  {{ log.status.toUpperCase() }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab 3: Leave Balances & Filing -->
    <div v-if="activeTab === 'leave'" class="space-y-4">
      <!-- Leave Balance Badges -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div v-for="bal in leaveBalances" :key="bal.type" class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 text-center shadow-sm">
          <span class="text-xs font-semibold text-slate-500 uppercase block">{{ bal.name }}</span>
          <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 my-1 block">{{ bal.remaining }}</span>
          <span class="text-[10px] text-slate-400 font-mono">Allocated: {{ bal.allocated }} • Used: {{ bal.used }}</span>
        </div>
      </div>

      <!-- File Leave Request Card -->
      <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 dark:text-white text-base">File New Leave Application</h3>
        
        <form @submit.prevent="submitLeave" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Leave Type</label>
            <select v-model="leaveForm.type" class="w-full min-h-[44px] px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
              <option value="SL">Sick Leave (SL)</option>
              <option value="VL">Vacation Leave (VL)</option>
              <option value="SOLO_PARENT">Solo Parent Leave</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Number of Days</label>
            <input type="number" step="0.5" min="0.5" v-model="leaveForm.days" class="w-full min-h-[44px] px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" required>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Start Date</label>
            <input type="date" v-model="leaveForm.startDate" class="w-full min-h-[44px] px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" required>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">End Date</label>
            <input type="date" v-model="leaveForm.endDate" class="w-full min-h-[44px] px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" required>
          </div>

          <div class="sm:col-span-2">
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Reason / Justification</label>
            <textarea v-model="leaveForm.reason" rows="2" class="w-full p-3 border border-slate-300 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white" placeholder="Medical condition, personal matters, or emergency..." required></textarea>
          </div>

          <div class="sm:col-span-2 flex justify-end">
            <button type="submit" class="min-h-[44px] px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition active:scale-95">
              Submit Leave for Dean & HR Approval
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tab 4: Faculty Load Overview -->
    <div v-if="activeTab === 'workload'" class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-900 dark:text-white text-base">Approved Teaching Load (1st Sem 2026-2027)</h3>
          <p class="text-xs text-slate-500">Institutional Regular Cap: 18.00 Units • Approved by Dean: Aug 28, 2026</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
          Dean Verified
        </span>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
          <span class="text-[11px] text-slate-400 uppercase block">Lecture Units</span>
          <span class="text-lg font-bold text-slate-800 dark:text-slate-200">18.00</span>
        </div>
        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
          <span class="text-[11px] text-slate-400 uppercase block">Lab Hours (x0.75)</span>
          <span class="text-lg font-bold text-slate-800 dark:text-slate-200">4.00 (3.00)</span>
        </div>
        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
          <span class="text-[11px] text-slate-400 uppercase block">Total Load</span>
          <span class="text-lg font-bold text-slate-800 dark:text-slate-200">21.00</span>
        </div>
        <div class="p-3 bg-indigo-50 dark:bg-indigo-950/60 rounded-lg">
          <span class="text-[11px] text-indigo-600 dark:text-indigo-400 uppercase block font-semibold">Credited Overload</span>
          <span class="text-lg font-extrabold text-indigo-700 dark:text-indigo-300">3.00 Units</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const activeTab = ref('payslips');

const tabs = [
  { id: 'payslips', label: 'Payslip & Salary' },
  { id: 'dtr', label: 'Daily Time Record' },
  { id: 'leave', label: 'Leave & Balances' },
  { id: 'workload', label: 'Teaching Workload' },
];

const dtrLogs = ref([
  { date: 'Oct 01, 2026', timeIn: '07:55 AM', timeOut: '05:05 PM', tardinessMin: 0, undertimeMin: 0, status: 'present' },
  { date: 'Oct 02, 2026', timeIn: '08:25 AM', timeOut: '05:00 PM', tardinessMin: 25, undertimeMin: 0, status: 'present' },
  { date: 'Oct 05, 2026', timeIn: '—', timeOut: '—', tardinessMin: 0, undertimeMin: 0, status: 'on_leave' },
  { date: 'Oct 06, 2026', timeIn: '07:58 AM', timeOut: '05:10 PM', tardinessMin: 0, undertimeMin: 0, status: 'present' },
]);

const leaveBalances = ref([
  { name: 'Sick Leave', remaining: '13.0', allocated: '15.0', used: '2.0' },
  { name: 'Vacation Leave', remaining: '15.0', allocated: '15.0', used: '0.0' },
  { name: 'Solo Parent', remaining: '7.0', allocated: '7.0', used: '0.0' },
  { name: 'Emergency Leave', remaining: '3.0', allocated: '3.0', used: '0.0' },
]);

const leaveForm = ref({
  type: 'SL',
  days: 1.0,
  startDate: '',
  endDate: '',
  reason: '',
});

function submitLeave() {
  alert(`Leave request submitted for ${leaveForm.value.days} days of ${leaveForm.value.type}. Sent to Dept Head for approval.`);
}

function downloadPdf() {
  alert('Downloading AES-256 encrypted PDF payslip with employee DOB password protection.');
}
</script>
